<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\AlertBook;
use App\Models\AlertDepartment;

class AlertBooksSeeder extends Seeder
{
    public function run(): void
    {
        $months = [8, 9];
        $year = 2024;
        $departments = AlertDepartment::all();
        foreach ($months as $month) {
            foreach ($departments as $dept) {
                AlertBook::create([
                    'title' => $dept->name . ' Book ' . Str::random(4),
                    'pdf_path' => 'alert_books/sample_' . strtolower(Str::random(6)) . '.pdf',
                    'cover_image' => 'alert_books/covers/sample_' . strtolower(Str::random(6)) . '.jpg',
                    'department_id' => $dept->id,
                    'month' => $month,
                    'year' => $year,
                ]);
            }
        }
    }
}
