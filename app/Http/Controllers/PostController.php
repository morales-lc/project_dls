<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Models\LibrarySetting;
use App\Models\LibraryAnnouncement;
use App\Models\LibrarySlideshowImage;
use App\Models\AlertBook;



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

        // Fetch 20 newest alert books ordered by year and month descending
        $latestAlertBooks = AlertBook::orderByDesc('year')
            ->orderByDesc('month')
            ->limit(20)
            ->get();

        // Collect bookmarked AlertBook IDs for authenticated users
        $bookmarkedAlertBookIds = [];
        if (Auth::check()) {
            $sf = Auth::user()->studentFaculty ?? null;
            if ($sf) {
                $bookmarkedAlertBookIds = \App\Models\Bookmark::where('student_faculty_id', $sf->id)
                    ->where('bookmarkable_type', AlertBook::class)
                    ->pluck('bookmarkable_id')
                    ->toArray();
            }
        }

        return view('dashboard', [
            'posts' => $posts,
            'bookmarkedPostIds' => $bookmarkedPostIds,
            'librarySettings' => $settings,
            'libraryAnnouncements' => $announcements,
            'slideshowImages' => $slideshowImages,
            'latestAlertBooks' => $latestAlertBooks,
            'bookmarkedAlertBookIds' => $bookmarkedAlertBookIds,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|string|in:Announcement,Event,Update,Post',
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:15000',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'youtube_link' => 'nullable|url|max:500',
            'website_link' => 'nullable|url|max:500',
            'media_type' => 'required|in:image,youtube,website',
        ], [
            'type.required' => 'Post type is required.',
            'type.in' => 'Invalid post type selected.',
            'title.required' => 'Title is required.',
            'title.max' => 'Title cannot exceed 255 characters.',
            'description.required' => 'Description is required.',
            'description.max' => 'Description cannot exceed 15000 characters.',
            'photo.image' => 'The file must be an image.',
            'photo.mimes' => 'Image must be a JPEG, PNG, JPG, or GIF file.',
            'photo.max' => 'Image size cannot exceed 5MB.',
            'youtube_link.url' => 'YouTube link must be a valid URL.',
            'youtube_link.max' => 'YouTube link cannot exceed 500 characters.',
            'website_link.url' => 'Website link must be a valid URL.',
            'website_link.max' => 'Website link cannot exceed 500 characters.',
            'media_type.required' => 'Please select a media type.',
            'media_type.in' => 'Invalid media type selected.',
        ]);

        $sanitizedDescription = $this->sanitizePostDescription($request->description);
        if ($sanitizedDescription === '') {
            return back()->withInput()->withErrors(['description' => 'Description is required.']);
        }

        $photoPath = null;
        $hasPhoto = $request->hasFile('photo');
        $hasYoutube = $request->filled('youtube_link');
        $hasWebsite = $request->filled('website_link');
        
        // Validate that the appropriate media field is provided based on media_type
        if ($request->media_type === 'image' && !$hasPhoto) {
            return back()->withInput()->withErrors(['photo' => 'Please upload an image file.']);
        }
        if ($request->media_type === 'youtube' && !$hasYoutube) {
            return back()->withInput()->withErrors(['youtube_link' => 'Please provide a YouTube link.']);
        }
        if ($request->media_type === 'website' && !$hasWebsite) {
            return back()->withInput()->withErrors(['website_link' => 'Please provide a website link.']);
        }
        
        // Validate YouTube link format if provided
        if ($hasYoutube) {
            $videoId = $this->extractYouTubeVideoId($request->youtube_link);
            if (!$videoId) {
                return back()->withInput()->withErrors(['youtube_link' => 'Invalid YouTube URL format. Please provide a valid YouTube video link (e.g., https://www.youtube.com/watch?v=VIDEO_ID or https://youtu.be/VIDEO_ID).']);
            }
            
            // Check if the YouTube video is accessible
            if (!$this->isYouTubeVideoAccessible($videoId)) {
                return back()->withInput()->withErrors(['youtube_link' => 'This YouTube video is unavailable or does not exist. Please check the video ID and try again.']);
            }
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
            'description' => $sanitizedDescription,
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
            'type' => 'required|string|in:Announcement,Event,Update,Post',
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:15000',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'youtube_link' => 'nullable|url|max:500',
            'website_link' => 'nullable|url|max:500',
            'media_type' => 'required|in:image,youtube,website',
        ], [
            'type.required' => 'Post type is required.',
            'type.in' => 'Invalid post type selected.',
            'title.required' => 'Title is required.',
            'title.max' => 'Title cannot exceed 255 characters.',
            'description.required' => 'Description is required.',
            'description.max' => 'Description cannot exceed 15000 characters.',
            'photo.image' => 'The file must be an image.',
            'photo.mimes' => 'Image must be a JPEG, PNG, JPG, or GIF file.',
            'photo.max' => 'Image size cannot exceed 5MB.',
            'youtube_link.url' => 'YouTube link must be a valid URL.',
            'youtube_link.max' => 'YouTube link cannot exceed 500 characters.',
            'website_link.url' => 'Website link must be a valid URL.',
            'website_link.max' => 'Website link cannot exceed 500 characters.',
            'media_type.required' => 'Please select a media type.',
            'media_type.in' => 'Invalid media type selected.',
        ]);

        $sanitizedDescription = $this->sanitizePostDescription($request->description);
        if ($sanitizedDescription === '') {
            return back()->withInput()->withErrors(['description' => 'Description is required.']);
        }
        
        // Validate YouTube link format if provided
        if ($request->filled('youtube_link')) {
            $videoId = $this->extractYouTubeVideoId($request->youtube_link);
            if (!$videoId) {
                return back()->withInput()->withErrors(['youtube_link' => 'Invalid YouTube URL format. Please provide a valid YouTube video link (e.g., https://www.youtube.com/watch?v=VIDEO_ID or https://youtu.be/VIDEO_ID).']);
            }
            
            // Check if the YouTube video is accessible
            if (!$this->isYouTubeVideoAccessible($videoId)) {
                return back()->withInput()->withErrors(['youtube_link' => 'This YouTube video is unavailable or does not exist. Please check the video ID and try again.']);
            }
        }

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
            'description' => $sanitizedDescription,
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

    /**
     * Sanitize rich text HTML from Quill before persisting.
     */
    private function sanitizePostDescription(?string $description): string
    {
        $description = trim((string) $description);
        if ($description === '') {
            return '';
        }

        $allowedTags = ['p', 'br', 'strong', 'em', 'u', 's', 'ol', 'ul', 'li', 'a', 'blockquote', 'code', 'pre', 'h1', 'h2', 'h3'];
        $allowedAttributes = [
            'a' => ['href', 'target', 'rel'],
        ];

        $dom = new \DOMDocument('1.0', 'UTF-8');
        $wrappedHtml = '<div id="post-description-root">' . $description . '</div>';

        $internalErrors = libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="utf-8" ?>' . $wrappedHtml, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
        libxml_use_internal_errors($internalErrors);

        $root = $dom->getElementById('post-description-root');
        if (!$root) {
            return '';
        }

        $this->sanitizeHtmlNode($root, $allowedTags, $allowedAttributes);

        $cleanHtml = '';
        foreach ($root->childNodes as $child) {
            $cleanHtml .= $dom->saveHTML($child);
        }

        $cleanHtml = trim($cleanHtml);
        if (trim(strip_tags($cleanHtml)) === '') {
            return '';
        }

        return $cleanHtml;
    }

    private function sanitizeHtmlNode(\DOMNode $node, array $allowedTags, array $allowedAttributes): void
    {
        if ($node->nodeType === XML_COMMENT_NODE) {
            $node->parentNode?->removeChild($node);
            return;
        }

        if ($node instanceof \DOMElement) {
            $tagName = strtolower($node->tagName);
            if (!in_array($tagName, $allowedTags, true) && $tagName !== 'div') {
                $parent = $node->parentNode;
                if ($parent) {
                    $removeEntirely = in_array($tagName, ['script', 'style', 'iframe', 'object', 'embed'], true);
                    if (!$removeEntirely) {
                        while ($node->firstChild) {
                            $parent->insertBefore($node->firstChild, $node);
                        }
                    }
                    $parent->removeChild($node);
                }
                return;
            }

            if ($tagName !== 'div' && $node->hasAttributes()) {
                $allowedForTag = $allowedAttributes[$tagName] ?? [];
                $attrsToRemove = [];

                foreach ($node->attributes as $attribute) {
                    $attrName = strtolower($attribute->nodeName);
                    if (str_starts_with($attrName, 'on') || !in_array($attrName, $allowedForTag, true)) {
                        $attrsToRemove[] = $attribute->nodeName;
                    }
                }

                foreach ($attrsToRemove as $attrName) {
                    $node->removeAttribute($attrName);
                }
            }

            if ($tagName === 'a') {
                $href = trim((string) $node->getAttribute('href'));
                if ($href === '') {
                    $node->removeAttribute('href');
                } else {
                    $scheme = parse_url($href, PHP_URL_SCHEME);
                    if ($scheme !== null && !in_array(strtolower($scheme), ['http', 'https', 'mailto'], true)) {
                        $node->removeAttribute('href');
                    }
                }

                $target = strtolower((string) $node->getAttribute('target'));
                if (!in_array($target, ['', '_blank', '_self'], true)) {
                    $node->removeAttribute('target');
                    $target = '';
                }

                if ($target === '_blank') {
                    $existingRel = trim((string) $node->getAttribute('rel'));
                    $relParts = $existingRel === '' ? [] : preg_split('/\s+/', $existingRel);
                    $relParts = array_filter(array_unique(array_map('strtolower', $relParts ?: [])));

                    if (!in_array('noopener', $relParts, true)) {
                        $relParts[] = 'noopener';
                    }
                    if (!in_array('noreferrer', $relParts, true)) {
                        $relParts[] = 'noreferrer';
                    }

                    $node->setAttribute('rel', implode(' ', $relParts));
                } else {
                    $node->removeAttribute('rel');
                }
            }
        }

        $children = [];
        foreach ($node->childNodes as $child) {
            $children[] = $child;
        }

        foreach ($children as $child) {
            $this->sanitizeHtmlNode($child, $allowedTags, $allowedAttributes);
        }
    }
    
    /**
     * Extract YouTube video ID from various YouTube URL formats
     * 
     * Accepts the following YouTube URL formats:
     * - https://www.youtube.com/watch?v=VIDEO_ID
     * - https://youtube.com/watch?v=VIDEO_ID
     * - http://www.youtube.com/watch?v=VIDEO_ID
     * - https://youtu.be/VIDEO_ID
     * - https://www.youtube.com/embed/VIDEO_ID
     * - https://www.youtube.com/v/VIDEO_ID
     * 
     * Video IDs must be 11 characters long (alphanumeric, dash, underscore)
     * 
     * @param string $url The YouTube URL
     * @return string|null The video ID if valid, null otherwise
     */
    private function extractYouTubeVideoId($url)
    {
        // YouTube video ID pattern: 11 characters, alphanumeric plus dash and underscore
        $patterns = [
            // Standard watch URL: youtube.com/watch?v=VIDEO_ID
            '/^(?:https?:\/\/)?(?:www\.)?youtube\.com\/watch\?v=([a-zA-Z0-9_-]{11})(?:&.*)?$/',
            // Short URL: youtu.be/VIDEO_ID
            '/^(?:https?:\/\/)?(?:www\.)?youtu\.be\/([a-zA-Z0-9_-]{11})(?:\?.*)?$/',
            // Embed URL: youtube.com/embed/VIDEO_ID
            '/^(?:https?:\/\/)?(?:www\.)?youtube\.com\/embed\/([a-zA-Z0-9_-]{11})(?:\?.*)?$/',
            // Old-style v URL: youtube.com/v/VIDEO_ID
            '/^(?:https?:\/\/)?(?:www\.)?youtube\.com\/v\/([a-zA-Z0-9_-]{11})(?:\?.*)?$/',
        ];
        
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $url, $matches)) {
                // $matches[1] contains the video ID
                return $matches[1];
            }
        }
        
        return null;
    }
    
    /**
     * Check if a YouTube video is accessible by verifying the oEmbed endpoint
     * 
     * Uses YouTube's oEmbed API to verify the video exists and is accessible.
     * This is more reliable than checking the video page directly.
     * 
     * @param string $videoId The YouTube video ID (11 characters)
     * @return bool True if video is accessible, false otherwise
     */
    private function isYouTubeVideoAccessible($videoId)
    {
        // Use YouTube oEmbed API to check if video exists and is accessible
        $oembedUrl = "https://www.youtube.com/oembed?url=https://www.youtube.com/watch?v={$videoId}&format=json";
        
        try {
            // Set a short timeout to avoid long waits
            $context = stream_context_create([
                'http' => [
                    'timeout' => 5, // 5 seconds timeout
                    'ignore_errors' => true,
                ],
            ]);
            
            $response = @file_get_contents($oembedUrl, false, $context);
            
            // Check if we got a valid response
            if ($response === false) {
                return false;
            }
            
            // Parse the JSON response
            $data = json_decode($response, true);
            
            // If we got valid data with a title, the video is accessible
            return !empty($data) && isset($data['title']);
            
        } catch (\Exception $e) {
            // If any error occurs, consider the video inaccessible
            return false;
        }
    }
}
