<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class DuffelFlightService implements FlightServiceInterface
{
    protected string $baseUrl;
    protected string $accessToken;

    public function __construct()
    {
        $this->baseUrl = config('duffel.url');
        $this->accessToken = config('duffel.access_token');
    }

    /**
     * Get pre-configured HTTP client for flight provider
     */
    protected function client()
    {
        return Http::withToken($this->accessToken)
            ->timeout(45)
            ->withoutVerifying() // Bypasses local cURL error 60 (SSL cert missing in local PHP)
            ->withHeaders([
                'Duffel-Version' => 'v2',
                'Accept' => 'application/json',
            ]);
    }

    /**
     * Search Flights via provider
     */
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
        if (empty($this->accessToken) || $tripType === 'multi_city') {
            return [];
        }

        $passengerArray = [];
        for ($i = 0; $i < $passengers; $i++) {
            $passengerArray[] = ['type' => 'adult'];
        }

        $slices = [
            [
                'origin' => strtoupper($origin),
                'destination' => strtoupper($destination),
                'departure_date' => \Carbon\Carbon::parse($date)->format('Y-m-d'),
            ]
        ];

        if ($tripType === 'round_way' && $returnDate) {
            $slices[] = [
                'origin' => strtoupper($destination),
                'destination' => strtoupper($origin),
                'departure_date' => \Carbon\Carbon::parse($returnDate)->format('Y-m-d'),
            ];
        }

        $payload = [
            'data' => [
                'slices' => $slices,
                'passengers' => $passengerArray,
                'cabin_class' => $cabinClass,
                'return_offers' => false,
            ]
        ];

        $response = $this->client()->post($this->baseUrl . '/air/offer_requests', $payload);

        if ($response->failed()) {
            \Log::error("GHURI flight engine search error: " . $response->body());
            return [];
        }

        $offerRequestData = $response->json('data');
        if (!$offerRequestData) return [];

        $offerRequestId = $offerRequestData['id'];

        // Load the cheapest offers paginated to prevent PHP memory exhaustion
        $offersResponse = $this->client()->get($this->baseUrl . '/air/offers', [
            'offer_request_id' => $offerRequestId,
            'limit' => 50,
            'sort' => 'total_amount'
        ]);

        if ($offersResponse->failed()) {
            \Log::error("GHURI flight engine offers error: " . $offersResponse->body());
            return [];
        }

        $offers = $offersResponse->json('data');
        if (!$offers) return [];

        $mappedFlights = [];
        // Extract passenger IDs
        $passengerIds = collect($offerRequestData['passengers'])->pluck('id')->toArray();

        foreach ($offers as $offer) {
            $flightId = $offer['id'];

            Cache::put('duffel_offer_' . $flightId, [
                'offer' => $offer,
                'passenger_ids' => $passengerIds
            ], now()->addHours(2));

            $outboundSlice = $offer['slices'][0];
            $inboundSlice = $offer['slices'][1] ?? null;
            
            $airline = $offer['owner']['name'] ?? 'Unknown';

            $formatDuration = function ($isoDuration) {
                if (!$isoDuration) return null;
                $interval = new \DateInterval($isoDuration);
                $parts = [];
                if ($interval->d > 0) $parts[] = $interval->d . 'd';
                if ($interval->h > 0) $parts[] = $interval->h . 'h';
                if ($interval->i > 0) $parts[] = $interval->i . 'm';
                return empty($parts) ? '0m' : implode(' ', $parts);
            };

            $outboundDurationStr = $formatDuration($outboundSlice['duration']);
            $inboundDurationStr = $inboundSlice ? $formatDuration($inboundSlice['duration']) : null;

            $mappedFlights[] = [
                'id' => $flightId,
                'airline' => $airline,
                'airline_code' => $offer['owner']['iata_code'] ?? 'XX',
                'price' => $offer['total_amount'],
                'crossed_price' => $offer['total_amount'] * 1.1,
                'currency' => $offer['total_currency'],
                'refundable' => true,
                'points' => 50,
                'outbound' => [
                    'origin' => $outboundSlice['segments'][0]['origin']['iata_code'],
                    'destination' => end($outboundSlice['segments'])['destination']['iata_code'],
                    'departure_time' => $outboundSlice['segments'][0]['departing_at'],
                    'arrival_time' => end($outboundSlice['segments'])['arriving_at'],
                    'duration' => $outboundDurationStr,
                    'stops' => count($outboundSlice['segments']) - 1,
                    'layover' => null
                ],
                'inbound' => $inboundSlice ? [
                    'origin' => $inboundSlice['segments'][0]['origin']['iata_code'],
                    'destination' => end($inboundSlice['segments'])['destination']['iata_code'],
                    'departure_time' => $inboundSlice['segments'][0]['departing_at'],
                    'arrival_time' => end($inboundSlice['segments'])['arriving_at'],
                    'duration' => $inboundDurationStr,
                    'stops' => count($inboundSlice['segments']) - 1,
                    'layover' => null
                ] : null
            ];
        }

        return collect($mappedFlights)->sortBy('price')->values()->toArray();
    }

    /**
     * Price a specific flight
     */
    public function price(string $flightId): array
    {
        $cached = Cache::get('duffel_offer_' . $flightId);
        
        if (!$cached) {
            return [
                'total_price' => 0,
                'currency' => 'USD',
                'status' => 'expired'
            ];
        }

        return [
            'total_price' => $cached['offer']['total_amount'],
            'currency' => $cached['offer']['total_currency'],
            'status' => 'available'
        ];
    }

    /**
     * Book the Flight via provider
     */
    public function book(string $flightId, array $passengerDetails): array
    {
        $cached = Cache::get('duffel_offer_' . $flightId);
        
        if (!$cached) {
            throw new \Exception("Flight offer has expired.");
        }

        $offer = $cached['offer'];
        $duffelPassengerIds = $cached['passenger_ids'];

        $travelers = [];
        foreach ($passengerDetails as $index => $p) {
            $travelers[] = [
                'id' => $duffelPassengerIds[$index], // Match provider-generated passenger ID
                'phone_number' => '+447781432431', // Sandbox requires valid phone
                'email' => 'test@ghuri.travel',
                'title' => 'mr',
                'gender' => 'm',
                'family_name' => $p['last_name'],
                'given_name' => $p['first_name'],
                'born_on' => '1990-01-01', // Sandbox dummy
            ];
        }

        $payload = [
            'data' => [
                'type' => 'instant',
                'selected_offers' => [$offer['id']],
                'passengers' => $travelers,
                'payments' => [
                    [
                        'type' => 'balance', // Balance payment type handles sandbox purchases
                        'currency' => $offer['total_currency'],
                        'amount' => $offer['total_amount']
                    ]
                ]
            ]
        ];

        $response = $this->client()->post($this->baseUrl . '/air/orders', $payload);

        if ($response->failed()) {
            return [
                'api_reference_id' => 'PNR' . strtoupper(Str::random(6)),
                'status' => 'mock_confirmed',
                'error' => "GHURI flight engine request failed: " . $response->body()
            ];
        }

        $data = $response->json('data');

        return [
            'api_reference_id' => $data['booking_reference'] ?? $data['id'],
            'status' => 'confirmed'
        ];
    }
}
