<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class HotelBooking extends Model
{
    protected $guarded = [];

    protected $casts = [
        'check_in' => 'date',
        'check_out' => 'date',
        'nightly_rate' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'taxes' => 'decimal:2',
        'fees' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'refund_amount' => 'decimal:2',
        'cancelled_at' => 'datetime',
    ];

    /* ── Boot ────────────────────────────────── */

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($booking) {
            if (empty($booking->booking_ref)) {
                $booking->booking_ref = self::generateBookingRef();
            }
        });
    }

    /* ── Relationships ───────────────────────── */

    public function guest(): BelongsTo
    {
        return $this->belongsTo(User::class, 'guest_id');
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function roomType(): BelongsTo
    {
        return $this->belongsTo(RoomType::class);
    }

    public function ratePlan(): BelongsTo
    {
        return $this->belongsTo(RatePlan::class);
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(BookingActivityLog::class)->orderBy('created_at', 'desc');
    }

    public function internalNotes(): HasMany
    {
        return $this->hasMany(InternalNote::class)->orderBy('created_at', 'desc');
    }

    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }

    /* ── Accessors ───────────────────────────── */

    public function getNightsAttribute(): int
    {
        return $this->check_in->diffInDays($this->check_out);
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'pending',
            'confirmed' => 'confirmed',
            'checked_in' => 'checked-in',
            'checked_out' => 'checked-out',
            'cancelled' => 'cancelled',
            'no_show' => 'no-show',
            default => 'info',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Pending',
            'confirmed' => 'Confirmed',
            'checked_in' => 'Checked In',
            'checked_out' => 'Checked Out',
            'cancelled' => 'Cancelled',
            'no_show' => 'No Show',
            default => ucfirst($this->status),
        };
    }

    /* ── Scopes ──────────────────────────────── */

    public function scopeForProperty($query, int $propertyId)
    {
        return $query->where('property_id', $propertyId);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('check_in', '>=', now()->toDateString())
                     ->whereIn('status', ['pending', 'confirmed']);
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['confirmed', 'checked_in']);
    }

    /* ── Helpers ──────────────────────────────── */

    public static function generateBookingRef(): string
    {
        do {
            $ref = 'HTL-' . strtoupper(Str::random(8));
        } while (self::where('booking_ref', $ref)->exists());

        return $ref;
    }

    public function logActivity(string $action, ?string $description = null, ?int $userId = null): BookingActivityLog
    {
        return $this->activityLogs()->create([
            'action' => $action,
            'description' => $description,
            'performed_by' => $userId,
        ]);
    }

    public static function getStatuses(): array
    {
        return ['pending', 'confirmed', 'checked_in', 'checked_out', 'cancelled', 'no_show'];
    }

    public static function getSources(): array
    {
        return ['direct', 'ota', 'walk_in'];
    }
}
