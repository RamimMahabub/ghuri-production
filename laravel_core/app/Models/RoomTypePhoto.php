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
        
        if (env('CLOUDINARY_URL')) {
            preg_match('/@([a-zA-Z0-9_-]+)/', env('CLOUDINARY_URL'), $matches);
            $cloudName = $matches[1] ?? 'dx9oznwhu';
            return "https://res.cloudinary.com/{$cloudName}/image/upload/" . ltrim($this->file_path, '/');
        }

        return asset('storage/' . $this->file_path);
    }
}
