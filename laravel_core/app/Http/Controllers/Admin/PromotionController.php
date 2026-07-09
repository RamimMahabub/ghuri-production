<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PromotionController extends Controller
{
    public function index()
    {
        $promotions = Promotion::with('property')->latest()->paginate(20);
        return view('admin.promotions.index', compact('promotions'));
    }

    public function create()
    {
        $properties = Property::approved()->get();
        return view('admin.promotions.create', compact('properties'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'code' => 'nullable|string|max:50',
            'type' => 'required|in:promo_code,flash_deal,package',
            'discount_type' => 'required|in:percent,flat',
            'discount_value' => 'required|numeric|min:0',
            'valid_from' => 'nullable|date',
            'valid_to' => 'nullable|date|after_or_equal:valid_from',
            'min_nights' => 'integer|min:1',
            'max_usage_total' => 'nullable|integer|min:1',
        ]);

        if ($request->hasFile('image')) {
            $validated['image_path'] = $request->file('image')->store('promotions', 'public');
        }
        unset($validated['image']);

        if (empty($validated['code'])) {
            $validated['code'] = strtoupper(Str::random(8));
        }

        Promotion::create($validated);

        return redirect()->route('admin.promotions.index')
            ->with('success', 'Promotion created.');
    }

    public function edit(Promotion $promotion)
    {
        $properties = Property::approved()->get();
        return view('admin.promotions.edit', compact('promotion', 'properties'));
    }

    public function update(Request $request, Promotion $promotion)
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'code' => 'nullable|string|max:50',
            'type' => 'required|in:promo_code,flash_deal,package',
            'discount_type' => 'required|in:percent,flat',
            'discount_value' => 'required|numeric|min:0',
            'valid_from' => 'nullable|date',
            'valid_to' => 'nullable|date|after_or_equal:valid_from',
            'min_nights' => 'integer|min:1',
            'max_usage_total' => 'nullable|integer|min:1',
        ]);

        if ($request->hasFile('image')) {
            if ($promotion->image_path) {
                Storage::disk('public')->delete($promotion->image_path);
            }
            $validated['image_path'] = $request->file('image')->store('promotions', 'public');
        }
        unset($validated['image']);

        $promotion->update($validated);

        return redirect()->route('admin.promotions.index')
            ->with('success', 'Promotion updated.');
    }

    public function destroy(Promotion $promotion)
    {
        if ($promotion->image_path) {
            Storage::disk('public')->delete($promotion->image_path);
        }
        $promotion->delete();
        return back()->with('success', 'Promotion deleted.');
    }
}
