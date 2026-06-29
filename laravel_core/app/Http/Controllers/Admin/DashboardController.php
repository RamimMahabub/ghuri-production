<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'total_sales' => 0,
            'bookings' => 0,
            'active_properties' => 0,
            'pending_properties' => 0,
            'total_commission' => 0,
            'active_customers' => 0,
            'active_partners' => 0,
        ];

        if (Schema::hasTable('hotel_bookings')) {
            $stats['bookings'] = DB::table('hotel_bookings')->count();
            
            // Total sales from hotel bookings
            $stats['total_sales'] = (float) DB::table('hotel_bookings')
                ->whereIn('status', ['confirmed', 'completed'])
                ->sum('total');
                
            // Estimate commission (assuming average 15% if no commission table check)
            $stats['total_commission'] = $stats['total_sales'] * 0.15;
        }
        
        if (Schema::hasTable('commissions')) {
            // If commissions table exists and tracks earnings, we could query it.
            // For now, we will rely on a simple calculation or if it's tracked in another way.
        }

        if (Schema::hasTable('properties')) {
            $stats['active_properties'] = DB::table('properties')->where('status', 'active')->count();
            $stats['pending_properties'] = DB::table('properties')->where('status', 'pending_approval')->count();
        }

        if (Schema::hasTable('users')) {
            $stats['active_customers'] = DB::table('users')->where('role', 'customer')->count();
            $stats['active_partners'] = DB::table('users')->where('role', 'property_owner')->count();
        }
        
        // Fetch recent pending properties for quick action
        $pendingPropertiesList = [];
        if (Schema::hasTable('properties')) {
            $pendingPropertiesList = DB::table('properties')
                ->where('status', 'pending_approval')
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();
        }

        return view('admin.dashboard', [
            'stats' => $stats,
            'pendingPropertiesList' => $pendingPropertiesList,
            'pageTitle' => 'OTA Dashboard',
            'pageSubtitle' => 'Platform Overview & Operations',
        ]);
    }
}
