<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Catalog;

class CatalogController extends Controller
{
 


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

        // determine if current user bookmarked this catalog
        $catalogBookmarked = false;
        if ($sf) {
            $catalogBookmarked = \App\Models\Bookmark::where('student_faculty_id', $sf->id)
                ->where('bookmarkable_type', \App\Models\Catalog::class)
                ->where('bookmarkable_id', $catalog->id)
                ->exists();
        }

        return view('catalogs.show', compact('catalog', 'recommendations', 'jotformUrl', 'catalogBookmarked'));
    }

    // Catalog search
    // Catalog search
    public function search(Request $request)
    {
        $q = strtolower($request->input('q', ''));

        // Normalize query: remove commas, periods, extra spaces
        $normalizedQ = preg_replace('/[[:punct:]]+/', ' ', $q);
        $normalizedQ = preg_replace('/\s+/', ' ', trim($normalizedQ));

        $query = Catalog::query();

        if ($normalizedQ) {
            $query->where(function ($sub) use ($normalizedQ) {
                // normalize both sides in SQL
                $sub->whereRaw("LOWER(REGEXP_REPLACE(title, '[[:punct:]]+', '')) LIKE ?", ["%{$normalizedQ}%"])
                    ->orWhereRaw("LOWER(REGEXP_REPLACE(author, '[[:punct:]]+', '')) LIKE ?", ["%{$normalizedQ}%"])
                    ->orWhereRaw("LOWER(REGEXP_REPLACE(publisher, '[[:punct:]]+', '')) LIKE ?", ["%{$normalizedQ}%"])
                    ->orWhereRaw("LOWER(REGEXP_REPLACE(call_number, '[[:punct:]]+', '')) LIKE ?", ["%{$normalizedQ}%"])
                    ->orWhereRaw("LOWER(REGEXP_REPLACE(subjects, '[[:punct:]]+', '')) LIKE ?", ["%{$normalizedQ}%"])
                    ->orWhereRaw("LOWER(isbn) LIKE ?", ["%{$normalizedQ}%"])
                    ->orWhereRaw("LOWER(issn) LIKE ?", ["%{$normalizedQ}%"])
                    ->orWhereRaw("LOWER(lccn) LIKE ?", ["%{$normalizedQ}%"]);
            });
        }

        // Filters
        if ($request->filled('year')) {
            $query->where('year', $request->input('year'));
        }
        if ($request->filled('format')) {
            $query->where('format', $request->input('format'));
        }

        // Order by relevance: Title matches first
        $query->orderByRaw("
        CASE
            WHEN LOWER(title) LIKE ? THEN 1
            WHEN LOWER(author) LIKE ? THEN 2
            ELSE 3
        END
    ", ["%{$normalizedQ}%", "%{$normalizedQ}%"]);

        $catalogs = $query->paginate(12)->appends($request->except('page'));

        // Save search history
        try {
            $user = \Illuminate\Support\Facades\Auth::user();
            if ($user && ($sf = $user->studentFaculty ?? null)) {
                \App\Models\SearchHistory::create([
                    'student_faculty_id' => $sf->id,
                    'query' => $normalizedQ,
                    'results_count' => $catalogs->total(),
                ]);
            }
        } catch (\Throwable $e) {
            // ignore silently
        }

        return view('catalogs.search', compact('catalogs'));
    }
}
