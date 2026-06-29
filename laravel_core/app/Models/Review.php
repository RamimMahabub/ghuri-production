<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    protected $guarded = [];

    protected $casts = [
        'overall_score' => 'decimal:1',
        'cleanliness_score' => 'decimal:1',
        'location_score' => 'decimal:1',
        'service_score' => 'decimal:1',
        'value_score' => 'decimal:1',
        'facilities_score' => 'decimal:1',
        'responded_at' => 'datetime',
        'is_featured' => 'boolean',
    ];

    public function hotelBooking(): BelongsTo
    {
        return $this->belongsTo(HotelBooking::class);
    }

    public function guest(): BelongsTo
    {
        return $this->belongsTo(User::class, 'guest_id');
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function getRatingLabelAttribute(): string
    {
        return match (true) {
            $this->overall_score >= 9 => 'Exceptional',
            $this->overall_score >= 8 => 'Excellent',
            $this->overall_score >= 7 => 'Very Good',
            $this->overall_score >= 6 => 'Good',
            $this->overall_score >= 5 => 'Average',
            default => 'Below Average',
        };
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }
}
