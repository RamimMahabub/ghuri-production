<?php

namespace App\Http\Controllers;

use App\Models\HotelBooking;
use App\Services\BookingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MyBookingsController extends Controller
{
    public function __construct(protected BookingService $bookingService) {}

    public function index(Request $request)
    {
        $tab = $request->get('tab', 'upcoming');

        $query = HotelBooking::where('guest_id', Auth::id())
            ->with(['property', 'roomType', 'ratePlan']);

        $bookings = match ($tab) {
            'upcoming' => $query->whereIn('status', ['pending', 'confirmed'])->where('check_in', '>=', now())->orderBy('check_in')->get(),
            'past' => $query->whereIn('status', ['checked_out'])->orderBy('check_out', 'desc')->get(),
            'cancelled' => $query->where('status', 'cancelled')->orderBy('cancelled_at', 'desc')->get(),
            default => $query->orderBy('created_at', 'desc')->get(),
        };

        return view('hotels.my-bookings.index', compact('bookings', 'tab'));
    }

    public function show(HotelBooking $booking)
    {
        if ($booking->guest_id !== Auth::id()) abort(403);

        $booking->load(['property', 'roomType', 'ratePlan', 'activityLogs']);

        return view('hotels.my-bookings.show', compact('booking'));
    }

    public function cancel(Request $request, HotelBooking $booking)
    {
        if ($booking->guest_id !== Auth::id()) abort(403);

        $reason = $request->input('reason', 'Cancelled by guest');

        $this->bookingService->cancelBooking($booking, $reason, Auth::id());

        return redirect()->route('my-bookings.index', ['tab' => 'cancelled'])
            ->with('success', 'Booking cancelled. Refund amount: $' . number_format($booking->fresh()->refund_amount, 2));
    }
    public function voucher(HotelBooking $booking)
    {
        if ($booking->guest_id !== Auth::id()) abort(403);
        if (!in_array($booking->status, ['confirmed', 'checked_in', 'checked_out'])) abort(404);

        $booking->load(['property', 'roomType']);

        return view('hotels.my-bookings.voucher', compact('booking'));
    }

    public function review(Request $request, HotelBooking $booking)
    {
        if ($booking->guest_id !== Auth::id()) abort(403);
        if ($booking->status !== 'checked_out') abort(403, 'You can only review completed stays.');

        $request->validate([
            'overall_score' => 'required|numeric|min:1|max:10',
            'public_review' => 'required|string|max:1000',
        ]);

        // Prevent multiple reviews for same booking
        if (\App\Models\Review::where('hotel_booking_id', $booking->id)->exists()) {
            return back()->with('error', 'You have already submitted a review for this booking.');
        }

        \App\Models\Review::create([
            'property_id' => $booking->property_id,
            'hotel_booking_id' => $booking->id,
            'guest_id' => Auth::id(),
            'overall_score' => $request->overall_score,
            'public_review' => $request->public_review,
            'status' => 'published',
            'cleanliness_score' => $request->overall_score,
            'location_score' => $request->overall_score,
            'service_score' => $request->overall_score,
            'value_score' => $request->overall_score,
            'facilities_score' => $request->overall_score,
        ]);

        return back()->with('success', 'Thank you! Your review has been submitted.');
    }
}
