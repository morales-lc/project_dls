<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class GuestUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create or update a guest user with known credentials
        User::updateOrCreate(
            [ 'email' => 'guest@example.com' ],
            [
                'name' => 'Guest User',
                'username' => 'guest',
                'contact_number' => null,
                'address' => null,
                'role' => 'guest',
                // Either rely on 'hashed' cast or explicitly hash:
                'password' => Hash::make('guest12345'),
            ]
        );
    }

    
}
