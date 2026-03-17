<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ResourceView;
use App\Models\Program;
use Carbon\Carbon;

class ResourceViewsSeeder extends Seeder
{
    /**
     * Seed resource_views with realistic analytics data across programs, courses, and time.
     */
    public function run(): void
    {
        $programs = Program::with('courses')->get();
        if ($programs->isEmpty()) {
            $this->command?->warn('No programs found. Run ProgramSeeder first.'); //status
            return;
        }

        // Define date boundaries to cover: last year full + current year YTD
        $now = Carbon::now();
        $startWindow = (clone $now)->subYears(1)->startOfYear(); // Jan 1 last year
        $endWindow = (clone $now)->endOfDay(); // today

        // Helper to pick a random date between two dates
        $randDate = function (Carbon $start, Carbon $end): Carbon {
            $ts = random_int($start->getTimestamp(), $end->getTimestamp());
            return Carbon::createFromTimestamp($ts);
        };

        // Seed data per program
        foreach ($programs as $program) {
            $courseNames = $program->courses->pluck('name')->all();
            if (empty($courseNames)) {
                // Ensure at least one course-like label exists for the seed
                $courseNames = ['General'];
            }

            // Generate a mix of MIDES views and SIDLAK downloads
            $batches = [
                ['document_type' => 'mides', 'action' => 'view', 'count' => random_int(50, 140)],
                ['document_type' => 'sidlak', 'action' => 'download', 'count' => random_int(30, 100)],
            ];

            foreach ($batches as $batch) {
                for ($i = 0; $i < $batch['count']; $i++) {
                    $when = $randDate($startWindow, $endWindow);
                    ResourceView::create([
                        'student_faculty_id' => null, // optional
                        'document_type' => $batch['document_type'],
                        'document_id' => null,
                        'program_id' => $program->id,
                        'course' => $courseNames[array_rand($courseNames)],
                        'role' => (random_int(0, 1) ? 'student' : 'faculty'),
                        'action' => $batch['action'],
                        'created_at' => $when,
                        'updated_at' => $when,
                    ]);
                }
            }

            // Add a small focused cluster in a known "semester" window to ease testing
            $semStart = Carbon::create($now->year, 1, 15)->startOfDay();
            $semEnd = Carbon::create($now->year, 6, 15)->endOfDay();
            for ($j = 0; $j < random_int(20, 40); $j++) {
                $when = $randDate($semStart, $semEnd);
                ResourceView::create([
                    'student_faculty_id' => null,
                    'document_type' => 'mides',
                    'document_id' => null,
                    'program_id' => $program->id,
                    'course' => $courseNames[array_rand($courseNames)],
                    'role' => (random_int(0, 3) === 0 ? 'faculty' : 'student'),
                    'action' => 'view',
                    'created_at' => $when,
                    'updated_at' => $when,
                ]);
            }
        }

        $this->command?->info('ResourceViews seeded for analytics testing.');
    }
}
