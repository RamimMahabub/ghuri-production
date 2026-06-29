<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BookingController extends Controller
{
    protected $flightService;

    public function __construct(\App\Services\FlightServiceInterface $flightService)
    {
        $this->flightService = $flightService;
    }

    public function checkout(Request $request, $flightId)
    {
        try {
            $priceInfo = $this->flightService->price($flightId);
        } catch (\Throwable $e) {
            Log::error('Flight pricing failed', [
                'flight_id' => $flightId,
                'message' => $e->getMessage(),
            ]);

            return redirect()->route('dashboard')
                ->withErrors(['checkout' => 'Unable to validate fare right now. Please search again.']);
        }

        if (($priceInfo['status'] ?? 'expired') !== 'available') {
            return redirect()->route('dashboard')
                ->withErrors(['checkout' => 'Selected fare is no longer available. Please search again.']);
        }
        
        return view('bookings.checkout', [
            'flightId' => $flightId,
            'priceInfo' => $priceInfo,
            'passengers' => max(1, (int) $request->query('passengers', 1))
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'flight_id' => 'required|string',
            'title' => 'required|array',
            'first_name' => 'required|array',
            'last_name' => 'required|array',
            'dob' => 'required|array',
            'nationality' => 'required|array',
            'email' => 'required|email',
            'phone' => 'required|string',
            'payment_method' => 'required|string'
        ]);

        if (
            count($request->title) !== count($request->first_name) ||
            count($request->first_name) !== count($request->last_name) ||
            count($request->last_name) !== count($request->dob) ||
            count($request->dob) !== count($request->nationality)
        ) {
            return back()->withInput()->withErrors(['passengers' => 'Passenger details are incomplete.']);
        }

        $passengerDetails = [];
        foreach($request->first_name as $index => $fname) {
            $passengerDetails[] = [
                'title' => $request->title[$index] ?? 'Mr',
                'first_name' => $fname,
                'last_name' => $request->last_name[$index] ?? '',
                'dob' => $request->dob[$index] ?? '1990-01-01',
                'nationality' => $request->nationality[$index] ?? 'IN',
                'email' => $request->email,
                'phone' => $request->phone,
            ];
        }

        try {
            $priceInfo = $this->flightService->price($request->flight_id);
            if (($priceInfo['status'] ?? 'expired') !== 'available') {
                return back()->withInput()->withErrors(['fare' => 'Fare is no longer available. Please search again.']);
            }

            $apiResponse = $this->flightService->book($request->flight_id, $passengerDetails);
        } catch (\Throwable $e) {
            Log::error('Flight booking failed', [
                'flight_id' => $request->flight_id,
                'message' => $e->getMessage(),
            ]);

            return back()->withInput()->withErrors(['booking' => $e->getMessage() ?: 'Booking failed at provider side.']);
        }

        /** @var \App\Models\User $user */
        $user = auth()->user();

        $booking = DB::transaction(function () use ($user, $apiResponse, $priceInfo, $passengerDetails, $request) {
            $booking = \App\Models\Booking::create([
                'user_id' => $user->id,
                'type' => 'flight',
                'api_reference_id' => $apiResponse['api_reference_id'] ?? 'N/A',
                'total_amount' => $priceInfo['total_price'] ?? 0,
                'status' => ($apiResponse['status'] ?? 'confirmed')
            ]);

            foreach($passengerDetails as $p) {
                $booking->passengers()->create([
                    'title' => $p['title'],
                    'first_name' => $p['first_name'],
                    'last_name' => $p['last_name'],
                    'date_of_birth' => $p['dob'],
                    'nationality' => $p['nationality'],
                    'email' => $p['email'],
                    'phone' => $p['phone']
                ]);
            }

            $booking->payments()->create([
                'provider' => $request->payment_method,
                'amount' => $priceInfo['total_price'] ?? 0,
                'status' => 'successful'
            ]);

            return $booking;
        });

        return redirect()->route('dashboard')->with('success', 'Booking Confirmed! PNR: ' . $booking->api_reference_id);
    }

    public function show($id)
    {
        $booking = \App\Models\Booking::with(['passengers', 'payments'])->where('user_id', auth()->id())->findOrFail($id);

        // You might want to parse the api_response generic json to extract flight details, 
        // since we didn't save flight paths strictly in DB tables here, only PNR. 
        // Or fetch flight via API. We'll pass it to view.

        return view('bookings.show', compact('booking'));
    }
}
