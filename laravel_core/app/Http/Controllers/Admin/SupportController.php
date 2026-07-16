<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportMessage;
use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SupportController extends Controller
{
    public function index(Request $request)
    {
        $query = SupportTicket::with(['requester', 'assignee', 'property'])->withCount('messages');
        $queue = $request->get('queue', 'all');
        if ($queue === 'unassigned') $query->whereNull('assigned_to')->whereNotIn('status', ['resolved', 'closed']);
        elseif ($queue === 'mine') $query->where('assigned_to', $request->user()->id);
        elseif ($queue === 'urgent') $query->where('priority', 'urgent')->whereNotIn('status', ['resolved', 'closed']);
        elseif ($queue === 'resolved') $query->whereIn('status', ['resolved', 'closed']);
        elseif ($queue === 'open') $query->whereNotIn('status', ['resolved', 'closed']);
        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('search')) $query->where(fn ($q) => $q->where('ticket_number', 'like', '%'.$request->search.'%')->orWhere('subject', 'like', '%'.$request->search.'%')->orWhereHas('requester', fn ($u) => $u->where('name', 'like', '%'.$request->search.'%')->orWhere('email', 'like', '%'.$request->search.'%')));

        $tickets = $query->latest('last_message_at')->paginate(20)->withQueryString();
        $counts = ['unassigned' => SupportTicket::whereNull('assigned_to')->whereNotIn('status', ['resolved', 'closed'])->count(), 'mine' => SupportTicket::where('assigned_to', $request->user()->id)->whereNotIn('status', ['resolved', 'closed'])->count(), 'urgent' => SupportTicket::where('priority', 'urgent')->whereNotIn('status', ['resolved', 'closed'])->count()];
        return view('admin.support.index', compact('tickets', 'counts', 'queue'));
    }

    public function show(Request $request, SupportTicket $ticket)
    {
        $ticket->update(['staff_last_read_at' => now()]);
        $ticket->load(['requester', 'assignee', 'property', 'booking', 'messages.sender', 'messages.attachments', 'events.actor']);
        $agents = User::whereIn('role', ['admin', 'manager', 'support_agent', 'ticketing_officer', 'accounts_officer'])->orderBy('name')->get();
        return view('admin.support.show', compact('ticket', 'agents'));
    }

    public function reply(Request $request, SupportTicket $ticket)
    {
        abort_if($ticket->status === 'closed', 422, 'This ticket is closed.');
        $data = $request->validate(['message' => ['required', 'string', 'max:10000'], 'is_internal' => ['nullable', 'boolean'], 'attachments' => ['nullable', 'array', 'max:5'], 'attachments.*' => ['file', 'mimes:jpg,jpeg,png,webp,pdf,doc,docx', 'max:5120']]);
        DB::transaction(function () use ($request, $ticket, $data) {
            $internal = (bool) ($data['is_internal'] ?? false);
            $message = $ticket->messages()->create(['sender_id' => $request->user()->id, 'body' => $data['message'], 'is_internal' => $internal]);
            foreach ($request->file('attachments', []) as $file) {
                $path = $file->store("support/{$ticket->id}", 'local');
                $message->attachments()->create(['original_name' => $file->getClientOriginalName(), 'path' => $path, 'mime_type' => $file->getMimeType() ?: 'application/octet-stream', 'size' => $file->getSize()]);
            }
            $updates = ['staff_last_read_at' => now(), 'last_message_at' => now()];
            if (! $internal) $updates['status'] = 'waiting_for_customer';
            if (! $ticket->assigned_to) $updates['assigned_to'] = $request->user()->id;
            $ticket->update($updates);
            $ticket->events()->create(['actor_id' => $request->user()->id, 'event' => $internal ? 'internal_note_added' : 'agent_replied']);
        });
        return back()->with('success', ($data['is_internal'] ?? false) ? 'Internal note added.' : 'Reply sent to the requester.');
    }

    public function update(Request $request, SupportTicket $ticket)
    {
        $data = $request->validate(['status' => ['required', Rule::in(SupportTicket::STATUSES)], 'priority' => ['required', Rule::in(SupportTicket::PRIORITIES)], 'assigned_to' => ['nullable', 'exists:users,id'], 'resolution_summary' => ['nullable', 'string', 'max:3000']]);
        if ($data['assigned_to']) abort_unless(User::whereKey($data['assigned_to'])->whereIn('role', ['admin', 'manager', 'support_agent', 'ticketing_officer', 'accounts_officer'])->exists(), 422);
        if (in_array($data['status'], ['resolved', 'closed']) && blank($data['resolution_summary'] ?? $ticket->resolution_summary)) return back()->withErrors(['resolution_summary' => 'Add a resolution summary before resolving this ticket.']);
        $before = $ticket->only(['status', 'priority', 'assigned_to']);
        $data['resolved_at'] = in_array($data['status'], ['resolved', 'closed']) ? ($ticket->resolved_at ?? now()) : null;
        $ticket->update($data);
        $ticket->events()->create(['actor_id' => $request->user()->id, 'event' => 'ticket_updated', 'metadata' => ['before' => $before, 'after' => $ticket->only(['status', 'priority', 'assigned_to'])]]);
        return back()->with('success', 'Ticket details updated.');
    }
}
