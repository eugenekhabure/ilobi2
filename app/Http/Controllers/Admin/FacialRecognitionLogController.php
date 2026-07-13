<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FacialRecognitionLog;
use App\Models\Employee;
use App\Models\Visitor;
use App\Models\ResidentProfile;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class FacialRecognitionLogController extends Controller
{
    /**
     * Display a listing of facial recognition logs.
     */
    public function index()
    {
        return view('admin.facial-recognition.index');
    }

    /**
     * Get facial recognition logs data for DataTables.
     */
    public function getLogs(Request $request)
    {
        if ($request->ajax()) {
            $data = FacialRecognitionLog::orderBy('created_at', 'desc');
            
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('status_badge', function ($row) {
                    $colors = [
                        'matched' => 'success',
                        'unmatched' => 'danger',
                        'error' => 'warning'
                    ];
                    $labels = [
                        'matched' => '✅ Matched',
                        'unmatched' => '❌ Unmatched',
                        'error' => '⚠️ Error'
                    ];
                    return '<span class="badge badge-' . ($colors[$row->status] ?? 'secondary') . '">' . ($labels[$row->status] ?? ucfirst($row->status)) . '</span>';
                })
                ->addColumn('type_badge', function ($row) {
                    return '<span class="badge badge-info">' . ucfirst($row->type) . '</span>';
                })
                ->addColumn('confidence_display', function ($row) {
                    if ($row->confidence_score === null) {
                        return 'N/A';
                    }
                    $color = $row->confidence_score >= 80 ? 'success' : ($row->confidence_score >= 60 ? 'warning' : 'danger');
                    return '<span class="text-' . $color . '">' . number_format($row->confidence_score, 2) . '%</span>';
                })
                ->addColumn('image_preview', function ($row) {
                    if ($row->image_path) {
                        return '<img src="' . asset('storage/' . $row->image_path) . '" width="50" height="50" class="rounded" alt="Face image">';
                    }
                    return 'N/A';
                })
                ->addColumn('action', function ($row) {
                    $btn = '<a href="' . route('admin.facial-recognition.show', $row->id) . '" class="btn btn-info btn-sm" title="View Details">View</a> ';
                    $btn .= '<a href="javascript:void(0)" class="btn btn-danger btn-sm delete-log" data-id="' . $row->id . '" title="Delete">Delete</a>';
                    return $btn;
                })
                ->rawColumns(['status_badge', 'type_badge', 'confidence_display', 'image_preview', 'action'])
                ->make(true);
        }
    }

    /**
     * Display the specified facial recognition log.
     */
    public function show($id)
    {
        $log = FacialRecognitionLog::findOrFail($id);
        
        // Load related model if exists
        if ($log->related_id && $log->type !== 'unknown') {
            $relatedModel = null;
            switch ($log->type) {
                case 'employee':
                    $relatedModel = Employee::find($log->related_id);
                    break;
                case 'visitor':
                    $relatedModel = Visitor::find($log->related_id);
                    break;
                case 'resident':
                    $relatedModel = ResidentProfile::find($log->related_id);
                    break;
            }
            $log->related_model = $relatedModel;
        }
        
        return view('admin.facial-recognition.show', compact('log'));
    }

    /**
     * Remove the specified facial recognition log from storage.
     */
    public function destroy($id)
    {
        $log = FacialRecognitionLog::findOrFail($id);
        
        // Delete image file if exists
        if ($log->image_path && file_exists(storage_path('app/public/' . $log->image_path))) {
            unlink(storage_path('app/public/' . $log->image_path));
        }
        
        $log->delete();

        return response()->json(['success' => 'Facial recognition log deleted successfully!']);
    }

    /**
     * Delete all logs.
     */
    public function deleteAll(Request $request)
    {
        $request->validate([
            'type' => 'nullable|in:visitor,employee,resident,unknown',
            'status' => 'nullable|in:matched,unmatched,error',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
        ]);

        $query = FacialRecognitionLog::query();

        if ($request->type) {
            $query->where('type', $request->type);
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $count = $query->count();
        
        // Delete image files
        $logs = $query->get();
        foreach ($logs as $log) {
            if ($log->image_path && file_exists(storage_path('app/public/' . $log->image_path))) {
                unlink(storage_path('app/public/' . $log->image_path));
            }
        }
        
        $query->delete();

        return response()->json([
            'success' => $count . ' facial recognition logs deleted successfully!',
            'count' => $count
        ]);
    }

    /**
     * Get dashboard statistics for facial recognition.
     */
    public function getStats()
    {
        $stats = [
            'total' => FacialRecognitionLog::count(),
            'matched' => FacialRecognitionLog::where('status', 'matched')->count(),
            'unmatched' => FacialRecognitionLog::where('status', 'unmatched')->count(),
            'error' => FacialRecognitionLog::where('status', 'error')->count(),
            'today' => FacialRecognitionLog::whereDate('created_at', today())->count(),
            'by_type' => [
                'visitor' => FacialRecognitionLog::where('type', 'visitor')->count(),
                'employee' => FacialRecognitionLog::where('type', 'employee')->count(),
                'resident' => FacialRecognitionLog::where('type', 'resident')->count(),
                'unknown' => FacialRecognitionLog::where('type', 'unknown')->count(),
            ],
            'avg_confidence' => FacialRecognitionLog::where('status', 'matched')->avg('confidence_score'),
        ];

        return response()->json($stats);
    }

    /**
     * Get chart data for facial recognition.
     */
    public function getChartData(Request $request)
    {
        $days = $request->days ?? 30;
        
        $data = FacialRecognitionLog::selectRaw('DATE(created_at) as date, 
            COUNT(*) as total,
            SUM(CASE WHEN status = "matched" THEN 1 ELSE 0 END) as matched,
            SUM(CASE WHEN status = "unmatched" THEN 1 ELSE 0 END) as unmatched,
            SUM(CASE WHEN status = "error" THEN 1 ELSE 0 END) as error')
            ->whereDate('created_at', '>=', now()->subDays($days))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        return response()->json($data);
    }

    /**
     * Export logs to CSV.
     */
    public function export(Request $request)
    {
        $query = FacialRecognitionLog::query();

        if ($request->type) {
            $query->where('type', $request->type);
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="facial_recognition_logs_' . date('Y-m-d') . '.csv"',
        ];

        $callback = function() use ($logs) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Type', 'Full Name', 'Phone Number', 'Status', 'Confidence Score', 'Device Name', 'IP Address', 'Created At']);

            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->id,
                    $log->type,
                    $log->full_name,
                    $log->phone_number,
                    $log->status,
                    $log->confidence_score,
                    $log->device_name,
                    $log->ip_address,
                    $log->created_at,
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}