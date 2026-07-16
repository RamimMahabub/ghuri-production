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
        
        $promoBannerEnabled = Setting::get('promo_banner_enabled', '0');
        $promoBannerTitle = Setting::get('promo_banner_title', 'Big Summer Sale: Save up to 40%');
        $promoBannerSubtitle = Setting::get('promo_banner_subtitle', 'Book memorable stays, tours and experiences with ease.');
        $promoBannerButtonText = Setting::get('promo_banner_button_text', 'Search');
        $promoBannerButtonUrl = Setting::get('promo_banner_button_url', '#');

        return view('admin.settings.index', compact(
            'exchangeRate', 'exchangeRateType',
            'promoBannerEnabled', 'promoBannerTitle', 
            'promoBannerSubtitle', 'promoBannerButtonText', 'promoBannerButtonUrl'
        ));
    }

    /**
     * Update the global settings.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'exchange_rate_type' => 'required|in:live,manual',
            'exchange_rate_usd_bdt' => 'required_if:exchange_rate_type,manual|numeric|min:0.01',
            'promo_banner_enabled' => 'nullable|boolean',
            'promo_banner_title' => 'nullable|string|max:255',
            'promo_banner_subtitle' => 'nullable|string|max:500',
            'promo_banner_button_text' => 'nullable|string|max:50',
            'promo_banner_button_url' => 'nullable|string|max:255',
        ]);

        Setting::set('exchange_rate_type', $validated['exchange_rate_type']);
        if (isset($validated['exchange_rate_usd_bdt'])) {
            Setting::set('exchange_rate_usd_bdt', $validated['exchange_rate_usd_bdt']);
        }
        
        Setting::set('promo_banner_enabled', $request->has('promo_banner_enabled') ? '1' : '0');
        if (isset($validated['promo_banner_title'])) Setting::set('promo_banner_title', $validated['promo_banner_title']);
        if (isset($validated['promo_banner_subtitle'])) Setting::set('promo_banner_subtitle', $validated['promo_banner_subtitle']);
        if (isset($validated['promo_banner_button_text'])) Setting::set('promo_banner_button_text', $validated['promo_banner_button_text']);
        if (isset($validated['promo_banner_button_url'])) Setting::set('promo_banner_button_url', $validated['promo_banner_button_url']);

        return back()->with('success', 'Settings updated successfully.');
    }
}
