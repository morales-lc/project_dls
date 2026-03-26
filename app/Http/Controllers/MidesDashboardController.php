<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MidesDocument;
use App\Models\MidesCategory;

class MidesDashboardController extends Controller
{
    private function getTagSuggestions(int $limit = 20): array
    {
        $rawTags = MidesDocument::query()
            ->whereNotNull('tags')
            ->where('tags', '!=', '')
            ->pluck('tags');

        $counts = [];
        foreach ($rawTags as $tagString) {
            $parts = explode(',', (string) $tagString);
            foreach ($parts as $part) {
                $tag = strtolower(trim($part));
                if ($tag === '') {
                    continue;
                }
                $counts[$tag] = ($counts[$tag] ?? 0) + 1;
            }
        }

        $tags = array_keys($counts);
        usort($tags, function ($a, $b) use ($counts) {
            $countCompare = $counts[$b] <=> $counts[$a];
            return $countCompare !== 0 ? $countCompare : strcmp($a, $b);
        });

        return array_slice($tags, 0, $limit);
    }

    private function parseTagFilters(Request $request): array
    {
        $raw = $request->input('tags', '');
        if (is_array($raw)) {
            $raw = implode(',', $raw);
        }

        $parts = array_filter(array_map('trim', explode(',', (string) $raw)));
        return array_values(array_unique(array_map('strtolower', $parts)));
    }

    private function applyTagFilters($query, array $tags): void
    {
        foreach ($tags as $tag) {
            $query->whereRaw('LOWER(tags) LIKE ?', ['%' . $tag . '%']);
        }
    }

    private function normalizeDirection(?string $direction): string
    {
        return strtolower((string) $direction) === 'asc' ? 'asc' : 'desc';
    }

    private function resolveSort(string $sort): string
    {
        $allowed = ['publication_date', 'year', 'title', 'author'];
        return in_array($sort, $allowed, true) ? $sort : 'publication_date';
    }

    // AJAX endpoint for fetching programs by type
    public function getPrograms(Request $request)
    {
        $type = $request->input('type');
        $typeTrimmed = trim($type ?? '');
        $programs = collect();
        if ($typeTrimmed) {
            if ($typeTrimmed === 'Graduate Theses') {
                $programs = MidesCategory::where('type', 'Graduate Theses')->select('id', 'name')->get();
            } elseif ($typeTrimmed === 'Undergraduate Baby Theses') {
                $programs = MidesCategory::where('type', 'Undergraduate Baby Theses')->select('id', 'name')->get();
            } elseif ($typeTrimmed === 'Senior High School Research Paper') {
                $programs = MidesCategory::where('type', 'Senior High School Research Paper')->select('id', 'name')->get();
            } else {
                // fallback to substring matching for other types
                $t = strtolower($typeTrimmed);
                if (str_contains($t, 'graduate')) {
                    $programs = MidesCategory::whereRaw('lower(type) like ?', ['%graduate%'])->select('id', 'name')->get();
                } elseif (str_contains($t, 'undergrad') || str_contains($t, 'undergraduate') || str_contains($t, 'baby')) {
                    $programs = MidesCategory::whereRaw('lower(type) like ?', ['%undergrad%'])->orWhereRaw('lower(type) like ?', ['%undergraduate%'])->orWhereRaw('lower(type) like ?', ['%baby%'])->select('id', 'name')->get();
                } elseif (str_contains($t, 'senior') || str_contains($t, 'high') || str_contains($t, 'school')) {
                    $programs = MidesCategory::whereRaw('lower(type) like ?', ['%senior%'])->orWhereRaw('lower(type) like ?', ['%high%'])->orWhereRaw('lower(type) like ?', ['%school%'])->select('id', 'name')->get();
                }
            }
        }
        return response()->json(['programs' => $programs->toArray()]);
    }
    public function facultyTheses()
    {
        $request = request();
        $search = request('search');
        $sort = $this->resolveSort(request('sort', 'publication_date'));
        $direction = $this->normalizeDirection(request('direction', 'desc'));
        $tagFilters = $this->parseTagFilters($request);
        $tagSuggestions = $this->getTagSuggestions();

        $query = MidesDocument::with('midesCategory')->where('type', 'Faculty/Theses/Dissertations');
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%$search%")
                    ->orWhere('author', 'like', "%$search%")
                    ->orWhere('advisors', 'like', "%$search%")
                    ->orWhere('description', 'like', "%$search%")
                    ->orWhere('year', 'like', "%$search%")
                    ->orWhere('publication_date', 'like', "%$search%")
                    ->orWhere('tags', 'like', "%$search%");
            });
        }
        $this->applyTagFilters($query, $tagFilters);

        $documents = $query->orderBy($sort, $direction)->paginate(12)->appends($request->query());
        return view('mides-faculty-theses-list', compact('documents', 'search', 'sort', 'direction', 'tagFilters', 'tagSuggestions'));
    }
    public function index(Request $request)
    {
        $query = MidesDocument::with('midesCategory');
        $search = $request->input('search');
        $type = $request->input('type');
        $category = $request->input('category');
        $program = $request->input('program');
        $publicationDate = $request->input('publication_date');
        $year = $request->input('year');
        $tagFilters = $this->parseTagFilters($request);
        $tagSuggestions = $this->getTagSuggestions();

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
        
        // Exact match for type filter
        if ($type) {
            $query->where('type', $type);
        }
        
        if ($category) {
            if (is_numeric($category)) {
                $query->where('mides_category_id', $category);
            } else {
                $query->where(function($q) use ($category) {
                    $q->where('category', $category)
                        ->orWhereHas('midesCategory', function ($q2) use ($category) {
                            $q2->where('name', $category);
                        });
                });
            }
        }
        if ($program) {
            if (is_numeric($program)) {
                $query->where('mides_category_id', $program);
            } else {
                $query->where(function($q) use ($program) {
                    $q->where('program', $program)
                        ->orWhereHas('midesCategory', function ($q2) use ($program) {
                            $q2->where('name', $program);
                        });
                });
            }
        }
        if ($publicationDate) {
            $query->whereDate('publication_date', $publicationDate);
        } elseif ($year) {
            $query->whereYear('publication_date', $year);
        }

        $this->applyTagFilters($query, $tagFilters);

        $documents = $query->orderBy('publication_date', 'desc')->paginate(12)->appends($request->query());
        $types = MidesCategory::select('type')->distinct()->pluck('type');
        
        // Get categories/programs based on selected type
        $categories = collect();
        $programs = collect();
        if ($type) {
            $categories = MidesCategory::where('type', $type)->pluck('name');
            $programs = MidesCategory::where('type', $type)->pluck('name');
        }

        $years = MidesDocument::select('year')->distinct()->orderBy('year', 'desc')->pluck('year');

        return view('mides', compact('documents', 'types', 'categories', 'programs', 'years', 'search', 'type', 'category', 'program', 'publicationDate', 'tagFilters', 'tagSuggestions'));
    }
    public function search(Request $request)
    {
        $query = MidesDocument::query();
        $search = $request->input('q');
        $type = $request->input('type');
        $category = $request->input('category');
        $program = $request->input('program');
        $publicationDate = $request->input('publication_date');
        $year = $request->input('year');
        $tagFilters = $this->parseTagFilters($request);
        $tagSuggestions = $this->getTagSuggestions();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%$search%")
                    ->orWhere('author', 'like', "%$search%")
                    ->orWhere('advisors', 'like', "%$search%")
                    ->orWhere('description', 'like', "%$search%")
                    ->orWhere('year', 'like', "%$search%")
                    ->orWhere('publication_date', 'like', "%$search%")
                    ->orWhere('tags', 'like', "%$search%")
                    ->orWhere('category', 'like', "%$search%")
                    ->orWhere('program', 'like', "%$search%")
                    ->orWhere('type', 'like', "%$search%");
            });
        }
        
        // Exact match for type filter
        if ($type) {
            $query->where('type', $type);
        }
        
        if ($program) {
            if (is_numeric($program)) {
                $query->where('mides_category_id', $program);
            } else {
                $query->where(function($q) use ($program) {
                    $q->where('category', $program)
                        ->orWhere('program', $program)
                        ->orWhereHas('midesCategory', function ($q2) use ($program) {
                            $q2->where('name', $program);
                        });
                });
            }
        }
        if ($category) {
            if (is_numeric($category)) {
                $query->where('mides_category_id', $category);
            } else {
                $query->where(function($q) use ($category) {
                    $q->where('category', $category)
                        ->orWhereHas('midesCategory', function ($q2) use ($category) {
                            $q2->where('name', $category);
                        });
                });
            }
        }
        if ($publicationDate) {
            $query->whereDate('publication_date', $publicationDate);
        } elseif ($year) {
            $query->whereYear('publication_date', $year);
        }

        $this->applyTagFilters($query, $tagFilters);

        $documents = $query->orderBy('publication_date', 'desc')->paginate(12)->appends($request->query());
        $types = MidesCategory::select('type')->distinct()->pluck('type');

        // categories based on type
        $categories = collect();
        if ($type) {
            $categories = MidesCategory::where('type', $type)->pluck('name');
        }

        // Determine programs list based on selected type
        $programs = collect();
        if ($type) {
            $programs = MidesCategory::where('type', $type)->pluck('name');
        }

        $years = MidesDocument::select('year')->distinct()->orderBy('year', 'desc')->pluck('year');

        return view('mides-search-results', compact('documents', 'types', 'categories', 'programs', 'years', 'search', 'type', 'category', 'program', 'publicationDate', 'tagFilters', 'tagSuggestions'));
    }

    // Viewer specifically for search results (returns standalone viewer in iframe)
    public function viewer($id)
    {
        $doc = MidesDocument::findOrFail($id);
        return view('mides-pdf-viewer', compact('doc'));
    }
}
