<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\MidesDocument;

class MidesSeniorHighController extends Controller
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

    private function normalizeDirection(?string $direction): string
    {
        return strtolower((string) $direction) === 'asc' ? 'asc' : 'desc';
    }

    private function resolveSort(string $sort): string
    {
        $allowed = ['publication_date', 'year', 'title', 'author'];
        return in_array($sort, $allowed, true) ? $sort : 'publication_date';
    }

    // Show card list of Senior High School programs
    public function programs()
    {
        $programs = [
            'Accountancy, Business and Management (ABM)',
            'Humanities and Social Sciences Strand (HUMSS)',
            'Science, Technology, Engineering and Mathematics (STEM)',
            'Technical-Vocational-Livelihood (TVL)',
            'Information Computer Technology',
            'Culinary Arts',
        ];
        return view('mides-seniorhigh-programs', compact('programs'));
    }

    // Show all records for a selected program
    public function program($program)
    {
    $request = request();
    $search = request()->input('search');
    $sort = $this->resolveSort(request()->input('sort', 'publication_date'));
    $direction = $this->normalizeDirection(request()->input('direction', 'desc'));
    $tagFilters = $this->parseTagFilters($request);
    $tagSuggestions = $this->getTagSuggestions();

        // Support program as id (mides_category_id) or as name
        $query = DB::table('mides_documents')->where('type', 'Senior High School Research Paper');
        if (is_numeric($program)) {
            $query->where('mides_category_id', $program);
        } else {
            $query->where('program', $program);
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

        foreach ($tagFilters as $tag) {
            $query->whereRaw('LOWER(tags) LIKE ?', ['%' . $tag . '%']);
        }

        $records = $query->orderBy($sort, $direction)->get();
        return view('mides-seniorhigh-list', compact('program', 'records', 'search', 'sort', 'direction', 'tagFilters', 'tagSuggestions'));
    }

    public function viewer($id)
{
        $doc = \App\Models\MidesDocument::with('midesCategory')->findOrFail($id);
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
