<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SidlakJournal;
use App\Models\SidlakArticle;
use Illuminate\Support\Facades\Auth;

class SidlakJournalController extends Controller
{
    public function manage(Request $request) {
        // Provide a list of distinct years for the year filter
        $years = SidlakJournal::select('year')->distinct()->orderBy('year', 'desc')->pluck('year');

        $query = SidlakJournal::query();

        // Apply filters when provided via query string (GET)
        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }

        if ($request->filled('month')) {
            $query->where('month', $request->month);
        }

        if ($request->filled('q')) {
            $q = trim($request->q);
            $query->where('title', 'like', "%{$q}%");
        }

        // Preserve the same ordering used elsewhere (by year desc, then month desc)
        $journals = $query
            ->orderByDesc('year')
            ->orderByRaw("STR_TO_DATE(CONCAT('01 ', month, ' ', year), '%d %M %Y') DESC")
            ->get();

        $selectedYear = $request->year;
        $selectedMonth = $request->month;
        $q = $request->q;

        return view('sidlak.manage', compact('journals', 'years', 'selectedYear', 'selectedMonth', 'q'));
    }

    public function edit($id) {
        $journal = SidlakJournal::findOrFail($id);
        return view('sidlak.edit', compact('journal'));
    }

    public function update(Request $request, $id) {
        $journal = SidlakJournal::findOrFail($id);
        $request->validate([
            'title' => 'required|string|max:255',
            'month_year' => 'required|string',
            'print_issn' => 'required|string|regex:/^\d{4}-\d{4}$/',
            'cover_photo' => 'nullable|image|max:2048',
            'editors.*.name' => 'required_with:editors.*.title|string|max:255',
            'editors.*.title' => 'required_with:editors.*.name|string|max:255',
            'peer_reviewers.*.name' => 'required_with:peer_reviewers.*.title|string|max:255',
            'peer_reviewers.*.title' => 'required_with:peer_reviewers.*.name|string|max:255',
            'peer_reviewers.*.institution' => 'required_with:peer_reviewers.*.name|string|max:255',
            'peer_reviewers.*.city' => 'required_with:peer_reviewers.*.name|string|max:255',
        ]);

        // Split month_year (YYYY-MM) into month and year
        $month = '';
        $year = '';
        if (preg_match('/^(\d{4})-(\d{2})$/', $request->month_year, $matches)) {
            $year = $matches[1];
            $month = date('F', mktime(0, 0, 0, (int)$matches[2], 10));
        }

        $journal->title = $request->title;
        $journal->month = $month;
        $journal->year = $year;
        $journal->print_issn = $request->print_issn;
        if ($request->hasFile('cover_photo')) {
            $journal->cover_photo = $request->file('cover_photo')->store('sidlak_covers', 'public');
        }
        $journal->save();

        // Update editors: delete all and re-create
        $journal->editors()->delete();
        if ($request->editors) {
            foreach ($request->editors as $editor) {
                if (!empty($editor['name']) && !empty($editor['title'])) {
                    $journal->editors()->create([
                        'name' => $editor['name'],
                        'title' => $editor['title'],
                    ]);
                }
            }
        }

        // Update peer reviewers: delete all and re-create
        $journal->peerReviewers()->delete();
        if ($request->peer_reviewers) {
            foreach ($request->peer_reviewers as $reviewer) {
                if (!empty($reviewer['name']) && !empty($reviewer['title']) && !empty($reviewer['institution']) && !empty($reviewer['city'])) {
                    $journal->peerReviewers()->create([
                        'name' => $reviewer['name'],
                        'title' => $reviewer['title'],
                        'institution' => $reviewer['institution'],
                        'city' => $reviewer['city'],
                    ]);
                }
            }
        }

        // Handle research articles update, addition, and deletion
        if ($request->articles) {
            foreach ($request->articles as $article) {
                // Remove article if flagged
                if (isset($article['remove']) && $article['remove'] == '1' && isset($article['id'])) {
                    SidlakArticle::where('id', $article['id'])->delete();
                    continue;
                }
                // Update existing article
                if (isset($article['id'])) {
                    $existing = SidlakArticle::find($article['id']);
                    if ($existing) {
                        $existing->title = $article['title'] ?? $existing->title;
                        $existing->authors = $article['authors'] ?? $existing->authors;
                        if (isset($article['pdf_file']) && is_object($article['pdf_file'])) {
                            $existing->pdf_file = $article['pdf_file']->store('sidlak_articles', 'public');
                        }
                        $existing->save();
                    }
                } else {
                    // Add new article
                    $pdfPath = null;
                    if (isset($article['pdf_file']) && is_object($article['pdf_file'])) {
                        $pdfPath = $article['pdf_file']->store('sidlak_articles', 'public');
                    }
                    $journal->articles()->create([
                        'title' => $article['title'] ?? '',
                        'authors' => $article['authors'] ?? '',
                        'pdf_file' => $pdfPath,
                    ]);
                }
            }
        }

        return redirect()->route('sidlak.manage')->with('success', 'Journal updated successfully!');
    }

    public function destroy($id) {
        $journal = SidlakJournal::findOrFail($id);
        $journal->delete();
        return redirect()->route('sidlak.manage')->with('success', 'Journal deleted successfully!');
    }
    public function index() {
        $journals = SidlakJournal::with('articles')
            ->orderByDesc('year')
            ->orderByRaw("STR_TO_DATE(CONCAT('01 ', month, ' ', year), '%d %M %Y') DESC")
            ->get();
        return view('sidlak.index', compact('journals'));
    }

    public function create() {
        return view('sidlak.create');
    }

    public function store(Request $request) {
        $request->validate([
            'title' => 'required|string|max:255',
            'month_year' => 'required|string',
            'print_issn' => 'required|string|regex:/^\d{4}-\d{4}$/',
            'cover_photo' => 'nullable|image|max:2048',
            'articles.*.title' => 'required|string|max:255',
            'articles.*.authors' => 'required|string',
            'articles.*.pdf_file' => 'required|file|mimes:pdf',
            'editors.*.name' => 'required_with:editors.*.title|string|max:255',
            'editors.*.title' => 'required_with:editors.*.name|string|max:255',
            'peer_reviewers.*.name' => 'required_with:peer_reviewers.*.title|string|max:255',
            'peer_reviewers.*.title' => 'required_with:peer_reviewers.*.name|string|max:255',
            'peer_reviewers.*.institution' => 'required_with:peer_reviewers.*.name|string|max:255',
            'peer_reviewers.*.city' => 'required_with:peer_reviewers.*.name|string|max:255',
        ]);

        // Split month_year (YYYY-MM) into month and year
        $month = '';
        $year = '';
        if (preg_match('/^(\d{4})-(\d{2})$/', $request->month_year, $matches)) {
            $year = $matches[1];
            $month = date('F', mktime(0, 0, 0, (int)$matches[2], 10));
        }

        $coverPhotoPath = null;
        if ($request->hasFile('cover_photo')) {
            $coverPhotoPath = $request->file('cover_photo')->store('sidlak_covers', 'public');
        }

        $journal = SidlakJournal::create([
            'title' => $request->title,
            'month' => $month,
            'year' => $year,
            'cover_photo' => $coverPhotoPath,
            'print_issn' => $request->print_issn,
        ]);

        if ($request->articles) {
            foreach ($request->articles as $article) {
                $pdfPath = null;
                if (isset($article['pdf_file']) && is_object($article['pdf_file'])) {
                    $pdfPath = $article['pdf_file']->store('sidlak_articles', 'public');
                }
                SidlakArticle::create([
                    'sidlak_journal_id' => $journal->id,
                    'title' => $article['title'],
                    'authors' => $article['authors'],
                    'pdf_file' => $pdfPath,
                ]);
            }
        }

        // Save editors
        if ($request->editors) {
            foreach ($request->editors as $editor) {
                if (!empty($editor['name']) && !empty($editor['title'])) {
                    $journal->editors()->create([
                        'name' => $editor['name'],
                        'title' => $editor['title'],
                    ]);
                }
            }
        }

        // Save peer reviewers
        if ($request->peer_reviewers) {
            foreach ($request->peer_reviewers as $reviewer) {
                if (!empty($reviewer['name']) && !empty($reviewer['title']) && !empty($reviewer['institution']) && !empty($reviewer['city'])) {
                    $journal->peerReviewers()->create([
                        'name' => $reviewer['name'],
                        'title' => $reviewer['title'],
                        'institution' => $reviewer['institution'],
                        'city' => $reviewer['city'],
                    ]);
                }
            }
        }

        return redirect()->route('sidlak.index')->with('success', 'Journal, editors, peer reviewers, and articles added!');
    }

    public function show($id) {
        $journal = SidlakJournal::with('articles')->findOrFail($id);

        $bookmarkedArticleIds = [];
        $journalBookmarked = false;
        if (Auth::check()) {
            $sf = Auth::user()->studentFaculty ?? null;
            if ($sf) {
                $bookmarkedArticleIds = \App\Models\Bookmark::where('student_faculty_id', $sf->id)
                    ->where('bookmarkable_type', \App\Models\SidlakArticle::class)
                    ->pluck('bookmarkable_id')
                    ->toArray();

                $journalBookmarked = \App\Models\Bookmark::where('student_faculty_id', $sf->id)
                    ->where('bookmarkable_type', \App\Models\SidlakJournal::class)
                    ->where('bookmarkable_id', $journal->id)
                    ->exists();
            }
        }

        return view('sidlak.show', compact('journal', 'bookmarkedArticleIds', 'journalBookmarked'));
    }
}
