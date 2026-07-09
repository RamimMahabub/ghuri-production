<?php

namespace App\Helpers;

use App\Services\CurrencyConverterService;

class Currency
{
    /**
     * Format a base USD amount to the user's selected session currency.
     *
     * @param float $amount The amount in base USD (or BDT if specified).
     * @param string $baseCurrency The base currency of the amount.
     * @return string The formatted amount with currency symbol.
     */
    public static function format(float $amount, string $baseCurrency = 'USD'): string
    {
        $userCurrency = session('currency', 'BDT'); // Default is BDT
        
        $currencyConverter = app(CurrencyConverterService::class);
        
        // Convert to target currency
        if ($baseCurrency === $userCurrency) {
            $convertedAmount = $amount;
        } elseif ($userCurrency === 'BDT' && $baseCurrency === 'USD') {
            $convertedAmount = $currencyConverter->convertToBDT($amount, 'USD')['bdt_amount'];
        } elseif ($userCurrency === 'USD' && $baseCurrency === 'BDT') {
            // Need to convert BDT to USD. 
            // Invert the USD->BDT rate.
            $rate = $currencyConverter->getExchangeRate('BDT', 'USD');
            $convertedAmount = round($amount * $rate, 2);
        } else {
            // Fallback for unexpected currency conversions
            $rate = $currencyConverter->getExchangeRate($baseCurrency, $userCurrency);
            $convertedAmount = round($amount * $rate, 2);
        }

        $symbol = $userCurrency === 'USD' ? '$' : '৳';
        
        // Determine precision
        // If it's a clean integer, we might format with 0 decimals, else 2.
        // For standard UI, 2 decimals is safe for USD, 0 or 2 for BDT.
        $decimals = ($userCurrency === 'BDT') ? 0 : 2;

        return $symbol . number_format($convertedAmount, $decimals);
    }
}
