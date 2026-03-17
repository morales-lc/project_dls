<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Bookmark;
use App\Models\MidesDocument;

use Illuminate\Routing\Controller as BaseController;

/**
 * Bookmark Controller
 * 
 * Manages user bookmarks for various content types in the digital library.
 * Supports polymorphic bookmarking for multiple resource types including
 * MIDES documents, SIDLAK articles, catalog items, and alert books.
 * 
 * Only students and faculty can create bookmarks.
 * 
 * @package App\Http\Controllers
 */
class BookmarkController extends BaseController
{
    /**
     * Apply authentication middleware to all methods
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display all bookmarks for the authenticated student/faculty user
     * 
     * Shows a list of all bookmarked items with their associated content.
     * Non-student/faculty users will see an empty list.
     * 
     * @param Request $request HTTP request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // We expect student/faculty have related StudentFaculty model
        $sf = $user->studentFaculty ?? null;
        if (! $sf) {
            // If the logged-in user is not student/faculty, show empty list
            $bookmarks = collect();
        } else {
            $bookmarks = Bookmark::with('bookmarkable')
                ->where('student_faculty_id', $sf->id)
                ->latest()
                ->get();
        }

        return view('bookmarks.index', compact('bookmarks'));
    }

    /**
     * Toggle bookmark status for a resource (add or remove)
     * 
     * Creates a bookmark if it doesn't exist, removes it if it does.
     * Supports polymorphic bookmarking for multiple resource types:
     * - mides: MIDES documents
     * - sidlak: SIDLAK articles
     * - sidlak_journal: SIDLAK journals
     * - post: Posts
     * - information_literacy: Information literacy posts
     * - catalog: Catalog items
     * - alert_book: Alert service books
     * 
     * Only students and faculty can create bookmarks.
     * Supports both AJAX and regular form submissions.
     * 
     * @param Request $request HTTP request with 'id' and 'type' parameters
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function toggle(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
            'type' => 'required|string',
        ]);

        $user = Auth::user();
        $sf = $user->studentFaculty ?? null;
        if (! $sf) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Only students and faculty can bookmark items.'
                ], 403);
            }
            return back()->with('error', 'Only students and faculty can bookmark items.');
        }

        $id = $request->id;
        $type = $request->type;

        // Map bookmark type strings to their corresponding Eloquent model classes
        // This enables polymorphic relationships for different resource types
        $map = [
            'mides' => MidesDocument::class,
            'sidlak' => \App\Models\SidlakArticle::class,
            'sidlak_journal' => \App\Models\SidlakJournal::class,
            'post' => \App\Models\Post::class,
            // Information Literacy posts
            'information_literacy' => \App\Models\InformationLiteracyPost::class,
            // Catalog items
            'catalog' => \App\Models\Catalog::class,
            // Alert Service books
            'alert_book' => \App\Models\AlertBook::class,
        ];

        if (! isset($map[$type])) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid bookmark type.'
                ], 422);
            }
            return back()->with('error', 'Invalid bookmark type.');
        }

        $modelClass = $map[$type];
        $item = $modelClass::find($id);
        if (! $item) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Item not found.'
                ], 404);
            }
            return back()->with('error', 'Item not found.');
        }

        $existing = Bookmark::where('student_faculty_id', $sf->id)
            ->where('bookmarkable_id', $item->id)
            ->where('bookmarkable_type', $modelClass)
            ->first();

        if ($existing) {
            $existing->delete();
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['status' => 'removed', 'message' => 'Removed bookmark.']);
            }
            return back()->with('success', 'Removed bookmark.');
        }

        Bookmark::create([
            'student_faculty_id' => $sf->id,
            'bookmarkable_id' => $item->id,
            'bookmarkable_type' => $modelClass,
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['status' => 'bookmarked', 'message' => 'Bookmarked.']);
        }

        return back()->with('success', 'Bookmarked.');
    }
}
