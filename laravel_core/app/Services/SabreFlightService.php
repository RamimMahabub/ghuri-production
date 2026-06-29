<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class SabreFlightService implements FlightServiceInterface
{
    protected string $baseUrl;
    protected string $tokenPath;
    protected string $shopPath;
    protected string $username;
    protected string $password;
    protected string $pointOfSale;
    protected string $companyCode;
    protected ?string $pcc;
    protected string $currency;
    protected int $maxSolutions;
    protected array $lastDiagnostics = [];

    public function __construct()
    {
        $this->baseUrl = rtrim((string) config('sabre.base_url'), '/');
        $this->tokenPath = (string) config('sabre.token_path');
        $this->shopPath = (string) config('sabre.shop_path');
        $this->username = (string) config('sabre.username');
        $this->password = (string) config('sabre.password');
        $this->pointOfSale = (string) config('sabre.point_of_sale', 'US');
        $this->companyCode = (string) config('sabre.company_code', 'TN');
        $this->pcc = config('sabre.pcc');
        $this->currency = (string) config('sabre.currency', 'USD');
        $this->maxSolutions = (int) config('sabre.max_solutions', 20);
    }

    public function search(
        string $origin,
        string $destination,
        string $date,
        int $passengers,
        string $tripType = 'one_way',
        ?string $returnDate = null,
        string $cabinClass = 'economy'
    ): array {
        if ($tripType === 'multi_city') {
            return [];
        }

        if ($this->username === '' || $this->password === '') {
            throw new \Exception('Sabre flight credentials are missing. Set SABRE_USERNAME and SABRE_PASSWORD.');
        }

        $token = $this->getToken();
        $payload = $this->buildSearchPayload($origin, $destination, $date, $passengers, $tripType, $returnDate, $cabinClass);

        \Log::debug('Sabre search request payload', [
            'payload' => json_encode($payload, JSON_PRETTY_PRINT),
        ]);

        $response = Http::withoutVerifying()
            ->timeout(60)
            ->acceptJson()
            ->withToken($token)
            ->withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])
            ->post($this->baseUrl . $this->shopPath, $payload);

        if ($response->failed()) {
            throw new \Exception('Sabre flight search failed: ' . $response->body());
        }

        $responseData = $response->json();
        $transformed = app(SabreBFMTransformer::class)->transform($responseData, [
            'origin' => $origin,
            'destination' => $destination,
            'date' => $date,
            'passengers' => $passengers,
            'tripType' => $tripType,
            'returnDate' => $returnDate,
            'cabinClass' => $cabinClass,
            'currency' => $this->currency,
        ]);

        $diagnostics = $transformed['diagnostics'] ?? [
            'provider' => 'sabre_bfm',
            'success' => false,
            'errors' => [],
            'warnings' => [],
            'response_keys' => array_keys($responseData),
        ];
        $this->lastDiagnostics = $diagnostics;

        if (!empty($diagnostics['errors'])) {
            \Log::warning('Sabre BFM diagnostics', [
                'origin' => $origin,
                'destination' => $destination,
                'date' => $date,
                'trip_type' => $tripType,
                'errors' => $diagnostics['errors'],
                'warnings' => $diagnostics['warnings'] ?? [],
                'response_keys' => $diagnostics['response_keys'] ?? array_keys($responseData),
            ]);
        }

        if (!empty($diagnostics['warnings'])) {
            \Log::info('Sabre BFM warnings', [
                'origin' => $origin,
                'destination' => $destination,
                'date' => $date,
                'trip_type' => $tripType,
                'warnings' => $diagnostics['warnings'],
            ]);
        }

        $mappedFlights = [];
        foreach ($transformed['flights'] ?? [] as $flight) {
            $flightId = (string) ($flight['id'] ?? '');
            if ($flightId === '') {
                continue;
            }

            $sourceOffer = $flight['source_offer'] ?? [];
            Cache::put($this->cacheKey($flightId), [
                'offer' => $sourceOffer,
                'total_price' => (float) ($flight['price'] ?? 0),
                'currency' => (string) ($flight['currency'] ?? $this->currency),
            ], now()->addHours(2));

            unset($flight['source_offer']);
            $mappedFlights[] = $flight;
        }

        if (empty($mappedFlights)) {
            \Log::info('Sabre BFM returned no normalized flights', [
                'origin' => $origin,
                'destination' => $destination,
                'date' => $date,
                'trip_type' => $tripType,
                'response_keys' => $diagnostics['response_keys'] ?? array_keys($responseData),
                'errors' => $diagnostics['errors'] ?? [],
                'warnings' => $diagnostics['warnings'] ?? [],
            ]);

            return [];
        }

        return $mappedFlights;
    }

    public function lastDiagnostics(): array
    {
        return $this->lastDiagnostics;
    }

    public function price(string $flightId): array
    {
        $cached = Cache::get($this->cacheKey($flightId));

        if (!$cached) {
            return [
                'total_price' => 0,
                'currency' => 'USD',
                'status' => 'expired',
            ];
        }

        return [
            'total_price' => $cached['total_price'] ?? 0,
            'currency' => $cached['currency'] ?? 'USD',
            'status' => 'available',
        ];
    }

    public function book(string $flightId, array $passengerDetails): array
    {
        $cached = Cache::get($this->cacheKey($flightId));

        if (!$cached) {
            throw new \Exception('Flight offer has expired.');
        }

        return [
            'api_reference_id' => 'PNR' . strtoupper(Str::random(6)),
            'status' => 'confirmed',
        ];
    }

    protected function getToken(): string
    {
        $configuredToken = trim((string) config('sabre.access_token'));
        if ($configuredToken !== '') {
            return $configuredToken;
        }

        $cacheKey = 'sabre_token_' . md5($this->username . '|' . $this->pointOfSale . '|' . $this->companyCode);
        $cachedToken = Cache::get($cacheKey);
        if (is_string($cachedToken) && $cachedToken !== '') {
            return $cachedToken;
        }

        $response = Http::withoutVerifying()
            ->timeout(30)
            ->asForm()
            ->withBasicAuth($this->username, $this->password)
            ->post($this->baseUrl . $this->tokenPath, [
                'grant_type' => 'client_credentials',
            ]);

        if ($response->failed()) {
            throw new \Exception('Failed to authenticate with Sabre: ' . $response->body());
        }

        $accessToken = (string) $response->json('access_token', '');
        if ($accessToken === '') {
            throw new \Exception('Sabre authentication succeeded but no access token was returned.');
        }

        $expiresIn = max(60, (int) $response->json('expires_in', 3600) - 60);
        Cache::put($cacheKey, $accessToken, now()->addSeconds($expiresIn));

        return $accessToken;
    }

    protected function buildSearchPayload(
        string $origin,
        string $destination,
        string $date,
        int $passengers,
        string $tripType,
        ?string $returnDate,
        string $cabinClass
    ): array {
        $originDestinationInformation = [
            [
                'DepartureDateTime' => Carbon::parse($date)->startOfDay()->format('Y-m-d\TH:i:s'),
                'OriginLocation' => ['LocationCode' => strtoupper($origin)],
                'DestinationLocation' => ['LocationCode' => strtoupper($destination)],
            ],
        ];

        if (($tripType === 'round_way' || $tripType === 'round_trip') && $returnDate) {
            $originDestinationInformation[] = [
                'DepartureDateTime' => Carbon::parse($returnDate)->startOfDay()->format('Y-m-d\TH:i:s'),
                'OriginLocation' => ['LocationCode' => strtoupper($destination)],
                'DestinationLocation' => ['LocationCode' => strtoupper($origin)],
            ];
        }

        return [
            'OTA_AirLowFareSearchRQ' => [
                'Version' => '5',
                'POS' => [
                    'Source' => [
                        [
                            'PseudoCityCode' => $this->pcc,
                            'RequestorID' => [
                                'Type' => '1',
                                'ID' => '1',
                                'CompanyName' => [
                                    'Code' => $this->companyCode,
                                ],
                            ],
                        ],
                    ],
                ],
                'OriginDestinationInformation' => $originDestinationInformation,
                'TravelPreferences' => [
                    'MaxStopsQuantity' => 0,
                ],
                'TravelerInfoSummary' => [
                    'AirTravelerAvail' => [
                        [
                            'PassengerTypeQuantity' => [
                                [
                                    'Code' => 'ADT',
                                    'Quantity' => $passengers,
                                ],
                            ],
                        ],
                    ],
                ],
                'TPA_Extensions' => [
                    'IntelliSellTransaction' => [
                        'RequestType' => [
                            'Name' => max(1, $this->maxSolutions) . 'ITINS',
                        ],
                    ],
                ],
            ],
        ];
    }

    protected function extractOffers(array $response): array
    {
        $paths = [
            'groupedItineraryResponse.itineraryGroups',
            'groupedItineraryResponse.itineraryGroups.itineraries',
            'groupedItineraryResponse.itineraryGroups.itinerary',
            'OTA_AirLowFareSearchRS.PricedItineraries.PricedItinerary',
            'OTA_AirLowFareSearchRS.PricedItinerary',
            'PricedItineraries.PricedItinerary',
            'PricedItinerary',
            'data',
        ];

        foreach ($paths as $path) {
            $value = data_get($response, $path);
            if (!empty($value)) {
                return $this->normalizeList($value);
            }
        }

        return [];
    }

    protected function extractLegs(array $offer): array
    {
        $paths = [
            'AirItinerary.OriginDestinationOptions.OriginDestinationOption',
            'AirItinerary.OriginDestinationOption',
            'OriginDestinationOptions.OriginDestinationOption',
            'OriginDestinationOption',
            'ItineraryGroup.OriginDestinationOption',
        ];

        foreach ($paths as $path) {
            $value = data_get($offer, $path);
            if (empty($value)) {
                continue;
            }

            $legs = [];
            foreach ($this->normalizeList($value) as $legNode) {
                $segments = $this->extractSegmentsFromLeg($legNode);
                if (!empty($segments)) {
                    $legs[] = [
                        'segments' => $segments,
                        'duration' => $this->resolveLegDuration($legNode, $segments),
                    ];
                }
            }

            if (!empty($legs)) {
                return $legs;
            }
        }

        $segments = $this->extractSegmentsFromLeg($offer);
        if (!empty($segments)) {
            return [[
                'segments' => $segments,
                'duration' => $this->resolveLegDuration($offer, $segments),
            ]];
        }

        return [];
    }

    protected function extractSegmentsFromLeg(array $legNode): array
    {
        $paths = [
            'FlightSegment',
            'FlightSegments.FlightSegment',
            'Segments.Segment',
            'segment',
        ];

        foreach ($paths as $path) {
            $value = data_get($legNode, $path);
            if (empty($value)) {
                continue;
            }

            $segments = [];
            foreach ($this->normalizeList($value) as $segmentNode) {
                $segments[] = $this->normalizeSegment($segmentNode);
            }

            $segments = array_values(array_filter($segments, fn (array $segment) => !empty($segment['departure_time']) || !empty($segment['arrival_time'])));

            if (!empty($segments)) {
                return $segments;
            }
        }

        $singleSegment = $this->normalizeSegment($legNode);
        return (!empty($singleSegment['departure_time']) || !empty($singleSegment['arrival_time'])) ? [$singleSegment] : [];
    }

    protected function normalizeSegment(array $segmentNode): array
    {
        $departureTime = data_get($segmentNode, 'DepartureDateTime')
            ?? data_get($segmentNode, 'departure.at')
            ?? data_get($segmentNode, 'DepartureDate')
            ?? '';

        $arrivalTime = data_get($segmentNode, 'ArrivalDateTime')
            ?? data_get($segmentNode, 'arrival.at')
            ?? data_get($segmentNode, 'ArrivalDate')
            ?? '';

        return [
            'origin' => strtoupper((string) (data_get($segmentNode, 'DepartureAirportLocationCode')
                ?? data_get($segmentNode, 'departure.iataCode')
                ?? data_get($segmentNode, 'OriginLocation.LocationCode')
                ?? data_get($segmentNode, 'OriginLocation')
                ?? '')),
            'destination' => strtoupper((string) (data_get($segmentNode, 'ArrivalAirportLocationCode')
                ?? data_get($segmentNode, 'arrival.iataCode')
                ?? data_get($segmentNode, 'DestinationLocation.LocationCode')
                ?? data_get($segmentNode, 'DestinationLocation')
                ?? '')),
            'departure_time' => $this->normalizeDateTime($departureTime),
            'arrival_time' => $this->normalizeDateTime($arrivalTime),
            'flight_number' => trim((string) ((data_get($segmentNode, 'MarketingAirlineCode') ?? data_get($segmentNode, 'airlineCode') ?? '') . '-' . (data_get($segmentNode, 'FlightNumber') ?? data_get($segmentNode, 'number') ?? '')), '-'),
            'airline' => (string) (data_get($segmentNode, 'MarketingAirlineName') ?? data_get($segmentNode, 'OperatingAirlineName') ?? data_get($segmentNode, 'carrier.name') ?? data_get($segmentNode, 'MarketingAirlineCode') ?? data_get($segmentNode, 'carrierCode') ?? ''),
            'airline_code' => strtoupper((string) (data_get($segmentNode, 'MarketingAirlineCode') ?? data_get($segmentNode, 'carrierCode') ?? data_get($segmentNode, 'OperatingAirlineCode') ?? '')),
            'duration' => $this->formatDuration(
                data_get($segmentNode, 'JourneyDuration')
                ?? data_get($segmentNode, 'ElapsedTime')
                ?? data_get($segmentNode, 'duration')
            ),
        ];
    }

    protected function resolveLegDuration(array $legNode, array $segments): string
    {
        $duration = $this->formatDuration(
            data_get($legNode, 'ElapsedTime')
            ?? data_get($legNode, 'JourneyDuration')
            ?? data_get($legNode, 'duration')
        );

        if ($duration !== '') {
            return $duration;
        }

        $firstDeparture = $segments[0]['departure_time'] ?? null;
        $lastArrival = $segments[count($segments) - 1]['arrival_time'] ?? null;
        if ($firstDeparture && $lastArrival) {
            return $this->formatDuration(Carbon::parse($firstDeparture)->diffInMinutes(Carbon::parse($lastArrival)));
        }

        return '';
    }

    protected function resolveTotalPrice(array $offer): float
    {
        $paths = [
            'AirItineraryPricingInfo.ItinTotalFare.TotalFare.Amount',
            'ItinTotalFare.TotalFare.Amount',
            'price.total',
            'PricingInformation.TotalPrice',
            'PricingInformation.Fare.TotalFare.Amount',
        ];

        foreach ($paths as $path) {
            $value = data_get($offer, $path);
            if ($value !== null && $value !== '') {
                return (float) $value;
            }
        }

        return 0.0;
    }

    protected function resolveCurrency(array $offer, ?string $fallback = null): string
    {
        $paths = [
            'AirItineraryPricingInfo.ItinTotalFare.TotalFare.CurrencyCode',
            'ItinTotalFare.TotalFare.CurrencyCode',
            'price.currency',
            'PricingInformation.CurrencyCode',
        ];

        foreach ($paths as $path) {
            $value = data_get($offer, $path);
            if (is_string($value) && $value !== '') {
                return strtoupper($value);
            }
        }

        return strtoupper((string) ($fallback ?? $this->currency));
    }

    protected function formatDuration(mixed $duration): string
    {
        if ($duration === null || $duration === '') {
            return '';
        }

        if (is_numeric($duration)) {
            $minutes = (int) $duration;
            return intdiv($minutes, 60) . 'h ' . ($minutes % 60) . 'm';
        }

        if (is_string($duration)) {
            if (preg_match('/^PT(?:(\d+)H)?(?:(\d+)M)?$/', $duration, $matches)) {
                $hours = (int) ($matches[1] ?? 0);
                $minutes = (int) ($matches[2] ?? 0);
                return $hours . 'h ' . $minutes . 'm';
            }

            if (preg_match('/^(\d+):(\d{2})$/', $duration, $matches)) {
                return (int) $matches[1] . 'h ' . (int) $matches[2] . 'm';
            }
        }

        return (string) $duration;
    }

    protected function normalizeDateTime(mixed $value): string
    {
        if ($value === null || $value === '') {
            return '';
        }

        if (!is_string($value)) {
            return (string) $value;
        }

        try {
            return Carbon::parse($value)->toDateTimeString();
        } catch (\Throwable $throwable) {
            return $value;
        }
    }

    protected function normalizeList(mixed $value): array
    {
        if (!is_array($value)) {
            return [];
        }

        if ($this->isSequentialArray($value)) {
            return $value;
        }

        return [$value];
    }

    protected function isSequentialArray(array $value): bool
    {
        return array_keys($value) === range(0, count($value) - 1);
    }

    protected function cacheKey(string $flightId): string
    {
        return 'sabre_offer_' . $flightId;
    }
}