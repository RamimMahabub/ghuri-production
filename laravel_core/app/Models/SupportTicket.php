<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SupportTicket extends Model
{
    use HasFactory;

    public const STATUSES = ['new', 'open', 'waiting_for_customer', 'waiting_for_support', 'escalated', 'resolved', 'closed'];
    public const PRIORITIES = ['low', 'normal', 'high', 'urgent'];

    protected $fillable = ['ticket_number', 'requester_id', 'requester_type', 'assigned_to', 'property_id', 'booking_id', 'category', 'priority', 'status', 'subject', 'requester_last_read_at', 'staff_last_read_at', 'last_message_at', 'resolved_at', 'resolution_summary', 'rating', 'rating_comment'];

    protected function casts(): array
    {
        return ['requester_last_read_at' => 'datetime', 'staff_last_read_at' => 'datetime', 'last_message_at' => 'datetime', 'resolved_at' => 'datetime'];
    }

    protected static function booted(): void
    {
        static::creating(function (self $ticket) {
            do {
                $number = 'GHR-'.now()->format('Ym').'-'.Str::upper(Str::random(6));
            } while (self::where('ticket_number', $number)->exists());
            $ticket->ticket_number ??= $number;
            $ticket->last_message_at ??= now();
        });
    }

    public function requester() { return $this->belongsTo(User::class, 'requester_id'); }
    public function assignee() { return $this->belongsTo(User::class, 'assigned_to'); }
    public function property() { return $this->belongsTo(Property::class); }
    public function booking() { return $this->belongsTo(HotelBooking::class); }
    public function messages() { return $this->hasMany(SupportMessage::class); }
    public function events() { return $this->hasMany(SupportTicketEvent::class); }

    public function isUnreadFor(User $user): bool
    {
        $readAt = $user->isInternalUser() ? $this->staff_last_read_at : $this->requester_last_read_at;
        return $this->last_message_at && (! $readAt || $this->last_message_at->gt($readAt));
    }
}
