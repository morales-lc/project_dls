<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MidesDocument;
use App\Models\MidesCategory;

class MidesDashboardController extends Controller {

    // AJAX endpoint for fetching programs by type
    public function getPrograms(Request $request)
    {
        $type = $request->input('type');
        $typeTrimmed = trim($type ?? '');
        $programs = collect();
        if ($typeTrimmed) {
            if ($typeTrimmed === 'Graduate Theses') {
                $programs = MidesCategory::where('type', 'Graduate Theses')->pluck('name');
            } elseif ($typeTrimmed === 'Undergraduate Baby Theses') {
                $programs = MidesCategory::where('type', 'Undergraduate Baby Theses')->pluck('name');
            } elseif ($typeTrimmed === 'Senior High School Research Paper') {
                $programs = MidesCategory::where('type', 'Senior High School Research Paper')->pluck('name');
            } else {
                // fallback to substring matching for other types
                $t = strtolower($typeTrimmed);
                if (str_contains($t, 'graduate')) {
                    $programs = MidesCategory::whereRaw('lower(type) like ?', ['%graduate%'])->pluck('name');
                } elseif (str_contains($t, 'undergrad') || str_contains($t, 'undergraduate') || str_contains($t, 'baby')) {
                    $programs = MidesCategory::whereRaw('lower(type) like ?', ['%undergrad%'])->orWhereRaw('lower(type) like ?', ['%undergraduate%'])->orWhereRaw('lower(type) like ?', ['%baby%'])->pluck('name');
                } elseif (str_contains($t, 'senior') || str_contains($t, 'high') || str_contains($t, 'school')) {
                    $programs = MidesCategory::whereRaw('lower(type) like ?', ['%senior%'])->orWhereRaw('lower(type) like ?', ['%high%'])->orWhereRaw('lower(type) like ?', ['%school%'])->pluck('name');
                }
            }
        }
        return response()->json(['programs' => $programs->toArray()]);
    }
    public function facultyTheses()
    {
        $search = request('search');
        $sort = request('sort', 'year');
        $direction = request('direction', 'desc');

        $query = MidesDocument::where('type', 'Faculty/Theses/Dissertations');
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%$search%")
                  ->orWhere('author', 'like', "%$search%")
                  ->orWhere('year', 'like', "%$search%");
            });
        }
        $documents = $query->orderBy($sort, $direction)->paginate(12)->appends(['search' => $search, 'sort' => $sort, 'direction' => $direction]);
        return view('mides-faculty-theses-list', compact('documents', 'search', 'sort', 'direction'));
    }
    public function index(Request $request)
    {
        $query = MidesDocument::query();
        $search = $request->input('search');
        $type = $request->input('type');
        $category = $request->input('category');
        $program = $request->input('program');
        $year = $request->input('year');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%$search%")
                  ->orWhere('author', 'like', "%$search%")
                  ->orWhere('year', 'like', "%$search%")
                  ->orWhere('category', 'like', "%$search%")
                  ->orWhere('program', 'like', "%$search%")
                  ->orWhere('type', 'like', "%$search%");
            });
        }
        if ($type) $query->where('type', $type);
        if ($category) $query->where('category', $category);
        if ($program) $query->where('program', $program);
        if ($year) $query->where('year', $year);

        $documents = $query->orderBy('year', 'desc')->paginate(12);
        $types = MidesCategory::select('type')->distinct()->pluck('type');
        $categories = MidesCategory::whereRaw('lower(type) like ?', ["%" . strtolower($type ?: 'graduate') . "%"])->pluck('name');

        // Determine programs list based on selected type (substring mapping)
        $programs = collect();
        if ($type) {
            $t = strtolower($type);
            if (str_contains($t, 'graduate')) {
                $programs = MidesCategory::whereRaw('lower(type) like ?', ['%graduate%'])->pluck('name');
            } elseif (str_contains($t, 'undergrad') || str_contains($t, 'undergraduate') || str_contains($t, 'baby')) {
                $programs = MidesCategory::whereRaw('lower(type) like ?', ['%undergrad%'])->orWhereRaw('lower(type) like ?', ['%undergraduate%'])->orWhereRaw('lower(type) like ?', ['%baby%'])->pluck('name');
            } elseif (str_contains($t, 'senior') || str_contains($t, 'high') || str_contains($t, 'school')) {
                $programs = MidesCategory::whereRaw('lower(type) like ?', ['%senior%'])->orWhereRaw('lower(type) like ?', ['%high%'])->orWhereRaw('lower(type) like ?', ['%school%'])->pluck('name');
            }
        }

        // If no specific type selected, default to undergraduate programs for convenience
        if ($programs->isEmpty()) {
            $programs = MidesCategory::whereRaw('lower(type) like ?', ['%undergrad%'])->orWhereRaw('lower(type) like ?', ['%undergraduate%'])->orWhereRaw('lower(type) like ?', ['%baby%'])->pluck('name');
        }

        $years = MidesDocument::select('year')->distinct()->orderBy('year', 'desc')->pluck('year');

    return view('mides', compact('documents', 'types', 'categories', 'programs', 'years', 'search', 'type', 'category', 'program'));
    }
    public function search(Request $request)
    {
        $query = MidesDocument::query();
        $search = $request->input('q');
        $type = $request->input('type');
        $category = $request->input('category');
        $program = $request->input('program');
        $year = $request->input('year');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%$search%")
                  ->orWhere('author', 'like', "%$search%")
                  ->orWhere('year', 'like', "%$search%")
                  ->orWhere('category', 'like', "%$search%")
                  ->orWhere('program', 'like', "%$search%")
                  ->orWhere('type', 'like', "%$search%") ;
            });
        }
        if ($type) {
            $typeLower = strtolower($type);
            if (str_contains($typeLower, 'graduate')) {
                $query->where('type', 'Graduate Theses');
            } elseif (str_contains($typeLower, 'undergrad') || str_contains($typeLower, 'undergraduate') || str_contains($typeLower, 'baby')) {
                $query->where('type', 'Undergraduate Baby Theses');
            } elseif (str_contains($typeLower, 'senior') || str_contains($typeLower, 'high') || str_contains($typeLower, 'school')) {
                $query->where('type', 'Senior High School Research Paper');
            } elseif (str_contains($typeLower, 'faculty') || str_contains($typeLower, 'dissertation')) {
                $query->where('type', 'Faculty/Theses/Dissertations');
            } else {
                $query->where('type', $type);
            }
        }
        if ($program) $query->where('category', $program)->orWhere('program', $program);
        if ($category) $query->where('category', $category);
        if ($year) $query->where('year', $year);

        $documents = $query->orderBy('year', 'desc')->paginate(12);
        $types = MidesCategory::select('type')->distinct()->pluck('type');

        // categories based on type
        $categories = MidesCategory::whereRaw('lower(type) like ?', ["%" . strtolower($type ?: 'graduate') . "%"])->pluck('name');

        // Determine programs list based on selected type (substring mapping)
        $programs = collect();
        if ($type) {
            $t = strtolower($type);
            if (str_contains($t, 'graduate')) {
                $programs = MidesCategory::whereRaw('lower(type) like ?', ['%graduate%'])->pluck('name');
            } elseif (str_contains($t, 'undergrad') || str_contains($t, 'undergraduate') || str_contains($t, 'baby')) {
                $programs = MidesCategory::whereRaw('lower(type) like ?', ['%undergrad%'])->orWhereRaw('lower(type) like ?', ['%undergraduate%'])->orWhereRaw('lower(type) like ?', ['%baby%'])->pluck('name');
            } elseif (str_contains($t, 'senior') || str_contains($t, 'high') || str_contains($t, 'school')) {
                $programs = MidesCategory::whereRaw('lower(type) like ?', ['%senior%'])->orWhereRaw('lower(type) like ?', ['%high%'])->orWhereRaw('lower(type) like ?', ['%school%'])->pluck('name');
            }
        }
        // fallback to undergraduate programs
        if ($programs->isEmpty()) {
            $programs = MidesCategory::whereRaw('lower(type) like ?', ['%undergrad%'])->orWhereRaw('lower(type) like ?', ['%undergraduate%'])->orWhereRaw('lower(type) like ?', ['%baby%'])->pluck('name');
        }

        $years = MidesDocument::select('year')->distinct()->orderBy('year', 'desc')->pluck('year');

    return view('mides-search-results', compact('documents', 'types', 'categories', 'programs', 'years', 'search', 'type', 'category', 'program'));
    }
}
