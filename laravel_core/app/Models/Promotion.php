<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Promotion extends Model
{
    protected $guarded = [];

    protected $casts = [
        'applies_to' => 'array',
        'valid_from' => 'date',
        'valid_to' => 'date',
        'is_single_use' => 'boolean',
        'is_active' => 'boolean',
        'discount_value' => 'decimal:2',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function isValid(): bool
    {
        if (!$this->is_active) return false;
        if ($this->valid_from && now()->lt($this->valid_from)) return false;
        if ($this->valid_to && now()->gt($this->valid_to)) return false;
        if ($this->max_usage_total && $this->used_count >= $this->max_usage_total) return false;
        return true;
    }

    public function getDiscountDisplayAttribute(): string
    {
        return $this->discount_type === 'percent'
            ? "{$this->discount_value}% off"
            : "$" . number_format($this->discount_value, 2) . " off";
    }

    public function getImageUrlAttribute(): string
    {
        if ($this->image_path) {
            return asset('storage/' . $this->image_path);
        }

        return $this->property 
            ? $this->property->cover_photo_url 
            : 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=400&q=80'; // Fallback image
    }
}
