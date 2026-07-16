<?php

namespace App\Http\Controllers;

use App\Models\HotelBooking;
use App\Models\Property;
use App\Models\SupportAttachment;
use App\Models\SupportMessage;
use App\Models\SupportTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class SupportTicketController extends Controller
{
    public function index(Request $request)
    {
        $tickets = SupportTicket::where('requester_id', $request->user()->id)
            ->with(['assignee', 'messages' => fn ($q) => $q->where('is_internal', false)->latest()->limit(1)])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->latest('last_message_at')->paginate(12)->withQueryString();

        return view('support.index', $this->portalData($request) + compact('tickets'));
    }

    public function create(Request $request)
    {
        $user = $request->user();
        $properties = $user->isPropertyOwner() ? $user->properties()->orderBy('name')->get() : collect();
        $bookings = $user->isCustomer()
            ? HotelBooking::where('guest_id', $user->id)->latest()->limit(30)->get()
            : HotelBooking::whereHas('property', fn ($q) => $q->where('owner_id', $user->id))->latest()->limit(30)->get();

        return view('support.create', $this->portalData($request) + compact('properties', 'bookings'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category' => ['required', Rule::in(array_keys($this->categories($request->user())))],
            'priority' => ['required', Rule::in(SupportTicket::PRIORITIES)],
            'subject' => ['required', 'string', 'max:180'],
            'message' => ['required', 'string', 'max:10000'],
            'property_id' => ['nullable', 'integer'],
            'booking_id' => ['nullable', 'integer'],
            'attachments' => ['nullable', 'array', 'max:5'],
            'attachments.*' => ['file', 'mimes:jpg,jpeg,png,webp,pdf,doc,docx', 'max:5120'],
        ]);
        $this->validateContext($request, $data);

        $ticket = DB::transaction(function () use ($request, $data) {
            $ticket = SupportTicket::create([
                'requester_id' => $request->user()->id,
                'requester_type' => $request->user()->role,
                'property_id' => $data['property_id'] ?? null,
                'booking_id' => $data['booking_id'] ?? null,
                'category' => $data['category'],
                'priority' => $data['priority'],
                'subject' => $data['subject'],
                'status' => 'new',
                'requester_last_read_at' => now(),
            ]);
            $message = $ticket->messages()->create(['sender_id' => $request->user()->id, 'body' => $data['message']]);
            $this->storeAttachments($request, $message);
            $ticket->events()->create(['actor_id' => $request->user()->id, 'event' => 'ticket_created']);
            return $ticket;
        });

        return redirect()->route($this->prefix($request).'.show', $ticket)->with('success', "Your request {$ticket->ticket_number} has been sent to our support team.");
    }

    public function show(Request $request, SupportTicket $ticket)
    {
        $this->ensureRequester($request, $ticket);
        $ticket->update(['requester_last_read_at' => now()]);
        $ticket->load(['messages' => fn ($q) => $q->where('is_internal', false)->with(['sender', 'attachments']), 'assignee', 'property', 'booking']);
        return view('support.show', $this->portalData($request) + compact('ticket'));
    }

    public function reply(Request $request, SupportTicket $ticket)
    {
        $this->ensureRequester($request, $ticket);
        abort_if($ticket->status === 'closed', 422, 'This conversation is closed.');
        $data = $request->validate(['message' => ['required', 'string', 'max:10000'], 'attachments' => ['nullable', 'array', 'max:5'], 'attachments.*' => ['file', 'mimes:jpg,jpeg,png,webp,pdf,doc,docx', 'max:5120']]);

        DB::transaction(function () use ($request, $ticket, $data) {
            $message = $ticket->messages()->create(['sender_id' => $request->user()->id, 'body' => $data['message']]);
            $this->storeAttachments($request, $message);
            $ticket->update(['status' => 'waiting_for_support', 'last_message_at' => now(), 'requester_last_read_at' => now(), 'resolved_at' => null]);
            $ticket->events()->create(['actor_id' => $request->user()->id, 'event' => 'requester_replied']);
        });
        return back()->with('success', 'Your message has been sent.');
    }

    public function rate(Request $request, SupportTicket $ticket)
    {
        $this->ensureRequester($request, $ticket);
        abort_unless(in_array($ticket->status, ['resolved', 'closed']), 422);
        $data = $request->validate(['rating' => ['required', 'integer', 'between:1,5'], 'rating_comment' => ['nullable', 'string', 'max:1000']]);
        $ticket->update($data);
        return back()->with('success', 'Thank you for sharing your feedback.');
    }

    private function validateContext(Request $request, array $data): void
    {
        $user = $request->user();
        if (! empty($data['property_id'])) {
            abort_unless($user->isPropertyOwner() && Property::whereKey($data['property_id'])->where('owner_id', $user->id)->exists(), 403);
        }
        if (! empty($data['booking_id'])) {
            $allowed = $user->isCustomer()
                ? HotelBooking::whereKey($data['booking_id'])->where('guest_id', $user->id)->exists()
                : HotelBooking::whereKey($data['booking_id'])->whereHas('property', fn ($q) => $q->where('owner_id', $user->id))->exists();
            abort_unless($allowed, 403);
        }
    }

    private function ensureRequester(Request $request, SupportTicket $ticket): void { abort_unless($ticket->requester_id === $request->user()->id, 403); }
    private function prefix(Request $request): string { return $request->user()->isPropertyOwner() ? 'property-owner.support' : 'support'; }
    private function portalData(Request $request): array { return ['layout' => $request->user()->isPropertyOwner() ? 'pms-layout' : 'customer-layout', 'routePrefix' => $this->prefix($request), 'categories' => $this->categories($request->user())]; }
    private function categories($user): array { return $user->isPropertyOwner() ? ['property_approval' => 'Property approval', 'booking_guest' => 'Booking or guest issue', 'rooms_availability' => 'Rooms & availability', 'rates_promotions' => 'Rates & promotions', 'commission' => 'Commission', 'payout_banking' => 'Payout or banking', 'account_access' => 'Account access', 'technical' => 'Technical problem', 'other' => 'Other'] : ['hotel_booking' => 'Hotel booking', 'flight_booking' => 'Flight booking', 'payment_refund' => 'Payment or refund', 'cancellation' => 'Cancellation', 'account_login' => 'Account or login', 'property_complaint' => 'Property complaint', 'other' => 'Other']; }

    private function storeAttachments(Request $request, SupportMessage $message): void
    {
        foreach ($request->file('attachments', []) as $file) {
            $path = $file->store("support/{$message->support_ticket_id}", 'local');
            $message->attachments()->create(['original_name' => $file->getClientOriginalName(), 'path' => $path, 'mime_type' => $file->getMimeType() ?: 'application/octet-stream', 'size' => $file->getSize()]);
        }
    }
}
