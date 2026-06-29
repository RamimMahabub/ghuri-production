<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RatePlan extends Model
{
    protected $guarded = [];

    protected $casts = [
        'price_supplement_per_adult' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function roomType(): BelongsTo
    {
        return $this->belongsTo(RoomType::class);
    }

    public static function getPlanCodes(): array
    {
        return [
            'RO' => 'Room Only',
            'BB' => 'Bed & Breakfast',
            'HB' => 'Half Board',
            'FB' => 'Full Board',
            'AI' => 'All Inclusive',
        ];
    }

    public function getPlanDisplayNameAttribute(): string
    {
        return self::getPlanCodes()[$this->plan_code] ?? $this->plan_name;
    }
}
