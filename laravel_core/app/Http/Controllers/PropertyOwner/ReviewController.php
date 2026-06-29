<?php

namespace App\Http\Controllers\PropertyOwner;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function index()
    {
        $propertyIds = Property::where('owner_id', Auth::id())->pluck('id');
        $reviews = Review::whereIn('property_id', $propertyIds)
            ->with(['guest', 'property', 'hotelBooking.roomType'])
            ->latest()
            ->paginate(20);

        return view('property-owner.reviews.index', compact('reviews'));
    }

    public function respond(Request $request, Review $review)
    {
        $request->validate(['response' => 'required|string|max:1000']);

        $review->update([
            'hotel_response' => $request->response,
            'responded_at' => now(),
        ]);

        return back()->with('success', 'Response posted.');
    }
}
