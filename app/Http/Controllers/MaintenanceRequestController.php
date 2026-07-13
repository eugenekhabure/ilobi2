<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceRequest;
use App\Models\MaintenanceCategory;
use App\Models\MaintenanceComment;
use App\Models\Person;
use App\Models\Facility;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MaintenanceRequestController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
        $this->middleware('auth');
    }

    /**
     * Display a listing of maintenance requests.
     */
    public function index(Request $request)
    {
        $facilityId = Auth::user()->facility_id;
        $user = Auth::user();

        $query = MaintenanceRequest::where('facility_id', $facilityId)
            ->with(['category', 'requester', 'assignee']);

        // Filter by status
        if ($request->has('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        // Filter by priority
        if ($request->has('priority') && $request->priority != 'all') {
            $query->where('priority', $request->priority);
        }

        // Filter by category
        if ($request->has('category') && $request->category != 'all') {
            $query->where('category_id', $request->category);
        }

        // If user is a resident, show only their requests
        if ($user->person && $user->person->residentProfile) {
            $query->where('requested_by', $user->person->id);
        }

        $requests = $query->orderBy('created_at', 'desc')
            ->paginate(20);

        $categories = MaintenanceCategory::where('facility_id', $facilityId)
            ->where('is_active', true)
            ->get();

        $stats = [
            'total' => MaintenanceRequest::where('facility_id', $facilityId)->count(),
            'pending' => MaintenanceRequest::where('facility_id', $facilityId)->where('status', 'pending')->count(),
            'in_progress' => MaintenanceRequest::where('facility_id', $facilityId)->where('status', 'in_progress')->count(),
            'completed' => MaintenanceRequest::where('facility_id', $facilityId)->where('status', 'completed')->count(),
            'emergency' => MaintenanceRequest::where('facility_id', $facilityId)->where('priority', 'emergency')->count(),
        ];

        $statuses = [
            'all' => 'All Statuses',
            'pending' => 'Pending',
            'assigned' => 'Assigned',
            'in_progress' => 'In Progress',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
        ];

        $priorities = [
            'all' => 'All Priorities',
            'low' => 'Low',
            'medium' => 'Medium',
            'high' => 'High',
            'emergency' => 'Emergency',
        ];

        return view('admin.maintenance.index', compact('requests', 'categories', 'stats', 'statuses', 'priorities'));
    }

    /**
     * Show the form for creating a new maintenance request.
     */
    public function create()
    {
        $facilityId = Auth::user()->facility_id;

        $categories = MaintenanceCategory::where('facility_id', $facilityId)
            ->where('is_active', true)
            ->get();

        $priorities = [
            'low' => 'Low',
            'medium' => 'Medium',
            'high' => 'High',
            'emergency' => 'Emergency',
        ];

        $units = $this->getUserUnits();

        return view('admin.maintenance.create', compact('categories', 'priorities', 'units'));
    }

    /**
     * Store a newly created maintenance request.
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
            'category_id' => 'required|exists:maintenance_categories,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:low,medium,high,emergency',
            'unit_number' => 'nullable|string|max:50',
            'block_name' => 'nullable|string|max:50',
            'photo' => 'nullable|image|max:5120',
        ]);

        // Handle photo upload
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('maintenance', 'public');
        }

        $maintenanceRequest = MaintenanceRequest::create([
            'facility_id' => $facilityId,
            'category_id' => $request->category_id,
            'requested_by' => $person->id,
            'title' => $request->title,
            'description' => $request->description,
            'unit_number' => $request->unit_number,
            'block_name' => $request->block_name,
            'priority' => $request->priority,
            'status' => 'pending',
            'photo' => $photoPath,
            'requested_at' => now(),
        ]);

        // Send notification to facility management
        $this->notifyManagement($maintenanceRequest);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Maintenance request submitted successfully!',
                'request' => $maintenanceRequest,
            ], 201);
        }

        return redirect()->route('admin.maintenance.show', $maintenanceRequest->id)
            ->with('success', 'Maintenance request submitted successfully!');
    }

    /**
     * Display the specified maintenance request.
     */
    public function show($id)
    {
        $facilityId = Auth::user()->facility_id;

        $request = MaintenanceRequest::where('facility_id', $facilityId)
            ->with(['category', 'requester', 'assignee', 'comments.user'])
            ->findOrFail($id);

        $staff = Person::where('facility_id', $facilityId)
            ->whereHas('employeeProfile')
            ->get();

        return view('admin.maintenance.show', compact('request', 'staff'));
    }

    /**
     * Update the specified maintenance request.
     */
    public function update(Request $request, $id)
    {
        $facilityId = Auth::user()->facility_id;

        $maintenanceRequest = MaintenanceRequest::where('facility_id', $facilityId)
            ->findOrFail($id);

        $validated = $request->validate([
            'status' => 'sometimes|in:pending,assigned,in_progress,completed,cancelled',
            'priority' => 'sometimes|in:low,medium,high,emergency',
            'assigned_to' => 'nullable|exists:people,id',
            'admin_notes' => 'nullable|string',
        ]);

        // If assigned_to is provided, update assigned_at
        if (isset($validated['assigned_to'])) {
            $validated['assigned_at'] = now();
        }

        // If status is completed, update completed_at
        if (isset($validated['status']) && $validated['status'] === 'completed') {
            $validated['completed_at'] = now();
        }

        $maintenanceRequest->update($validated);

        // Notify the requester about status change
        if (isset($validated['status'])) {
            $this->notifyRequester($maintenanceRequest);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Maintenance request updated successfully!',
                'request' => $maintenanceRequest,
            ]);
        }

        return redirect()->route('admin.maintenance.show', $maintenanceRequest->id)
            ->with('success', 'Maintenance request updated successfully!');
    }

    /**
     * Add a comment to a maintenance request.
     */
    public function addComment(Request $request, $id)
    {
        $facilityId = Auth::user()->facility_id;
        $user = Auth::user();

        $maintenanceRequest = MaintenanceRequest::where('facility_id', $facilityId)
            ->findOrFail($id);

        $request->validate([
            'comment' => 'required|string|max:500',
            'photo' => 'nullable|image|max:5120',
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('maintenance_comments', 'public');
        }

        $comment = MaintenanceComment::create([
            'maintenance_request_id' => $maintenanceRequest->id,
            'user_id' => $user->id,
            'comment' => $request->comment,
            'user_type' => $user->person && $user->person->residentProfile ? 'resident' : 'staff',
            'photo' => $photoPath,
        ]);

        // Notify the other party
        $this->notifyComment($maintenanceRequest, $comment);

        return redirect()->route('admin.maintenance.show', $maintenanceRequest->id)
            ->with('success', 'Comment added successfully!');
    }

    /**
     * Delete a maintenance request.
     */
    public function destroy($id)
    {
        $facilityId = Auth::user()->facility_id;

        $maintenanceRequest = MaintenanceRequest::where('facility_id', $facilityId)
            ->findOrFail($id);

        // Delete photo if exists
        if ($maintenanceRequest->photo) {
            Storage::disk('public')->delete($maintenanceRequest->photo);
        }

        $maintenanceRequest->delete();

        return redirect()->route('admin.maintenance.index')
            ->with('success', 'Maintenance request deleted successfully!');
    }

    /**
     * Get user's units (for residential).
     */
    protected function getUserUnits()
    {
        $user = Auth::user();
        $units = [];

        if ($user->person && $user->person->residentProfile) {
            $residentProfile = $user->person->residentProfile;
            $units[] = [
                'unit' => $residentProfile->subUnit->name ?? 'N/A',
                'block' => $residentProfile->subUnit->parent->name ?? 'N/A',
            ];
        }

        return $units;
    }

    /**
     * Notify management about a new request.
     */
    protected function notifyManagement($request)
    {
        try {
            $facility = $request->facility;
            $managementUsers = User::where('facility_id', $facility->id)
                ->whereHas('roles', function ($query) {
                    $query->where('name', 'Facility Manager');
                })
                ->get();

            foreach ($managementUsers as $user) {
                $this->notificationService->sendToUser(
                    $user->id,
                    '🔧 New Maintenance Request: ' . $request->title,
                    "Priority: " . ucfirst($request->priority) . "\nUnit: " . ($request->unit_number ?? 'N/A'),
                    ['url' => '/admin/maintenance/' . $request->id]
                );
            }
        } catch (\Exception $e) {
            \Log::error('Failed to send maintenance notification: ' . $e->getMessage());
        }
    }

    /**
     * Notify requester about status change.
     */
    protected function notifyRequester($request)
    {
        try {
            $requester = $request->requester;
            if ($requester && $requester->user) {
                $this->notificationService->sendToUser(
                    $requester->user->id,
                    '🔧 Maintenance Request Update: ' . $request->title,
                    "Status: " . ucfirst($request->status),
                    ['url' => '/admin/maintenance/' . $request->id]
                );
            }
        } catch (\Exception $e) {
            \Log::error('Failed to send maintenance notification: ' . $e->getMessage());
        }
    }

    /**
     * Notify about a new comment.
     */
    protected function notifyComment($request, $comment)
    {
        try {
            $user = Auth::user();
            $isResident = $user->person && $user->person->residentProfile;

            // If resident commented, notify management
            // If staff commented, notify resident
            if ($isResident) {
                $managementUsers = User::where('facility_id', $request->facility_id)
                    ->whereHas('roles', function ($query) {
                        $query->where('name', 'Facility Manager');
                    })
                    ->get();
                foreach ($managementUsers as $user) {
                    $this->notificationService->sendToUser(
                        $user->id,
                        '💬 New Comment on Request: ' . $request->title,
                        "Resident: " . ($comment->user->name ?? '') . "\n" . $comment->comment,
                        ['url' => '/admin/maintenance/' . $request->id]
                    );
                }
            } else {
                // Notify the resident
                $requester = $request->requester;
                if ($requester && $requester->user) {
                    $this->notificationService->sendToUser(
                        $requester->user->id,
                        '💬 Staff Response: ' . $request->title,
                        "Staff: " . ($comment->user->name ?? '') . "\n" . $comment->comment,
                        ['url' => '/admin/maintenance/' . $request->id]
                    );
                }
            }
        } catch (\Exception $e) {
            \Log::error('Failed to send comment notification: ' . $e->getMessage());
        }
    }

    /**
     * Get maintenance request stats for API.
     */
    public function getStatsApi(Request $request)
    {
        $facilityId = $request->facility_id ?? Auth::user()->facility_id;

        $stats = [
            'total' => MaintenanceRequest::where('facility_id', $facilityId)->count(),
            'pending' => MaintenanceRequest::where('facility_id', $facilityId)->where('status', 'pending')->count(),
            'in_progress' => MaintenanceRequest::where('facility_id', $facilityId)->where('status', 'in_progress')->count(),
            'completed' => MaintenanceRequest::where('facility_id', $facilityId)->where('status', 'completed')->count(),
            'emergency' => MaintenanceRequest::where('facility_id', $facilityId)->where('priority', 'emergency')->count(),
        ];

        return response()->json($stats);
    }
}