<?php

namespace App\Services;

use Carbon\Carbon;

class SabreBFMTransformer
{
    public function transform(array $response, array $context = []): array
    {
        $origin = strtoupper((string) ($context['origin'] ?? ''));
        $destination = strtoupper((string) ($context['destination'] ?? ''));
        $date = (string) ($context['date'] ?? now()->toDateString());
        $returnDate = $context['returnDate'] ?? null;
        $tripType = (string) ($context['tripType'] ?? 'one_way');
        $currencyFallback = (string) ($context['currency'] ?? 'USD');

        $diagnostics = [
            'provider' => 'sabre_bfm',
            'success' => false,
            'errors' => [],
            'warnings' => [],
            'response_keys' => array_keys($response),
        ];

        $messages = $this->collectMessages($response);
        foreach ($messages as $message) {
            $severity = strtolower((string) ($message['severity'] ?? 'info'));
            $text = trim((string) ($message['text'] ?? ''));
            $code = trim((string) ($message['code'] ?? ''));
            $formatted = $code !== '' ? $text . ' (' . $code . ')' : $text;

            if ($text === '') {
                continue;
            }

            if ($severity === 'error') {
                $diagnostics['errors'][] = $formatted;
            } else {
                $diagnostics['warnings'][] = $formatted;
            }
        }

        $offers = $this->extractOffers($response);
        if (empty($offers)) {
            $diagnostics['errors'][] = 'No Sabre itineraries were returned.';

            return [
                'flights' => [],
                'diagnostics' => $diagnostics,
            ];
        }

        $flights = [];

        foreach ($offers as $index => $offer) {
            $legs = $this->extractLegs($offer);
            if (empty($legs)) {
                continue;
            }

            $outboundLeg = $legs[0];
            $inboundLeg = $legs[1] ?? null;

            $outboundFirst = $outboundLeg['segments'][0] ?? null;
            $outboundLast = $outboundLeg['segments'][count($outboundLeg['segments']) - 1] ?? null;

            if (!$outboundFirst || !$outboundLast) {
                continue;
            }

            $price = (float) $this->resolveTotalPrice($offer);
            $currency = $this->resolveCurrency($offer, $currencyFallback);

            $flightId = 'sabre_' . md5(json_encode([
                $index,
                $origin,
                $destination,
                $date,
                $returnDate,
                $tripType,
                $outboundFirst['departure_time'] ?? null,
                $outboundLast['arrival_time'] ?? null,
                $price,
                $currency,
            ]));

            $flight = [
                'id' => $flightId,
                'airline' => $outboundFirst['airline'] ?: $outboundFirst['airline_code'] ?: 'Unknown',
                'airline_code' => $outboundFirst['airline_code'] ?: 'XX',
                'flight_number' => $outboundFirst['flight_number'] ?: 'Unknown',
                'price' => $price,
                'crossed_price' => round($price * 1.08, 2),
                'currency' => $currency,
                'refundable' => true,
                'points' => 25,
                'outbound' => [
                    'origin' => $outboundFirst['origin'] ?: $origin,
                    'destination' => $outboundLast['destination'] ?: $destination,
                    'departure_time' => $outboundFirst['departure_time'] ?? null,
                    'arrival_time' => $outboundLast['arrival_time'] ?? null,
                    'duration' => $outboundLeg['duration'] ?? '',
                    'stops' => max(0, count($outboundLeg['segments']) - 1),
                    'layover' => null,
                ],
                'inbound' => $inboundLeg ? [
                    'origin' => $inboundLeg['segments'][0]['origin'] ?? strtoupper((string) ($context['destination'] ?? $destination)),
                    'destination' => $inboundLeg['segments'][count($inboundLeg['segments']) - 1]['destination'] ?? strtoupper((string) ($context['origin'] ?? $origin)),
                    'departure_time' => $inboundLeg['segments'][0]['departure_time'] ?? null,
                    'arrival_time' => $inboundLeg['segments'][count($inboundLeg['segments']) - 1]['arrival_time'] ?? null,
                    'duration' => $inboundLeg['duration'] ?? '',
                    'stops' => max(0, count($inboundLeg['segments']) - 1),
                    'layover' => null,
                ] : null,
                'source_offer' => $offer,
            ];

            $flights[] = $flight;
        }

        $diagnostics['success'] = !empty($flights);
        if (!$diagnostics['success'] && empty($diagnostics['errors'])) {
            $diagnostics['errors'][] = 'Sabre returned content, but no itineraries could be normalized.';
        }

        return [
            'flights' => collect($flights)->sortBy('price')->values()->toArray(),
            'diagnostics' => $diagnostics,
        ];
    }

    protected function collectMessages(array $response): array
    {
        $candidates = [
            data_get($response, 'groupedItineraryResponse.messages'),
            data_get($response, 'messages'),
            data_get($response, 'OTA_AirLowFareSearchRS.messages'),
        ];

        foreach ($candidates as $candidate) {
            if (is_array($candidate) && !empty($candidate)) {
                return $this->normalizeList($candidate);
            }
        }

        return [];
    }

    protected function extractOffers(array $response): array
    {
        $paths = [
            'groupedItineraryResponse.itineraryGroups',
            'groupedItineraryResponse.itineraryGroups.itineraries',
            'groupedItineraryResponse.itineraryGroups.itinerary',
            'groupedItineraryResponse.itineraryGroups.itineraryGroup',
            'groupedItineraryResponse.itineraryGroups.fares',
            'groupedItineraryResponse.itineraryGroups.legDescriptions',
            'groupedItineraryResponse.itineraryGroups.groupDescription',
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

    protected function resolveCurrency(array $offer, string $fallback): string
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

        return strtoupper($fallback);
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
}