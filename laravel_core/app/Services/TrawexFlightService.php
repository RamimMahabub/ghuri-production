<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class TrawexFlightService implements FlightServiceInterface
{
    protected string $baseUrl;
    protected string $userId;
    protected string $userPassword;
    protected string $access;

    public function __construct()
    {
        $this->baseUrl = config('trawex.url') ?? '';
        $this->userId = config('trawex.user_id') ?? '';
        $this->userPassword = config('trawex.password') ?? '';
        $this->access = config('trawex.access') ?? '';
    }

    /**
     * Search Flights
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
        if (empty($this->userId)) {
            throw new \Exception('Flight provider credentials are missing in environment variables. Please add your API keys in deployment settings.');
        }

        $journeyType = 'OneWay';
        if ($tripType === 'round_way' || $tripType === 'round_trip' || $returnDate !== null) {
            $journeyType = 'Return';
        }

        $originDestInfo = [
            'departureDate' => $date,
            'airportOriginCode' => $origin,
            'airportDestinationCode' => $destination,
        ];

        if ($journeyType === 'Return') {
            $originDestInfo['returnDate'] = $returnDate;
        }

        $classMap = [
            'economy' => 'Economy',
            'premium_economy' => 'PremiumEconomy',
            'business' => 'Business',
            'first' => 'First',
        ];

        $payload = [
            'user_id' => $this->userId,
            'user_password' => $this->userPassword,
            'access' => $this->access,
            'ip_address' => config('trawex.ip_address'),
            'requiredCurrency' => 'USD',
            'journeyType' => $journeyType,
            'OriginDestinationInfo' => [$originDestInfo],
            'class' => $classMap[$cabinClass] ?? 'Economy',
            'adults' => $passengers, // Assuming all requested passengers are adults
            'childs' => 0,
            'infants' => 0,
        ];

        $response = Http::withoutVerifying()->connectTimeout(10)->timeout(25)->post($this->baseUrl . '/aeroVE5/availability', $payload);

        if ($response->failed()) {
            throw new \Exception('GHURI flight engine HTTP error: ' . $response->status() . ' - ' . $response->body());
        }

        $data = $response->json();
        $mappedFlights = [];
        
        $searchResponse = $data['AirSearchResponse'] ?? null;
        if (!$searchResponse || !isset($searchResponse['AirSearchResult']['FareItineraries'])) {
            $errorMsg = 'Unknown error';
            if (isset($data['AirSearchResponse']['Errors'])) {
                 $errorMsg = json_encode($data['AirSearchResponse']['Errors']);
            } elseif (isset($data['Errors'])) {
                 $errorMsg = json_encode($data['Errors']);
            }
            throw new \Exception('GHURI flight engine returned no flights. Error: ' . $errorMsg);
        }

        $sessionId = $searchResponse['session_id'] ?? null;
        $itineraries = $searchResponse['AirSearchResult']['FareItineraries'];

        if (!$sessionId || !is_array($itineraries)) {
            return [];
        }

        foreach ($itineraries as $index => $offerWrapper) {
            $fareItin = $offerWrapper['FareItinerary'] ?? null;
            if (!$fareItin) continue;

            $fareInfo = $fareItin['AirItineraryFareInfo'] ?? [];
            $fareSourceCode = $fareInfo['FareSourceCode'] ?? null;
            
            // Generate a unique MD5 hash as our internal flight ID
            $flightId = md5($sessionId . $fareSourceCode . $index);
            
            // Cache the session_id along with the itinerary data
            Cache::put('ghuri_offer_' . $flightId, [
                'session_id' => $sessionId,
                'FareItinerary' => $fareItin
            ], now()->addHours(2));

            $totalFares = $fareInfo['ItinTotalFares'] ?? [];
            $price = $totalFares['TotalFare']['Amount'] ?? 0;
            $currency = $totalFares['TotalFare']['CurrencyCode'] ?? 'USD';
            $baseFare = $totalFares['BaseFare']['Amount'] ?? '0';
            $totalTax = $totalFares['TotalTax']['Amount'] ?? '0';
            $serviceTax = $totalFares['ServiceTax']['Amount'] ?? '0';
            $equivFare = $totalFares['EquivFare']['Amount'] ?? '0';

            // Fare breakdown (first passenger type)
            $fareBreakdown = $fareInfo['FareBreakdown'][0] ?? [];
            $passengerFare = $fareBreakdown['PassengerFare'] ?? [];
            $taxesList = $passengerFare['Taxes'] ?? [];
            $penaltyDetails = $fareBreakdown['PenaltyDetails'] ?? [];
            $baggageList = $fareBreakdown['Baggage'] ?? [];
            $cabinBaggageList = $fareBreakdown['CabinBaggage'] ?? [];
            $passengerTypeQty = $fareBreakdown['PassengerTypeQuantity'] ?? [];

            $options = $fareItin['OriginDestinationOptions'] ?? [];
            $outboundOptions = $options[0]['OriginDestinationOption'] ?? [];
            $inboundOptions = $options[1]['OriginDestinationOption'] ?? [];

            $normalizeSegment = function ($segmentData): array {
                return $segmentData['FlightSegment'] ?? $segmentData;
            };

            $getSegmentWrapper = function ($segmentData): array {
                if (isset($segmentData['FlightSegment'])) {
                    return $segmentData;
                }
                return [];
            };

            $outboundSegments = collect($outboundOptions)
                ->map(fn ($opt) => $normalizeSegment($opt))
                ->filter(fn ($seg) => is_array($seg) && !empty($seg))
                ->values();

            $outboundWrappers = collect($outboundOptions)
                ->filter(fn ($opt) => isset($opt['FlightSegment']))
                ->values();

            if ($outboundSegments->isEmpty()) {
                continue;
            }

            $inboundSegments = collect($inboundOptions)
                ->map(fn ($opt) => $normalizeSegment($opt))
                ->filter(fn ($seg) => is_array($seg) && !empty($seg))
                ->values();

            $inboundWrappers = collect($inboundOptions)
                ->filter(fn ($opt) => isset($opt['FlightSegment']))
                ->values();

            $firstOut = $outboundSegments->first();
            $lastOut = $outboundSegments->last();

            $airlineCode = $firstOut['MarketingAirlineCode'] ?? 'XX';
            $airline = $firstOut['MarketingAirlineName'] ?? $airlineCode;
            $flightNumber = $firstOut['FlightNumber'] ?? 'Unknown';

            $departure = $firstOut['DepartureDateTime'] ?? Carbon::parse($date)->setTime(8, 0)->toDateTimeString();
            $arrival = $lastOut['ArrivalDateTime'] ?? Carbon::parse($date)->setTime(10, 0)->toDateTimeString();

            $outboundDurationMins = (int) collect($outboundSegments)->sum(fn ($seg) => (int) ($seg['JourneyDuration'] ?? 0));
            if ($outboundDurationMins <= 0) {
                $outboundDurationMins = Carbon::parse($arrival)->diffInMinutes(Carbon::parse($departure));
            }

            $durationStr = intdiv((int) $outboundDurationMins, 60) . 'h ' . ($outboundDurationMins % 60) . 'm';
            $outboundStops = max(0, count($outboundSegments) - 1);

            $buildSegments = function ($segments, $wrappers) {
                $result = [];
                $segmentArray = $segments->values()->all();
                $wrapperArray = $wrappers->values()->all();
                foreach ($segmentArray as $i => $seg) {
                    $wrapper = $wrapperArray[$i] ?? [];
                    $durMins = (int) ($seg['JourneyDuration'] ?? 0);
                    $seatsRemaining = $wrapper['SeatsRemaining'] ?? [];
                    $opAirline = $seg['OperatingAirline'] ?? [];
                    $stopQtyInfo = $wrapper['StopQuantityInfo'] ?? [];
                    $segData = [
                        'departure_airport' => $seg['DepartureAirportLocationCode'] ?? '',
                        'arrival_airport' => $seg['ArrivalAirportLocationCode'] ?? '',
                        'departure_time' => $seg['DepartureDateTime'] ?? '',
                        'arrival_time' => $seg['ArrivalDateTime'] ?? '',
                        'flight_number' => ($seg['MarketingAirlineCode'] ?? '') . '-' . ($seg['FlightNumber'] ?? ''),
                        'airline' => $seg['MarketingAirlineName'] ?? ($seg['MarketingAirlineCode'] ?? ''),
                        'airline_code' => $seg['MarketingAirlineCode'] ?? '',
                        'duration_mins' => $durMins,
                        'duration' => $durMins > 0 ? intdiv($durMins, 60) . 'h ' . ($durMins % 60) . 'm' : '',
                        'cabin_class' => $seg['CabinClassCode'] ?? '',
                        'cabin_class_text' => $seg['CabinClassText'] ?? '',
                        'eticket' => (bool) ($seg['Eticket'] ?? false),
                        'booking_class' => $wrapper['ResBookDesigCode'] ?? '',
                        'booking_class_text' => $wrapper['ResBookDesigText'] ?? '',
                        'seats_remaining' => (int) ($seatsRemaining['Number'] ?? 0),
                        'seats_below_minimum' => (bool) ($seatsRemaining['BelowMinimum'] ?? false),
                        'stop_quantity' => (int) ($wrapper['StopQuantity'] ?? 0),
                        'meal_code' => $seg['MealCode'] ?? '',
                        'marriage_group' => $seg['MarriageGroup'] ?? '',
                        'operating_airline_code' => $opAirline['Code'] ?? '',
                        'operating_airline_name' => $opAirline['Name'] ?? '',
                        'operating_airline_equipment' => $opAirline['Equipment'] ?? '',
                        'operating_flight_number' => $opAirline['FlightNumber'] ?? '',
                        'stop_location' => $stopQtyInfo['LocationCode'] ?? '',
                        'stop_arrival' => $stopQtyInfo['ArrivalDateTime'] ?? '',
                        'stop_departure' => $stopQtyInfo['DepartureDateTime'] ?? '',
                        'stop_duration' => $stopQtyInfo['Duration'] ?? '',
                        'layover_duration' => null,
                        'layover_airport' => null,
                    ];
                    if ($i > 0) {
                        $prevArrival = $segmentArray[$i - 1]['ArrivalDateTime'] ?? null;
                        $currDeparture = $seg['DepartureDateTime'] ?? null;
                        if ($prevArrival && $currDeparture) {
                            $layoverMins = Carbon::parse($prevArrival)->diffInMinutes(Carbon::parse($currDeparture));
                            if ($layoverMins > 0) {
                                $segData['layover_duration'] = intdiv((int)$layoverMins, 60) . 'h ' . ($layoverMins % 60) . 'm';
                                $segData['layover_airport'] = $segData['departure_airport'];
                            }
                        }
                    }
                    $result[] = $segData;
                }
                return $result;
            };

            $inbound = null;
            if ($journeyType === 'Return' && $inboundSegments->isNotEmpty()) {
                $firstIn = $inboundSegments->first();
                $lastIn = $inboundSegments->last();
                $inDep = $firstIn['DepartureDateTime'] ?? ($returnDate ? Carbon::parse($returnDate)->setTime(8, 0)->toDateTimeString() : null);
                $inArr = $lastIn['ArrivalDateTime'] ?? ($returnDate ? Carbon::parse($returnDate)->setTime(10, 0)->toDateTimeString() : null);
                $inDurationMins = (int) collect($inboundSegments)->sum(fn ($seg) => (int) ($seg['JourneyDuration'] ?? 0));
                if ($inDurationMins <= 0 && $inDep && $inArr) {
                    $inDurationMins = Carbon::parse($inArr)->diffInMinutes(Carbon::parse($inDep));
                }

                $inbound = [
                    'origin' => $firstIn['DepartureAirportLocationCode'] ?? $destination,
                    'destination' => $lastIn['ArrivalAirportLocationCode'] ?? $origin,
                    'departure_time' => $inDep,
                    'arrival_time' => $inArr,
                    'duration' => intdiv((int) $inDurationMins, 60) . 'h ' . ($inDurationMins % 60) . 'm',
                    'stops' => max(0, count($inboundSegments) - 1),
                    'layover' => null,
                    'segments' => $buildSegments($inboundSegments, $inboundWrappers),
                ];
            }

            $mappedFlights[] = [
                'id' => $flightId,
                'airline' => $airline,
                'airline_code' => $airlineCode,
                'flight_number' => $flightNumber,
                'origin' => $origin,
                'destination' => $destination,
                'departure_time' => $departure,
                'arrival_time' => $arrival,
                'duration' => $durationStr,
                'stops' => $outboundStops,
                'price' => floatval($price),
                'currency' => $currency,
                'crossed_price' => round(floatval($price) * 1.08, 2),
                'base_fare' => floatval($baseFare),
                'total_tax' => floatval($totalTax),
                'service_tax' => floatval($serviceTax),
                'equiv_fare' => floatval($equivFare),
                'taxes' => $taxesList,
                'refundable' => (strtolower((string) ($fareInfo['IsRefundable'] ?? '')) === 'yes') || (strtolower((string) ($fareItin['FareType'] ?? '')) !== 'non_refundable'),
                'fare_type' => $fareInfo['FareType'] ?? ($fareItin['FareType'] ?? ''),
                'is_refundable_text' => $fareInfo['IsRefundable'] ?? '',
                'penalty_details' => [
                    'currency' => $penaltyDetails['Currency'] ?? $currency,
                    'refund_allowed' => (bool) ($penaltyDetails['RefundAllowed'] ?? false),
                    'refund_penalty_amount' => floatval($penaltyDetails['RefundPenaltyAmount'] ?? 0),
                    'change_allowed' => (bool) ($penaltyDetails['ChangeAllowed'] ?? false),
                    'change_penalty_amount' => floatval($penaltyDetails['ChangePenaltyAmount'] ?? 0),
                ],
                'baggage' => $baggageList,
                'cabin_baggage' => $cabinBaggageList,
                'passenger_type' => $passengerTypeQty['Code'] ?? 'ADT',
                'passenger_quantity' => (int) ($passengerTypeQty['Quantity'] ?? 1),
                'points' => 25,
                'outbound' => [
                    'origin' => $firstOut['DepartureAirportLocationCode'] ?? $origin,
                    'destination' => $lastOut['ArrivalAirportLocationCode'] ?? $destination,
                    'departure_time' => $departure,
                    'arrival_time' => $arrival,
                    'duration' => $durationStr,
                    'stops' => $outboundStops,
                    'layover' => null,
                    'segments' => $buildSegments($outboundSegments, $outboundWrappers),
                ],
                'inbound' => $inbound,
            ];
        }

        return collect($mappedFlights)->sortBy('price')->values()->toArray();
    }

    /**
     * Price a specific flight / Validate Fare Method
     */
    public function price(string $flightId): array
    {
        $cachedData = Cache::get('ghuri_offer_' . $flightId);
        
        if (!$cachedData || !isset($cachedData['session_id']) || !isset($cachedData['FareItinerary'])) {
            return [
                'total_price' => 0,
                'currency' => 'USD',
                'status' => 'expired'
            ];
        }

        $sessionId = $cachedData['session_id'];
        $fareSourceCode = $cachedData['FareItinerary']['AirItineraryFareInfo']['FareSourceCode'] ?? '';

        $payload = [
            'session_id' => $sessionId,
            'fare_source_code' => $fareSourceCode
        ];

        $response = Http::withoutVerifying()->connectTimeout(10)->timeout(25)->post($this->baseUrl . '/aeroVE5/revalidate', $payload);

        if ($response->failed()) {
            Log::error('GHURI fare validation failed: ' . $response->body());
            return [
                'total_price' => 0,
                'currency' => 'USD',
                'status' => 'expired'
            ];
        }

        $data = $response->json();
        $result = $data['AirRevalidateResponse']['AirRevalidateResult'] ?? null;

        // Verify if the fare is still valid
        if (!$result || ($result['IsValid'] ?? false) == false || ($result['IsValid'] ?? 'false') === 'false') {
            return [
                'total_price' => 0,
                'currency' => 'USD',
                'status' => 'expired'
            ];
        }

        // Successfully revalidated, fetch the latest price elements
        $validatedItin = $result['FareItineraries']['FareItinerary'] ?? [];
        $totalFare = $validatedItin['AirItineraryFareInfo']['ItinTotalFares']['TotalFare'] ?? [];
        $priceAmount = $totalFare['Amount'] ?? 0;
        $currency = $totalFare['CurrencyCode'] ?? 'USD';

        // Re-cache the validated result and any new FareSourceCode if updated
        if (isset($validatedItin['AirItineraryFareInfo']['FareSourceCode'])) {
            $cachedData['FareItinerary'] = $validatedItin;
            Cache::put('ghuri_offer_' . $flightId, $cachedData, now()->addHours(2));
        }

        return [
            'total_price' => floatval($priceAmount),
            'currency' => $currency,
            'status' => 'available'
        ];
    }

    /**
     * Book the Flight
     */
    public function book(string $flightId, array $passengerDetails): array
    {
        $cachedData = Cache::get('ghuri_offer_' . $flightId);
        
        if (!$cachedData || !isset($cachedData['session_id']) || !isset($cachedData['FareItinerary'])) {
            throw new \Exception("Flight offer has expired.");
        }

        $sessionId = $cachedData['session_id'];
        $fareItin = $cachedData['FareItinerary'];
        $fareSourceCode = $fareItin['AirItineraryFareInfo']['FareSourceCode'] ?? '';
        $fareType = $fareItin['FareType'] ?? 'Public';

        $titles = [];
        $firstNames = [];
        $lastNames = [];
        $dobs = [];
        $nationalities = [];

        foreach ($passengerDetails as $p) {
            $titles[] = $p['title'] ?? 'Mr';
            $firstNames[] = $p['first_name'] ?? 'Unknown';
            $lastNames[] = $p['last_name'] ?? 'Unknown';
            $dobs[] = $p['dob'] ?? '1990-01-01';
            $nationalities[] = $p['nationality'] ?? 'IN';
        }

        $paxInfo = [
            'clientRef' => 'SYS_GEN_' . strtoupper(Str::random(6)),
            'customerEmail' => $passengerDetails[0]['email'] ?? 'test@ghuri.travel',
            'customerPhone' => $passengerDetails[0]['phone'] ?? '1234567890',
            'paxDetails' => [
                [
                    'adult' => [
                        'title' => $titles,
                        'firstName' => $firstNames,
                        'lastName' => $lastNames,
                        'dob' => $dobs,
                        'nationality' => $nationalities
                    ]
                ]
            ]
        ];

        $payload = [
            'flightBookingInfo' => [
                'flight_session_id' => $sessionId,
                'fare_source_code' => $fareSourceCode,
                'IsPassportMandatory' => 'false',
                'fareType' => $fareType,
                'areaCode' => '080',
                'countryCode' => '91'
            ],
            'paxInfo' => $paxInfo
        ];

        $response = Http::withoutVerifying()->connectTimeout(10)->timeout(25)->post($this->baseUrl . '/aeroVE5/booking', $payload);

        if ($response->failed()) {
            Log::error('GHURI booking failed: ' . $response->body());
            throw new \Exception('Failed to process booking in GHURI engine.');
        }

        $data = $response->json();
        $result = $data['BookFlightResponse']['BookFlightResult'] ?? null;

        $successRaw = $result['Success'] ?? false;
        $isSuccess = ($successRaw === true || strtolower(trim((string)$successRaw)) === 'true');

        if (!$result || !$isSuccess) {
             $errorMsg = 'Booking rejected by provider.';
             if (isset($result['Errors'][0]['Errors']['ErrorMessage'])) {
                 $errorMsg = $result['Errors'][0]['Errors']['ErrorMessage'];
             } else if (isset($result['Errors']['ErrorMessage'])) {
                 $errorMsg = $result['Errors']['ErrorMessage'];
             }
             throw new \Exception($errorMsg);
        }

        $status = strtolower($result['Status'] ?? 'pending');
        
        return [
            'api_reference_id' => (!empty($result['UniqueID']) ? $result['UniqueID'] : ('PNR' . strtoupper(Str::random(6)))),
            'status' => $status
        ];
    }
}
