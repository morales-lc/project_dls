<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MidesSeniorHighController extends Controller
{
    // Show card list of Senior High School programs
    public function programs()
    {
        $programs = [
            'Accountancy, Business and Management (ABM)',
            'Humanities and Social Sciences Strand (HUMSS)',
            'Science, Technology, Engineering and Mathematics (STEM)',
            'Technical-Vocational-Livelihood (TVL)',
            'Information Computer Technology',
            'Culinary Arts',
        ];
        return view('mides-seniorhigh-programs', compact('programs'));
    }

    // Show all records for a selected program
    public function program($program)
    {
        $records = DB::table('mides_documents')
            ->where('type', 'Senior High School Research Paper')
            ->where('program', $program)
            ->orderByDesc('year')
            ->get();
        return view('mides-seniorhigh-list', compact('program', 'records'));
    }
}
