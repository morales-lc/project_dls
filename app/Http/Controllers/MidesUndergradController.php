<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MidesDocument;
use App\Models\MidesCategory;

class MidesUndergradController extends Controller
{
    public function index()
    {
        $programs = MidesCategory::where('type', 'Undergraduate Baby Theses')->pluck('name');
        return view('mides-undergrad-programs', compact('programs'));
    }

    public function program($program)
    {
        $documents = MidesDocument::where('type', 'Undergraduate Baby Theses')
            ->where('program', $program)
            ->orderBy('year', 'desc')
            ->paginate(12);
        return view('mides-undergrad-list', compact('documents', 'program'));
    }

    public function programs()
    {
    $programs = MidesCategory::where('type', 'Undergraduate Baby Theses')->select('name')->distinct()->pluck('name');
    return view('mides-undergrad-programs', compact('programs'));
    }
}
