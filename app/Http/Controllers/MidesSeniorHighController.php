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
    $search = request()->input('search');
    $sort = request()->input('sort', 'year');
    $direction = request()->input('direction', 'desc');

        // Support program as id (mides_category_id) or as name
        $query = DB::table('mides_documents')->where('type', 'Senior High School Research Paper');
        if (is_numeric($program)) {
            $query->where('mides_category_id', $program);
        } else {
            $query->where('program', $program);
        }
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%$search%")
                  ->orWhere('author', 'like', "%$search%")
                  ->orWhere('year', 'like', "%$search%");
            });
        }
        $records = $query->orderBy($sort, $direction)->get();
        return view('mides-seniorhigh-list', compact('program', 'records', 'search', 'sort', 'direction'));
    }

    public function viewer($id)
{
    $doc = \App\Models\MidesDocument::with('midesCategory')->findOrFail($id);
    return view('mides-pdf-viewer', compact('doc'));
}

}
