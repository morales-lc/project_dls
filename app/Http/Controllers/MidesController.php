<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MidesDocument;
use Illuminate\Support\Carbon;

class MidesController extends Controller
{
    private function parseTagList(?string $tags): array
    {
        if (!$tags) {
            return [];
        }

        $parts = array_map('trim', explode(',', $tags));
        $parts = array_filter($parts, function ($tag) {
            return $tag !== '';
        });

        return array_values(array_unique(array_map('strtolower', $parts)));
    }

    private function extractKeywords(string $text): array
    {
        $normalized = strtolower(preg_replace('/[^a-z0-9\s]/i', ' ', $text));
        $tokens = preg_split('/\s+/', $normalized, -1, PREG_SPLIT_NO_EMPTY);
        $stopWords = [
            'the', 'and', 'for', 'with', 'from', 'into', 'this', 'that', 'these', 'those',
            'study', 'analysis', 'using', 'based', 'toward', 'towards', 'thesis', 'research',
            'paper', 'faculty', 'graduate', 'undergraduate', 'senior', 'high', 'school', 'of',
            'in', 'on', 'to', 'a', 'an', 'is', 'are', 'by'
        ];

        $filtered = array_filter($tokens, function ($word) use ($stopWords) {
            return strlen($word) >= 4 && !in_array($word, $stopWords, true);
        });

        return array_values(array_unique($filtered));
    }

    private function buildRelatedDocuments(MidesDocument $doc, int $limit = 10)
    {
        $collectionTypes = [
            'Graduate Theses',
            'Undergraduate Baby Theses',
            'Senior High School Research Paper',
            'Faculty/Theses/Dissertations',
        ];

        $docTags = $this->parseTagList($doc->tags);
        $docKeywords = $this->extractKeywords(trim(($doc->title ?? '') . ' ' . ($doc->description ?? '')));

        $candidates = MidesDocument::with('midesCategory')
            ->where('id', '!=', $doc->id)
            ->orderByDesc('publication_date')
            ->limit(220)
            ->get();

        $scored = $candidates->map(function ($candidate) use ($doc, $docTags, $docKeywords) {
            $candidateTags = $this->parseTagList($candidate->tags);
            $tagMatches = count(array_intersect($docTags, $candidateTags));

            $candidateKeywords = $this->extractKeywords(trim(($candidate->title ?? '') . ' ' . ($candidate->description ?? '') . ' ' . ($candidate->tags ?? '')));
            $keywordMatches = count(array_intersect($docKeywords, $candidateKeywords));

            $candidate->relevance_score = ($tagMatches * 7) + ($keywordMatches * 2);
            $candidate->diversity_score = $candidate->type !== $doc->type ? 1 : 0;

            return $candidate;
        })->sort(function ($a, $b) {
            if ($a->relevance_score !== $b->relevance_score) {
                return $b->relevance_score <=> $a->relevance_score;
            }
            if ($a->diversity_score !== $b->diversity_score) {
                return $b->diversity_score <=> $a->diversity_score;
            }

            return strtotime((string) $b->publication_date) <=> strtotime((string) $a->publication_date);
        })->values();

        // Ensure diversity by selecting at least one per collection type when possible.
        $selected = collect();
        foreach ($collectionTypes as $type) {
            $pick = $scored->first(function ($candidate) use ($type) {
                return $candidate->type === $type;
            });

            if ($pick) {
                $selected->push($pick);
            }
        }

        $remaining = $scored->reject(function ($candidate) use ($selected) {
            return $selected->contains('id', $candidate->id);
        });

        $relatedDocuments = $selected->merge($remaining)->unique('id')->take($limit)->values();

        if ($relatedDocuments->count() < $limit) {
            $fillers = MidesDocument::with('midesCategory')
                ->where('id', '!=', $doc->id)
                ->whereNotIn('id', $relatedDocuments->pluck('id'))
                ->orderByDesc('publication_date')
                ->limit($limit - $relatedDocuments->count())
                ->get();

            $relatedDocuments = $relatedDocuments->merge($fillers)->take($limit)->values();
        }

        return $relatedDocuments;
    }

    private function normalizeForCompare(?string $value): string
    {
        return strtolower(trim((string) $value));
    }

    private function duplicateDocumentExists(array $payload, ?int $ignoreId = null): bool
    {
        $query = MidesDocument::query()
            ->whereRaw('LOWER(TRIM(title)) = ?', [$this->normalizeForCompare($payload['title'] ?? '')])
            ->whereRaw('LOWER(TRIM(author)) = ?', [$this->normalizeForCompare($payload['author'] ?? '')])
            ->whereDate('publication_date', $payload['publication_date']);

        if (!empty($payload['mides_category_id'])) {
            $query->where('mides_category_id', $payload['mides_category_id']);
        } else {
            $query->where('type', $payload['type'] ?? '')
                ->whereRaw('LOWER(TRIM(COALESCE(category, ""))) = ?', [$this->normalizeForCompare($payload['category'] ?? '')])
                ->whereRaw('LOWER(TRIM(COALESCE(program, ""))) = ?', [$this->normalizeForCompare($payload['program'] ?? '')]);
        }

        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        return $query->exists();
    }

    private function normalizeTags(?string $tags): ?string
    {
        if (!$tags) {
            return null;
        }

        $items = array_filter(array_map('trim', explode(',', $tags)));
        return $items ? implode(', ', array_unique($items)) : null;
    }

    private function normalizeDirection(?string $direction): string
    {
        return strtolower((string) $direction) === 'asc' ? 'asc' : 'desc';
    }

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
        $returnUrl = $request->input('return_url');
        return $returnUrl
            ? redirect($returnUrl)->with('success', 'Category added successfully!')
            : redirect()->route('mides.categories.panel')->with('success', 'Category added successfully!');
    }

    public function updateCategory(Request $request, $id)
    {
        $cat = \App\Models\MidesCategory::findOrFail($id);
        $cat->type = $request->type;
        $cat->name = $request->name;
        $cat->save();
        $returnUrl = $request->input('return_url');
        return $returnUrl
            ? redirect($returnUrl)->with('success', 'Category updated successfully!')
            : redirect()->route('mides.categories.panel')->with('success', 'Category updated successfully!');
    }

    public function deleteCategory(Request $request, $id)
    {
        $cat = \App\Models\MidesCategory::findOrFail($id);
        $cat->delete();
        $returnUrl = $request->input('return_url');
        return $returnUrl
            ? redirect($returnUrl)->with('success', 'Category deleted successfully!')
            : redirect()->route('mides.categories.panel')->with('success', 'Category deleted successfully!');
    }
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'type' => 'required|string',
            'mides_category_id' => 'nullable|exists:mides_categories,id',
            'author' => 'required|string|max:255',
            'advisors' => 'nullable|string|max:1000',
            'publication_date' => 'required|date|before_or_equal:today',
            'title' => 'required|string|max:500',
            'description' => 'nullable|string|max:5000',
            'tags' => 'nullable|string|max:1000',
            'pdf' => 'nullable|file|mimes:pdf|max:20480',
        ], [
            'author.required' => 'Author name is required.',
            'author.max' => 'Author name cannot exceed 255 characters.',
            'advisors.max' => 'Advisor(s) cannot exceed 1000 characters.',
            'publication_date.required' => 'Publication date is required.',
            'publication_date.date' => 'Publication date must be a valid date.',
            'publication_date.before_or_equal' => 'Publication date cannot be in the future.',
            'title.required' => 'Document title is required.',
            'title.max' => 'Title cannot exceed 500 characters.',
            'tags.max' => 'Tags cannot exceed 1000 characters.',
            'pdf.mimes' => 'The file must be a PDF document.',
            'pdf.max' => 'PDF file size cannot exceed 20MB.',
        ]);

        $doc = MidesDocument::findOrFail($id);
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

        $duplicatePayload = [
            'title' => $request->input('title'),
            'author' => $request->input('author'),
            'publication_date' => $request->input('publication_date'),
            'mides_category_id' => $doc->mides_category_id,
            'type' => $doc->type,
            'category' => $doc->category,
            'program' => $doc->program,
        ];

        if ($this->duplicateDocumentExists($duplicatePayload, (int) $doc->id)) {
            return back()
                ->withErrors(['title' => 'A MIDES document with the same title, author, publication date, and category/program already exists.'])
                ->withInput();
        }
        
        $doc->author = $request->author;
        $doc->advisors = $request->input('advisors');
        $doc->publication_date = $request->input('publication_date');
        $doc->year = Carbon::parse($request->input('publication_date'))->year;
        $doc->title = $request->title;
        $doc->description = $request->input('description');
        $doc->tags = $this->normalizeTags($request->input('tags'));
        
        if ($request->hasFile('pdf')) {
            $pdf = $request->file('pdf');
            $originalName = $pdf->getClientOriginalName();
            $pdfPath = $pdf->storeAs('mides_pdfs', $originalName, 'public');
            $doc->pdf_path = $pdfPath;
        }
        
        $doc->save();
        
        $returnUrl = $request->input('return_url');
        return $returnUrl
            ? redirect($returnUrl)->with('success', 'Document updated successfully!')
            : redirect()->route('mides.management')->with('success', 'Document updated successfully!');
    }

    public function destroy(Request $request, $id)
    {
        $doc = MidesDocument::findOrFail($id);
        $doc->delete();
        $returnUrl = $request->input('return_url');
        return $returnUrl
            ? redirect($returnUrl)->with('success', 'Document deleted successfully!')
            : redirect()->back()->with('success', 'Document deleted successfully!');
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
                    ->orWhere('advisors', 'like', "%$search%")
                    ->orWhere('description', 'like', "%$search%")
                    ->orWhere('year', 'like', "%$search%")
                    ->orWhere('publication_date', 'like', "%$search%")
                    ->orWhere('tags', 'like', "%$search%")
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

            // Filter by year and month (publication_date)
            $year = request('year');
            $month = request('month');
            if (!empty($year) && ctype_digit((string) $year)) {
                $query->whereYear('publication_date', (int) $year);
            }
            if (!empty($month) && ctype_digit((string) $month)) {
                $monthInt = (int) $month;
                if ($monthInt >= 1 && $monthInt <= 12) {
                    $query->whereMonth('publication_date', $monthInt);
                }
            }

        // Sorting
        $sort = request('sort', 'publication_date');
        if ($sort === 'latest') {
            $query->orderBy('created_at', 'desc');
        } elseif ($sort === 'oldest') {
            $query->orderBy('created_at', 'asc');
            } elseif ($sort === 'year_asc') {
                $query->orderBy('year', 'asc');
            } elseif ($sort === 'year_desc' || $sort === 'year') {
                $query->orderBy('year', 'desc');
        } else {
            $allowedSorts = [
                'publication_date',
                'author',
                'title',
            ];
            $sortColumn = in_array($sort, $allowedSorts, true) ? $sort : 'publication_date';
            $direction = $this->normalizeDirection(request('direction', 'desc'));
            $query->orderBy($sortColumn, $direction);
        }

        $documents = $query->paginate(12)->appends(request()->query());
        $types = \App\Models\MidesCategory::select('type')->distinct()->pluck('type');

        // Build lookup arrays for type and category/program names
        $typeNames = [];
        $categoryNames = [];
        foreach (\App\Models\MidesCategory::all() as $cat) {
            $typeNames[$cat->type] = $cat->type; // type is already readable
            $categoryNames[$cat->type][$cat->id] = $cat->name;
        }

        return view('mides-management', compact('documents', 'types', 'search', 'type', 'sort', 'year', 'month', 'typeNames', 'categoryNames'));
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
            'advisors' => 'nullable|string|max:1000',
            'publication_date' => 'required|date|before_or_equal:today',
            'title' => 'required|string',
            'description' => 'nullable|string|max:5000',
            'tags' => 'nullable|string|max:1000',
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

        $duplicatePayload = [
            'title' => $request->input('title'),
            'author' => $request->input('author'),
            'publication_date' => $request->input('publication_date'),
            'mides_category_id' => $midesCategoryId,
            'type' => $type,
            'category' => $category,
            'program' => $program,
        ];

        if ($this->duplicateDocumentExists($duplicatePayload)) {
            return back()
                ->withErrors(['title' => 'A MIDES document with the same title, author, publication date, and category/program already exists.'])
                ->withInput();
        }

        MidesDocument::create([
            'type' => $type,
            'category' => $category,
            'program' => $program,
            'mides_category_id' => $midesCategoryId,
            'author' => $request->author,
            'advisors' => $request->input('advisors'),
            'year' => Carbon::parse($request->input('publication_date'))->year,
            'publication_date' => $request->input('publication_date'),
            'title' => $request->title,
            'description' => $request->input('description'),
            'tags' => $this->normalizeTags($request->input('tags')),
            'pdf_path' => $pdfPath,
        ]);
        $returnUrl = $request->input('return_url');
        return $returnUrl
            ? redirect($returnUrl)->with('success', 'Document uploaded successfully!')
            : redirect()->route('mides.management')->with('success', 'Document uploaded successfully!');
    }

    public function show($id)
    {
        $doc = MidesDocument::with('midesCategory')->findOrFail($id);
        $relatedDocuments = $this->buildRelatedDocuments($doc, 10);

        $sf = optional(auth()->user()->studentFaculty);
        $isBookmarked = $sf && $sf->id
            ? \App\Models\Bookmark::where('student_faculty_id', $sf->id)
                ->where('bookmarkable_type', \App\Models\MidesDocument::class)
                ->where('bookmarkable_id', $doc->id)
                ->exists()
            : false;

        return view('mides-document-details', compact('doc', 'relatedDocuments', 'isBookmarked', 'sf'));
    }

    public function tag(Request $request, string $tag)
    {
        $decodedTag = trim(urldecode($tag));
        abort_if($decodedTag === '', 404);

        $sort = $request->input('sort', 'publication_date');
        $direction = $this->normalizeDirection($request->input('direction', 'desc'));
        $search = trim((string) $request->input('search', ''));

        $query = MidesDocument::with('midesCategory')
            ->whereRaw('LOWER(tags) LIKE ?', ['%' . strtolower($decodedTag) . '%']);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%$search%")
                    ->orWhere('author', 'like', "%$search%")
                    ->orWhere('advisors', 'like', "%$search%")
                    ->orWhere('description', 'like', "%$search%")
                    ->orWhere('tags', 'like', "%$search%");
            });
        }

        $allowedSorts = ['publication_date', 'year', 'title', 'author'];
        if (!in_array($sort, $allowedSorts, true)) {
            $sort = 'publication_date';
        }

        $documents = $query
            ->orderBy($sort, $direction)
            ->paginate(12)
            ->appends($request->query());

        $sf = optional(auth()->user()->studentFaculty);

        return view('mides-tag-results', [
            'documents' => $documents,
            'tag' => $decodedTag,
            'search' => $search,
            'sort' => $sort,
            'direction' => $direction,
            'sf' => $sf,
        ]);
    }
}
