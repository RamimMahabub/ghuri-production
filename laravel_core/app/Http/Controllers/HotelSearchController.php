<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\RoomType;
use App\Services\AvailabilityService;
use App\Services\PricingService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HotelSearchController extends Controller
{
    public function __construct(
        protected AvailabilityService $availabilityService,
        protected PricingService $pricingService
    ) {}

    public function index(Request $request)
    {
        $query = Property::approved()
            ->with(['photos', 'reviews'])
            ->withCount('reviews');

        // Destination search
        if ($request->filled('destination')) {
            $dest = $request->destination;
            $query->where(function ($q) use ($dest) {
                $q->where('name', 'like', "%{$dest}%")
                  ->orWhere('city', 'like', "%{$dest}%")
                  ->orWhere('country', 'like', "%{$dest}%")
                  ->orWhere('neighborhood', 'like', "%{$dest}%");
            });
        }

        // Filters
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('stars')) {
            $stars = is_array($request->stars) ? $request->stars : [$request->stars];
            $query->whereIn('stars', $stars);
        }
        if ($request->filled('min_price') || $request->filled('max_price')) {
            $query->whereHas('roomTypes', function ($q) use ($request) {
                $q->active();
                if ($request->filled('min_price')) {
                    $q->where('base_price_per_night', '>=', $request->min_price);
                }
                if ($request->filled('max_price')) {
                    $q->where('base_price_per_night', '<=', $request->max_price);
                }
            });
        }

        // Sort
        $sort = $request->get('sort', 'recommended');
        $query = match ($sort) {
            'price_low' => $query->orderByRaw('(SELECT MIN(base_price_per_night) FROM room_types WHERE room_types.property_id = properties.id AND room_types.status = "active") ASC'),
            'price_high' => $query->orderByRaw('(SELECT MIN(base_price_per_night) FROM room_types WHERE room_types.property_id = properties.id AND room_types.status = "active") DESC'),
            'stars' => $query->orderBy('stars', 'desc'),
            'rating' => $query->orderByRaw('(SELECT AVG(overall_score) FROM reviews WHERE reviews.property_id = properties.id AND reviews.status = "published") DESC'),
            default => $query->orderBy('created_at', 'desc'),
        };

        $properties = $query->paginate(15)->withQueryString();

        // Check availability if dates provided
        $checkIn = $request->filled('check_in') ? Carbon::parse($request->check_in) : null;
        $checkOut = $request->filled('check_out') ? Carbon::parse($request->check_out) : null;

        return view('hotels.search', compact('properties', 'checkIn', 'checkOut'));
    }
}
