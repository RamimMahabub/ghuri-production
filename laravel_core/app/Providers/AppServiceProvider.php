<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $provider = strtolower((string) config('services.flight_provider', env('FLIGHT_PROVIDER', 'sabre')));
        $sabreUsername = (string) config('sabre.username');
        $sabrePassword = (string) config('sabre.password');
        $sabrePcc = trim((string) config('sabre.pcc'));

        if ($provider === 'sabre' && ($sabreUsername === '' || $sabrePassword === '' || $sabrePcc === '')) {
            $provider = 'mock';
        }

        $serviceClass = match ($provider) {
            'amadeus' => \App\Services\AmadeusFlightService::class,
            'duffel' => \App\Services\DuffelFlightService::class,
            'mock' => \App\Services\MockFlightService::class,
            'sabre' => \App\Services\SabreFlightService::class,
            default => \App\Services\TrawexFlightService::class,
        };

        $this->app->bind(\App\Services\FlightServiceInterface::class, $serviceClass);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (env('APP_ENV') === 'production') {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }
    }
}
