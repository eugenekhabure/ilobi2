<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\AnnouncementRead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnnouncementController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of announcements.
     */
    public function index(Request $request)
    {
        $facilityId = Auth::user()->facility_id;

        $query = Announcement::where('facility_id', $facilityId)
            ->with(['creator', 'facility']);

        // Filter by category
        if ($request->has('category') && $request->category != 'all') {
            $query->where('category', $request->category);
        }

        // Filter by status
        if ($request->has('status')) {
            if ($request->status == 'active') {
                $query->active();
            } elseif ($request->status == 'expired') {
                $query->where(function ($q) {
                    $q->where('is_active', false)
                        ->orWhere('expires_at', '<', now());
                });
            }
        }

        $announcements = $query->orderBy('is_pinned', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $categories = [
            'all' => 'All Categories',
            'general' => '📢 General',
            'security' => '🛡️ Security',
            'maintenance' => '🔧 Maintenance',
            'events' => '🎉 Events',
            'emergency' => '🚨 Emergency',
        ];

        $stats = [
            'total' => Announcement::where('facility_id', $facilityId)->count(),
            'active' => Announcement::where('facility_id', $facilityId)->active()->count(),
            'pinned' => Announcement::where('facility_id', $facilityId)->where('is_pinned', true)->count(),
            'expired' => Announcement::where('facility_id', $facilityId)->where(function ($q) {
                $q->where('is_active', false)->orWhere('expires_at', '<', now());
            })->count(),
        ];

        return view('admin.announcements.index', compact('announcements', 'categories', 'stats'));
    }

    /**
     * Show the form for creating a new announcement.
     */
    public function create()
    {
        if (!Auth::user()->organization_id) {
            abort(403, 'Only Client Admin can create announcements.');
        }

        $categories = [
            'general' => '📢 General',
            'security' => '🛡️ Security',
            'maintenance' => '🔧 Maintenance',
            'events' => '🎉 Events',
            'emergency' => '🚨 Emergency',
        ];

        return view('admin.announcements.create', compact('categories'));
    }

    /**
     * Store a newly created announcement.
     */
    public function store(Request $request)
    {
        if (!Auth::user()->organization_id) {
            abort(403, 'Only Client Admin can create announcements.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category' => 'required|in:general,security,maintenance,events,emergency',
            'is_pinned' => 'nullable|boolean',
            'expires_at' => 'nullable|date|after:now',
        ]);

        $announcement = Announcement::create([
            'facility_id' => Auth::user()->facility_id,
            'created_by' => Auth::id(),
            'title' => $request->title,
            'content' => $request->content,
            'category' => $request->category,
            'is_pinned' => $request->has('is_pinned'),
            'expires_at' => $request->expires_at,
            'published_at' => now(),
            'is_active' => true,
        ]);

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Announcement created successfully!');
    }

    /**
     * Display the specified announcement.
     */
    public function show($id)
    {
        $facilityId = Auth::user()->facility_id;

        $announcement = Announcement::where('facility_id', $facilityId)
            ->with(['creator', 'facility'])
            ->findOrFail($id);

        // Mark as read for the current user
        $user = Auth::user();
        AnnouncementRead::markAsRead($announcement->id, $user);

        // Increment view count
        $announcement->increment('view_count');

        return view('admin.announcements.show', compact('announcement'));
    }

    /**
     * Show the form for editing the announcement.
     */
    public function edit($id)
    {
        if (!Auth::user()->organization_id) {
            abort(403, 'Only Client Admin can edit announcements.');
        }

        $facilityId = Auth::user()->facility_id;

        $announcement = Announcement::where('facility_id', $facilityId)
            ->findOrFail($id);

        $categories = [
            'general' => '📢 General',
            'security' => '🛡️ Security',
            'maintenance' => '🔧 Maintenance',
            'events' => '🎉 Events',
            'emergency' => '🚨 Emergency',
        ];

        return view('admin.announcements.edit', compact('announcement', 'categories'));
    }

    /**
     * Update the specified announcement.
     */
    public function update(Request $request, $id)
    {
        if (!Auth::user()->organization_id) {
            abort(403, 'Only Client Admin can edit announcements.');
        }

        $facilityId = Auth::user()->facility_id;

        $announcement = Announcement::where('facility_id', $facilityId)
            ->findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category' => 'required|in:general,security,maintenance,events,emergency',
            'is_pinned' => 'nullable|boolean',
            'expires_at' => 'nullable|date|after:now',
        ]);

        $announcement->update([
            'title' => $request->title,
            'content' => $request->content,
            'category' => $request->category,
            'is_pinned' => $request->has('is_pinned'),
            'expires_at' => $request->expires_at,
        ]);

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Announcement updated successfully!');
    }

    /**
     * Remove the specified announcement.
     */
    public function destroy($id)
    {
        if (!Auth::user()->organization_id) {
            abort(403, 'Only Client Admin can delete announcements.');
        }

        $facilityId = Auth::user()->facility_id;

        $announcement = Announcement::where('facility_id', $facilityId)
            ->findOrFail($id);

        $announcement->delete();

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Announcement deleted successfully!');
    }

    /**
     * Toggle pin status.
     */
    public function togglePin($id)
    {
        if (!Auth::user()->organization_id) {
            abort(403, 'Only Client Admin can pin announcements.');
        }

        $facilityId = Auth::user()->facility_id;

        $announcement = Announcement::where('facility_id', $facilityId)
            ->findOrFail($id);

        $announcement->update([
            'is_pinned' => !$announcement->is_pinned,
        ]);

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Announcement pin status updated!');
    }

    /**
     * Toggle active status.
     */
    public function toggleActive($id)
    {
        if (!Auth::user()->organization_id) {
            abort(403, 'Only Client Admin can manage announcements.');
        }

        $facilityId = Auth::user()->facility_id;

        $announcement = Announcement::where('facility_id', $facilityId)
            ->findOrFail($id);

        $announcement->update([
            'is_active' => !$announcement->is_active,
        ]);

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Announcement status updated!');
    }

    /**
     * Get announcements for PWA.
     */
    public function getPwaAnnouncements(Request $request)
    {
        $facilityId = $request->facility_id ?? Auth::user()->facility_id;

        $announcements = Announcement::where('facility_id', $facilityId)
            ->active()
            ->with(['creator'])
            ->orderBy('is_pinned', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Add read status for current user
        $user = Auth::user();
        foreach ($announcements as $announcement) {
            $announcement->is_read = AnnouncementRead::hasRead($announcement->id, $user);
        }

        return response()->json($announcements);
    }
}