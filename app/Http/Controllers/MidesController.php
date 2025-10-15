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
        // Support setting by mides_category_id (preferred) or legacy category_program
        $midesCategoryId = $request->input('mides_category_id');
        if ($midesCategoryId) {
            $cat = \App\Models\MidesCategory::find($midesCategoryId);
            if ($cat) {
                $doc->mides_category_id = $cat->id;
                $doc->type = $cat->type;
                if ($cat->type === 'Senior High School Research Paper') {
                    $doc->program = $cat->name;
                    $doc->category = null;
                } else {
                    $doc->category = $cat->name;
                    $doc->program = null;
                }
            }
        } else {
            $doc->type = $request->input('type');
            $category_program = $request->input('category_program');
            if ($doc->type === 'Graduate Theses') {
                $doc->category = $category_program;
                $doc->program = null;
            } elseif ($doc->type === 'Undergraduate Baby Theses') {
                $doc->category = $category_program;
                $doc->program = null;
            } elseif ($doc->type === 'Senior High School Research Paper') {
                $doc->program = $category_program;
                $doc->category = null;
            } else {
                $doc->category = null;
                $doc->program = null;
            }
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
        $query = MidesDocument::with('midesCategory');

        // Search
        $search = request('search');
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%$search%")
                    ->orWhere('author', 'like', "%$search%")
                    ->orWhere('year', 'like', "%$search%")
                    ->orWhere('type', 'like', "%$search%");
            })->orWhereHas('midesCategory', function ($q) use ($search) {
                $q->where('name', 'like', "%$search%");
            });
        }

        // Filter by type or by selected mides_category_id
        $type = request('type');
        $midesCategoryId = request('mides_category_id');
        if ($midesCategoryId) {
            $query->where('mides_category_id', $midesCategoryId);
        } elseif ($type) {
            // Keep backward compatibility: filter by raw type column or related category.type
            $query->where(function ($q) use ($type) {
                $q->where('type', $type)
                    ->orWhereHas('midesCategory', function ($q2) use ($type) {
                        $q2->where('type', $type);
                    });
            });
        }

        // Sorting
        $sort = request('sort', 'year');
        $direction = request('direction', 'desc');
        $query->orderBy($sort, $direction);

        $documents = $query->orderBy($sort, $direction)->paginate(12)->appends(request()->query());
        $types = \App\Models\MidesCategory::select('type')->distinct()->pluck('type');

        // Build lookup arrays for type and category/program names
        $typeNames = [];
        $categoryNames = [];
        foreach (\App\Models\MidesCategory::all() as $cat) {
            $typeNames[$cat->type] = $cat->type; // type is already readable
            $categoryNames[$cat->type][$cat->id] = $cat->name;
        }

        return view('mides-management', compact('documents', 'types', 'search', 'type', 'sort', 'direction', 'typeNames', 'categoryNames'));
    }

    public function create()
    {
        $types = \App\Models\MidesCategory::select('type')->distinct()->pluck('type');
        $graduateCategories = \App\Models\MidesCategory::where('type', 'Graduate Theses')->get();
        $undergradPrograms = \App\Models\MidesCategory::where('type', 'Undergraduate Baby Theses')->get();
        $seniorHighPrograms = \App\Models\MidesCategory::where('type', 'Senior High School Research Paper')->get();

        return view('mides-upload', compact('types', 'graduateCategories', 'undergradPrograms', 'seniorHighPrograms'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'nullable|string',
            'mides_category_id' => 'nullable|exists:mides_categories,id',
            'author' => 'required|string',
            'year' => 'required|digits:4',
            'title' => 'required|string',
            'pdf' => 'required|file|mimes:pdf|max:20480',
        ]);

        $pdf = $request->file('pdf');
        $originalName = $pdf->getClientOriginalName();
        $pdfPath = $pdf->storeAs('mides_pdfs', $originalName, 'public');

        $midesCategoryId = $request->input('mides_category_id');

        // Prepare fallback values for older columns for backward compatibility
        $type = $request->input('type');
        $category = $request->input('category');
        $program = $request->input('program');

        if ($midesCategoryId) {
            $cat = \App\Models\MidesCategory::find($midesCategoryId);
            if ($cat) {
                $type = $cat->type;
                // Set the appropriate legacy fields depending on type
                if ($cat->type === 'Senior High School Research Paper') {
                    $program = $cat->name;
                    $category = null;
                } else {
                    $category = $cat->name;
                    $program = null;
                }
            }
        }

        MidesDocument::create([
            'type' => $type,
            'category' => $category,
            'program' => $program,
            'mides_category_id' => $midesCategoryId,
            'author' => $request->author,
            'year' => $request->year,
            'title' => $request->title,
            'pdf_path' => $pdfPath,
        ]);

        return redirect()->route('mides.management')->with('success', 'Document uploaded successfully!');
    }
}
