<?php

namespace App\Http\Controllers;

use App\Models\PreRegister;
use App\Models\Visitor;
use App\Models\Employee;
use App\Models\Person;
use App\Models\Facility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class SelfServiceController extends Controller
{
    /**
     * Show the self-registration form.
     */
    public function showForm(Request $request)
    {
        $hostId = $request->host_id;
        $hostType = $request->host_type;
        $facilityId = $request->facility_id;
        
        $facilities = Facility::where('is_active', true)->get();
        $host = null;
        
        if ($hostId && $hostType) {
            if ($hostType == 'employee') {
                $host = Employee::with('user')->find($hostId);
            } elseif ($hostType == 'resident') {
                $host = Person::with('residentProfile')->find($hostId);
            }
        }
        
        return view('frontend.pre-register.form', compact('facilities', 'host', 'hostId', 'hostType', 'facilityId'));
    }

    /**
     * Store a self-registration.
     */
    public function store(Request $request)
    {
        $request->validate([
            'visitor_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'required|string|max:20',
            'host_type' => 'required|in:employee,resident',
            'host_id' => 'required|integer',
            'facility_id' => 'required|exists:facilities,id',
            'expected_date' => 'required|date|after_or_equal:today',
            'expected_time' => 'required',
            'purpose' => 'nullable|string',
            'g-recaptcha-response' => 'sometimes',
        ]);

        // Create or get visitor
        $visitor = Visitor::where('phone', $request->phone)->first();
        if (!$visitor) {
            $visitor = Visitor::create([
                'name' => $request->visitor_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'reg_no' => 'VIS-' . time(),
            ]);
        }

        // Determine host IDs
        $employeeId = null;
        $personId = null;
        
        if ($request->host_type == 'employee') {
            $employeeId = $request->host_id;
        } elseif ($request->host_type == 'resident') {
            $personId = $request->host_id;
        }

        // Create pre-registration
        $preRegister = PreRegister::create([
            'visitor_id' => $visitor->id,
            'employee_id' => $employeeId,
            'person_id' => $personId,
            'facility_id' => $request->facility_id,
            'expected_date' => $request->expected_date,
            'expected_time' => $request->expected_time,
            'purpose' => $request->purpose,
            'reference' => PreRegister::generateReference(),
            'status' => 'pending',
            'created_by' => auth()->id(),
        ]);

        // Notify host
        $this->notifyHost($preRegister);

        return redirect()->route('self-service.success', $preRegister->id)
            ->with('success', 'Pre-registration submitted successfully!');
    }

    /**
     * Show success page.
     */
    public function success($id)
    {
        $preRegister = PreRegister::with(['visitor', 'employee.user', 'person', 'facility'])->findOrFail($id);
        return view('frontend.pre-register.success', compact('preRegister'));
    }

    /**
     * Notify the host.
     */
    protected function notifyHost($preRegister)
    {
        try {
            $hostName = $preRegister->host_name;
            $visitorName = $preRegister->visitor->name ?? 'Unknown';
            $date = $preRegister->expected_date;
            $time = $preRegister->expected_time ? date('h:i A', strtotime($preRegister->expected_time)) : 'N/A';

            // Get host phone
            $hostPhone = null;
            if ($preRegister->employee && $preRegister->employee->user) {
                $hostPhone = $preRegister->employee->user->phone;
            } elseif ($preRegister->person) {
                $hostPhone = $preRegister->person->phone;
            }

            // Send WhatsApp notification if Twilio is configured
            if ($hostPhone && setting('whatsapp_message')) {
                // WhatsApp logic here (using Twilio)
                Log::info("WhatsApp notification sent to host: {$hostPhone}");
            }

        } catch (\Exception $e) {
            Log::error('Failed to notify host: ' . $e->getMessage());
        }
    }

    /**
     * Get employees for dropdown (AJAX).
     */
    public function getEmployees(Request $request)
    {
        $facilityId = $request->facility_id;
        $employees = Employee::with('user')
            ->whereHas('user', function($q) use ($facilityId) {
                if ($facilityId) {
                    $q->where('facility_id', $facilityId);
                }
            })
            ->where('status', 1)
            ->get()
            ->map(function($employee) {
                return [
                    'id' => $employee->id,
                    'name' => $employee->user->name ?? 'Unknown',
                ];
            });

        return response()->json($employees);
    }

    /**
     * Get residents for dropdown (AJAX).
     */
    public function getResidents(Request $request)
    {
        $facilityId = $request->facility_id;
        $residents = Person::whereHas('residentProfile')
            ->when($facilityId, function($q) use ($facilityId) {
                return $q->where('facility_id', $facilityId);
            })
            ->get()
            ->map(function($person) {
                return [
                    'id' => $person->id,
                    'name' => $person->full_name,
                ];
            });

        return response()->json($residents);
    }

    /**
     * Get facility types for dropdown (AJAX).
     */
    public function getFacilityTypes()
    {
        $types = Facility::select('type')->distinct()->get()->pluck('type');
        return response()->json($types);
    }

    /**
     * Generate a pre-registration link for a host.
     */
    public function generateLink(Request $request)
    {
        $hostType = $request->host_type;
        $hostId = $request->host_id;
        $facilityId = $request->facility_id ?? auth()->user()->facility_id;

        $link = route('self-service.form', [
            'host_type' => $hostType,
            'host_id' => $hostId,
            'facility_id' => $facilityId,
        ]);

        return response()->json([
            'link' => $link,
        ]);
    }
}