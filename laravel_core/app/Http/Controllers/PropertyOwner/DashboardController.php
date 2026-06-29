<?php

namespace App\Http\Controllers\PropertyOwner;

use App\Http\Controllers\Controller;
use App\Models\HotelBooking;
use App\Models\Property;
use App\Models\Review;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $propertyIds = Property::where('owner_id', $user->id)->pluck('id');

        // KPI Stats
        $today = Carbon::today();
        $monthStart = Carbon::now()->startOfMonth();

        $totalBookings = HotelBooking::whereIn('property_id', $propertyIds)->count();
        $monthBookings = HotelBooking::whereIn('property_id', $propertyIds)
            ->where('created_at', '>=', $monthStart)->count();

        $totalRevenue = HotelBooking::whereIn('property_id', $propertyIds)
            ->whereIn('status', ['confirmed', 'checked_in', 'checked_out'])
            ->sum('total');

        $monthRevenue = HotelBooking::whereIn('property_id', $propertyIds)
            ->whereIn('status', ['confirmed', 'checked_in', 'checked_out'])
            ->where('created_at', '>=', $monthStart)
            ->sum('total');

        $pendingCount = HotelBooking::whereIn('property_id', $propertyIds)
            ->where('status', 'pending')->count();

        $upcomingCheckins = HotelBooking::whereIn('property_id', $propertyIds)
            ->where('check_in', '>=', $today)
            ->where('check_in', '<=', $today->copy()->addDays(7))
            ->whereIn('status', ['confirmed'])
            ->with(['guest', 'roomType', 'property'])
            ->orderBy('check_in')
            ->limit(10)
            ->get();

        $upcomingCheckouts = HotelBooking::whereIn('property_id', $propertyIds)
            ->where('check_out', '>=', $today)
            ->where('check_out', '<=', $today->copy()->addDays(7))
            ->where('status', 'checked_in')
            ->with(['guest', 'roomType', 'property'])
            ->orderBy('check_out')
            ->limit(10)
            ->get();

        $recentReviews = Review::whereIn('property_id', $propertyIds)
            ->with(['guest', 'property'])
            ->latest()
            ->limit(5)
            ->get();

        $properties = Property::where('owner_id', $user->id)
            ->withCount('hotelBookings')
            ->get();

        // Chart Data: Last 7 days revenue
        $chartLabels = [];
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $d = Carbon::today()->subDays($i);
            $chartLabels[] = $d->format('M d');
            
            $dayRev = HotelBooking::whereIn('property_id', $propertyIds)
                ->whereIn('status', ['confirmed', 'checked_in', 'checked_out'])
                ->whereDate('created_at', $d)
                ->sum('total');
            $chartData[] = round($dayRev, 2);
        }

        return view('property-owner.dashboard', compact(
            'totalBookings', 'monthBookings', 'totalRevenue', 'monthRevenue',
            'pendingCount', 'upcomingCheckins', 'upcomingCheckouts',
            'recentReviews', 'properties', 'chartLabels', 'chartData'
        ));
    }
}
