<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ContactInfo;

class ContactInfoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        ContactInfo::create([
            'phone_college'     => '0916 980 5275',
            'phone_graduate'    => '0927 017 2382',
            'phone_senior_high' => '0975 977 4668',
            'phone_ibed'        => '0916 117 9542',
            'facebook_url'      => 'https://www.facebook.com/learningcommonslc',
            'email'             => 'learning.commons@lccdo.edu.ph',
            'website_url'       => 'https://lccdo.edu.ph',
        ]);
    }
}
