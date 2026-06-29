<?php

namespace App\Http\Controllers\PropertyOwner;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\RoomType;
use App\Models\RoomTypePhoto;
use App\Models\RatePlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoomController extends Controller
{
    public function index(Property $hotel)
    {
        $this->authorizeProperty($hotel);

        $roomTypes = $hotel->roomTypes()
            ->withCount(['photos', 'hotelBookings'])
            ->with('ratePlans')
            ->get();

        return view('property-owner.rooms.index', compact('hotel', 'roomTypes'));
    }

    public function create(Property $hotel)
    {
        $this->authorizeProperty($hotel);

        return view('property-owner.rooms.create', compact('hotel'));
    }

    public function store(Request $request, Property $hotel)
    {
        $this->authorizeProperty($hotel);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'size_sqm' => 'nullable|integer|min:1',
            'floor_level' => 'nullable|string|max:50',
            'max_adults' => 'required|integer|min:1|max:20',
            'max_children' => 'required|integer|min:0|max:10',
            'max_infants' => 'required|integer|min:0|max:5',
            'bed_config' => 'nullable|array',
            'amenities' => 'nullable|array',
            'base_price_per_night' => 'required|numeric|min:0',
            'inventory_count' => 'required|integer|min:1',
            'photos' => 'nullable|array',
            'photos.*' => 'image|max:5120',
            'rate_plans' => 'nullable|array',
        ]);

        $roomType = $hotel->roomTypes()->create([
            'name' => $validated['name'],
            'size_sqm' => $validated['size_sqm'] ?? null,
            'floor_level' => $validated['floor_level'] ?? null,
            'max_adults' => $validated['max_adults'],
            'max_children' => $validated['max_children'],
            'max_infants' => $validated['max_infants'],
            'bed_config' => $validated['bed_config'] ?? null,
            'amenities' => $validated['amenities'] ?? null,
            'base_price_per_night' => $validated['base_price_per_night'],
            'inventory_count' => $validated['inventory_count'],
            'status' => 'active',
        ]);

        // Create default RO rate plan
        $roomType->ratePlans()->create([
            'plan_code' => 'RO',
            'plan_name' => 'Room Only',
            'price_supplement_per_adult' => 0,
            'is_active' => true,
        ]);

        // Create additional rate plans if selected
        if (!empty($validated['rate_plans'])) {
            $planNames = RatePlan::getPlanCodes();
            foreach ($validated['rate_plans'] as $code => $data) {
                if ($code === 'RO') continue;
                if (!empty($data['enabled'])) {
                    $roomType->ratePlans()->create([
                        'plan_code' => $code,
                        'plan_name' => $planNames[$code] ?? $code,
                        'price_supplement_per_adult' => $data['supplement'] ?? 0,
                        'is_active' => true,
                    ]);
                }
            }
        }

        // Handle photos
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $index => $photo) {
                $path = $photo->store('rooms/' . $roomType->id, 'public');
                RoomTypePhoto::create([
                    'room_type_id' => $roomType->id,
                    'file_path' => $path,
                    'sort_order' => $index,
                ]);
            }
        }

        return redirect()->route('property-owner.hotels.rooms.index', $hotel)
            ->with('success', 'Room type "' . $roomType->name . '" created successfully.');
    }

    public function edit(Property $hotel, RoomType $room)
    {
        $this->authorizeProperty($hotel);
        $room->load(['photos', 'ratePlans']);

        return view('property-owner.rooms.edit', compact('hotel', 'room'));
    }

    public function update(Request $request, Property $hotel, RoomType $room)
    {
        $this->authorizeProperty($hotel);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'size_sqm' => 'nullable|integer|min:1',
            'floor_level' => 'nullable|string|max:50',
            'max_adults' => 'required|integer|min:1|max:20',
            'max_children' => 'required|integer|min:0|max:10',
            'max_infants' => 'required|integer|min:0|max:5',
            'bed_config' => 'nullable|array',
            'amenities' => 'nullable|array',
            'base_price_per_night' => 'required|numeric|min:0',
            'inventory_count' => 'required|integer|min:1',
            'photos' => 'nullable|array',
            'photos.*' => 'image|max:5120',
            'rate_plans' => 'nullable|array',
        ]);

        $room->update([
            'name' => $validated['name'],
            'size_sqm' => $validated['size_sqm'] ?? null,
            'floor_level' => $validated['floor_level'] ?? null,
            'max_adults' => $validated['max_adults'],
            'max_children' => $validated['max_children'],
            'max_infants' => $validated['max_infants'],
            'bed_config' => $validated['bed_config'] ?? null,
            'amenities' => $validated['amenities'] ?? null,
            'base_price_per_night' => $validated['base_price_per_night'],
            'inventory_count' => $validated['inventory_count'],
        ]);

        // Sync Rate Plans
        if (isset($validated['rate_plans'])) {
            $planNames = RatePlan::getPlanCodes();
            foreach (['BB', 'HB', 'FB', 'AI'] as $code) {
                $planData = $validated['rate_plans'][$code] ?? null;
                $existingPlan = $room->ratePlans()->where('plan_code', $code)->first();

                if (!empty($planData['enabled'])) {
                    if ($existingPlan) {
                        $existingPlan->update([
                            'price_supplement_per_adult' => $planData['supplement'] ?? 0,
                            'is_active' => true,
                        ]);
                    } else {
                        $room->ratePlans()->create([
                            'plan_code' => $code,
                            'plan_name' => $planNames[$code] ?? $code,
                            'price_supplement_per_adult' => $planData['supplement'] ?? 0,
                            'is_active' => true,
                        ]);
                    }
                } else if ($existingPlan) {
                    $existingPlan->delete();
                }
            }
        }

        // Handle new photos
        if ($request->hasFile('photos')) {
            $maxSortOrder = $room->photos()->max('sort_order') ?? -1;
            foreach ($request->file('photos') as $index => $photo) {
                $path = $photo->store('rooms/' . $room->id, 'public');
                RoomTypePhoto::create([
                    'room_type_id' => $room->id,
                    'file_path' => $path,
                    'sort_order' => $maxSortOrder + 1 + $index,
                ]);
            }
        }

        return redirect()->route('property-owner.hotels.rooms.index', $hotel)
            ->with('success', 'Room type updated.');
    }

    public function destroy(Property $hotel, RoomType $room)
    {
        $this->authorizeProperty($hotel);
        $room->delete();

        return redirect()->route('property-owner.hotels.rooms.index', $hotel)
            ->with('success', 'Room type deleted.');
    }

    public function toggleStatus(Property $hotel, RoomType $room)
    {
        $this->authorizeProperty($hotel);

        $room->update([
            'status' => $room->status === 'active' ? 'inactive' : 'active',
        ]);

        return back()->with('success', 'Room type ' . ($room->status === 'active' ? 'activated' : 'deactivated') . '.');
    }

    public function duplicate(Property $hotel, RoomType $room)
    {
        $this->authorizeProperty($hotel);

        $newRoom = $room->replicate();
        $newRoom->name = $room->name . ' (Copy)';
        $newRoom->save();

        // Duplicate rate plans
        foreach ($room->ratePlans as $plan) {
            $newPlan = $plan->replicate();
            $newPlan->room_type_id = $newRoom->id;
            $newPlan->save();
        }

        return back()->with('success', 'Room type duplicated as "' . $newRoom->name . '".');
    }

    private function authorizeProperty(Property $hotel): void
    {
        if ($hotel->owner_id !== Auth::id()) {
            abort(403);
        }
    }
}
