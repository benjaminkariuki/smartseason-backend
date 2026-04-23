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
        // Loop through our 3 env-defined agents
        for ($i = 1; $i <= 3; $i++) {
            $email = env("AGENT_{$i}_EMAIL");
            
            // Only create the user if the env variable actually exists
            if ($email) {
                User::updateOrCreate(
                    ['email' => $email], // Look for an existing user with this email
                    [
                        'name' => env("AGENT_{$i}_NAME"),
                        'password' => Hash::make(env("AGENT_{$i}_PASSWORD")),
                        'role' => 'field_agent',
                        'is_active' => true, // Assuming you have this from your AuthController logic
                    ]
                );
            }
        }

       
    }
}