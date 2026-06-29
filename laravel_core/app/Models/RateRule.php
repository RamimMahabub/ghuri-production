<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RateRule extends Model
{
    protected $guarded = [];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'adjustment_value' => 'decimal:2',
        'condition_value' => 'array',
        'is_active' => 'boolean',
    ];

    public function roomType(): BelongsTo
    {
        return $this->belongsTo(RoomType::class);
    }

    public static function getRuleTypes(): array
    {
        return [
            'weekend_surcharge' => 'Weekend Surcharge',
            'seasonal' => 'Seasonal Pricing',
            'occupancy' => 'Occupancy-Based',
            'last_minute' => 'Last Minute Deal',
            'early_bird' => 'Early Bird Discount',
            'long_stay' => 'Long Stay Discount',
        ];
    }

    /**
     * Check if this rule applies to a given date.
     */
    public function appliesTo(\Carbon\Carbon $date): bool
    {
        if (!$this->is_active) return false;

        if ($this->start_date && $date->lt($this->start_date)) return false;
        if ($this->end_date && $date->gt($this->end_date)) return false;

        if ($this->rule_type === 'weekend_surcharge') {
            return in_array($date->dayOfWeek, [5, 6]);
        }

        return true;
    }
}
