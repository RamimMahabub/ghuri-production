<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Availability extends Model
{
    protected $table = 'availability';
    protected $guarded = [];

    protected $casts = [
        'date' => 'date',
        'price_override' => 'decimal:2',
        'is_closed' => 'boolean',
    ];

    public function roomType(): BelongsTo
    {
        return $this->belongsTo(RoomType::class);
    }

    /**
     * Get the effective price for this date.
     * Falls back to the room type's base price if no override is set.
     */
    public function getEffectivePriceAttribute(): float
    {
        return $this->price_override ?? $this->roomType->base_price_per_night;
    }

    /**
     * Get the number of rooms actually available (after blocking).
     */
    public function getNetAvailableAttribute(): int
    {
        $total = $this->available_rooms ?? $this->roomType->inventory_count;
        return max(0, $total - $this->blocked_rooms);
    }

    public function isWeekend(): bool
    {
        return in_array($this->date->dayOfWeek, [5, 6]); // Fri, Sat
    }
}
