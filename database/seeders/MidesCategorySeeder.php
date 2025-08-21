<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MidesCategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            // Graduate Theses
            ['type' => 'Graduate Theses', 'name' => 'MAED-Childhood Education'],
            ['type' => 'Graduate Theses', 'name' => 'MAED-Elementary Education'],
            ['type' => 'Graduate Theses', 'name' => 'MAED-Educational Management'],
            ['type' => 'Graduate Theses', 'name' => 'MAED-Teaching English Communication Arts'],
            ['type' => 'Graduate Theses', 'name' => 'MAED-Teaching Physical Education'],
            ['type' => 'Graduate Theses', 'name' => 'Master of Arts in Human Resource Development'],
            ['type' => 'Graduate Theses', 'name' => 'Master in Library and Information Science'],
            ['type' => 'Graduate Theses', 'name' => 'Master in Business Administration'],
            ['type' => 'Graduate Theses', 'name' => 'Master of Science in Hospitality Management'],
            ['type' => 'Graduate Theses', 'name' => 'Master of Arts in Home Economics'],
            ['type' => 'Graduate Theses', 'name' => 'Master of Science in Social Work'],
            // Undergraduate Baby Theses
            ['type' => 'Undergraduate Baby Theses', 'name' => 'Nursing'],
            ['type' => 'Undergraduate Baby Theses', 'name' => 'Nutrition and Dietetics'],
            ['type' => 'Undergraduate Baby Theses', 'name' => 'Pharmacy'],
            ['type' => 'Undergraduate Baby Theses', 'name' => 'Mass Communication'],
            ['type' => 'Undergraduate Baby Theses', 'name' => 'Library and Information Science'],
            ['type' => 'Undergraduate Baby Theses', 'name' => 'Psychology'],
            ['type' => 'Undergraduate Baby Theses', 'name' => 'Accountancy'],
            ['type' => 'Undergraduate Baby Theses', 'name' => 'Business Administration'],
            ['type' => 'Undergraduate Baby Theses', 'name' => 'Feasibility Studies'],
            ['type' => 'Undergraduate Baby Theses', 'name' => 'Hotel and Restaurant Management'],
            ['type' => 'Undergraduate Baby Theses', 'name' => 'Tourism'],
            ['type' => 'Undergraduate Baby Theses', 'name' => 'Information Technology'],
            ['type' => 'Undergraduate Baby Theses', 'name' => 'Social Work'],
            ['type' => 'Undergraduate Baby Theses', 'name' => 'Elementary Education'],
            ['type' => 'Undergraduate Baby Theses', 'name' => 'Secondary Education'],
            ['type' => 'Undergraduate Baby Theses', 'name' => 'English'],
            ['type' => 'Undergraduate Baby Theses', 'name' => 'Filipino'],
            ['type' => 'Undergraduate Baby Theses', 'name' => 'Other'],
            // Faculty/Theses/Dissertations (no categories, just type for completeness)
            ['type' => 'Faculty/Theses/Dissertations', 'name' => ''],
            // Senior High School Research Paper
            ['type' => 'Senior High School Research Paper', 'name' => 'Accountancy, Business and Management (ABM)'],
            ['type' => 'Senior High School Research Paper', 'name' => 'Humanities and Social Sciences Strand (HUMSS)'],
            ['type' => 'Senior High School Research Paper', 'name' => 'Science, Technology, Engineering and Mathematics (STEM)'],
            ['type' => 'Senior High School Research Paper', 'name' => 'Technical-Vocational-Livelihood (TVL)'],
            ['type' => 'Senior High School Research Paper', 'name' => 'Information Computer Technology'],
            ['type' => 'Senior High School Research Paper', 'name' => 'Culinary Arts'],
        ];

        DB::table('mides_categories')->insert($categories);
    }
}
