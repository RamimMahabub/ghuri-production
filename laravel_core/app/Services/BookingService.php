<?php

namespace App\Services;

use App\Models\HotelBooking;
use App\Models\Property;
use App\Models\RoomType;
use Carbon\Carbon;
use Illuminate\Support\Str;

class BookingService
{
    public function __construct(
        protected AvailabilityService $availabilityService,
        protected PricingService $pricingService
    ) {}

    /**
     * Create a new hotel booking.
     */
    public function createBooking(array $data): HotelBooking
    {
        $checkIn = Carbon::parse($data['check_in']);
        $checkOut = Carbon::parse($data['check_out']);
        $rooms = $data['rooms_booked'] ?? 1;

        // Verify availability
        $available = $this->availabilityService->checkAvailability(
            $data['room_type_id'],
            $checkIn,
            $checkOut
        );

        if ($available < $rooms) {
            throw new \Exception("Not enough rooms available. Only {$available} rooms left.");
        }

        // Calculate pricing
        $pricing = $this->pricingService->calculateStayPrice(
            $data['room_type_id'],
            $checkIn,
            $checkOut,
            $rooms,
            $data['rate_plan_id'] ?? null,
            $data['promo_code'] ?? null,
            $data['property_id']
        );

        // Create the booking
        $booking = HotelBooking::create([
            'booking_ref' => HotelBooking::generateBookingRef(),
            'guest_id' => $data['guest_id'],
            'property_id' => $data['property_id'],
            'room_type_id' => $data['room_type_id'],
            'rate_plan_id' => $data['rate_plan_id'] ?? null,
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'adults' => $data['adults'] ?? 1,
            'children' => $data['children'] ?? 0,
            'infants' => $data['infants'] ?? 0,
            'rooms_booked' => $rooms,
            'nightly_rate' => $pricing['nightly_rate'],
            'subtotal' => $pricing['subtotal'],
            'taxes' => $pricing['taxes'],
            'fees' => $pricing['fees'],
            'discount_amount' => $pricing['discount'],
            'total' => $pricing['total'],
            'status' => $data['status'] ?? 'pending',
            'payment_status' => $data['payment_status'] ?? 'unpaid',
            'source' => $data['source'] ?? 'direct',
            'special_requests' => $data['special_requests'] ?? null,
            'estimated_arrival' => $data['estimated_arrival'] ?? null,
            'promo_code_used' => $data['promo_code'] ?? null,
        ]);

        // Log the booking activity
        $booking->logActivity('booked', 'Booking created', $data['guest_id']);

        return $booking;
    }

    /**
     * Confirm a booking.
     */
    public function confirmBooking(HotelBooking $booking, ?int $userId = null): HotelBooking
    {
        $booking->update(['status' => 'confirmed']);
        $booking->logActivity('confirmed', 'Booking confirmed by hotel', $userId);

        return $booking;
    }

    /**
     * Cancel a booking with refund calculation.
     */
    public function cancelBooking(HotelBooking $booking, ?string $reason = null, ?int $userId = null): HotelBooking
    {
        $refundAmount = $this->calculateRefund($booking);

        $booking->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancel_reason' => $reason,
            'refund_amount' => $refundAmount,
            'payment_status' => $refundAmount > 0 ? 'refunded' : $booking->payment_status,
        ]);

        $booking->logActivity('cancelled', "Booking cancelled. Reason: {$reason}. Refund: \${$refundAmount}", $userId);

        return $booking;
    }

    /**
     * Mark a booking as checked in.
     */
    public function checkIn(HotelBooking $booking, ?int $userId = null): HotelBooking
    {
        $booking->update(['status' => 'checked_in']);
        $booking->logActivity('checked_in', 'Guest checked in', $userId);

        return $booking;
    }

    /**
     * Mark a booking as checked out.
     */
    public function checkOut(HotelBooking $booking, ?int $userId = null): HotelBooking
    {
        $booking->update(['status' => 'checked_out']);
        $booking->logActivity('checked_out', 'Guest checked out', $userId);

        return $booking;
    }

    /**
     * Mark a booking as no-show.
     */
    public function markNoShow(HotelBooking $booking, ?int $userId = null): HotelBooking
    {
        $booking->update(['status' => 'no_show']);
        $booking->logActivity('no_show', 'Guest marked as no-show', $userId);

        return $booking;
    }

    /**
     * Calculate refund amount based on cancellation policy.
     */
    public function calculateRefund(HotelBooking $booking): float
    {
        if ($booking->payment_status !== 'paid') return 0;

        $property = $booking->property;
        $policy = $property->cancellation_policy ?? [];
        $daysBeforeCheckin = now()->diffInDays($booking->check_in, false);

        // Default: full refund if > 3 days before, 50% if 1-3 days, none if same day
        $freeCancelDays = $policy['free_cancel_days'] ?? 3;

        if ($daysBeforeCheckin > $freeCancelDays) {
            return $booking->total; // Full refund
        } elseif ($daysBeforeCheckin >= 1) {
            return round($booking->total * 0.5, 2); // 50% refund
        }

        return 0; // No refund
    }
}
