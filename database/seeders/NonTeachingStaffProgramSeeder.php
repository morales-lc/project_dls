<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Program;

class NonTeachingStaffProgramSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create the "Non-Teaching Staff" program without any courses.
        // Use firstOrCreate to keep this idempotent if seeder runs multiple times.
        Program::firstOrCreate([
            'name' => 'Non-Teaching Staff',
        ]);
    }
}
