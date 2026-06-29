<?php

namespace App\Http\Controllers\PropertyOwner;

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingsController extends Controller
{
    public function index()
    {
        $properties = Property::where('owner_id', Auth::id())->get();
        return view('property-owner.settings.index', compact('properties'));
    }

    public function update(Request $request)
    {
        // Settings update logic
        return back()->with('success', 'Settings updated.');
    }
}
