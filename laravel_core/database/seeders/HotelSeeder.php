<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Property;
use App\Models\PropertyPhoto;
use App\Models\RoomType;
use App\Models\RoomTypePhoto;
use App\Models\RatePlan;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class HotelSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure we have a property owner
        $owner = User::firstOrCreate(
            ['email' => 'owner@ghuri.travel'],
            [
                'name' => 'John Property Owner',
                'password' => bcrypt('password'),
                'role' => 'property_owner',
            ]
        );

        // Properties data
        $properties = [
            [
                'name' => 'The Grand Azure Resort & Spa',
                'type' => 'resort',
                'stars' => 5,
                'short_description' => 'Luxury beachfront resort with world-class spa and infinity pools.',
                'full_description' => 'Experience ultimate luxury at The Grand Azure Resort & Spa. Located on a pristine private beach, our 5-star resort offers breathtaking ocean views, award-winning dining, and a tranquil spa designed to rejuvenate your body and soul. Perfect for romantic getaways and unforgettable family vacations.',
                'check_in_time' => '15:00',
                'check_out_time' => '11:00',
                'address_line_1' => '123 Ocean View Drive',
                'city' => 'Bali',
                'country' => 'Indonesia',
                'amenities' => ['wifi_free', 'pool', 'spa', 'restaurant', 'bar', 'room_service', 'beach_access'],
                'cancellation_policy' => ['type' => 'free', 'free_cancel_days' => 3],
                'status' => 'approved',
                'owner_id' => $owner->id,
            ],
            [
                'name' => 'Metropolis Skyline Hotel',
                'type' => 'hotel',
                'stars' => 4,
                'short_description' => 'Modern business hotel in the heart of the financial district.',
                'full_description' => 'Metropolis Skyline Hotel caters to the modern business traveler and city explorer. Enjoy panoramic views of the city skyline, state-of-the-art conference facilities, and our rooftop lounge. Conveniently located near major subway lines and top attractions.',
                'check_in_time' => '14:00',
                'check_out_time' => '12:00',
                'address_line_1' => '456 Business Ave',
                'city' => 'New York',
                'country' => 'United States',
                'city_center_distance' => '0.5 km',
                'amenities' => ['wifi_free', 'gym', 'business_center', 'restaurant', 'front_desk_24h'],
                'cancellation_policy' => ['type' => 'partial', 'free_cancel_days' => 1],
                'status' => 'approved',
                'owner_id' => $owner->id,
            ],
        ];

        foreach ($properties as $propData) {
            $amenities = $propData['amenities'];
            $cancellation = $propData['cancellation_policy'];
            unset($propData['amenities'], $propData['cancellation_policy']);

            $property = Property::create(array_merge($propData, [
                'amenities' => $amenities,
                'cancellation_policy' => $cancellation,
            ]));

            // Room Types
            if ($property->stars === 5) {
                $this->createRoom($property, 'Oceanfront Suite', 65, 2, 1, 350.00, 10, ['king']);
                $this->createRoom($property, 'Deluxe Beach Villa', 120, 4, 2, 850.00, 5, ['king', 'queen']);
            } else {
                $this->createRoom($property, 'Standard City View', 28, 2, 0, 150.00, 50, ['queen']);
                $this->createRoom($property, 'Executive Skyline Room', 35, 2, 1, 220.00, 20, ['king']);
            }
        }
    }

    private function createRoom(Property $property, string $name, int $size, int $adults, int $children, float $price, int $inventory, array $beds)
    {
        $bedConfig = array_map(fn($b) => ['type' => $b, 'count' => 1], $beds);

        $room = RoomType::create([
            'property_id' => $property->id,
            'name' => $name,
            'size_sqm' => $size,
            'max_adults' => $adults,
            'max_children' => $children,
            'max_infants' => 0,
            'bed_config' => $bedConfig,
            'base_price_per_night' => $price,
            'inventory_count' => $inventory,
            'status' => 'active',
        ]);

        $room->ratePlans()->create([
            'plan_code' => 'RO',
            'plan_name' => 'Room Only',
            'price_supplement_per_adult' => 0,
            'is_active' => true,
        ]);

        $room->ratePlans()->create([
            'plan_code' => 'BB',
            'plan_name' => 'Bed & Breakfast',
            'price_supplement_per_adult' => 25.00,
            'is_active' => true,
        ]);
    }
}
