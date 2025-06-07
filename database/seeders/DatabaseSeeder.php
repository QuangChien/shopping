<?php

namespace Database\Seeders;

use App\Models\Customer;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed admin users
        $this->call([
            UserSeeder::class,
        ]);

        // Create test customers
        Customer::factory(5)->create();

        Customer::factory()->create([
            'first_name' => 'Test',
            'last_name' => 'Customer',
            'email' => 'customer@example.com',
        ]);

        // Run ecommerce data seeder
        $this->call([
            EcommerceSeeder::class,
        ]);
    }
}
