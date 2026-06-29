<?php

namespace App\Http\Controllers\PropertyOwner;

use App\Http\Controllers\Controller;
use App\Models\HotelBooking;
use App\Models\Property;
use Illuminate\Support\Facades\Auth;

class GuestController extends Controller
{
    public function index()
    {
        $propertyIds = Property::where('owner_id', Auth::id())->pluck('id');
        $guests = \App\Models\User::whereHas('hotelBookings', fn($q) => $q->whereIn('property_id', $propertyIds))
            ->withCount(['hotelBookings' => fn($q) => $q->whereIn('property_id', $propertyIds)])
            ->paginate(20);

        return view('property-owner.guests.index', compact('guests'));
    }

    public function show(\App\Models\User $guest)
    {
        $propertyIds = Property::where('owner_id', Auth::id())->pluck('id');
        $bookings = HotelBooking::where('guest_id', $guest->id)
            ->whereIn('property_id', $propertyIds)
            ->with(['property', 'roomType'])
            ->latest()
            ->get();

        return view('property-owner.guests.show', compact('guest', 'bookings'));
    }
}
