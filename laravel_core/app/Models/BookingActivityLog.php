<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingActivityLog extends Model
{
    protected $guarded = [];

    public function hotelBooking(): BelongsTo
    {
        return $this->belongsTo(HotelBooking::class);
    }

    public function performer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'performed_by');
    }

    public function getActionIconAttribute(): string
    {
        return match ($this->action) {
            'booked' => '📋',
            'confirmed' => '✅',
            'modified' => '✏️',
            'cancelled' => '❌',
            'checked_in' => '🏨',
            'checked_out' => '🚪',
            'note_added' => '📝',
            default => '📌',
        };
    }
}
