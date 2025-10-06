<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InformationLiteracyPost;
use Illuminate\Support\Facades\Storage;

class InformationLiteracyController extends Controller
{
    // List all posts
    public function index()
    {
        $posts = InformationLiteracyPost::orderBy('date_time', 'desc')->get();
        return view('information_literacy.index', compact('posts'));
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
