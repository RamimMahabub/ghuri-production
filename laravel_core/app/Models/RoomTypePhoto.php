<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoomTypePhoto extends Model
{
    protected $guarded = [];

    public function roomType(): BelongsTo
    {
        return $this->belongsTo(RoomType::class);
    }

    public function getUrlAttribute(): string
    {
        if (str_starts_with($this->file_path, 'http')) {
            return $this->file_path;
        }

        // Check if file actually exists, if not use a fallback image
        if (!\Illuminate\Support\Facades\Storage::disk('public')->exists($this->file_path)) {
            return 'https://images.unsplash.com/photo-1578683010236-d716f9a3f461?auto=format&fit=crop&w=400&q=80'; // Reliable room placeholder
        }

        return asset('storage/' . $this->file_path);
    }
}
