<?php

namespace App\Services;

use App\Models\Availability;
use App\Models\HotelBooking;
use App\Models\RoomType;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class AvailabilityService
{
    /**
     * Get available rooms for a room type on a specific date.
     */
    public function getAvailableRooms(int $roomTypeId, Carbon $date): int
    {
        $roomType = RoomType::findOrFail($roomTypeId);
        $totalRooms = $roomType->inventory_count;

        // Check for availability overrides
        $availability = Availability::where('room_type_id', $roomTypeId)
            ->where('date', $date->toDateString())
            ->first();

        if ($availability) {
            if ($availability->is_closed) return 0;
            $totalRooms = $availability->available_rooms ?? $totalRooms;
            $totalRooms -= $availability->blocked_rooms;
        }

        // Count active bookings for this date
        $bookedRooms = HotelBooking::where('room_type_id', $roomTypeId)
            ->where('check_in', '<=', $date->toDateString())
            ->where('check_out', '>', $date->toDateString())
            ->whereIn('status', ['confirmed', 'checked_in', 'pending'])
            ->sum('rooms_booked');

        return max(0, $totalRooms - $bookedRooms);
    }

    /**
     * Check availability for a date range.
     * Returns the minimum available rooms across all dates.
     */
    public function checkAvailability(int $roomTypeId, Carbon $checkIn, Carbon $checkOut): int
    {
        $roomType = RoomType::findOrFail($roomTypeId);
        $totalRooms = $roomType->inventory_count;

        // Pre-fetch availability overrides
        $overrides = Availability::where('room_type_id', $roomTypeId)
            ->whereBetween('date', [$checkIn->toDateString(), $checkOut->copy()->subDay()->toDateString()])
            ->get()
            ->keyBy(fn($a) => $a->date->toDateString());

        // Pre-fetch active bookings
        $bookings = HotelBooking::where('room_type_id', $roomTypeId)
            ->where('check_in', '<=', $checkOut->copy()->subDay()->toDateString())
            ->where('check_out', '>', $checkIn->toDateString())
            ->whereIn('status', ['confirmed', 'checked_in', 'pending'])
            ->get();

        $minAvailable = PHP_INT_MAX;
        $period = CarbonPeriod::create($checkIn, $checkOut->copy()->subDay());

        foreach ($period as $date) {
            $dateStr = $date->toDateString();
            $override = $overrides->get($dateStr);
            $dailyTotal = $totalRooms;

            if ($override) {
                if ($override->is_closed) return 0;
                $dailyTotal = $override->available_rooms ?? $totalRooms;
                $dailyTotal -= $override->blocked_rooms;
            }

            $bookedRooms = $bookings->filter(function ($b) use ($date) {
                return $b->check_in->lte($date) && $b->check_out->gt($date);
            })->sum('rooms_booked');

            $available = max(0, $dailyTotal - $bookedRooms);
            $minAvailable = min($minAvailable, $available);

            if ($minAvailable <= 0) return 0;
        }

        return $minAvailable === PHP_INT_MAX ? 0 : $minAvailable;
    }

    /**
     * Get availability data for a calendar view (monthly).
     */
    public function getCalendarData(int $roomTypeId, Carbon $startDate, Carbon $endDate): array
    {
        $roomType = RoomType::findOrFail($roomTypeId);
        $period = CarbonPeriod::create($startDate, $endDate);
        $data = [];

        // Pre-fetch availability overrides
        $overrides = Availability::where('room_type_id', $roomTypeId)
            ->whereBetween('date', [$startDate, $endDate])
            ->get()
            ->keyBy(fn($a) => $a->date->toDateString());

        // Pre-fetch booking counts
        $bookings = HotelBooking::where('room_type_id', $roomTypeId)
            ->where('check_in', '<=', $endDate->toDateString())
            ->where('check_out', '>', $startDate->toDateString())
            ->whereIn('status', ['confirmed', 'checked_in', 'pending'])
            ->get();

        foreach ($period as $date) {
            $dateStr = $date->toDateString();
            $override = $overrides->get($dateStr);

            $totalRooms = $override && $override->available_rooms !== null
                ? $override->available_rooms
                : $roomType->inventory_count;

            $blocked = $override ? $override->blocked_rooms : 0;
            $isClosed = $override ? $override->is_closed : false;

            // Count bookings overlapping this date
            $bookedCount = $bookings->filter(function ($b) use ($date) {
                return $b->check_in->lte($date) && $b->check_out->gt($date);
            })->sum('rooms_booked');

            $available = $isClosed ? 0 : max(0, $totalRooms - $blocked - $bookedCount);
            $price = $override && $override->price_override
                ? $override->price_override
                : $roomType->base_price_per_night;

            $data[$dateStr] = [
                'date' => $dateStr,
                'total' => $totalRooms,
                'available' => $available,
                'booked' => $bookedCount,
                'blocked' => $blocked,
                'price' => $price,
                'is_closed' => $isClosed,
                'is_weekend' => in_array($date->dayOfWeek, [5, 6]),
                'min_stay' => $override ? $override->min_stay : 1,
            ];
        }

        return $data;
    }

    /**
     * Bulk update availability for a date range.
     */
    public function bulkUpdate(int $roomTypeId, Carbon $startDate, Carbon $endDate, array $data): void
    {
        $period = CarbonPeriod::create($startDate, $endDate);

        foreach ($period as $date) {
            Availability::updateOrCreate(
                [
                    'room_type_id' => $roomTypeId,
                    'date' => $date->toDateString(),
                ],
                array_filter($data, fn($v) => $v !== null)
            );
        }
    }
}
