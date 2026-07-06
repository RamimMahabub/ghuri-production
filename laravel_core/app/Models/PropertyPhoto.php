<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PropertyPhoto extends Model
{
    protected $guarded = [];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function getUrlAttribute(): string
    {
        if (str_starts_with($this->file_path, 'http')) {
            return $this->file_path;
        }

        if (!\Illuminate\Support\Facades\Storage::disk('public')->exists($this->file_path)) {
            return 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=800&q=80';
        }

        return asset('storage/' . $this->file_path);
    }

    public static function getCategories(): array
    {
        return ['exterior', 'lobby', 'room', 'bathroom', 'pool', 'restaurant', 'view'];
    }
}
