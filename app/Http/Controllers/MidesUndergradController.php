<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MidesDocument;
use App\Models\MidesCategory;
use Illuminate\Support\Facades\Auth;

class MidesUndergradController extends Controller
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

    public function index()
    {
        $programs = MidesCategory::where('type', 'Undergraduate Baby Theses')->pluck('name');
        return view('mides-undergrad-programs', compact('programs'));
    }

    public function program($program)
    {
        $request = request();
        $search = request('search');
        $sort = $this->resolveSort(request('sort', 'publication_date'));
        $direction = $this->normalizeDirection(request('direction', 'desc'));
        $tagFilters = $this->parseTagFilters($request);
        $tagSuggestions = $this->getTagSuggestions();

    // allow $program to be id or name
    if (is_numeric($program)) {
        $query = MidesDocument::where('mides_category_id', $program);
    } else {
        $query = MidesDocument::where(function($q) use ($program) {
            $q->where('type', 'Undergraduate Baby Theses')->where('category', $program)
              ->orWhereHas('midesCategory', function($q2) use ($program) {
                  $q2->where('name', $program)->where('type', 'Undergraduate Baby Theses');
              });
        });
    }
        if ($search) {
            $query->where(function($q) use ($search) {
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
        return view('mides-undergrad-list', compact('documents', 'program', 'search', 'sort', 'direction', 'tagFilters', 'tagSuggestions'));
    }

    public function programs()
    {
        $programs = MidesCategory::where('type', 'Undergraduate Baby Theses')->select('name')->distinct()->pluck('name');
        return view('mides-undergrad-programs', compact('programs'));
    }

    // PDF viewer for Undergraduate Baby Theses
    public function viewer($id)
    {
        $doc = \App\Models\MidesDocument::findOrFail($id);
        try {
            $user = Auth::user();
            if ($user) {
                $sf = $user->studentFaculty ?? null;
                \App\Models\ResourceView::create([
                    'student_faculty_id' => $sf->id ?? null,
                    'document_type' => 'mides',
                    'document_id' => $doc->id,
                    'program_id' => $sf->program_id ?? null,
                    'course' => $sf->course ?? null,
                    'role' => $sf->role ?? null,
                    'action' => 'view',
                ]);
            }
        } catch (\Throwable $e) {
            // ignore
        }

        return view('mides-pdf-viewer', compact('doc'));
    }
}
