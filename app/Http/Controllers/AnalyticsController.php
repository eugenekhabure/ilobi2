<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use App\Models\VisitingDetails;
use App\Models\Visitor;
use App\Models\Invitation;
use App\Models\ResidentProfile;
use App\Models\Employee;
use App\Models\Person;
use App\Models\SubUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AnalyticsController extends Controller
{
    /**
     * Show the analytics dashboard
     */
    public function index()
    {
        $user = Auth::user();
        $isSuperAdmin = ($user->id == 1);
        
        // Get facilities based on user role
        if ($isSuperAdmin) {
            // Super Admin sees all facilities
            $facilities = Facility::all();
            $userFacility = null;
        } else {
            // Client Admin sees only their facility
            $facilities = Facility::where('id', $user->facility_id)->get();
            $userFacility = $user->facility_id;
        }
        
        return view('admin.analytics.index', compact('facilities', 'userFacility', 'isSuperAdmin'));
    }

    /**
     * Get dashboard summary stats based on facility type
     */
    public function getStats(Request $request)
    {
        $user = Auth::user();
        $isSuperAdmin = ($user->id == 1);
        
        // If not super admin, force their facility
        $facilityId = $request->facility_id;
        if (!$isSuperAdmin) {
            $facilityId = $user->facility_id;
        }
        
        $facility = Facility::find($facilityId);
        
        if (!$facility) {
            return response()->json(['error' => 'Facility not found'], 404);
        }

        $stats = [
            'total_visitors' => 0,
            'total_hosts' => 0,
            'checked_in' => 0,
            'checked_out' => 0,
            'today' => 0,
            'this_month' => 0,
            'pending_approvals' => 0,
            'host_label' => 'Employees',
            'host_type' => 'employees',
        ];

        // Visitor stats (common to all)
        $visitorQuery = VisitingDetails::where('facility_id', $facilityId);
        $stats['total_visitors'] = (clone $visitorQuery)->count();
        $stats['checked_in'] = (clone $visitorQuery)->whereNull('checkout_time')->count();
        $stats['checked_out'] = (clone $visitorQuery)->whereNotNull('checkout_time')->count();
        $stats['today'] = (clone $visitorQuery)->whereDate('created_at', today())->count();
        $stats['this_month'] = (clone $visitorQuery)->whereMonth('created_at', now()->month)->count();
        $stats['pending_approvals'] = (clone $visitorQuery)->where('status', 'pending')->count();

        // Host stats based on facility type
        switch ($facility->type) {
            case 'residential':
                $stats['total_hosts'] = ResidentProfile::whereHas('person', function($q) use ($facilityId) {
                    $q->where('facility_id', $facilityId);
                })->count();
                $stats['host_label'] = 'Residents';
                $stats['host_type'] = 'residents';
                break;
                
            case 'commercial':
            case 'corporate':
            case 'industrial':
                $stats['total_hosts'] = Employee::whereHas('user', function($q) use ($facilityId) {
                    $q->where('facility_id', $facilityId);
                })->count();
                $stats['host_label'] = 'Employees';
                $stats['host_type'] = 'employees';
                break;
                
            case 'school':
                $stats['total_hosts'] = Employee::whereHas('user', function($q) use ($facilityId) {
                    $q->where('facility_id', $facilityId);
                })->count();
                $stats['host_label'] = 'Teachers';
                $stats['host_type'] = 'teachers';
                break;
                
            case 'hospital':
                $stats['total_hosts'] = Employee::whereHas('user', function($q) use ($facilityId) {
                    $q->where('facility_id', $facilityId);
                })->count();
                $stats['host_label'] = 'Doctors';
                $stats['host_type'] = 'doctors';
                break;
                
            default:
                $stats['total_hosts'] = 0;
                $stats['host_label'] = 'Hosts';
                $stats['host_type'] = 'hosts';
        }

        return response()->json($stats);
    }

    /**
     * Get top hosts based on facility type
     */
    public function getTopHosts(Request $request)
    {
        $user = Auth::user();
        $isSuperAdmin = ($user->id == 1);
        
        $facilityId = $request->facility_id;
        if (!$isSuperAdmin) {
            $facilityId = $user->facility_id;
        }
        
        $facility = Facility::find($facilityId);
        $limit = $request->limit ?? 10;
        
        if (!$facility) {
            return response()->json(['error' => 'Facility not found'], 404);
        }

        $data = [];

        switch ($facility->type) {
            case 'residential':
                // Top residents by guest visits
                $data = DB::table('invitations')
                    ->join('people', 'invitations.host_person_id', '=', 'people.id')
                    ->join('resident_profiles', 'people.id', '=', 'resident_profiles.person_id')
                    ->join('sub_units', 'resident_profiles.sub_unit_id', '=', 'sub_units.id')
                    ->where('invitations.facility_id', $facilityId)
                    ->select(
                        'people.id',
                        'people.first_name',
                        'people.last_name',
                        'people.email',
                        'sub_units.name as unit',
                        DB::raw('COUNT(invitations.id) as total_guests')
                    )
                    ->groupBy('people.id', 'people.first_name', 'people.last_name', 'people.email', 'sub_units.name')
                    ->orderBy('total_guests', 'desc')
                    ->limit($limit)
                    ->get()
                    ->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'name' => $item->first_name . ' ' . $item->last_name,
                            'email' => $item->email,
                            'unit' => $item->unit,
                            'total' => $item->total_guests,
                        ];
                    });
                break;

            case 'commercial':
            case 'corporate':
            case 'industrial':
            case 'school':
            case 'hospital':
            default:
                // Top employees by visitors
                $data = DB::table('visiting_details')
                    ->join('employees', 'visiting_details.employee_id', '=', 'employees.id')
                    ->join('users', 'employees.user_id', '=', 'users.id')
                    ->where('visiting_details.facility_id', $facilityId)
                    ->select(
                        'users.id',
                        'users.first_name',
                        'users.last_name',
                        'users.email',
                        'employees.designation',
                        DB::raw('COUNT(visiting_details.id) as total_visitors')
                    )
                    ->groupBy('users.id', 'users.first_name', 'users.last_name', 'users.email', 'employees.designation')
                    ->orderBy('total_visitors', 'desc')
                    ->limit($limit)
                    ->get()
                    ->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'name' => $item->first_name . ' ' . $item->last_name,
                            'email' => $item->email,
                            'designation' => $item->designation ?? '',
                            'total' => $item->total_visitors,
                        ];
                    });
        }

        return response()->json($data);
    }

    /**
     * Get visitor trends (daily/weekly/monthly)
     */
    public function getTrends(Request $request)
    {
        $user = Auth::user();
        $isSuperAdmin = ($user->id == 1);
        
        $facilityId = $request->facility_id;
        if (!$isSuperAdmin) {
            $facilityId = $user->facility_id;
        }
        
        $period = $request->period ?? 'daily';
        
        $query = VisitingDetails::where('facility_id', $facilityId);

        switch ($period) {
            case 'daily':
                $format = '%Y-%m-%d';
                $limit = 30;
                break;
            case 'weekly':
                $format = '%Y-%W';
                $limit = 12;
                break;
            case 'monthly':
                $format = '%Y-%m';
                $limit = 12;
                break;
            default:
                $format = '%Y-%m-%d';
                $limit = 30;
        }

        $data = $query->select(
            DB::raw("DATE_FORMAT(created_at, '{$format}') as period"),
            DB::raw('COUNT(*) as count')
        )
        ->groupBy('period')
        ->orderBy('period', 'asc')
        ->limit($limit)
        ->get();

        return response()->json([
            'period' => $period,
            'data' => $data,
        ]);
    }

    /**
     * Get peak hours data
     */
    public function getPeakHours(Request $request)
    {
        $user = Auth::user();
        $isSuperAdmin = ($user->id == 1);
        
        $facilityId = $request->facility_id;
        if (!$isSuperAdmin) {
            $facilityId = $user->facility_id;
        }
        
        $data = VisitingDetails::where('facility_id', $facilityId)
            ->whereNotNull('checkin_time')
            ->select(
                DB::raw('HOUR(checkin_time) as hour'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('hour')
            ->orderBy('hour', 'asc')
            ->get();

        return response()->json($data);
    }

    /**
     * Get status distribution
     */
    public function getStatusDistribution(Request $request)
    {
        $user = Auth::user();
        $isSuperAdmin = ($user->id == 1);
        
        $facilityId = $request->facility_id;
        if (!$isSuperAdmin) {
            $facilityId = $user->facility_id;
        }
        
        $query = VisitingDetails::where('facility_id', $facilityId);

        $checkedIn = (clone $query)->whereNull('checkout_time')->count();
        $checkedOut = (clone $query)->whereNotNull('checkout_time')->count();
        $pending = (clone $query)->where('status', 'pending')->count();

        return response()->json([
            'checked_in' => $checkedIn,
            'checked_out' => $checkedOut,
            'pending' => $pending,
        ]);
    }

    /**
     * Get breakdown by sub-unit (blocks/floors/departments)
     */
    public function getBreakdown(Request $request)
    {
        $user = Auth::user();
        $isSuperAdmin = ($user->id == 1);
        
        $facilityId = $request->facility_id;
        if (!$isSuperAdmin) {
            $facilityId = $user->facility_id;
        }
        
        $facility = Facility::find($facilityId);
        
        if (!$facility) {
            return response()->json(['error' => 'Facility not found'], 404);
        }

        $data = [];

        switch ($facility->type) {
            case 'residential':
                // Breakdown by blocks
                $data = SubUnit::where('facility_id', $facilityId)
                    ->where('type', 'block')
                    ->withCount(['residentProfiles'])
                    ->get()
                    ->map(function ($block) {
                        return [
                            'name' => $block->name,
                            'count' => $block->resident_profiles_count,
                        ];
                    });
                break;

            case 'commercial':
                // Breakdown by floors
                $data = SubUnit::where('facility_id', $facilityId)
                    ->where('type', 'floor')
                    ->withCount(['occupants'])
                    ->get()
                    ->map(function ($floor) {
                        return [
                            'name' => $floor->name,
                            'count' => $floor->occupants_count,
                        ];
                    });
                break;

            case 'corporate':
            case 'school':
            case 'hospital':
            default:
                // Breakdown by departments
                $data = DB::table('departments')
                    ->leftJoin('employees', 'departments.id', '=', 'employees.department_id')
                    ->where('employees.facility_id', $facilityId)
                    ->select(
                        'departments.name',
                        DB::raw('COUNT(employees.id) as count')
                    )
                    ->groupBy('departments.name')
                    ->get()
                    ->map(function ($item) {
                        return [
                            'name' => $item->name,
                            'count' => $item->count,
                        ];
                    });
        }

        return response()->json($data);
    }

    /**
     * Get daily activity
     */
    public function getDailyActivity(Request $request)
    {
        $user = Auth::user();
        $isSuperAdmin = ($user->id == 1);
        
        $facilityId = $request->facility_id;
        if (!$isSuperAdmin) {
            $facilityId = $user->facility_id;
        }
        
        $date = $request->date ?? today()->format('Y-m-d');
        
        $data = VisitingDetails::where('facility_id', $facilityId)
            ->whereDate('created_at', $date)
            ->with(['visitor', 'employee.user'])
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'visitor_name' => $item->visitor->name ?? 'Unknown',
                    'host_name' => $item->employee->user->name ?? 'Unknown',
                    'checkin_time' => $item->checkin_time,
                    'checkout_time' => $item->checkout_time,
                    'purpose' => $item->purpose ?? '-',
                    'status' => $item->status ?? 'pending',
                ];
            });

        return response()->json($data);
    }

    /**
     * Get facility comparison (super admin only)
     */
    public function getFacilityComparison(Request $request)
    {
        $user = Auth::user();
        $isSuperAdmin = ($user->id == 1);
        
        if (!$isSuperAdmin) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $data = Facility::withCount(['visitors' => function ($query) {
                $query->whereMonth('created_at', now()->month);
            }])
            ->get()
            ->map(function ($facility) {
                return [
                    'name' => $facility->name,
                    'type' => $facility->type,
                    'visitors' => $facility->visitors_count,
                ];
            });

        return response()->json($data);
    }

    /**
     * Get facility type breakdown (super admin only)
     */
    public function getFacilityTypeBreakdown()
    {
        $user = Auth::user();
        $isSuperAdmin = ($user->id == 1);
        
        if (!$isSuperAdmin) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $data = Facility::select('type', DB::raw('COUNT(*) as count'))
            ->groupBy('type')
            ->get();

        return response()->json($data);
    }
}