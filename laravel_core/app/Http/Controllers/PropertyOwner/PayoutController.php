<?php

namespace App\Http\Controllers\PropertyOwner;

use App\Http\Controllers\Controller;
use App\Models\HotelBooking;
use App\Models\Property;
use App\Models\Payout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PayoutController extends Controller
{
    public function index()
    {
        $ownerId = Auth::id();
        $propertyIds = Property::where('owner_id', $ownerId)->pluck('id');

        $bookings = HotelBooking::with(['guest', 'property'])
            ->whereIn('property_id', $propertyIds)
            ->whereIn('status', ['pending', 'confirmed', 'checked_in', 'checked_out'])
            ->orderBy('check_in', 'desc')
            ->get();

        $totalPaidOut = Payout::whereIn('property_id', $propertyIds)
            ->where('status', 'processed')
            ->sum('amount');
            
        $withdrawable = 0;
        $pending = 0;
        $upcoming = 0;

        $today = now()->startOfDay();

        foreach ($bookings as $booking) {
            $payoutAmount = $booking->total - $booking->commission_amount;
            
            if ($booking->payout_id !== null) {
                $booking->payout_status = 'Paid Out';
            } elseif ($booking->status === 'checked_out') {
                $booking->payout_status = 'Withdrawable';
                $withdrawable += $payoutAmount;
            } elseif ($booking->status === 'checked_in' || ($booking->check_in <= $today && $booking->check_out > $today)) {
                $booking->payout_status = 'Ongoing';
                $pending += $payoutAmount;
            } else {
                $booking->payout_status = 'Upcoming';
                $upcoming += $payoutAmount;
            }
        }

        return view('property-owner.payouts.index', compact(
            'bookings',
            'withdrawable',
            'pending',
            'upcoming',
            'totalPaidOut'
        ));
    }
}
