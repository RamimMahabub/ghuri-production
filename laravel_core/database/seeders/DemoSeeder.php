<?php

namespace Database\Seeders;

use App\Models\Property;
use App\Models\RoomType;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * DemoSeeder — seeds all demo accounts for Vercel/production preview.
 *
 * All accounts are pre-verified (email_verified_at = now()) so visitors
 * can log in immediately without email verification.
 *
 * Run with: php artisan db:seed --class=DemoSeeder
 */
class DemoSeeder extends Seeder
{
    public function run(): void
    {
        // ── 1. ADMIN ────────────────────────────────────────────────
        User::firstOrCreate(
            ['email' => 'admin@ghuri.travel'],
            [
                'name'              => 'Admin User',
                'phone'             => '+8801700000001',
                'password'          => bcrypt('Admin@1234'),
                'role'              => 'admin',
                'email_verified_at' => now(),
            ]
        );

        // ── 2. MANAGER ──────────────────────────────────────────────
        User::firstOrCreate(
            ['email' => 'manager@ghuri.travel'],
            [
                'name'              => 'Manager User',
                'phone'             => '+8801700000002',
                'password'          => bcrypt('Manager@1234'),
                'role'              => 'manager',
                'email_verified_at' => now(),
            ]
        );

        // ── 3. SUPPORT AGENT ────────────────────────────────────────
        User::firstOrCreate(
            ['email' => 'support@ghuri.travel'],
            [
                'name'              => 'Support Agent',
                'phone'             => '+8801700000003',
                'password'          => bcrypt('Support@1234'),
                'role'              => 'support_agent',
                'email_verified_at' => now(),
            ]
        );

        // ── 4. TICKETING OFFICER ─────────────────────────────────────
        User::firstOrCreate(
            ['email' => 'ticketing@ghuri.travel'],
            [
                'name'              => 'Ticketing Officer',
                'phone'             => '+8801700000004',
                'password'          => bcrypt('Ticket@1234'),
                'role'              => 'ticketing_officer',
                'email_verified_at' => now(),
            ]
        );

        // ── 5. ACCOUNTS OFFICER ──────────────────────────────────────
        User::firstOrCreate(
            ['email' => 'accounts@ghuri.travel'],
            [
                'name'              => 'Accounts Officer',
                'phone'             => '+8801700000005',
                'password'          => bcrypt('Accounts@1234'),
                'role'              => 'accounts_officer',
                'email_verified_at' => now(),
            ]
        );

        // ── 6. PROPERTY OWNER (with 2 demo hotels) ──────────────────
        $owner = User::firstOrCreate(
            ['email' => 'owner@ghuri.travel'],
            [
                'name'              => 'Property Owner',
                'phone'             => '+8801700000006',
                'password'          => bcrypt('Owner@1234'),
                'role'              => 'property_owner',
                'email_verified_at' => now(),
            ]
        );

        $this->seedDemoProperties($owner);

        // ── 7. CUSTOMER ──────────────────────────────────────────────
        User::firstOrCreate(
            ['email' => 'customer@ghuri.travel'],
            [
                'name'              => 'Demo Customer',
                'phone'             => '+8801700000007',
                'password'          => bcrypt('Customer@1234'),
                'role'              => 'customer',
                'email_verified_at' => now(),
            ]
        );

        // ── Output table ─────────────────────────────────────────────
        $this->command->newLine();
        $this->command->info('✅  GHURI Demo accounts seeded successfully!');
        $this->command->newLine();
        $this->command->table(
            ['Role', 'Email', 'Password'],
            [
                ['Admin',             'admin@ghuri.travel',      'Admin@1234'],
                ['Manager',           'manager@ghuri.travel',    'Manager@1234'],
                ['Support Agent',     'support@ghuri.travel',    'Support@1234'],
                ['Ticketing Officer', 'ticketing@ghuri.travel',  'Ticket@1234'],
                ['Accounts Officer',  'accounts@ghuri.travel',   'Accounts@1234'],
                ['Property Owner',    'owner@ghuri.travel',      'Owner@1234'],
                ['Customer',          'customer@ghuri.travel',   'Customer@1234'],
            ]
        );
        $this->command->newLine();
        $this->command->warn('⚠  All accounts are pre-verified. No email needed.');
        $this->command->newLine();
    }

    // ─────────────────────────────────────────────────────────────────
    // Helper: seed 2 demo hotels for the property owner
    // ─────────────────────────────────────────────────────────────────
    private function seedDemoProperties(User $owner): void
    {
        // Only seed if this owner has no properties yet
        if ($owner->properties()->exists()) {
            return;
        }

        $properties = [
            [
                'name'              => 'GHURI Grand Hotel Dhaka',
                'type'              => 'hotel',
                'stars'             => 5,
                'short_description' => 'A premium 5-star hotel in the heart of Dhaka.',
                'full_description'  => 'GHURI Grand Hotel Dhaka offers world-class hospitality in the heart of Bangladesh\'s capital. Enjoy luxurious rooms, fine dining, rooftop pool, and state-of-the-art business facilities. Steps away from major attractions.',
                'check_in_time'     => '14:00',
                'check_out_time'    => '12:00',
                'address_line_1'    => '1 Gulshan Avenue',
                'city'              => 'Dhaka',
                'country'           => 'Bangladesh',
                'amenities'         => ['wifi_free', 'pool', 'gym', 'restaurant', 'bar', 'room_service', 'front_desk_24h', 'parking'],
                'cancellation_policy' => ['type' => 'free', 'free_cancel_days' => 2],
                'status'            => 'approved',
                'rooms'             => [
                    ['name' => 'Deluxe Room',        'size' => 32, 'adults' => 2, 'children' => 1, 'price' => 120.00, 'qty' => 20],
                    ['name' => 'Executive Suite',    'size' => 55, 'adults' => 2, 'children' => 1, 'price' => 220.00, 'qty' => 10],
                    ['name' => 'Presidential Suite', 'size' => 120,'adults' => 4, 'children' => 2, 'price' => 500.00, 'qty' => 3],
                ],
            ],
            [
                'name'              => 'GHURI Cox\'s Bazar Beach Resort',
                'type'              => 'resort',
                'stars'             => 4,
                'short_description' => 'Beachfront resort on the world\'s longest natural sea beach.',
                'full_description'  => 'Wake up to the sound of waves at GHURI Cox\'s Bazar Beach Resort. Our resort sits directly on the pristine shore of Cox\'s Bazar, the world\'s longest natural sea beach. Enjoy sunset views, seafood dining, and total relaxation.',
                'check_in_time'     => '15:00',
                'check_out_time'    => '11:00',
                'address_line_1'    => 'Beach Road, Kolatoli',
                'city'              => "Cox's Bazar",
                'country'           => 'Bangladesh',
                'amenities'         => ['wifi_free', 'pool', 'beach_access', 'restaurant', 'room_service', 'spa'],
                'cancellation_policy' => ['type' => 'free', 'free_cancel_days' => 3],
                'status'            => 'approved',
                'rooms'             => [
                    ['name' => 'Sea View Room',    'size' => 28, 'adults' => 2, 'children' => 1, 'price' => 90.00,  'qty' => 30],
                    ['name' => 'Beachfront Villa', 'size' => 80, 'adults' => 4, 'children' => 2, 'price' => 280.00, 'qty' => 8],
                ],
            ],
        ];

        foreach ($properties as $propData) {
            $rooms = $propData['rooms'];
            unset($propData['rooms']);

            $property = Property::create(array_merge($propData, [
                'owner_id' => $owner->id,
            ]));

            foreach ($rooms as $roomData) {
                $room = RoomType::create([
                    'property_id'          => $property->id,
                    'name'                 => $roomData['name'],
                    'size_sqm'             => $roomData['size'],
                    'max_adults'           => $roomData['adults'],
                    'max_children'         => $roomData['children'],
                    'max_infants'          => 0,
                    'bed_config'           => [['type' => 'queen', 'count' => 1]],
                    'base_price_per_night' => $roomData['price'],
                    'inventory_count'      => $roomData['qty'],
                    'status'               => 'active',
                ]);

                $room->ratePlans()->create([
                    'plan_code'                   => 'RO',
                    'plan_name'                   => 'Room Only',
                    'price_supplement_per_adult'  => 0,
                    'is_active'                   => true,
                ]);

                $room->ratePlans()->create([
                    'plan_code'                   => 'BB',
                    'plan_name'                   => 'Bed & Breakfast',
                    'price_supplement_per_adult'  => 15.00,
                    'is_active'                   => true,
                ]);
            }
        }
    }
}
