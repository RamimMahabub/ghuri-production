<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SearchController extends Controller
{
    protected $flightService;
    protected $currencyConverter;

    public function __construct(
        \App\Services\FlightServiceInterface $flightService,
        \App\Services\CurrencyConverterService $currencyConverter
    ) {
        $this->flightService = $flightService;
        $this->currencyConverter = $currencyConverter;
    }

    public function flights(Request $request)
    {
        $validated = $request->validate([
            'origin' => 'required|string',
            'destination' => 'required|string',
            'date' => 'required|date',
            'passengers' => 'required|integer|min:1',
            'trip_type' => 'nullable|string|in:one_way,round_way,multi_city',
            'return_date' => 'nullable|date|after_or_equal:date|required_if:trip_type,round_way',
            'class' => 'nullable|string|in:economy,premium_economy,business,first',
        ]);

        if (($validated['trip_type'] ?? 'one_way') === 'multi_city') {
            return back()
                ->withInput()
                ->withErrors(['search' => 'Multi-city search needs additional segment inputs and is not enabled in this form yet.']);
        }

        $providerNotice = null;
        $selectedProvider = strtolower((string) config('services.flight_provider', env('FLIGHT_PROVIDER', 'sabre')));
        if ($selectedProvider === 'sabre') {
            $missingSabreFields = [];
            if (trim((string) config('sabre.username')) === '') {
                $missingSabreFields[] = 'SABRE_USERNAME';
            }
            if (trim((string) config('sabre.password')) === '') {
                $missingSabreFields[] = 'SABRE_PASSWORD';
            }
            if (trim((string) config('sabre.pcc')) === '') {
                $missingSabreFields[] = 'SABRE_PCC';
            }

            if (!empty($missingSabreFields)) {
                $providerNotice = 'Sabre is selected, but ' . implode(', ', $missingSabreFields) . ' is missing. Showing fallback mock fares instead of live Sabre results.';
            }
        }

        try {
            $flights = $this->flightService->search(
                strtoupper($validated['origin']),
                strtoupper($validated['destination']),
                $validated['date'],
                (int) $validated['passengers'],
                $validated['trip_type'] ?? 'one_way',
                $validated['return_date'] ?? null,
                $validated['class'] ?? 'economy'
            );

            if (empty($flights) && $selectedProvider !== 'mock') {
                $flights = app(\App\Services\MockFlightService::class)->search(
                    strtoupper($validated['origin']),
                    strtoupper($validated['destination']),
                    $validated['date'],
                    (int) $validated['passengers'],
                    $validated['trip_type'] ?? 'one_way',
                    $validated['return_date'] ?? null,
                    $validated['class'] ?? 'economy'
                );

                $providerNotice = 'Live flight search returned no offers, so demo fares are shown instead.';
            }

            $debugData = null;
            if ($request->boolean('debug') && $this->flightService instanceof \App\Services\SabreFlightService && method_exists($this->flightService, 'lastDiagnostics')) {
                $debugData = $this->flightService->lastDiagnostics();
            }
        } catch (\Throwable $e) {
            Log::error('Flight search failed', [
                'message' => $e->getMessage(),
                'trip_type' => $validated['trip_type'] ?? 'one_way',
                'origin' => $validated['origin'] ?? null,
                'destination' => $validated['destination'] ?? null,
            ]);

            return back()
                ->withInput()
                ->withErrors(['search' => 'Search failed: ' . $e->getMessage()]);
        }

        return view('flights.results', [
            'flights' => $this->addBDTConversion($flights),
            'search' => $validated,
            'providerNotice' => $providerNotice,
            'debugData' => $debugData ?? null,
        ]);
    }

    /**
     * Add BDT conversion to all flight prices
     */
    private function addBDTConversion(array $flights): array
    {
        foreach ($flights as &$flight) {
            $currency = $flight['currency'] ?? 'USD';
            
            // Convert price to BDT
            $conversion = $this->currencyConverter->convertToBDT($flight['price'] ?? 0, $currency);
            $flight['bdt_price'] = $conversion['bdt_amount'];
            $flight['exchange_rate'] = $conversion['exchange_rate'];
            
            // Convert crossed price if exists
            if (!empty($flight['crossed_price'])) {
                $crossed = $this->currencyConverter->convertToBDT($flight['crossed_price'], $currency);
                $flight['crossed_price_bdt'] = $crossed['bdt_amount'];
            }
        }
        
        return $flights;
    }
}
