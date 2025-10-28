<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Ensure baseline reference data exists
        $this->call([
            \Database\Seeders\ProgramSeeder::class,
            \Database\Seeders\NonTeachingStaffProgramSeeder::class,
            \Database\Seeders\MidesCategorySeeder::class,
            \Database\Seeders\MidesDocumentSeeder::class,
            \Database\Seeders\MidesGraduateChildhoodEducationSeeder::class,
            \Database\Seeders\ResourceViewsSeeder::class,
            \Database\Seeders\GuestUserSeeder::class,
        ]);
    }
}
