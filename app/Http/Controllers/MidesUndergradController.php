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
        $search = request('search');
        $sort = request('sort', 'year');
        $direction = request('direction', 'desc');

    $query = MidesDocument::where('type', 'Undergraduate Baby Theses')->where('category', $program);
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%$search%")
                  ->orWhere('author', 'like', "%$search%")
                  ->orWhere('year', 'like', "%$search%");
            });
        }
        $documents = $query->orderBy($sort, $direction)->paginate(12)->appends(['search' => $search, 'sort' => $sort, 'direction' => $direction]);
        return view('mides-undergrad-list', compact('documents', 'program', 'search', 'sort', 'direction'));
    }

    public function programs()
    {
        $programs = MidesCategory::where('type', 'Undergraduate Baby Theses')->select('name')->distinct()->pluck('name');
        return view('mides-undergrad-programs', compact('programs'));
    }

    // PDF viewer for Undergraduate Baby Theses
    public function viewer($id)
    {
        $doc = \App\Models\MidesDocument::findOrFail($id);
        return view('mides-pdf-viewer', compact('doc'));
    }
}
