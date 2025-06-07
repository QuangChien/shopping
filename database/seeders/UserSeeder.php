<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Super Admin
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Super Admin',
                'email' => 'admin@example.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password'), // Mật khẩu mặc định: password
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Create Manager
        User::firstOrCreate(
            ['email' => 'manager@example.com'],
            [
                'name' => 'Manager User',
                'email' => 'manager@example.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Creat Staff
        User::firstOrCreate(
            ['email' => 'staff@example.com'],
            [
                'name' => 'Staff User',
                'email' => 'staff@example.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Create Editor
        User::firstOrCreate(
            ['email' => 'editor@example.com'],
            [
                'name' => 'Content Editor',
                'email' => 'editor@example.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Create Viewer
        User::firstOrCreate(
            ['email' => 'viewer@example.com'],
            [
                'name' => 'Viewer User',
                'email' => 'viewer@example.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Generate some random admin users using factory
        User::factory(3)->create([
            'is_active' => true,
        ]);

        // Print created admin information
        $this->command->info('✅ Admin users created successfully!');
        $this->command->info('Default admin accounts:');
        $this->command->table(
            ['Name', 'Email', 'Password'],
            [
                ['Super Admin', 'admin@example.com', 'password'],
                ['Manager User', 'manager@example.com', 'password'],
                ['Staff User', 'staff@example.com', 'password'],
                ['Content Editor', 'editor@example.com', 'password'],
                ['Viewer User', 'viewer@example.com', 'password'],
            ]
        );
    }
}
