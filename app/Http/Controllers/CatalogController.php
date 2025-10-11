<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Catalog;

class CatalogController extends Controller
{
    // Display paginated catalog list
    public function index()
    {
        $catalogs = Catalog::orderBy('title')->paginate(12);
        return view('catalogs.index', compact('catalogs'));
    }

    // Show catalog creation form
    public function create()
    {
        return view('catalogs.create');
    }

    // Store a new catalog entry
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:500',
            'author' => 'nullable|string|max:255',
            'call_number' => 'nullable|string|max:100',
            'sublocation' => 'nullable|string|max:255',
            'publisher' => 'nullable|string|max:255',
            'year' => 'nullable|string|max:50',
            'edition' => 'nullable|string|max:100',
            'format' => 'nullable|string|max:255',
            'content_type' => 'nullable|string|max:255',
            'media_type' => 'nullable|string|max:255',
            'carrier_type' => 'nullable|string|max:255',
            'isbn' => 'nullable|string|max:100',
            'issn' => 'nullable|string|max:100',
            'lccn' => 'nullable|string|max:100',
            'subjects' => 'nullable|string',
            'additional_details' => 'nullable|string',
        ]);

        Catalog::create($data);

        return redirect()
            ->route('catalogs.create')
            ->with('success', 'Catalog item added successfully.');
    }

    // Display a specific catalog item and related recommendations
    public function show($id)
    {
        $catalog = Catalog::findOrFail($id);

        // Recommendations: match author first, fallback to format
        $recommendations = Catalog::where('id', '!=', $catalog->id)
            ->when($catalog->author, function ($q) use ($catalog) {
                $q->where('author', $catalog->author);
            })
            ->orWhere(function ($q) use ($catalog) {
                if ($catalog->format) {
                    $q->where('format', $catalog->format);
                }
            })
            ->orderBy('title')
            ->limit(9)
            ->get();

        // If not enough, fill with random others (excluding current and already recommended)
        if ($recommendations->count() < 9) {
            $idsToExclude = $recommendations->pluck('id')->push($catalog->id)->all();
            $fillCount = 9 - $recommendations->count();
            $fillers = Catalog::whereNotIn('id', $idsToExclude)
                ->inRandomOrder()
                ->limit($fillCount)
                ->get();
            $recommendations = $recommendations->concat($fillers);
        }

        // Build Jotform URL for Book Borrowing
    $user = \Illuminate\Support\Facades\Auth::user();
        $sf = $user->studentFaculty ?? null;
        $first = $sf->first_name ?? '';
        $last = $sf->last_name ?? '';
        $email = $user->email ?? '';
        $department = $sf->department ?? 'Senior High';
        $course = $sf->course ?? '';
        $yrlvl = $sf->yrlvl ?? '';
        $programStrandGradeLevel = trim($course . ($yrlvl ? '-' . $yrlvl : '')) ?: 'BSSW-4';
        $designationRaw = $sf->role ?? 'Faculty';
        $designation = ucfirst(strtolower($designationRaw));

        // Compose examplePurposive: Title, Author, Call number, ISBN/LCCN/ISSN
        $examplePurposive = '';
        $examplePurposive .= $catalog->title ? $catalog->title . ', ' : '';
        $examplePurposive .= $catalog->author ? $catalog->author . ', ' : '';
        $examplePurposive .= $catalog->call_number ? $catalog->call_number . ', ' : '';
        // Prefer ISBN, then LCCN, then ISSN
        $idStr = $catalog->isbn ?: ($catalog->lccn ?: ($catalog->issn ?: ''));
        $examplePurposive .= $idStr;

        $baseUrl = 'https://jotform.com/221923899504465';
        $params = [
            'name[first]' => $first,
            'name[last]' => $last,
            'email11' => $email,
            'department' => $department,
            'programstrandgradeLevel' => $programStrandGradeLevel,
            'designation' => $designation,
            'whatKind' => 'Book Borrowing',
            'whatType' => 'Books',
            'examplePurposive' => $examplePurposive,
            'forList' => '',
            'forVideos' => '',
            'typeA43' => 'Yes',
            'titlesOf' => '',
        ];
        $jotformUrl = $baseUrl . '?' . http_build_query($params, '', '&', PHP_QUERY_RFC3986);

        return view('catalogs.show', compact('catalog', 'recommendations', 'jotformUrl'));
    }

    // Catalog search
    public function search(Request $request)
    {
        $query = Catalog::query();

        if ($request->filled('q')) {
            $q = $request->input('q');
            $query->where(function ($sub) use ($q) {
                $sub->where('title', 'like', "%{$q}%")
                    ->orWhere('author', 'like', "%{$q}%")
                    ->orWhere('isbn', 'like', "%{$q}%")
                    ->orWhere('issn', 'like', "%{$q}%")
                    ->orWhere('lccn', 'like', "%{$q}%")
                    ->orWhere('additional_details', 'like', "%{$q}%");
            });
        }

        $catalogs = $query->orderBy('title')
            ->paginate(10)
            ->appends($request->except('page'));

        return view('catalogs.search', compact('catalogs'));
    }
}
