<?php

namespace App\Http\Controllers\PropertyOwner;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PromotionController extends Controller
{
    public function index()
    {
        $propertyIds = Property::where('owner_id', Auth::id())->pluck('id');
        $promotions = Promotion::whereIn('property_id', $propertyIds)
            ->with('property')
            ->latest()
            ->paginate(20);

        return view('property-owner.promotions.index', compact('promotions'));
    }

    public function create()
    {
        $properties = Property::where('owner_id', Auth::id())->approved()->get();
        return view('property-owner.promotions.create', compact('properties'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'code' => 'nullable|string|max:50',
            'type' => 'required|in:promo_code,flash_deal,package',
            'discount_type' => 'required|in:percent,flat',
            'discount_value' => 'required|numeric|min:0',
            'valid_from' => 'nullable|date',
            'valid_to' => 'nullable|date|after_or_equal:valid_from',
            'min_nights' => 'integer|min:1',
            'max_usage_total' => 'nullable|integer|min:1',
        ]);

        if (empty($validated['code'])) {
            $validated['code'] = strtoupper(Str::random(8));
        }

        Promotion::create($validated);

        return redirect()->route('property-owner.promotions.index')
            ->with('success', 'Promotion created.');
    }

    public function destroy(Promotion $promotion)
    {
        $promotion->delete();
        return back()->with('success', 'Promotion deleted.');
    }
}
