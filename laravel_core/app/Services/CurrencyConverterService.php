<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class CurrencyConverterService
{
    protected string $apiUrl = 'https://api.exchangerate-api.com/v4/latest';

    /**
     * Convert currency amount to BDT using live exchange rates
     */
    public function convertToBDT(float $amount, string $fromCurrency = 'USD'): array
    {
        if ($fromCurrency === 'BDT') {
            return [
                'original_amount' => $amount,
                'original_currency' => $fromCurrency,
                'bdt_amount' => $amount,
                'exchange_rate' => 1,
            ];
        }

        $rate = $this->getExchangeRate($fromCurrency, 'BDT');

        return [
            'original_amount' => $amount,
            'original_currency' => $fromCurrency,
            'bdt_amount' => round($amount * $rate, 2),
            'exchange_rate' => round($rate, 2),
        ];
    }

    /**
     * Get exchange rate between two currencies
     */
    public function getExchangeRate(string $from, string $to): float
    {
        $cacheKey = 'exchange_rate_' . $from . '_' . $to;

        // Cache rates for 30 minutes (not 1 hour - need fresher rates)
        return Cache::remember($cacheKey, 1800, function () use ($from, $to) {
            try {
                // Try primary API
                $response = Http::timeout(10)->get($this->apiUrl . '/' . $from);

                if ($response->successful()) {
                    $data = $response->json();
                    if (isset($data['rates'][$to])) {
                        return (float) $data['rates'][$to];
                    }
                }
            } catch (\Exception $e) {
                \Log::warning('Currency conversion API error: ' . $e->getMessage());
            }

            // Fallback to alternative API
            try {
                $response = Http::timeout(10)->get('https://v6.exchangerate-api.com/v6/latest/' . $from);
                if ($response->successful()) {
                    $data = $response->json();
                    if (isset($data['conversion_rates'][$to])) {
                        return (float) $data['conversion_rates'][$to];
                    }
                }
            } catch (\Exception $e) {
                \Log::warning('Fallback currency API error: ' . $e->getMessage());
            }

            // Final fallback with better rates
            return $this->getFallbackRate($from, $to);
        });
    }

    /**
     * Improved fallback exchange rates (more accurate)
     */
    protected function getFallbackRate(string $from, string $to): float
    {
        // As of May 2026, approximate rates
        $rates = [
            'USD_BDT' => 108.50,
            'EUR_BDT' => 144.50,  // Updated from 117.50
            'GBP_BDT' => 137.50,
            'AUD_BDT' => 71.50,
            'CAD_BDT' => 79.50,
            'JPY_BDT' => 0.73,
            'INR_BDT' => 1.30,
        ];

        $key = $from . '_' . $to;
        return $rates[$key] ?? 1;
    }
}
