<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class PortalTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Test Customer User
        User::firstOrCreate(
            ['email' => 'customer@example.com'],
            [
                'name' => 'John Customer',
                'phone' => '+1234567890',
                'password' => bcrypt('password'),
                'role' => 'customer',
                'email_verified_at' => now(),
            ]
        );

        // Test Admin User
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Alice Admin',
                'phone' => '+1987654321',
                'password' => bcrypt('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        // Test Support Agent
        User::firstOrCreate(
            ['email' => 'support@example.com'],
            [
                'name' => 'Bob Support',
                'phone' => '+1111111111',
                'password' => bcrypt('password'),
                'role' => 'support_agent',
                'email_verified_at' => now(),
            ]
        );

        // Test Manager
        User::firstOrCreate(
            ['email' => 'manager@example.com'],
            [
                'name' => 'Carol Manager',
                'phone' => '+2222222222',
                'password' => bcrypt('password'),
                'role' => 'manager',
                'email_verified_at' => now(),
            ]
        );

        // Test Ticketing Officer
        User::firstOrCreate(
            ['email' => 'ticketing@example.com'],
            [
                'name' => 'David Ticketing',
                'phone' => '+3333333333',
                'password' => bcrypt('password'),
                'role' => 'ticketing_officer',
                'email_verified_at' => now(),
            ]
        );

        // Test Accounts Officer
        User::firstOrCreate(
            ['email' => 'accounts@example.com'],
            [
                'name' => 'Eve Accounts',
                'phone' => '+4444444444',
                'password' => bcrypt('password'),
                'role' => 'accounts_officer',
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('Portal test users created successfully!');
        $this->command->table(
            ['Email', 'Name', 'Role', 'Password'],
            [
                ['customer@example.com', 'John Customer', 'customer', 'password'],
                ['admin@example.com', 'Alice Admin', 'admin', 'password'],
                ['support@example.com', 'Bob Support', 'support_agent', 'password'],
                ['manager@example.com', 'Carol Manager', 'manager', 'password'],
                ['ticketing@example.com', 'David Ticketing', 'ticketing_officer', 'password'],
                ['accounts@example.com', 'Eve Accounts', 'accounts_officer', 'password'],
            ]
        );
    }
}
