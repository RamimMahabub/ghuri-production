<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Show the admin settings page.
     */
    public function index()
    {
        $exchangeRate = Setting::get('exchange_rate_usd_bdt', 120);
        $exchangeRateType = Setting::get('exchange_rate_type', 'live');

        return view('admin.settings.index', compact('exchangeRate', 'exchangeRateType'));
    }

    /**
     * Update the global settings.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'exchange_rate_type' => 'required|in:live,manual',
            'exchange_rate_usd_bdt' => 'required_if:exchange_rate_type,manual|numeric|min:0.01',
        ]);

        Setting::set('exchange_rate_type', $validated['exchange_rate_type']);
        if (isset($validated['exchange_rate_usd_bdt'])) {
            Setting::set('exchange_rate_usd_bdt', $validated['exchange_rate_usd_bdt']);
        }

        return back()->with('success', 'Settings updated successfully.');
    }
}
