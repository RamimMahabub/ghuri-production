<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Http\Request;

class PropertyApprovalController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'pending_approval');

        $properties = Property::where('status', $status)
            ->with(['owner', 'photos', 'roomTypes'])
            ->withCount(['roomTypes', 'photos'])
            ->latest()
            ->paginate(20);

        $counts = [
            'pending' => Property::where('status', 'pending_approval')->count(),
            'approved' => Property::where('status', 'approved')->count(),
            'rejected' => Property::where('status', 'rejected')->count(),
        ];

        return view('admin.properties.index', compact('properties', 'counts', 'status'));
    }

    public function review(Property $property)
    {
        $property->load([
            'owner', 'photos', 'roomTypes.photos', 'roomTypes.ratePlans',
        ]);

        $checklist = [
            'has_photos' => $property->photos()->count() >= 1,
            'has_room_types' => $property->roomTypes()->count() >= 1,
            'has_address' => !empty($property->city) && !empty($property->country),
            'has_description' => !empty($property->short_description),
            'has_pricing' => $property->roomTypes()->where('base_price_per_night', '>', 0)->exists(),
        ];

        return view('admin.properties.review', compact('property', 'checklist'));
    }

    public function approve(Property $property)
    {
        $property->update([
            'status' => 'approved',
            'approved_at' => now(),
        ]);

        return redirect()->route('admin.properties.index')
            ->with('success', '"' . $property->name . '" has been approved.');
    }

    public function reject(Request $request, Property $property)
    {
        $request->validate(['reason' => 'required|string']);

        $property->update([
            'status' => 'rejected',
            'admin_notes' => $request->reason,
        ]);

        return redirect()->route('admin.properties.index')
            ->with('success', '"' . $property->name . '" has been rejected.');
    }

    public function requestChanges(Request $request, Property $property)
    {
        $request->validate(['notes' => 'required|string']);

        $property->update([
            'status' => 'draft',
            'admin_notes' => $request->notes,
        ]);

        return redirect()->route('admin.properties.index')
            ->with('success', 'Changes requested for "' . $property->name . '".');
    }
}
