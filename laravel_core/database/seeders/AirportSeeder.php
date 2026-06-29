<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use App\Models\Airport;
use App\Helpers\CountryMapping;

class AirportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Truncating existing airports...');
        Airport::truncate();

        $this->command->info('Fetching airports data...');
        $response = Http::withoutVerifying()->get('https://raw.githubusercontent.com/mwgg/Airports/master/airports.json');

        if ($response->successful()) {
            $airports = $response->json();
            $data = [];

            foreach ($airports as $airport) {
                $iata = strtoupper($airport['iata'] ?? '');
                if (!empty($iata) && strlen($iata) === 3) {
                    $countryCode = strtoupper($airport['country'] ?? '');
                    $countryName = CountryMapping::getName($countryCode);

                    $data[] = [
                        'iata_code' => $iata,
                        'icao_code' => !empty($airport['icao']) ? strtoupper($airport['icao']) : null,
                        'name' => $airport['name'] ?? 'Unknown Airport',
                        'city' => $airport['city'] ?? null,
                        'country' => $countryCode,
                        'country_name' => $countryName,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            $this->command->info('Inserting ' . count($data) . ' airports...');
            $chunks = array_chunk($data, 500);
            foreach ($chunks as $chunk) {
                Airport::insertOrIgnore($chunk);
            }
            $this->command->info('Airports seeded successfully.');
        } else {
            $this->command->error('Failed to fetch airports data.');
        }
    }
}
