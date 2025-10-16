<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MidesDocument;
use App\Models\MidesCategory;
use Illuminate\Support\Facades\Auth;

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

    // allow $program to be id or name
    if (is_numeric($program)) {
        $query = MidesDocument::where('mides_category_id', $program);
    } else {
        $query = MidesDocument::where(function($q) use ($program) {
            $q->where('type', 'Undergraduate Baby Theses')->where('category', $program)
              ->orWhereHas('midesCategory', function($q2) use ($program) {
                  $q2->where('name', $program)->where('type', 'Undergraduate Baby Theses');
              });
        });
    }
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
        try {
            $user = Auth::user();
            if ($user) {
                $sf = $user->studentFaculty ?? null;
                \App\Models\ResourceView::create([
                    'student_faculty_id' => $sf->id ?? null,
                    'document_type' => 'mides',
                    'document_id' => $doc->id,
                    'program_id' => $sf->program_id ?? null,
                    'course' => $sf->course ?? null,
                    'role' => $sf->role ?? null,
                    'action' => 'view',
                ]);
            }
        } catch (\Throwable $e) {
            // ignore
        }

        return view('mides-pdf-viewer', compact('doc'));
    }
}
