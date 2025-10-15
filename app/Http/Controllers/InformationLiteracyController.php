<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InformationLiteracyPost;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class InformationLiteracyController extends Controller
{
    // List all posts
    public function index()
    {
        $posts = InformationLiteracyPost::orderBy('date_time', 'desc')->get();

        // compute bookmarked ids for authenticated student's faculty record
        $bookmarkedIds = [];
        if (Auth::check()) {
            $user = Auth::user();
            $sf = $user->studentFaculty ?? null;
            if ($sf) {
                $bookmarkedIds = \App\Models\Bookmark::where('student_faculty_id', $sf->id)
                    ->where('bookmarkable_type', \App\Models\InformationLiteracyPost::class)
                    ->pluck('bookmarkable_id')
                    ->toArray();
            }
        }

        return view('information_literacy.index', compact('posts', 'bookmarkedIds'));
    }

    // Management list with filters, sorting, and pagination
    public function manage(Request $request)
    {
        $query = InformationLiteracyPost::query();

        // search by title or facilitators
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('facilitators', 'like', "%{$search}%");
            });
        }

        // filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        // sorting
        $sort = $request->input('sort', 'date_time');
        $direction = $request->input('direction', 'desc');
        $allowedSorts = ['date_time', 'title'];
        if (!in_array($sort, $allowedSorts)) {
            $sort = 'date_time';
        }
        $direction = $direction === 'asc' ? 'asc' : 'desc';
        $query->orderBy($sort, $direction);

        $posts = $query->paginate(10)->appends($request->except('page'));

        return view('information_literacy.manage', compact('posts'));
    }

    // Show create form
    public function create()
    {
        return view('information_literacy.create');
    }

    // Store new post
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'date_time' => 'required|date',
            'facilitators' => 'required|string',
            'type' => 'required|in:onsite,online',
            'image' => 'nullable|image|max:4096',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('information_literacy_images', 'public');
        }

        InformationLiteracyPost::create([
            'title' => $request->title,
            'description' => $request->description,
            'date_time' => $request->date_time,
            'facilitators' => $request->facilitators,
            'type' => $request->type,
            'image' => $imagePath,
        ]);

        return redirect()->route('information_literacy.index')->with('success', 'Information Literacy post created!');
    }

    // Show edit form
    public function edit($id)
    {
        $post = InformationLiteracyPost::findOrFail($id);
        return view('information_literacy.edit', compact('post'));
    }

    // Update post
    public function update(Request $request, $id)
    {
        $post = InformationLiteracyPost::findOrFail($id);
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'date_time' => 'required|date',
            'facilitators' => 'required|string',
            'type' => 'required|in:onsite,online',
            'image' => 'nullable|image|max:4096',
        ]);

        $imagePath = $post->image;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('information_literacy_images', 'public');
        }

        $post->update([
            'title' => $request->title,
            'description' => $request->description,
            'date_time' => $request->date_time,
            'facilitators' => $request->facilitators,
            'type' => $request->type,
            'image' => $imagePath,
        ]);

        return redirect()->route('information_literacy.index')->with('success', 'Information Literacy post updated!');
    }

    // Delete post
    public function destroy($id)
    {
        $post = InformationLiteracyPost::findOrFail($id);
        if ($post->image) {
            Storage::disk('public')->delete($post->image);
        }
        $post->delete();
        return redirect()->route('information_literacy.index')->with('success', 'Information Literacy post deleted!');
    }
}
