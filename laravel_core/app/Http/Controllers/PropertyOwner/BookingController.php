<?php

namespace App\Http\Controllers\PropertyOwner;

use App\Http\Controllers\Controller;
use App\Models\HotelBooking;
use App\Models\Property;
use App\Services\BookingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    public function __construct(protected BookingService $bookingService) {}

    public function index(Request $request)
    {
        $propertyIds = Property::where('owner_id', Auth::id())->pluck('id');

        $query = HotelBooking::whereIn('property_id', $propertyIds)
            ->with(['guest', 'property', 'roomType', 'ratePlan']);

        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('property_id')) {
            $query->where('property_id', $request->property_id);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('booking_ref', 'like', "%{$search}%")
                  ->orWhereHas('guest', function ($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }
        if ($request->filled('date_from')) {
            $query->where('check_in', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('check_out', '<=', $request->date_to);
        }

        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');
        $query->orderBy($sort, $direction);

        $bookings = $query->paginate(20)->withQueryString();
        $properties = Property::where('owner_id', Auth::id())->get();

        return view('property-owner.bookings.index', compact('bookings', 'properties'));
    }

    public function show(HotelBooking $booking)
    {
        $this->authorizeBooking($booking);

        $booking->load([
            'guest', 'property', 'roomType', 'ratePlan',
            'activityLogs.performer', 'internalNotes.user', 'review',
        ]);

        return view('property-owner.bookings.show', compact('booking'));
    }

    public function create(Request $request)
    {
        $properties = Property::where('owner_id', Auth::id())
            ->with('activeRoomTypes.activeRatePlans')
            ->approved()
            ->get();

        return view('property-owner.bookings.create', compact('properties'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'room_type_id' => 'required|exists:room_types,id',
            'guest_name' => 'required|string|max:255',
            'guest_email' => 'required|email',
            'guest_phone' => 'nullable|string',
            'check_in' => 'required|date|after_or_equal:today',
            'check_out' => 'required|date|after:check_in',
            'adults' => 'required|integer|min:1',
            'children' => 'integer|min:0',
            'special_requests' => 'nullable|string',
        ]);

        // For walk-in bookings, find or create a guest user
        $guest = \App\Models\User::firstOrCreate(
            ['email' => $validated['guest_email']],
            [
                'name' => $validated['guest_name'],
                'password' => bcrypt(\Illuminate\Support\Str::random(16)),
                'phone' => $validated['guest_phone'] ?? null,
                'role' => 'customer',
            ]
        );

        try {
            $booking = $this->bookingService->createBooking([
                'guest_id' => $guest->id,
                'property_id' => $validated['property_id'],
                'room_type_id' => $validated['room_type_id'],
                'check_in' => $validated['check_in'],
                'check_out' => $validated['check_out'],
                'adults' => $validated['adults'],
                'children' => $validated['children'] ?? 0,
                'source' => 'walk_in',
                'status' => 'confirmed',
                'special_requests' => $validated['special_requests'] ?? null,
            ]);

            return redirect()->route('property-owner.bookings.show', $booking)
                ->with('success', 'Walk-in booking created: ' . $booking->booking_ref);
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function confirm(HotelBooking $booking)
    {
        $this->authorizeBooking($booking);
        $this->bookingService->confirmBooking($booking, Auth::id());
        return back()->with('success', 'Booking confirmed.');
    }

    public function cancel(Request $request, HotelBooking $booking)
    {
        $this->authorizeBooking($booking);
        $reason = $request->input('cancel_reason', 'Cancelled by hotel');
        $this->bookingService->cancelBooking($booking, $reason, Auth::id());
        return back()->with('success', 'Booking cancelled.');
    }

    public function checkIn(HotelBooking $booking)
    {
        $this->authorizeBooking($booking);
        $this->bookingService->checkIn($booking, Auth::id());
        return back()->with('success', 'Guest checked in.');
    }

    public function checkOut(HotelBooking $booking)
    {
        $this->authorizeBooking($booking);
        $this->bookingService->checkOut($booking, Auth::id());
        return back()->with('success', 'Guest checked out.');
    }

    public function noShow(HotelBooking $booking)
    {
        $this->authorizeBooking($booking);
        $this->bookingService->markNoShow($booking, Auth::id());
        return back()->with('success', 'Booking marked as no-show.');
    }

    private function authorizeBooking(HotelBooking $booking): void
    {
        $propertyIds = Property::where('owner_id', Auth::id())->pluck('id');
        if (!$propertyIds->contains($booking->property_id)) {
            abort(403);
        }
    }
}
