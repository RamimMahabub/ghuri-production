<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Commission;
use App\Models\Property;
use Illuminate\Http\Request;

class CommissionController extends Controller
{
    public function index()
    {
        $properties = Property::approved()->with(['owner', 'commissions' => function($q) {
            $q->orderBy('effective_from', 'desc');
        }])->paginate(20);

        $globalRate = 15.0; // Hardcoded fallback for MVP

        return view('admin.commissions.index', compact('properties', 'globalRate'));
    }

    public function updateGlobal(Request $request)
    {
        // Mock updating global settings
        return back()->with('success', 'Global commission rate updated successfully.');
    }

    public function updateProperty(Request $request, Property $property)
    {
        $request->validate([
            'rate_percent' => 'required|numeric|min:0|max:100',
        ]);

        Commission::create([
            'property_id' => $property->id,
            'rate_percent' => $request->rate_percent,
            'effective_from' => now(),
        ]);

        return back()->with('success', "Commission rate for {$property->name} updated to {$request->rate_percent}%.");
    }
}
