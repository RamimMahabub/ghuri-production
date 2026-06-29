<?php

namespace App\Http\Controllers\PropertyOwner;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\RateRule;
use App\Models\RoomType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RateRuleController extends Controller
{
    public function index(Property $hotel)
    {
        if ($hotel->owner_id !== Auth::id()) abort(403);

        $hotel->load('roomTypes.rateRules');
        $roomTypes = $hotel->roomTypes;

        return view('property-owner.rate-rules.index', compact('hotel', 'roomTypes'));
    }

    public function store(Request $request, Property $hotel)
    {
        if ($hotel->owner_id !== Auth::id()) abort(403);

        $validated = $request->validate([
            'room_type_id' => 'required|exists:room_types,id',
            'rule_type' => 'required|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'adjustment_type' => 'required|in:percent,flat',
            'adjustment_value' => 'required|numeric',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        
        // Ensure the room type belongs to this hotel
        $roomType = RoomType::findOrFail($validated['room_type_id']);
        if ($roomType->property_id !== $hotel->id) abort(403);

        RateRule::create($validated);

        return back()->with('success', 'Rate rule added successfully.');
    }

    public function destroy(Property $hotel, RateRule $rateRule)
    {
        if ($hotel->owner_id !== Auth::id()) abort(403);
        if ($rateRule->roomType->property_id !== $hotel->id) abort(403);

        $rateRule->delete();

        return back()->with('success', 'Rate rule deleted.');
    }
}
