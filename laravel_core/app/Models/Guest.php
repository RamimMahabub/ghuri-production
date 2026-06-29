<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Guest extends Model
{
    protected $guarded = [];

    protected $casts = [
        'preferences' => 'array',
        'tags' => 'array',
        'is_vip' => 'boolean',
        'marketing_opt_in' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
