<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MidesDocument;
use App\Models\MidesCategory;

class MidesDashboardController extends Controller
{
    public function facultyTheses()
    {
        $documents = MidesDocument::where('type', 'Faculty/Theses/Dissertations')->orderBy('year', 'desc')->paginate(12);
        return view('mides-faculty-theses-list', compact('documents'));
    }
    public function index(Request $request)
    {
        $query = MidesDocument::query();
        $search = $request->input('search');
        $type = $request->input('type');
        $category = $request->input('category');
        $program = $request->input('program');

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

        $documents = $query->orderBy('year', 'desc')->paginate(12);
        $types = MidesCategory::select('type')->distinct()->pluck('type');
        $categories = MidesCategory::where('type', $type ?: 'Graduate Theses')->pluck('name');
        $programs = MidesCategory::where('type', 'Undergraduate Baby Theses')->pluck('name');

    return view('mides', compact('documents', 'types', 'categories', 'programs', 'search', 'type', 'category', 'program'));
    }
    public function search(Request $request)
    {
        $query = MidesDocument::query();
        $search = $request->input('q');
        $type = $request->input('type');
        $category = $request->input('category');
        $program = $request->input('program');

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
        if ($type) $query->where('type', $type == 'graduate' ? 'Graduate Theses' : 'Undergraduate Baby Theses');
        if ($category) $query->where('category', $category);
        if ($program) $query->where('program', $program);

        $documents = $query->orderBy('year', 'desc')->paginate(12);
        $types = MidesCategory::select('type')->distinct()->pluck('type');
        $categories = MidesCategory::where('type', $type == 'graduate' ? 'Graduate Theses' : 'Undergraduate Baby Theses')->pluck('name');
        $programs = MidesCategory::where('type', 'Undergraduate Baby Theses')->pluck('name');

    return view('mides-search-results', compact('documents', 'types', 'categories', 'programs', 'search', 'type', 'category', 'program'));
    }
}
