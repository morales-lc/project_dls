<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Catalog;
use App\Models\MidesDocument;
use App\Models\SidlakJournal;
use App\Models\SidlakArticle;

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

        // Initialize tokens list for relevance scoring later
        $tokens = [];
        $mode = strtolower($request->input('mode', 'or')) === 'and' ? 'and' : 'or';
        if ($normalizedQ) {
            // Prepare LIKE for the full normalized query
            $likeFull = "%{$normalizedQ}%";
            // Tokenize and remove common stopwords to improve matching
            $rawTokens = preg_split('/\s+/', $normalizedQ) ?: [];
            $stop = ['and','or','the','a','an','of','on','for','to','in','with','by','from','at','as','is','are','was','were','be','been','it','this','that'];
            $tokens = array_values(array_filter($rawTokens, function($t) use ($stop) {
                $t = trim($t);
                if ($t === '') return false;
                if (in_array($t, $stop, true)) return false;
                // keep tokens length >= 2 or special cases like c, c++ handled below
                return mb_strlen($t) >= 2 || in_array($t, ['c','c++','c#','r'], true);
            }));

            // Try to use FULLTEXT on MySQL for speed and relevance
            $usedFulltext = false;
            try {
                $driver = \Illuminate\Support\Facades\DB::connection()->getDriverName();
                if ($driver === 'mysql') {
                    // Ensure the FULLTEXT index exists before using MATCH ... AGAINST
                    $ftCount = collect(\Illuminate\Support\Facades\DB::select(
                        "SELECT COUNT(1) as cnt FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND INDEX_TYPE = 'FULLTEXT'",
                        ['catalogs']
                    ))->first();
                    $hasFulltext = $ftCount && (int)($ftCount->cnt ?? 0) > 0;
                    
                    if ($hasFulltext) {
                    // Build boolean query for WHERE using tokens
                    $tokensFT = $tokens;
                    // Use original words for phrase boosting in ORDER BY
                    $boolean = '';
                    if ($mode === 'and') {
                        // require all tokens, allow prefix matches
                        $boolean = implode(' ', array_map(function($t){ return '+'.str_replace('"','', $t).'*'; }, $tokensFT));
                    } else {
                        $boolean = implode(' ', array_map(function($t){ return str_replace('"','', $t).'*'; }, $tokensFT));
                    }
                    if (trim($boolean) !== '') {
                        $query->whereRaw(
                            "MATCH (title, subjects, additional_details, author, publisher) AGAINST (? IN BOOLEAN MODE)",
                            [$boolean]
                        );
                        // Order by natural language relevance as secondary sorter
                        $query->orderByRaw(
                            "MATCH (title, subjects, additional_details, author, publisher) AGAINST (? IN NATURAL LANGUAGE MODE) DESC",
                            [$normalizedQ]
                        );
                        $usedFulltext = true;
                    }
                    }
                }
            } catch (\Throwable $e) {
                $usedFulltext = false; // fallback below
            }

            if (!$usedFulltext && $mode === 'and' && count($tokens) > 0) {
                // AND mode: every token must appear in at least one of the key fields
                $query->where(function($must) use ($tokens) {
                    foreach ($tokens as $t) {
                        $like = '%'.str_replace('%','\\%',$t).'%';
                        $must->where(function($qq) use ($like) {
                            $qq->whereRaw("LOWER(title) LIKE ?", [$like])
                               ->orWhereRaw("LOWER(subjects) LIKE ?", [$like])
                               ->orWhereRaw("LOWER(additional_details) LIKE ?", [$like])
                               ->orWhereRaw("LOWER(author) LIKE ?", [$like])
                               ->orWhereRaw("LOWER(publisher) LIKE ?", [$like])
                               ->orWhereRaw("LOWER(call_number) LIKE ?", [$like])
                               ->orWhereRaw("LOWER(isbn) LIKE ?", [$like])
                               ->orWhereRaw("LOWER(issn) LIKE ?", [$like])
                               ->orWhereRaw("LOWER(lccn) LIKE ?", [$like]);
                        });
                    }
                });
            } elseif (!$usedFulltext) {
                // OR mode (default): phrase across key text fields + token-level OR across broader set
                $query->where(function ($sub) use ($likeFull, $tokens) {
                    // Phrase across the most relevant large text fields only (faster)
                    $sub->whereRaw("LOWER(REGEXP_REPLACE(title, '[[:punct:]]+', '')) LIKE ?", [$likeFull])
                        ->orWhereRaw("LOWER(REGEXP_REPLACE(subjects, '[[:punct:]]+', '')) LIKE ?", [$likeFull])
                        ->orWhereRaw("LOWER(REGEXP_REPLACE(additional_details, '[[:punct:]]+', '')) LIKE ?", [$likeFull]);

                    // Token-level matches: for each token, match across many fields
                    foreach ($tokens as $t) {
                        $like = '%'.str_replace('%','\\%',$t).'%';
                        $sub->orWhere(function($qq) use ($like) {
                            $qq->whereRaw("LOWER(title) LIKE ?", [$like])
                               ->orWhereRaw("LOWER(subjects) LIKE ?", [$like])
                               ->orWhereRaw("LOWER(additional_details) LIKE ?", [$like])
                               ->orWhereRaw("LOWER(author) LIKE ?", [$like])
                               ->orWhereRaw("LOWER(publisher) LIKE ?", [$like])
                               ->orWhereRaw("LOWER(format) LIKE ?", [$like])
                               ->orWhereRaw("LOWER(content_type) LIKE ?", [$like])
                               ->orWhereRaw("LOWER(media_type) LIKE ?", [$like])
                               ->orWhereRaw("LOWER(carrier_type) LIKE ?", [$like])
                               ->orWhereRaw("LOWER(isbn) LIKE ?", [$like])
                               ->orWhereRaw("LOWER(issn) LIKE ?", [$like])
                               ->orWhereRaw("LOWER(lccn) LIKE ?", [$like])
                               ->orWhereRaw("LOWER(call_number) LIKE ?", [$like]);
                        });
                    }
                });
            }
        }

        // Filters
        if ($request->filled('year')) {
            $query->where('year', $request->input('year'));
        }
        if ($request->filled('format')) {
            $query->where('format', $request->input('format'));
        }

        // Order by relevance only for fallback path; for FULLTEXT path we already applied ORDER BY MATCH
        if (empty($usedFulltext)) {
            // Order by relevance: prioritize title/subjects/additional_details then author, then others
            $bindings = [];
            $relevanceSql = [];

            // Heavier weight for exact-phrase (normalized) matches
            $bindings[] = "%{$normalizedQ}%"; $relevanceSql[] = "(CASE WHEN LOWER(REGEXP_REPLACE(title, '[[:punct:]]+', '')) LIKE ? THEN 8 ELSE 0 END)";
            $bindings[] = "%{$normalizedQ}%"; $relevanceSql[] = "(CASE WHEN LOWER(REGEXP_REPLACE(subjects, '[[:punct:]]+', '')) LIKE ? THEN 6 ELSE 0 END)";
            $bindings[] = "%{$normalizedQ}%"; $relevanceSql[] = "(CASE WHEN LOWER(REGEXP_REPLACE(additional_details, '[[:punct:]]+', '')) LIKE ? THEN 5 ELSE 0 END)";
            $bindings[] = "%{$normalizedQ}%"; $relevanceSql[] = "(CASE WHEN LOWER(REGEXP_REPLACE(author, '[[:punct:]]+', '')) LIKE ? THEN 4 ELSE 0 END)";
            $bindings[] = "%{$normalizedQ}%"; $relevanceSql[] = "(CASE WHEN LOWER(REGEXP_REPLACE(publisher, '[[:punct:]]+', '')) LIKE ? THEN 2 ELSE 0 END)";

            // Token-level contributions
            $tokenList = $tokens ?? [];
            foreach ($tokenList as $t) {
                $like = '%'.str_replace('%','\\%',$t).'%';
                $bindings[] = $like; $relevanceSql[] = "(CASE WHEN LOWER(title) LIKE ? THEN 4 ELSE 0 END)";
                $bindings[] = $like; $relevanceSql[] = "(CASE WHEN LOWER(subjects) LIKE ? THEN 3 ELSE 0 END)";
                $bindings[] = $like; $relevanceSql[] = "(CASE WHEN LOWER(additional_details) LIKE ? THEN 2 ELSE 0 END)";
                $bindings[] = $like; $relevanceSql[] = "(CASE WHEN LOWER(author) LIKE ? THEN 2 ELSE 0 END)";
            }

            $orderExpr = '('.implode(' + ', $relevanceSql).') DESC, title ASC';
            if (!empty($relevanceSql)) {
                $query->orderByRaw($orderExpr, $bindings);
            } else {
                $query->orderBy('title');
            }
        }

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

        // Search MIDES documents and SIDLAK journals/articles using the same query terms
        $midesDocuments = collect();
        $sidlakJournals = collect();
        $sidlakArticles = collect();

        if ($normalizedQ) {
            // MIDES Documents
            $midesQuery = MidesDocument::query();
            $midesQuery->where(function ($mq) use ($normalizedQ, $tokens) {
                $mq->whereRaw("LOWER(title) LIKE ?", ["%{$normalizedQ}%"])
                   ->orWhereRaw("LOWER(author) LIKE ?", ["%{$normalizedQ}%"])
                   ->orWhereRaw("LOWER(year) LIKE ?", ["%{$normalizedQ}%"])
                   ->orWhereRaw("LOWER(category) LIKE ?", ["%{$normalizedQ}%"])
                   ->orWhereRaw("LOWER(program) LIKE ?", ["%{$normalizedQ}%"])
                   ->orWhereRaw("LOWER(type) LIKE ?", ["%{$normalizedQ}%"]);
                foreach ($tokens as $t) {
                    $like = '%' . str_replace('%', '\\%', $t) . '%';
                    $mq->orWhereRaw("LOWER(title) LIKE ?", [$like])
                       ->orWhereRaw("LOWER(author) LIKE ?", [$like]);
                }
            });
            $midesDocuments = $midesQuery->orderBy('year', 'desc')->limit(8)->get();

            // SIDLAK Journals
            $sjQuery = SidlakJournal::query();
            $sjQuery->where(function ($jq) use ($normalizedQ, $tokens) {
                $jq->whereRaw("LOWER(title) LIKE ?", ["%{$normalizedQ}%"]);
                foreach ($tokens as $t) {
                    $like = '%' . str_replace('%', '\\%', $t) . '%';
                    $jq->orWhereRaw("LOWER(title) LIKE ?", [$like]);
                }
            });
            $sidlakJournals = $sjQuery->orderByDesc('year')->limit(8)->get();

            // SIDLAK Articles
            $saQuery = SidlakArticle::with('journal');
            $saQuery->where(function ($aq) use ($normalizedQ, $tokens) {
                $aq->whereRaw("LOWER(title) LIKE ?", ["%{$normalizedQ}%"])
                   ->orWhereRaw("LOWER(authors) LIKE ?", ["%{$normalizedQ}%"]);
                foreach ($tokens as $t) {
                    $like = '%' . str_replace('%', '\\%', $t) . '%';
                    $aq->orWhereRaw("LOWER(title) LIKE ?", [$like])
                       ->orWhereRaw("LOWER(authors) LIKE ?", [$like]);
                }
            });
            $sidlakArticles = $saQuery->limit(8)->get();
        }

        return view('catalogs.search', compact('catalogs', 'midesDocuments', 'sidlakJournals', 'sidlakArticles'));
    }
}
