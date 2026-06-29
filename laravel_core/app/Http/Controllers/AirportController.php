<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Airport;
use App\Helpers\CountryMapping;
use Illuminate\Support\Facades\Log;

class AirportController extends Controller
{
    /**
     * Powerful airport search endpoint.
     * Supports: IATA code, ICAO code, city name, airport name,
     * country name, country code, and multi-word queries.
     */
    public function search(Request $request)
    {
        $query = trim((string) $request->input('q'));

        if (empty($query) || strlen($query) < 1) {
            return response()->json([]);
        }

        $upperQuery = strtoupper($query);
        $lowerQuery = mb_strtolower($query);
        $likeQuery = '%' . $query . '%';
        $prefixQuery = $query . '%';
        $lowerLike = '%' . $lowerQuery . '%';
        $lowerPrefix = $lowerQuery . '%';

        // Resolve country codes from country name search
        $countryCodes = CountryMapping::searchCodes($query);

        try {
            $builder = Airport::query();

            $builder->where(function ($q) use (
                $upperQuery, $lowerQuery, $likeQuery, $prefixQuery,
                $lowerLike, $lowerPrefix, $countryCodes, $query
            ) {
                // 1. IATA code exact match (3 chars)
                if (strlen($upperQuery) <= 4) {
                    $q->orWhere('iata_code', $upperQuery);
                }

                // 2. IATA code starts with query
                $q->orWhere('iata_code', 'like', $upperQuery . '%');

                // 3. ICAO code exact/prefix match
                if (strlen($upperQuery) <= 4) {
                    $q->orWhere('icao_code', $upperQuery);
                }
                $q->orWhere('icao_code', 'like', $upperQuery . '%');

                // 4. City name prefix match (case-insensitive via lower)
                $q->orWhereRaw('LOWER(city) LIKE ?', [$lowerPrefix]);

                // 5. City name contains query
                $q->orWhereRaw('LOWER(city) LIKE ?', [$lowerLike]);

                // 6. Airport name contains query
                $q->orWhereRaw('LOWER(name) LIKE ?', [$lowerLike]);

                // 7. Country name prefix match
                $q->orWhereRaw('LOWER(country_name) LIKE ?', [$lowerPrefix]);

                // 8. Country name contains query
                $q->orWhereRaw('LOWER(country_name) LIKE ?', [$lowerLike]);

                // 9. Country code exact match
                $q->orWhere('country', $upperQuery);

                // 10. Country codes resolved from country name mapping
                if (!empty($countryCodes)) {
                    $q->orWhereIn('country', $countryCodes);
                }

                // 11. Multi-word search: split query and match each word in city/name/country
                $words = array_filter(explode(' ', $lowerQuery));
                if (count($words) > 1) {
                    foreach ($words as $word) {
                        if (strlen($word) >= 2) {
                            $q->orWhere(function ($wordQ) use ($word) {
                                $wordPattern = '%' . $word . '%';
                                $wordQ->whereRaw('LOWER(city) LIKE ?', [$wordPattern])
                                      ->orWhereRaw('LOWER(name) LIKE ?', [$wordPattern])
                                      ->orWhereRaw('LOWER(country_name) LIKE ?', [$wordPattern]);
                            });
                        }
                    }
                }
            });

            // Smart ranking: assign priority score based on match quality
            $orderSql = "
                CASE
                    WHEN iata_code = ? THEN 1
                    WHEN icao_code = ? THEN 2
                    WHEN LOWER(city) = ? THEN 3
                    WHEN LOWER(name) = ? THEN 4
                    WHEN iata_code LIKE ? THEN 5
                    WHEN icao_code LIKE ? THEN 6
                    WHEN LOWER(city) LIKE ? THEN 7
                    WHEN LOWER(country_name) = ? THEN 8
                    WHEN country = ? THEN 9
                    WHEN LOWER(name) LIKE ? THEN 10
                    WHEN LOWER(country_name) LIKE ? THEN 11
                    ELSE 12
                END
            ";

            $airports = $builder
                ->orderByRaw($orderSql, [
                    $upperQuery,
                    $upperQuery,
                    $lowerQuery,
                    $lowerQuery,
                    $upperQuery . '%',
                    $upperQuery . '%',
                    $lowerPrefix,
                    $lowerQuery,
                    $upperQuery,
                    $lowerLike,
                    $lowerPrefix,
                ])
                ->orderBy('city', 'asc')
                ->orderBy('name', 'asc')
                ->limit(20)
                ->get();

            // Remove duplicates by IATA code (keep highest ranked = first occurrence)
            $seen = [];
            $results = [];

            foreach ($airports as $airport) {
                if (isset($seen[$airport->iata_code])) {
                    continue;
                }
                $seen[$airport->iata_code] = true;

                $displayName = $this->buildDisplayName($airport);

                $results[] = [
                    'code'          => $airport->iata_code,
                    'icao'          => $airport->icao_code,
                    'name'          => $airport->name,
                    'city'          => $airport->city,
                    'country'       => $airport->country,
                    'country_name'  => $airport->country_name,
                    'display_name'  => $displayName,
                    'subtitle'      => $this->buildSubtitle($airport),
                ];
            }

            return response()->json($results);
        } catch (\Throwable $e) {
            Log::error('Airport search DB error: ' . $e->getMessage());
            return response()->json([]);
        }
    }

    /**
     * Build a rich display name for an airport.
     */
    private function buildDisplayName(Airport $airport): string
    {
        $parts = [];

        if ($airport->city) {
            $parts[] = $airport->city;
        }

        $parts[] = '(' . $airport->iata_code . ')';

        if ($airport->name) {
            $parts[] = '- ' . $airport->name;
        }

        return implode(' ', $parts);
    }

    /**
     * Build a subtitle string with country info.
     */
    private function buildSubtitle(Airport $airport): string
    {
        $parts = [];

        if ($airport->country_name) {
            $parts[] = $airport->country_name;
        } elseif ($airport->country) {
            $parts[] = $airport->country;
        }

        if ($airport->icao_code) {
            $parts[] = 'ICAO: ' . $airport->icao_code;
        }

        return implode(' · ', $parts);
    }
}
