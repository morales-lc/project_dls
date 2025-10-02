<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class AdminLibrarianSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@lccdo.edu.ph'],
            [
                'name' => 'Admin User',
                'username' => 'admin',
                'contact_number' => '09123456789',
                'address' => 'Admin Address',
                'password' => bcrypt('adminpassword'),
                'role' => 'admin',
            ]
        );

        User::updateOrCreate(
            ['email' => 'librarian@lccdo.edu.ph'],
            [
                'name' => 'Librarian User',
                'username' => 'librarian',
                'contact_number' => '09876543210',
                'address' => 'Librarian Address',
                'password' => bcrypt('librarianpassword'),
                'role' => 'librarian',
            ]
        );
    }
}
