<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::latest()->get();

        // Eager-load bookmarked post IDs for the authenticated student/faculty to avoid per-item DB checks in Blade
        $bookmarkedPostIds = [];
        if (Auth::check()) {
            $sf = Auth::user()->studentFaculty ?? null;
            if ($sf) {
                $bookmarkedPostIds = \App\Models\Bookmark::where('student_faculty_id', $sf->id)
                    ->where('bookmarkable_type', Post::class)
                    ->pluck('bookmarkable_id')
                    ->toArray();
            }
        }

        return view('dashboard', ['posts' => $posts, 'bookmarkedPostIds' => $bookmarkedPostIds]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|string|max:50',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'photo' => 'nullable|image|max:2048',
            'youtube_link' => 'nullable|url',
            'website_link' => 'nullable|url',
            'og_image' => 'nullable|url',
        ]);


        $photoPath = null;
        $hasPhoto = $request->hasFile('photo');
        $hasYoutube = $request->filled('youtube_link');
        $hasWebsite = $request->filled('website_link');
        if (!$hasPhoto && !$hasYoutube && !$hasWebsite) {
            return back()->withInput()->withErrors(['media_type' => 'You must provide an image, YouTube link, or website link.']);
        }
        if ($hasPhoto) {
            $photoPath = $request->file('photo')->store('posts', 'public');
        }

        $ogImage = $request->og_image;
        // If website_link is provided and og_image is not, try to fetch OG image
        if ($request->website_link && !$ogImage) {
            try {
                $html = @file_get_contents($request->website_link);
                if ($html) {
                    preg_match('/<meta[^>]+property=["\']og:image["\'][^>]+content=["\']([^"\']+)["\']/i', $html, $matches);
                    if (!empty($matches[1])) {
                        $ogImage = $matches[1];
                    } else {
                        // Try alternate OG image meta format
                        preg_match('/<meta[^>]+content=["\']([^"\']+)["\'][^>]+property=["\']og:image["\']/i', $html, $matches2);
                        if (!empty($matches2[1])) {
                            $ogImage = $matches2[1];
                        }
                    }
                }
            } catch (\Exception $e) {
                // Ignore errors, fallback to null
            }
        }

        Post::create([
            'type' => $request->type,
            'title' => $request->title,
            'description' => $request->description,
            'photo' => $photoPath,
            'youtube_link' => $request->youtube_link,
            'website_link' => $request->website_link,
            'og_image' => $ogImage,
        ]);

    return redirect()->route('post.management')->with('success', 'Post created successfully!');
    }

    public function adminManagement()
    {
        $posts = Post::latest()->get();
        return view('admin-posts-management', compact('posts'));
    }

    public function edit($id)
    {
        $post = Post::findOrFail($id);
        return view('posts-edit', compact('post'));
    }

    public function update(Request $request, $id)
    {
        $post = Post::findOrFail($id);
        $request->validate([
            'type' => 'required|string|max:50',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'photo' => 'nullable|image|max:2048',
            'youtube_link' => 'nullable|url',
            'website_link' => 'nullable|url',
            'og_image' => 'nullable|url',
        ]);

        $photoPath = $post->photo;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('posts', 'public');
        }

        $ogImage = $request->og_image;
        // If website_link is provided and og_image is not, try to fetch OG image
        if ($request->website_link && !$ogImage) {
            try {
                $html = @file_get_contents($request->website_link);
                if ($html) {
                    preg_match('/<meta[^>]+property=["\']og:image["\'][^>]+content=["\']([^"\']+)["\']/i', $html, $matches);
                    if (!empty($matches[1])) {
                        $ogImage = $matches[1];
                    } else {
                        // Try alternate OG image meta format
                        preg_match('/<meta[^>]+content=["\']([^"\']+)["\'][^>]+property=["\']og:image["\']/i', $html, $matches2);
                        if (!empty($matches2[1])) {
                            $ogImage = $matches2[1];
                        }
                    }
                }
            } catch (\Exception $e) {
                // Ignore errors, fallback to null
            }
        }

        $post->update([
            'type' => $request->type,
            'title' => $request->title,
            'description' => $request->description,
            'photo' => $photoPath,
            'youtube_link' => $request->youtube_link,
            'website_link' => $request->website_link,
            'og_image' => $ogImage,
        ]);

        return redirect()->route('post.management')->with('success', 'Post updated successfully!');
    }

    public function destroy($id)
    {
        $post = Post::findOrFail($id);
        if ($post->photo) {
            Storage::disk('public')->delete($post->photo);
        }
        $post->delete();
        return redirect()->route('admin.posts.management')->with('success', 'Post deleted successfully!');
    }

    public function postManagement()
    {
        $posts = Post::latest()->get();
        return view('post-management', compact('posts'));
    }

    // Return a JSON representation of a post for client-side modal population
    public function showJson($id)
    {
        $post = Post::findOrFail($id);
        return response()->json([
            'id' => $post->id,
            'title' => $post->title,
            'type' => $post->type,
            'description' => $post->description,
            'photo' => $post->photo ? asset('storage/' . $post->photo) : null,
            'youtube_link' => $post->youtube_link,
            'website_link' => $post->website_link,
            'og_image' => $post->og_image,
        ]);
    }
}
