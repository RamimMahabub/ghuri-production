<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Property extends Model
{
    protected $guarded = [];

    protected $casts = [
        'languages_spoken' => 'array',
        'amenities' => 'array',
        'cancellation_policy' => 'array',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'approved_at' => 'datetime',
    ];

    /* ── Relationships ───────────────────────── */

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function photos(): HasMany
    {
        return $this->hasMany(PropertyPhoto::class)->orderBy('sort_order');
    }

    public function roomTypes(): HasMany
    {
        return $this->hasMany(RoomType::class);
    }

    public function activeRoomTypes(): HasMany
    {
        return $this->hasMany(RoomType::class)->where('status', 'active');
    }

    public function hotelBookings(): HasMany
    {
        return $this->hasMany(HotelBooking::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function promotions(): HasMany
    {
        return $this->hasMany(Promotion::class);
    }

    public function commissions(): HasMany
    {
        return $this->hasMany(Commission::class);
    }

    public function payouts(): HasMany
    {
        return $this->hasMany(Payout::class);
    }

    /* ── Accessors ───────────────────────────── */

    public function getCoverPhotoUrlAttribute(): string
    {
        if ($this->cover_photo) {
            if (str_starts_with($this->cover_photo, 'http')) {
                return $this->cover_photo;
            }
            return asset('storage/' . $this->cover_photo);
        }

        if ($this->relationLoaded('photos')) {
            $coverPhoto = $this->photos->where('is_cover', true)->first() ?? $this->photos->first();
        } else {
            $coverPhoto = $this->photos()->where('is_cover', true)->first() ?? $this->photos()->first();
        }

        if ($coverPhoto) {
            return $coverPhoto->url;
        }

        return asset('images/placeholder-hotel.jpg');
    }

    public function getFullAddressAttribute(): string
    {
        return collect([
            $this->address_line_1,
            $this->address_line_2,
            $this->city,
            $this->state,
            $this->country,
            $this->postal_code,
        ])->filter()->implode(', ');
    }

    public function getLowestPriceAttribute(): ?float
    {
        return $this->activeRoomTypes()->min('base_price_per_night');
    }

    public function getAverageRatingAttribute(): ?float
    {
        $avg = $this->reviews()->where('status', 'published')->avg('overall_score');
        return $avg ? round($avg, 1) : null;
    }

    public function getReviewCountAttribute(): int
    {
        return $this->reviews()->where('status', 'published')->count();
    }

    /* ── Scopes ──────────────────────────────── */

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePendingApproval($query)
    {
        return $query->where('status', 'pending_approval');
    }

    public function scopeByCity($query, string $city)
    {
        return $query->where('city', 'like', "%{$city}%");
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByStars($query, int $stars)
    {
        return $query->where('stars', '>=', $stars);
    }

    /* ── Helpers ──────────────────────────────── */

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isPendingApproval(): bool
    {
        return $this->status === 'pending_approval';
    }

    public static function getTypes(): array
    {
        return ['hotel', 'resort', 'villa', 'hostel', 'apartment', 'guesthouse'];
    }

    public function getAmenityGroups(): array
    {
        return $this->amenities ?? [];
    }
}
