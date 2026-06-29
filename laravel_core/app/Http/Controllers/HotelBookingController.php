<?php

namespace App\Http\Controllers;

use App\Models\HotelBooking;
use App\Models\Property;
use App\Models\RoomType;
use App\Models\RatePlan;
use App\Services\BookingService;
use App\Services\PricingService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HotelBookingController extends Controller
{
    public function __construct(
        protected BookingService $bookingService,
        protected PricingService $pricingService
    ) {}

    /**
     * Step 1 — Single-page checkout (Review + Guest Details)
     */
    public function step1(Property $property, RoomType $roomType, Request $request)
    {
        $checkIn = Carbon::parse($request->get('check_in', now()->addDay()->toDateString()));
        $checkOut = Carbon::parse($request->get('check_out', now()->addDays(2)->toDateString()));
        $adults = (int) $request->get('adults', 2);
        $children = (int) $request->get('children', 0);
        $ratePlanId = $request->get('rate_plan_id');

        $pricing = $this->pricingService->calculateStayPrice(
            $roomType->id, $checkIn, $checkOut, 1, $ratePlanId, null, $property->id
        );

        $ratePlan = $ratePlanId ? RatePlan::find($ratePlanId) : null;

        // Load room photos for display
        $roomType->load('photos');
        $property->load('photos');

        return view('hotels.booking.step-1', compact(
            'property', 'roomType', 'pricing', 'ratePlan',
            'checkIn', 'checkOut', 'adults', 'children'
        ));
    }

    /**
     * Step 2 — Guest Details (kept for backward compat, now unused)
     */
    public function step2(Request $request)
    {
        $data = $request->validate([
            'property_id'  => 'required|exists:properties,id',
            'room_type_id' => 'required|exists:room_types,id',
            'check_in'     => 'required|date',
            'check_out'    => 'required|date|after:check_in',
            'adults'       => 'required|integer|min:1',
            'children'     => 'integer|min:0',
            'rate_plan_id' => 'nullable|exists:rate_plans,id',
        ]);

        $property = Property::findOrFail($data['property_id']);
        $roomType = RoomType::findOrFail($data['room_type_id']);

        return view('hotels.booking.step-2', compact('property', 'roomType', 'data'));
    }

    /**
     * Step 3 — Payment (kept for backward compat, now unused)
     */
    public function step3(Request $request)
    {
        $data = $request->validate([
            'property_id'      => 'required|exists:properties,id',
            'room_type_id'     => 'required|exists:room_types,id',
            'check_in'         => 'required|date',
            'check_out'        => 'required|date|after:check_in',
            'adults'           => 'required|integer|min:1',
            'children'         => 'integer|min:0',
            'rate_plan_id'     => 'nullable|exists:rate_plans,id',
            'first_name'       => 'required|string|max:100',
            'last_name'        => 'required|string|max:100',
            'email'            => 'required|email',
            'phone'            => 'required|string|max:20',
            'country'          => 'nullable|string|max:100',
            'special_requests' => 'nullable|string|max:500',
            'estimated_arrival'=> 'nullable|string',
        ]);

        $property = Property::findOrFail($data['property_id']);
        $roomType = RoomType::findOrFail($data['room_type_id']);
        $checkIn  = Carbon::parse($data['check_in']);
        $checkOut = Carbon::parse($data['check_out']);

        $pricing = $this->pricingService->calculateStayPrice(
            $roomType->id, $checkIn, $checkOut, 1,
            $data['rate_plan_id'] ?? null, null, $property->id
        );

        return view('hotels.booking.step-3', compact('property', 'roomType', 'data', 'pricing'));
    }

    /**
     * Confirm Booking — accepts both old (3-step) and new (single-page) form submissions.
     */
    public function confirm(Request $request)
    {
        $data = $request->validate([
            'property_id'       => 'required|exists:properties,id',
            'room_type_id'      => 'required|exists:room_types,id',
            'check_in'          => 'required|date',
            'check_out'         => 'required|date|after:check_in',
            'adults'            => 'required|integer|min:1',
            'children'          => 'integer|min:0',
            'rate_plan_id'      => 'nullable|exists:rate_plans,id',
            'special_requests'  => 'nullable|string|max:1000',
            'estimated_arrival' => 'nullable|string',
            'promo_code'        => 'nullable|string|max:50',
            // Guest fields (from single-page checkout)
            'first_name'        => 'nullable|string|max:100',
            'last_name'         => 'nullable|string|max:100',
            'email'             => 'nullable|email',
            'phone'             => 'nullable|string|max:20',
            'country'           => 'nullable|string|max:100',
            'request_options'   => 'nullable|array',
        ]);

        // Combine checkbox special requests with text
        $specialRequests = collect($data['request_options'] ?? [])
            ->push($data['special_requests'] ?? null)
            ->filter()
            ->implode('. ');

        try {
            $booking = $this->bookingService->createBooking([
                'guest_id'          => Auth::id(),
                'property_id'       => $data['property_id'],
                'room_type_id'      => $data['room_type_id'],
                'check_in'          => $data['check_in'],
                'check_out'         => $data['check_out'],
                'adults'            => $data['adults'],
                'children'          => $data['children'] ?? 0,
                'rate_plan_id'      => $data['rate_plan_id'] ?? null,
                'source'            => 'direct',
                'special_requests'  => $specialRequests ?: null,
                'estimated_arrival' => $data['estimated_arrival'] ?? null,
                'promo_code'        => $data['promo_code'] ?? null,
                'status'            => 'pending_payment',
                'payment_status'    => 'unpaid',
            ]);

            $paymentMethod = $request->input('payment_method', 'pending');

            if ($paymentMethod === 'sslcommerz') {
                // Initiate SSLCommerz Payment
                $sslcommerz = new \App\Services\SSLCommerzService();
                $paymentUrl = $sslcommerz->initiatePayment($booking, $data);

                return redirect()->away($paymentUrl);
            }

            // Pay at property or pending
            return redirect()->route('hotels.book.confirmation', $booking)->with('success', 'Booking confirmed successfully!');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Confirmation Page
     */
    public function confirmation(HotelBooking $booking)
    {
        if ($booking->guest_id !== Auth::id()) abort(403);

        $booking->load(['property', 'roomType', 'ratePlan']);

        return view('hotels.booking.confirmation', compact('booking'));
    }
}
