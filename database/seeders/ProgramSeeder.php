<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Program;
use App\Models\Course;

class ProgramSeeder extends Seeder
{
    public function run(): void
    {
        $programs = [
            'Accountancy, Accounting Information System, and Information Technology' => [
                'Bachelor of Science in Accountancy & Accounting Information System',
                'Bachelor of Science in Information Technology',
                'Bachelor of Science in Information System',
            ],
            'Allied Health Program' => [
                'Bachelor of Science in Nursing',
                'Bachelor of Science in Nutrition and Dietetics',
                'Bachelor of Science in Pharmacy',
            ],
            'Arts and Sciences Program' => [
                'Bachelor of Arts in Communication',
                'Bachelor of Arts in English Language',
                'Bachelor of Library and Information Science',
                'Bachelor of Music',
                'Bachelor of Science in Psychology',
            ],
            'Business Education Program' => [
                'Bachelor of Science in Business Administration',
            ],
            'Hospitality Management Program' => [
                'Bachelor of Science in Hospitality Management',
                'Bachelor of Science in Tourism Management',
            ],
            'Social Work Program' => [
                'Bachelor of Science in Social Work',
            ],
            'Teacher Education Program' => [
                'Bachelor of Culture and Arts Education',
                'Bachelor of Early Childhood Education',
                'Bachelor of Elementary Education',
                'Bachelor of Technology and Livelihood Education​',
                'Bachelor of Secondary Education',
            ],
            'Graduate School' => [
                'Master of Arts in Education',
                'Master of Arts in Human Resource Development',
                'Master of Arts in Home Economics',
                'Master of Library and Information Science',
                'Master in Business Education',
                'Master of Science in Social Work',
            ],
            'Senior High School' => [
                'Accountancy, Business and Management (ABM)',
                'Humanities and Social Sciences (HUMSS)',
                'Science and Technology, Engineering, and Mathematics (STEM)',
                'Technical Vocational Livelihood (TVL) - Culinary and ICT',
            ],
            'Junior High School' => [
                // No courses for Junior High School
            ],
        ];

        foreach ($programs as $programName => $courses) {
            $program = Program::create(['name' => $programName]);
            foreach ($courses as $course) {
                Course::create([
                    'program_id' => $program->id,
                    'name' => $course,
                ]);
            }
        }
    }
}
