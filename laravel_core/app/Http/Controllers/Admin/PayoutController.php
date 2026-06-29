<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payout;
use App\Models\Property;
use Illuminate\Http\Request;

class PayoutController extends Controller
{
    public function index()
    {
        $properties = Property::approved()->with('owner')->get();
        
        $payoutData = [];
        foreach ($properties as $property) {
            $pendingBookings = \App\Models\HotelBooking::where('property_id', $property->id)
                ->where('status', 'checked_out')
                ->whereNull('payout_id')
                ->get();
                
            $pendingAmount = $pendingBookings->sum(function($booking) {
                return $booking->total - $booking->commission_amount;
            });

            $payoutData[] = (object) [
                'property' => $property,
                'pending_amount' => $pendingAmount,
                'last_payout' => Payout::where('property_id', $property->id)->latest('processed_at')->first()
            ];
        }

        return view('admin.payouts.index', compact('payoutData'));
    }

    public function process(Request $request, Property $property)
    {
        $pendingBookings = \App\Models\HotelBooking::where('property_id', $property->id)
            ->where('status', 'checked_out')
            ->whereNull('payout_id')
            ->get();
            
        $pendingAmount = $pendingBookings->sum(function($booking) {
            return $booking->total - $booking->commission_amount;
        });

        if ($pendingAmount <= 0) {
            return back()->with('error', "No pending payouts available for {$property->name}.");
        }

        $payout = Payout::create([
            'property_id' => $property->id,
            'amount' => $pendingAmount,
            'status' => 'processed',
            'period_start' => now()->subMonth()->startOfMonth(),
            'period_end' => now(),
            'processed_at' => now(),
            'reference' => 'PAY-' . strtoupper(\Illuminate\Support\Str::random(6))
        ]);
        
        // Link the bookings to the newly created payout
        \App\Models\HotelBooking::where('property_id', $property->id)
            ->where('status', 'checked_out')
            ->whereNull('payout_id')
            ->update(['payout_id' => $payout->id]);

        return back()->with('success', "Payout of $" . number_format($pendingAmount, 2) . " processed for {$property->name}.");
    }
}
