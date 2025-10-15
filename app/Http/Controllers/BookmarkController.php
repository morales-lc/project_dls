<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Bookmark;
use App\Models\MidesDocument;

use Illuminate\Routing\Controller as BaseController;

class BookmarkController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Show bookmarks for the authenticated student/faculty
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

    // Toggle bookmark for a given MidesDocument (polymorphic)
    public function toggle(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
            'type' => 'required|string',
        ]);

        $user = Auth::user();
        $sf = $user->studentFaculty ?? null;
        if (! $sf) {
            return back()->with('error', 'Only students and faculty can bookmark items.');
        }

        $id = $request->id;
        $type = $request->type;

        // Map allowed types to model classes
        $map = [
            'mides' => MidesDocument::class,
            'sidlak' => \App\Models\SidlakArticle::class,
            'sidlak_journal' => \App\Models\SidlakJournal::class,
            'post' => \App\Models\Post::class,
            // Information Literacy posts
            'information_literacy' => \App\Models\InformationLiteracyPost::class,
            // Catalog items
            'catalog' => \App\Models\Catalog::class,
        ];

        if (! isset($map[$type])) {
            return back()->with('error', 'Invalid bookmark type.');
        }

        $modelClass = $map[$type];
        $item = $modelClass::find($id);
        if (! $item) {
            return back()->with('error', 'Item not found.');
        }

        $existing = Bookmark::where('student_faculty_id', $sf->id)
            ->where('bookmarkable_id', $item->id)
            ->where('bookmarkable_type', $modelClass)
            ->first();

        if ($existing) {
            $existing->delete();
            if ($request->expectsJson()) {
                return response()->json(['status' => 'removed', 'message' => 'Removed bookmark.']);
            }
            return back()->with('success', 'Removed bookmark.');
        }

        Bookmark::create([
            'student_faculty_id' => $sf->id,
            'bookmarkable_id' => $item->id,
            'bookmarkable_type' => $modelClass,
        ]);

        if ($request->expectsJson()) {
            return response()->json(['status' => 'bookmarked', 'message' => 'Bookmarked.']);
        }

        return back()->with('success', 'Bookmarked.');
    }
}
