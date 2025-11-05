<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Models\LibrarySetting;
use App\Models\LibraryAnnouncement;
use App\Models\LibrarySlideshowImage;

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

        // Load library settings and active announcements for the dashboard section
        $settings = LibrarySetting::singleton();
        $announcements = LibraryAnnouncement::where('active', true)->orderBy('position')->get();
        $slideshowImages = LibrarySlideshowImage::active()->get();

        return view('dashboard', [
            'posts' => $posts,
            'bookmarkedPostIds' => $bookmarkedPostIds,
            'librarySettings' => $settings,
            'libraryAnnouncements' => $announcements,
            'slideshowImages' => $slideshowImages,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|string|max:50',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
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
        $returnUrl = $request->input('return_url');
        return $returnUrl
            ? redirect($returnUrl)->with('success', 'Post created successfully!')
            : redirect()->route('post.management')->with('success', 'Post created successfully!');
    }

    public function adminManagement()
    {
        $posts = Post::latest()->get();
        return view('admin-posts-management', compact('posts'));
    }

    public function create()
    {
        return view('posts-create');
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
            'description' => 'nullable|string',
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
        $returnUrl = $request->input('return_url');
        return $returnUrl
            ? redirect($returnUrl)->with('success', 'Post updated successfully!')
            : redirect()->route('post.management')->with('success', 'Post updated successfully!');
    }

    public function destroy(Request $request, $id)
    {
        $post = Post::findOrFail($id);
        if ($post->photo) {
            Storage::disk('public')->delete($post->photo);
        }
        $post->delete();
        $returnUrl = $request->input('return_url');
        return $returnUrl
            ? redirect($returnUrl)->with('success', 'Post deleted successfully!')
            : redirect()->back()->with('success', 'Post deleted successfully!');
    }

    public function postManagement(Request $request)
    {
        $types = ['Announcement', 'Event', 'Update', 'Post'];
        $paginatedPosts = [];

        foreach ($types as $type) {
            $paginatedPosts[$type] = Post::where('type', $type)
                ->latest()
                ->paginate(5, ['*'], strtolower($type) . '_page') // separate page query key
                ->appends($request->except(strtolower($type) . '_page')); // preserve filters per tab
        }

        return view('post-management', [
            'types' => $types,
            'paginatedPosts' => $paginatedPosts,
        ]);
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
