<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MidesDocument;

class MidesController extends Controller
{
    public function categoriesPanel()
    {
        $categories = \App\Models\MidesCategory::orderBy('type')->orderBy('name')->get();
        $types = [
            'Graduate Theses',
            'Undergraduate Baby Theses',
            'Senior High School Research Paper',
        ];
        return view('mides-categories-panel', compact('categories', 'types'));
    }

    public function addCategory(Request $request)
    {
        $request->validate([
            'type' => 'required',
            'name' => 'required|string',
        ]);
        \App\Models\MidesCategory::create([
            'type' => $request->type,
            'name' => $request->name,
        ]);
        return redirect()->route('mides.categories.panel')->with('success', 'Category added successfully!');
    }

    public function updateCategory(Request $request, $id)
    {
        $cat = \App\Models\MidesCategory::findOrFail($id);
        $cat->type = $request->type;
        $cat->name = $request->name;
        $cat->save();
        return redirect()->route('mides.categories.panel')->with('success', 'Category updated successfully!');
    }

    public function deleteCategory($id)
    {
        $cat = \App\Models\MidesCategory::findOrFail($id);
        $cat->delete();
        return redirect()->route('mides.categories.panel')->with('success', 'Category deleted successfully!');
    }
    public function update(Request $request, $id)
    {
        $doc = MidesDocument::findOrFail($id);
        $doc->type = $request->type;
        // Handle category/program logic
        if ($doc->type === 'Graduate Theses') {
            $doc->category = $request->category_program;
            $doc->program = null;
        } elseif ($doc->type === 'Undergraduate Baby Theses') {
            // Save selected program in category column
            $doc->category = $request->category_program;
            $doc->program = null;
        } elseif ($doc->type === 'Senior High School Research Paper') {
            $doc->program = $request->category_program;
            $doc->category = null;
        } else {
            $doc->category = null;
            $doc->program = null;
        }
        $doc->author = $request->author;
        $doc->year = $request->year;
        $doc->title = $request->title;
        if ($request->hasFile('pdf')) {
            $pdf = $request->file('pdf');
            $originalName = $pdf->getClientOriginalName();
            $pdfPath = $pdf->storeAs('mides_pdfs', $originalName, 'public');
            $doc->pdf_path = $pdfPath;
        }
        $doc->save();
        return redirect()->route('mides.management')->with('success', 'Document updated successfully!');
    }

    public function destroy($id)
    {
        $doc = MidesDocument::findOrFail($id);
        $doc->delete();
        return redirect()->route('mides.management')->with('success', 'Document deleted successfully!');
    }
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

        // Always save undergraduate program in category column for Undergraduate Baby Theses
        $category = $request->category;
        $program = null;
        if ($request->type === 'Undergraduate Baby Theses') {
            $category = $request->undergrad_program;
        } elseif ($request->type === 'Senior High School Research Paper') {
            $program = $request->senior_high_program;
        }
        MidesDocument::create([
            'type' => $request->type,
            'category' => $category,
            'program' => $program,
            'author' => $request->author,
            'year' => $request->year,
            'title' => $request->title,
            'pdf_path' => $pdfPath,
        ]);

        return redirect()->route('mides.management')->with('success', 'Document uploaded successfully!');
    }
}
