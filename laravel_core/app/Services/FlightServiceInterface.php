<?php

namespace App\Services;

interface FlightServiceInterface
{
    /**
     * Search for flights based on given criteria.
     *
     * @param string $origin
     * @param string $destination
     * @param string $date (YYYY-MM-DD)
     * @param int $passengers
     * @return array
     */
    public function search(
        string $origin,
        string $destination,
        string $date,
        int $passengers,
        string $tripType = 'one_way',
        ?string $returnDate = null,
        string $cabinClass = 'economy'
    ): array;

    /**
     * Retrieve the exact price and availability for a chosen flight.
     *
     * @param string $flightId
     * @return array
     */
    public function price(string $flightId): array;

    /**
     * Book the flight with passenger details.
     *
     * @param string $flightId
     * @param array $passengerDetails
     * @return array
     */
    public function book(string $flightId, array $passengerDetails): array;
}
