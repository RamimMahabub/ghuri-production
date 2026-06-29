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
}
