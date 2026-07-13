<?php

namespace App\Http\Controllers;

use App\Models\CommunityPost;
use App\Models\CommunityComment;
use App\Models\CommunityLike;
use App\Models\Person;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CommunityController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of community posts.
     */
    public function index(Request $request)
    {
        $facilityId = Auth::user()->facility_id;

        $query = CommunityPost::where('facility_id', $facilityId)
            ->with(['author', 'facility']);

        // Filter by type
        if ($request->has('type') && $request->type != 'all') {
            $query->where('type', $request->type);
        }

        // Filter by status
        if ($request->has('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        // Show only published posts for residents
        $user = Auth::user();
        if ($user->person && $user->person->residentProfile) {
            $query->where('status', 'published');
        }

        $posts = $query->orderBy('is_featured', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $stats = [
            'total' => CommunityPost::where('facility_id', $facilityId)->count(),
            'published' => CommunityPost::where('facility_id', $facilityId)->where('status', 'published')->count(),
            'pending' => CommunityPost::where('facility_id', $facilityId)->where('status', 'pending')->count(),
            'archived' => CommunityPost::where('facility_id', $facilityId)->where('status', 'archived')->count(),
        ];

        $types = [
            'all' => 'All Types',
            'announcement' => '📢 Announcement',
            'event' => '🎉 Event',
            'classified' => '🏷️ Classified',
            'lost_found' => '🔍 Lost & Found',
            'general' => '📝 General',
        ];

        $statuses = [
            'all' => 'All Statuses',
            'published' => 'Published',
            'pending' => 'Pending',
            'rejected' => 'Rejected',
            'archived' => 'Archived',
        ];

        return view('admin.community.index', compact('posts', 'stats', 'types', 'statuses'));
    }

    /**
     * Show the form for creating a new post.
     */
    public function create()
    {
        $types = [
            'announcement' => '📢 Announcement',
            'event' => '🎉 Event',
            'classified' => '🏷️ Classified',
            'lost_found' => '🔍 Lost & Found',
            'general' => '📝 General',
        ];

        return view('admin.community.create', compact('types'));
    }

    /**
     * Store a newly created post.
     */
    public function store(Request $request)
    {
        $facilityId = Auth::user()->facility_id;
        $user = Auth::user();

        // Get the person (resident) associated with the user
        $person = $user->person;
        if (!$person) {
            return back()->withErrors(['error' => 'No resident profile found for your account.']);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|in:announcement,event,classified,lost_found,general',
            'featured_image' => 'nullable|image|max:5120',
            'event_date' => 'nullable|date',
            'location' => 'nullable|string|max:255',
        ]);

        // Handle image upload
        $imagePath = null;
        if ($request->hasFile('featured_image')) {
            $imagePath = $request->file('featured_image')->store('community', 'public');
        }

        // Determine status (auto-publish for admins, pending for residents)
        $status = 'pending';
        if ($user->organization_id) {
            $status = 'published';
        }

        $post = CommunityPost::create([
            'facility_id' => $facilityId,
            'author_id' => $person->id,
            'title' => $request->title,
            'content' => $request->content,
            'type' => $request->type,
            'status' => $status,
            'featured_image' => $imagePath,
            'is_featured' => $request->has('is_featured'),
            'published_at' => $status === 'published' ? now() : null,
            'event_date' => $request->event_date,
            'location' => $request->location,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Post created successfully!',
                'post' => $post,
            ], 201);
        }

        return redirect()->route('admin.community.index')
            ->with('success', 'Post created successfully!');
    }

    /**
     * Display the specified post.
     */
    public function show($id)
    {
        $facilityId = Auth::user()->facility_id;

        $post = CommunityPost::where('facility_id', $facilityId)
            ->with(['author', 'comments.author', 'comments.replies'])
            ->findOrFail($id);

        // Increment view count
        $post->incrementViews();

        // Check if user has liked the post
        $user = Auth::user();
        $hasLiked = false;
        if ($user->person) {
            $hasLiked = $post->hasLiked($user->person->id);
        }

        return view('admin.community.show', compact('post', 'hasLiked'));
    }

    /**
     * Show the form for editing a post.
     */
    public function edit($id)
    {
        $facilityId = Auth::user()->facility_id;

        $post = CommunityPost::where('facility_id', $facilityId)
            ->findOrFail($id);

        $types = [
            'announcement' => '📢 Announcement',
            'event' => '🎉 Event',
            'classified' => '🏷️ Classified',
            'lost_found' => '🔍 Lost & Found',
            'general' => '📝 General',
        ];

        return view('admin.community.edit', compact('post', 'types'));
    }

    /**
     * Update a post.
     */
    public function update(Request $request, $id)
    {
        $facilityId = Auth::user()->facility_id;

        $post = CommunityPost::where('facility_id', $facilityId)
            ->findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|in:announcement,event,classified,lost_found,general',
            'featured_image' => 'nullable|image|max:5120',
            'event_date' => 'nullable|date',
            'location' => 'nullable|string|max:255',
        ]);

        $updateData = [
            'title' => $request->title,
            'content' => $request->content,
            'type' => $request->type,
            'is_featured' => $request->has('is_featured'),
            'event_date' => $request->event_date,
            'location' => $request->location,
        ];

        // Handle image upload
        if ($request->hasFile('featured_image')) {
            // Delete old image
            if ($post->featured_image) {
                Storage::disk('public')->delete($post->featured_image);
            }
            $updateData['featured_image'] = $request->file('featured_image')->store('community', 'public');
        }

        $post->update($updateData);

        return redirect()->route('admin.community.show', $post->id)
            ->with('success', 'Post updated successfully!');
    }

    /**
     * Delete a post.
     */
    public function destroy($id)
    {
        $facilityId = Auth::user()->facility_id;

        $post = CommunityPost::where('facility_id', $facilityId)
            ->findOrFail($id);

        // Delete featured image
        if ($post->featured_image) {
            Storage::disk('public')->delete($post->featured_image);
        }

        $post->delete();

        return redirect()->route('admin.community.index')
            ->with('success', 'Post deleted successfully!');
    }

    /**
     * Update post status.
     */
    public function updateStatus(Request $request, $id)
    {
        $facilityId = Auth::user()->facility_id;

        $post = CommunityPost::where('facility_id', $facilityId)
            ->findOrFail($id);

        $request->validate([
            'status' => 'required|in:pending,published,rejected,archived',
        ]);

        $post->update([
            'status' => $request->status,
            'published_at' => $request->status === 'published' ? now() : $post->published_at,
        ]);

        return redirect()->route('admin.community.index')
            ->with('success', 'Post status updated successfully!');
    }

    /**
     * Toggle featured status.
     */
    public function toggleFeatured($id)
    {
        $facilityId = Auth::user()->facility_id;

        $post = CommunityPost::where('facility_id', $facilityId)
            ->findOrFail($id);

        $post->update([
            'is_featured' => !$post->is_featured,
        ]);

        return redirect()->route('admin.community.index')
            ->with('success', 'Featured status updated!');
    }

    /**
     * Add a comment to a post.
     */
    public function addComment(Request $request, $id)
    {
        $facilityId = Auth::user()->facility_id;
        $user = Auth::user();

        $post = CommunityPost::where('facility_id', $facilityId)
            ->findOrFail($id);

        $person = $user->person;
        if (!$person) {
            return back()->withErrors(['error' => 'No profile found.']);
        }

        $request->validate([
            'content' => 'required|string|max:500',
            'parent_id' => 'nullable|exists:community_comments,id',
        ]);

        $comment = CommunityComment::create([
            'post_id' => $post->id,
            'author_id' => $person->id,
            'parent_id' => $request->parent_id,
            'content' => $request->content,
            'is_approved' => true,
        ]);

        // Increment comment count
        $post->increment('comment_count');

        return redirect()->route('admin.community.show', $post->id)
            ->with('success', 'Comment added successfully!');
    }

    /**
     * Delete a comment.
     */
    public function deleteComment($id)
    {
        $comment = CommunityComment::findOrFail($id);

        // Check if user has permission
        $user = Auth::user();
        if (!$user->organization_id && $comment->author_id !== $user->person->id) {
            abort(403);
        }

        $postId = $comment->post_id;
        $comment->delete();

        // Decrement comment count
        CommunityPost::where('id', $postId)->decrement('comment_count');

        return redirect()->route('admin.community.show', $postId)
            ->with('success', 'Comment deleted successfully!');
    }

    /**
     * Like a post.
     */
    public function likePost($id)
    {
        $facilityId = Auth::user()->facility_id;
        $user = Auth::user();

        $post = CommunityPost::where('facility_id', $facilityId)
            ->findOrFail($id);

        $person = $user->person;
        if (!$person) {
            return response()->json(['error' => 'No profile found.'], 422);
        }

        // Check if already liked
        $existingLike = CommunityLike::where('post_id', $post->id)
            ->where('person_id', $person->id)
            ->first();

        if ($existingLike) {
            $existingLike->delete();
            $post->decrement('like_count');
            $liked = false;
        } else {
            CommunityLike::create([
                'post_id' => $post->id,
                'person_id' => $person->id,
            ]);
            $post->increment('like_count');
            $liked = true;
        }

        if (request()->wantsJson()) {
            return response()->json([
                'liked' => $liked,
                'like_count' => $post->like_count,
            ]);
        }

        return redirect()->route('admin.community.show', $post->id);
    }

    /**
     * Get posts for PWA.
     */
    public function getPwaPosts(Request $request)
    {
        $facilityId = $request->facility_id ?? Auth::user()->facility_id;

        $posts = CommunityPost::where('facility_id', $facilityId)
            ->where('status', 'published')
            ->with(['author', 'facility'])
            ->orderBy('is_featured', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        // Add like status for current user
        $user = Auth::user();
        if ($user && $user->person) {
            foreach ($posts as $post) {
                $post->has_liked = $post->hasLiked($user->person->id);
            }
        }

        return response()->json($posts);
    }

    /**
     * Get post details for PWA.
     */
    public function getPwaPost($id)
    {
        $post = CommunityPost::where('status', 'published')
            ->with(['author', 'comments.author'])
            ->findOrFail($id);

        $post->incrementViews();

        $user = Auth::user();
        if ($user && $user->person) {
            $post->has_liked = $post->hasLiked($user->person->id);
        }

        return response()->json($post);
    }
}