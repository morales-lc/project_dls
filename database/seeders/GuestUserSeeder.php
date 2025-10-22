<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;

class GuestUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create or update a guest user with known credentials
        $email = config('services.alinet.guest_email', 'guest@example.com');
        $password = config('services.alinet.guest_password', 'guest12345');

        User::updateOrCreate(
            [ 'email' => $email ],
            [
                'name' => 'Guest User',
                'username' => 'guest',
                'contact_number' => null,
                'address' => null,
                'role' => 'guest',
                // Either rely on 'hashed' cast or explicitly hash:
                'password' => Hash::make($password),
                // Store encrypted plaintext for operational emails
                'guest_plain_password' => Crypt::encryptString($password),
            ]
        );
    }

    
}
