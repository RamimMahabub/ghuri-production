<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\Review;
use App\Services\AvailabilityService;
use App\Services\PricingService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HotelController extends Controller
{
    public function __construct(
        protected AvailabilityService $availabilityService,
        protected PricingService $pricingService
    ) {}

    public function show(Property $property, Request $request)
    {
        if (!$property->isApproved()) {
            abort(404);
        }

        $property->load([
            'photos',
            'activeRoomTypes.photos',
            'activeRoomTypes.activeRatePlans',
        ]);

        $checkIn = $request->filled('check_in') ? Carbon::parse($request->check_in) : Carbon::tomorrow();
        $checkOut = $request->filled('check_out') ? Carbon::parse($request->check_out) : Carbon::tomorrow()->addDay();
        $guests = $request->get('guests', 2);

        // Calculate availability and prices for each room type
        $roomsData = [];
        foreach ($property->activeRoomTypes as $roomType) {
            $available = $this->availabilityService->checkAvailability(
                $roomType->id, $checkIn, $checkOut
            );

            $pricing = $this->pricingService->calculateStayPrice(
                $roomType->id, $checkIn, $checkOut
            );

            $roomsData[$roomType->id] = [
                'available' => $available,
                'pricing' => $pricing,
            ];
        }

        // Reviews
        $reviews = Review::where('property_id', $property->id)
            ->published()
            ->with('guest')
            ->latest()
            ->paginate(10);

        $stats = Review::where('property_id', $property->id)
            ->published()
            ->selectRaw('
                AVG(cleanliness_score) as cleanliness,
                AVG(location_score) as location,
                AVG(service_score) as service,
                AVG(value_score) as value,
                AVG(facilities_score) as facilities
            ')->first();

        $reviewStats = [
            'average' => $property->average_rating,
            'count' => $property->review_count,
            'cleanliness' => $stats ? $stats->cleanliness : null,
            'location' => $stats ? $stats->location : null,
            'service' => $stats ? $stats->service : null,
            'value' => $stats ? $stats->value : null,
            'facilities' => $stats ? $stats->facilities : null,
        ];

        return view('hotels.show', compact(
            'property', 'roomsData', 'reviews', 'reviewStats',
            'checkIn', 'checkOut', 'guests'
        ));
    }
}
