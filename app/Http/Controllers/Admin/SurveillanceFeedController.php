<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SurveillanceFeed;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class SurveillanceFeedController extends Controller
{
    /**
     * Display a listing of surveillance feeds.
     */
    public function index()
    {
        return view('admin.surveillance.index');
    }

    /**
     * Get surveillance feeds data for DataTables.
     */
    public function getFeeds(Request $request)
    {
        if ($request->ajax()) {
            $data = SurveillanceFeed::with('createdBy')->orderBy('created_at', 'desc');
            
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('status_badge', function ($row) {
                    $colors = [
                        'online' => 'success',
                        'offline' => 'danger',
                        'recording' => 'primary',
                        'error' => 'warning'
                    ];
                    $labels = [
                        'online' => '🟢 Online',
                        'offline' => '🔴 Offline',
                        'recording' => '🔵 Recording',
                        'error' => '🟡 Error'
                    ];
                    return '<span class="badge badge-' . ($colors[$row->status] ?? 'secondary') . '">' . ($labels[$row->status] ?? ucfirst($row->status)) . '</span>';
                })
                ->addColumn('recording_status', function ($row) {
                    return $row->is_recording ? '✅ Yes' : '❌ No';
                })
                ->addColumn('created_by_name', function ($row) {
                    return $row->createdBy ? $row->createdBy->name : 'N/A';
                })
                ->addColumn('action', function ($row) {
                    $btn = '';
                    
                    if ($row->status === 'online') {
                        $btn .= '<a href="' . route('admin.surveillance.stream', $row->id) . '" class="btn btn-success btn-sm" title="View Stream">Stream</a> ';
                    }
                    
                    $btn .= '<a href="' . route('admin.surveillance.show', $row->id) . '" class="btn btn-info btn-sm" title="View Details">View</a> ';
                    $btn .= '<a href="' . route('admin.surveillance.edit', $row->id) . '" class="btn btn-primary btn-sm" title="Edit">Edit</a> ';
                    $btn .= '<a href="javascript:void(0)" class="btn btn-danger btn-sm delete-feed" data-id="' . $row->id . '" title="Delete">Delete</a>';
                    
                    return $btn;
                })
                ->rawColumns(['status_badge', 'action'])
                ->make(true);
        }
    }

    /**
     * Show the form for creating a new surveillance feed.
     */
    public function create()
    {
        return view('admin.surveillance.create');
    }

    /**
     * Store a newly created surveillance feed in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'camera_url' => 'required|url',
            'stream_url' => 'nullable|url',
            'camera_type' => 'required|in:ip,usb,hikvision,zkteco',
            'brand' => 'nullable|string|max:100',
            'model' => 'nullable|string|max:100',
            'ip_address' => 'nullable|ip',
            'port' => 'nullable|integer|min:1|max:65535',
            'username' => 'nullable|string|max:255',
            'password' => 'nullable|string|max:255',
            'status' => 'nullable|in:online,offline,recording,error',
            'is_recording' => 'boolean',
            'storage_limit_days' => 'nullable|integer|min:1|max:365',
            'notes' => 'nullable|string',
        ]);

        $surveillanceFeed = SurveillanceFeed::create([
            'name' => $request->name,
            'location' => $request->location,
            'camera_url' => $request->camera_url,
            'stream_url' => $request->stream_url,
            'camera_type' => $request->camera_type,
            'brand' => $request->brand,
            'model' => $request->model,
            'ip_address' => $request->ip_address,
            'port' => $request->port,
            'username' => $request->username,
            'password' => $request->password,
            'status' => $request->status ?? 'offline',
            'is_recording' => $request->has('is_recording'),
            'recording_path' => $request->is_recording ? 'recordings/' . date('Y') . '/' . date('m') . '/' : null,
            'storage_limit_days' => $request->storage_limit_days ?? 30,
            'notes' => $request->notes,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('admin.surveillance.index')
            ->with('success', 'Surveillance feed created successfully!');
    }

    /**
     * Display the specified surveillance feed.
     */
    public function show($id)
    {
        $feed = SurveillanceFeed::with('createdBy')->findOrFail($id);
        return view('admin.surveillance.show', compact('feed'));
    }

    /**
     * Show the form for editing the specified surveillance feed.
     */
    public function edit($id)
    {
        $feed = SurveillanceFeed::findOrFail($id);
        return view('admin.surveillance.edit', compact('feed'));
    }

    /**
     * Update the specified surveillance feed in storage.
     */
    public function update(Request $request, $id)
    {
        $feed = SurveillanceFeed::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'camera_url' => 'required|url',
            'stream_url' => 'nullable|url',
            'camera_type' => 'required|in:ip,usb,hikvision,zkteco',
            'brand' => 'nullable|string|max:100',
            'model' => 'nullable|string|max:100',
            'ip_address' => 'nullable|ip',
            'port' => 'nullable|integer|min:1|max:65535',
            'username' => 'nullable|string|max:255',
            'password' => 'nullable|string|max:255',
            'status' => 'required|in:online,offline,recording,error',
            'is_recording' => 'boolean',
            'storage_limit_days' => 'nullable|integer|min:1|max:365',
            'notes' => 'nullable|string',
        ]);

        $feed->update([
            'name' => $request->name,
            'location' => $request->location,
            'camera_url' => $request->camera_url,
            'stream_url' => $request->stream_url,
            'camera_type' => $request->camera_type,
            'brand' => $request->brand,
            'model' => $request->model,
            'ip_address' => $request->ip_address,
            'port' => $request->port,
            'username' => $request->username,
            'password' => $request->password,
            'status' => $request->status,
            'is_recording' => $request->has('is_recording'),
            'storage_limit_days' => $request->storage_limit_days ?? 30,
            'notes' => $request->notes,
        ]);

        return redirect()->route('admin.surveillance.index')
            ->with('success', 'Surveillance feed updated successfully!');
    }

    /**
     * Remove the specified surveillance feed from storage.
     */
    public function destroy($id)
    {
        $feed = SurveillanceFeed::findOrFail($id);
        
        // Delete any recordings if they exist
        if ($feed->recording_path && Storage::disk('public')->exists($feed->recording_path)) {
            Storage::disk('public')->deleteDirectory($feed->recording_path);
        }
        
        $feed->delete();

        return response()->json(['success' => 'Surveillance feed deleted successfully!']);
    }

    /**
     * Stream the specified surveillance feed.
     */
    public function stream($id)
    {
        $feed = SurveillanceFeed::findOrFail($id);
        
        if ($feed->status !== 'online' && $feed->status !== 'recording') {
            return redirect()->route('admin.surveillance.index')
                ->with('error', 'Camera is not online. Please check the connection.');
        }

        return view('admin.surveillance.stream', compact('feed'));
    }

    /**
     * Test connection for a surveillance feed.
     */
    public function testConnection($id)
    {
        $feed = SurveillanceFeed::findOrFail($id);
        
        // Simulate connection test (in real implementation, you would ping the camera)
        // For now, we'll just toggle status
        $status = $feed->status === 'online' || $feed->status === 'recording' ? 'offline' : 'online';
        
        $feed->update(['status' => $status]);

        return response()->json([
            'success' => true,
            'message' => 'Connection test completed. Status: ' . $status,
            'status' => $status
        ]);
    }

    /**
     * Toggle recording for a surveillance feed.
     */
    public function toggleRecording($id)
    {
        $feed = SurveillanceFeed::findOrFail($id);
        
        $newRecordingStatus = !$feed->is_recording;
        $newStatus = $newRecordingStatus ? 'recording' : 'online';
        
        $feed->update([
            'is_recording' => $newRecordingStatus,
            'status' => $newStatus,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Recording ' . ($newRecordingStatus ? 'started' : 'stopped'),
            'is_recording' => $newRecordingStatus
        ]);
    }

    /**
     * Get dashboard statistics for surveillance.
     */
    public function getStats()
    {
        $stats = [
            'total' => SurveillanceFeed::count(),
            'online' => SurveillanceFeed::where('status', 'online')->count(),
            'offline' => SurveillanceFeed::where('status', 'offline')->count(),
            'recording' => SurveillanceFeed::where('status', 'recording')->count(),
            'error' => SurveillanceFeed::where('status', 'error')->count(),
            'by_type' => [
                'ip' => SurveillanceFeed::where('camera_type', 'ip')->count(),
                'usb' => SurveillanceFeed::where('camera_type', 'usb')->count(),
                'hikvision' => SurveillanceFeed::where('camera_type', 'hikvision')->count(),
                'zkteco' => SurveillanceFeed::where('camera_type', 'zkteco')->count(),
            ],
        ];

        return response()->json($stats);
    }

    /**
     * Get all online cameras.
     */
    public function getOnlineCameras()
    {
        $cameras = SurveillanceFeed::whereIn('status', ['online', 'recording'])
            ->select('id', 'name', 'location', 'stream_url', 'status')
            ->get();

        return response()->json($cameras);
    }

    /**
     * Get active recording cameras.
     */
    public function getRecordingCameras()
    {
        $cameras = SurveillanceFeed::where('status', 'recording')
            ->where('is_recording', true)
            ->select('id', 'name', 'location', 'stream_url', 'status', 'recording_path')
            ->get();

        return response()->json($cameras);
    }
}