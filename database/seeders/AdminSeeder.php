<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $adminEmail = env('ADMIN_EMAIL');
        if (!$adminEmail) {
            throw new \Exception('ADMIN_EMAIL environment variable is required');
        }

        $adminPassword = env('ADMIN_PASSWORD');
        if (!$adminPassword) {
            throw new \Exception('ADMIN_PASSWORD environment variable is required');
        }

        $adminRole = env('ADMIN_ROLE');
        if (!$adminRole) {
            throw new \Exception('ADMIN_ROLE environment variable is required');
        }

        User::updateOrCreate(
            ['email' => $adminEmail], // Ensures we don't duplicate if run twice
            [
                'name' => env('ADMIN_NAME', 'System Administrator'),
                'password' => Hash::make($adminPassword),
                'role' => $adminRole,
            ]
        );
    }
}