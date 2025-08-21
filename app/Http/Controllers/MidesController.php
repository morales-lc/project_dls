<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MidesDocument;

class MidesController extends Controller
{
    public function index()
    {
        $query = MidesDocument::query();

        // Search
        $search = request('search');
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

        // Filter by type
        $type = request('type');
        if ($type) {
            $query->where('type', $type);
        }

        // Sorting
        $sort = request('sort', 'year');
        $direction = request('direction', 'desc');
        $query->orderBy($sort, $direction);

        $documents = $query->paginate(12)->appends(request()->query());
        $types = \App\Models\MidesCategory::select('type')->distinct()->pluck('type');

        return view('mides-management', compact('documents', 'types', 'search', 'type', 'sort', 'direction'));
    }

    public function create()
    {
    $types = \App\Models\MidesCategory::select('type')->distinct()->pluck('type');
    $graduateCategories = \App\Models\MidesCategory::where('type', 'Graduate Theses')->pluck('name');
    $undergradPrograms = \App\Models\MidesCategory::where('type', 'Undergraduate Baby Theses')->pluck('name');
        $seniorHighPrograms = [
            'Accountancy, Business and Management (ABM)',
            'Humanities and Social Sciences Strand (HUMSS)',
            'Science, Technology, Engineering and Mathematics (STEM)',
            'Technical-Vocational-Livelihood (TVL)',
            'Information Computer Technology',
            'Culinary Arts',
        ];

        return view('mides-upload', compact('types', 'graduateCategories', 'undergradPrograms', 'seniorHighPrograms'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required',
            'category' => 'nullable|string',
            'program' => 'nullable|string',
            'author' => 'required|string',
            'year' => 'required|digits:4',
            'title' => 'required|string',
            'pdf' => 'required|file|mimes:pdf|max:20480',
        ]);

        $pdf = $request->file('pdf');
        $originalName = $pdf->getClientOriginalName();
        $pdfPath = $pdf->storeAs('mides_pdfs', $originalName, 'public');

        MidesDocument::create([
            'type' => $request->type,
            'category' => $request->category,
            'program' => $request->program,
            'author' => $request->author,
            'year' => $request->year,
            'title' => $request->title,
            'pdf_path' => $pdfPath,
        ]);

        return redirect()->route('mides.management')->with('success', 'Document uploaded successfully!');
    }
}
