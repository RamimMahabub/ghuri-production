<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HotelBooking;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $query = HotelBooking::with(['property', 'guest', 'roomType'])->latest();
        
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        $bookings = $query->paginate(20);

        return view('admin.bookings.index', compact('bookings'));
    }

    public function show(HotelBooking $booking)
    {
        $booking->load(['property', 'guest', 'roomType', 'activityLogs']);
        return view('admin.bookings.show', compact('booking'));
    }
}
