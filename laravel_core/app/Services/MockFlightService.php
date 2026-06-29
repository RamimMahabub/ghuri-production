<?php

namespace App\Services;

use Illuminate\Support\Str;
use Carbon\Carbon;

class MockFlightService implements FlightServiceInterface
{
    public function search(
        string $origin,
        string $destination,
        string $date,
        int $passengers,
        string $tripType = 'one_way',
        ?string $returnDate = null,
        string $cabinClass = 'economy'
    ): array
    {
        if ($tripType === 'multi_city') {
            return [];
        }

        $depDate = Carbon::parse($date);
        
        $inboundLeg1 = null;
        $inboundLeg2 = null;
        $inboundLeg3 = null;

        if ($tripType === 'round_way' && $returnDate) {
            $retDate = Carbon::parse($returnDate);
            $inboundLeg1 = [
                'origin' => $destination,
                'destination' => $origin,
                'departure_time' => $retDate->copy()->setTime(2, 25)->toDateTimeString(),
                'arrival_time' => $retDate->copy()->setTime(9, 35)->toDateTimeString(),
                'duration' => '5hr 10min',
                'stops' => 0,
                'layover' => null
            ];
            
            $inboundLeg2 = [
                'origin' => $destination,
                'destination' => $origin,
                'departure_time' => $retDate->copy()->setTime(16, 45)->toDateTimeString(),
                'arrival_time' => $retDate->copy()->setTime(23, 20)->toDateTimeString(),
                'duration' => '4hr 35min',
                'stops' => 0,
                'layover' => null
            ];
            
            $inboundLeg3 = [
                'origin' => $destination,
                'destination' => $origin,
                'departure_time' => $retDate->copy()->setTime(2, 25)->toDateTimeString(),
                'arrival_time' => $retDate->copy()->setTime(9, 35)->toDateTimeString(),
                'duration' => '5hr 10min',
                'stops' => 0,
                'layover' => null
            ];
        }

        return [
            [
                'id' => 'fl_' . Str::random(10),
                'airline' => 'US-Bangla Airlines',
                'airline_code' => 'BS',
                'price' => 64525,
                'crossed_price' => 70525,
                'currency' => '৳',
                'refundable' => true,
                'points' => 55,
                'is_best_deal' => true,
                'is_mock' => true,
                'outbound' => [
                    'origin' => $origin,
                    'destination' => $destination,
                    'departure_time' => $depDate->copy()->setTime(6, 25)->toDateTimeString(),
                    'arrival_time' => $depDate->copy()->setTime(11, 30)->toDateTimeString(),
                    'duration' => '7hr 5min',
                    'stops' => 0,
                    'layover' => null
                ],
                'inbound' => $inboundLeg1
            ],
            [
                'id' => 'fl_' . Str::random(10),
                'airline' => 'Emirates',
                'airline_code' => 'EK',
                'price' => 69818,
                'crossed_price' => 75818,
                'currency' => '৳',
                'refundable' => true,
                'points' => 61,
                'is_preferred' => true,
                'is_mock' => true,
                'outbound' => [
                    'origin' => $origin,
                    'destination' => $destination,
                    'departure_time' => $depDate->copy()->setTime(1, 40)->toDateTimeString(),
                    'arrival_time' => $depDate->copy()->setTime(4, 30)->toDateTimeString(),
                    'duration' => '4hr 50min',
                    'stops' => 0,
                    'layover' => null
                ],
                'inbound' => $inboundLeg2
            ],
            [
                'id' => 'fl_' . Str::random(10),
                'airline' => 'Biman Bangladesh',
                'airline_code' => 'BG',
                'price' => 72757,
                'crossed_price' => 78000,
                'currency' => '৳',
                'refundable' => false,
                'points' => 30,
                'is_mock' => true,
                'outbound' => [
                    'origin' => $origin,
                    'destination' => $destination,
                    'departure_time' => $depDate->copy()->setTime(21, 55)->toDateTimeString(),
                    'arrival_time' => $depDate->copy()->setTime(1, 25)->addDay()->toDateTimeString(),
                    'duration' => '5hr 30min',
                    'stops' => 0,
                    'layover' => null
                ],
                'inbound' => $inboundLeg3
            ]
        ];
    }

    public function price(string $flightId): array
    {
        return [
            'flight_id' => $flightId,
            'price_confirmed' => true,
            'total_price' => 64525,
            'currency' => '৳',
        ];
    }

    public function book(string $flightId, array $passengerDetails): array
    {
        return [
            'status' => 'confirmed',
            'api_reference_id' => strtoupper(Str::random(6)),
            'flight_id' => $flightId,
            'message' => 'Booking confirmed via Mock GDS',
        ];
    }
}
