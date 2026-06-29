<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RoomType extends Model
{
    protected $guarded = [];

    protected $casts = [
        'bed_config' => 'array',
        'amenities' => 'array',
        'base_price_per_night' => 'decimal:2',
    ];

    /* ── Relationships ───────────────────────── */

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(RoomTypePhoto::class)->orderBy('sort_order');
    }

    public function ratePlans(): HasMany
    {
        return $this->hasMany(RatePlan::class);
    }

    public function activeRatePlans(): HasMany
    {
        return $this->hasMany(RatePlan::class)->where('is_active', true);
    }

    public function availability(): HasMany
    {
        return $this->hasMany(Availability::class);
    }

    public function rateRules(): HasMany
    {
        return $this->hasMany(RateRule::class);
    }

    public function hotelBookings(): HasMany
    {
        return $this->hasMany(HotelBooking::class);
    }

    /* ── Accessors ───────────────────────────── */

    public function getMaxOccupancyAttribute(): int
    {
        return $this->max_adults + $this->max_children;
    }

    public function getBedConfigDisplayAttribute(): string
    {
        if (!$this->bed_config) return 'N/A';

        return collect($this->bed_config)->map(function ($bed) {
            $count = $bed['count'] ?? 1;
            $type = ucfirst($bed['type'] ?? 'bed');
            return $count > 1 ? "{$count} {$type} beds" : "1 {$type} bed";
        })->implode(' + ');
    }

    public function getPrimaryPhotoUrlAttribute(): string
    {
        $photo = $this->photos()->first();
        return $photo ? asset('storage/' . $photo->file_path) : asset('images/placeholder-room.jpg');
    }

    /* ── Scopes ──────────────────────────────── */

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /* ── Helpers ──────────────────────────────── */

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public static function getBedTypes(): array
    {
        return ['king', 'queen', 'twin', 'double', 'single', 'bunk', 'sofa'];
    }

    public static function getAmenityOptions(): array
    {
        return [
            'AC', 'Smart TV', 'Minibar', 'Safe', 'Balcony', 'Bathtub',
            'Shower', 'Sea View', 'City View', 'Garden View', 'Mountain View',
            'Hair Dryer', 'Coffee Machine', 'Desk', 'Iron', 'Wardrobe',
            'Connecting Rooms', 'Soundproofing', 'Kitchenette',
        ];
    }
}
