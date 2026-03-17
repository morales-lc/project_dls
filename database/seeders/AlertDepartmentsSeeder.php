<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AlertDepartment;

class AlertDepartmentsSeeder extends Seeder
{
    public function run(): void
    {
        $programs = [
            'Accountancy, Accounting Information System, and Information Technology Program',
            'Allied Health Program',
            'Arts and Sciences Program',
            'Business Education Program',
            'Social Work Program',
            'Teacher Education Program',
            'Graduate School',
            'Senior Highschool',
        ];
        $categories = [
            'LIBRARY AND INFORMATION SCIENCE',
            'PSYCHOLOGY',
            'LET REVIEWER',
            'NSTP',
            'ENGLISH',
            'FILIPINO',
            'PHYSICAL EDUCATION',
            'TOURISM',
        ];
        foreach ($programs as $prog) {
            AlertDepartment::create(['name' => $prog, 'type' => 'program']);
        }
        foreach ($categories as $cat) {
            AlertDepartment::create(['name' => $cat, 'type' => 'category']);
        }
    }
}
