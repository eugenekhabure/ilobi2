<?php

namespace App\Http\Controllers;

use App\Models\Amenity;
use App\Models\AmenityBooking;
use App\Models\AmenityTimeSlot;
use App\Models\Person;
use App\Models\Facility;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AmenityController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
        $this->middleware('auth');
    }

    /**
     * Display a listing of amenities.
     */
    public function index(Request $request)
    {
        $facilityId = Auth::user()->facility_id;

        $query = Amenity::where('facility_id', $facilityId);

        if ($request->has('is_active') && $request->is_active != '') {
            $query->where('is_active', $request->is_active);
        }

        $amenities = $query->orderBy('name')->paginate(20);

        $stats = [
            'total' => Amenity::where('facility_id', $facilityId)->count(),
            'active' => Amenity::where('facility_id', $facilityId)->where('is_active', true)->count(),
            'inactive' => Amenity::where('facility_id', $facilityId)->where('is_active', false)->count(),
        ];

        return view('admin.amenities.index', compact('amenities', 'stats'));
    }

    /**
     * Show the form for creating a new amenity.
     */
    public function create()
    {
        $icons = [
            'gym' => '🏋️ Gym',
            'pool' => '🏊 Pool',
            'meeting_room' => '🏢 Meeting Room',
            'conference_room' => '📊 Conference Room',
            'clubhouse' => '🏠 Clubhouse',
            'tennis_court' => '🎾 Tennis Court',
            'basketball_court' => '🏀 Basketball Court',
            'playground' => '🎠 Playground',
            'parking' => '🅿️ Parking',
            'garden' => '🌳 Garden',
            'sauna' => '🧖 Sauna',
            'spa' => '💆 Spa',
            'other' => '📍 Other',
        ];

        $days = [
            'monday' => 'Monday',
            'tuesday' => 'Tuesday',
            'wednesday' => 'Wednesday',
            'thursday' => 'Thursday',
            'friday' => 'Friday',
            'saturday' => 'Saturday',
            'sunday' => 'Sunday',
        ];

        return view('admin.amenities.create', compact('icons', 'days'));
    }

    /**
     * Store a newly created amenity.
     */
    public function store(Request $request)
    {
        $facilityId = Auth::user()->facility_id;

        $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'capacity' => 'nullable|integer|min:1',
            'max_booking_days' => 'nullable|integer|min:1',
            'advance_notice_hours' => 'nullable|integer|min:0',
            'price' => 'nullable|numeric|min:0',
            'requires_approval' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        $amenity = Amenity::create([
            'facility_id' => $facilityId,
            'name' => $request->name,
            'icon' => $request->icon,
            'description' => $request->description,
            'location' => $request->location,
            'capacity' => $request->capacity ?? 10,
            'max_booking_days' => $request->max_booking_days ?? 7,
            'advance_notice_hours' => $request->advance_notice_hours ?? 2,
            'price' => $request->price ?? 0,
            'requires_approval' => $request->has('requires_approval'),
            'is_active' => $request->has('is_active'),
        ]);

        // Save time slots
        if ($request->has('time_slots')) {
            foreach ($request->time_slots as $slot) {
                if (!empty($slot['day_of_week']) && !empty($slot['start_time']) && !empty($slot['end_time'])) {
                    AmenityTimeSlot::create([
                        'amenity_id' => $amenity->id,
                        'day_of_week' => $slot['day_of_week'],
                        'start_time' => $slot['start_time'],
                        'end_time' => $slot['end_time'],
                        'duration_minutes' => $slot['duration_minutes'] ?? 60,
                        'max_bookings' => $slot['max_bookings'] ?? 1,
                        'is_active' => true,
                    ]);
                }
            }
        }

        return redirect()->route('admin.amenities.index')
            ->with('success', 'Amenity created successfully!');
    }

    /**
     * Display the specified amenity.
     */
    public function show($id)
    {
        $facilityId = Auth::user()->facility_id;

        $amenity = Amenity::where('facility_id', $facilityId)
            ->with(['timeSlots', 'bookings' => function ($query) {
                $query->orderBy('booking_date', 'desc')->limit(10);
            }])
            ->findOrFail($id);

        return view('admin.amenities.show', compact('amenity'));
    }

    /**
     * Show the form for editing an amenity.
     */
    public function edit($id)
    {
        $facilityId = Auth::user()->facility_id;

        $amenity = Amenity::where('facility_id', $facilityId)
            ->with('timeSlots')
            ->findOrFail($id);

        $icons = [
            'gym' => '🏋️ Gym',
            'pool' => '🏊 Pool',
            'meeting_room' => '🏢 Meeting Room',
            'conference_room' => '📊 Conference Room',
            'clubhouse' => '🏠 Clubhouse',
            'tennis_court' => '🎾 Tennis Court',
            'basketball_court' => '🏀 Basketball Court',
            'playground' => '🎠 Playground',
            'parking' => '🅿️ Parking',
            'garden' => '🌳 Garden',
            'sauna' => '🧖 Sauna',
            'spa' => '💆 Spa',
            'other' => '📍 Other',
        ];

        $days = [
            'monday' => 'Monday',
            'tuesday' => 'Tuesday',
            'wednesday' => 'Wednesday',
            'thursday' => 'Thursday',
            'friday' => 'Friday',
            'saturday' => 'Saturday',
            'sunday' => 'Sunday',
        ];

        return view('admin.amenities.edit', compact('amenity', 'icons', 'days'));
    }

    /**
     * Update an amenity.
     */
    public function update(Request $request, $id)
    {
        $facilityId = Auth::user()->facility_id;

        $amenity = Amenity::where('facility_id', $facilityId)
            ->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'capacity' => 'nullable|integer|min:1',
            'max_booking_days' => 'nullable|integer|min:1',
            'advance_notice_hours' => 'nullable|integer|min:0',
            'price' => 'nullable|numeric|min:0',
            'requires_approval' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        $amenity->update([
            'name' => $request->name,
            'icon' => $request->icon,
            'description' => $request->description,
            'location' => $request->location,
            'capacity' => $request->capacity ?? 10,
            'max_booking_days' => $request->max_booking_days ?? 7,
            'advance_notice_hours' => $request->advance_notice_hours ?? 2,
            'price' => $request->price ?? 0,
            'requires_approval' => $request->has('requires_approval'),
            'is_active' => $request->has('is_active'),
        ]);

        // Update time slots
        if ($request->has('time_slots')) {
            // Delete existing time slots
            $amenity->timeSlots()->delete();

            // Create new time slots
            foreach ($request->time_slots as $slot) {
                if (!empty($slot['day_of_week']) && !empty($slot['start_time']) && !empty($slot['end_time'])) {
                    AmenityTimeSlot::create([
                        'amenity_id' => $amenity->id,
                        'day_of_week' => $slot['day_of_week'],
                        'start_time' => $slot['start_time'],
                        'end_time' => $slot['end_time'],
                        'duration_minutes' => $slot['duration_minutes'] ?? 60,
                        'max_bookings' => $slot['max_bookings'] ?? 1,
                        'is_active' => true,
                    ]);
                }
            }
        }

        return redirect()->route('admin.amenities.index')
            ->with('success', 'Amenity updated successfully!');
    }

    /**
     * Delete an amenity.
     */
    public function destroy($id)
    {
        $facilityId = Auth::user()->facility_id;

        $amenity = Amenity::where('facility_id', $facilityId)
            ->findOrFail($id);

        // Check if there are any bookings
        if ($amenity->bookings()->count() > 0) {
            return redirect()->route('admin.amenities.index')
                ->with('error', 'Cannot delete amenity with existing bookings. Deactivate it instead.');
        }

        $amenity->delete();

        return redirect()->route('admin.amenities.index')
            ->with('success', 'Amenity deleted successfully!');
    }

    /**
     * Toggle amenity status.
     */
    public function toggleStatus($id)
    {
        $facilityId = Auth::user()->facility_id;

        $amenity = Amenity::where('facility_id', $facilityId)
            ->findOrFail($id);

        $amenity->update([
            'is_active' => !$amenity->is_active,
        ]);

        return redirect()->route('admin.amenities.index')
            ->with('success', 'Amenity status updated!');
    }

    /**
     * Display bookings for an amenity.
     */
    public function bookings($id, Request $request)
    {
        $facilityId = Auth::user()->facility_id;

        $amenity = Amenity::where('facility_id', $facilityId)
            ->findOrFail($id);

        $query = AmenityBooking::where('amenity_id', $amenity->id)
            ->with(['bookedBy', 'facility']);

        if ($request->has('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        if ($request->has('date_from')) {
            $query->whereDate('booking_date', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('booking_date', '<=', $request->date_to);
        }

        $bookings = $query->orderBy('booking_date', 'desc')
            ->orderBy('start_time', 'desc')
            ->paginate(20);

        $stats = [
            'total' => AmenityBooking::where('amenity_id', $amenity->id)->count(),
            'pending' => AmenityBooking::where('amenity_id', $amenity->id)->where('status', 'pending')->count(),
            'confirmed' => AmenityBooking::where('amenity_id', $amenity->id)->where('status', 'confirmed')->count(),
            'completed' => AmenityBooking::where('amenity_id', $amenity->id)->where('status', 'completed')->count(),
            'cancelled' => AmenityBooking::where('amenity_id', $amenity->id)->where('status', 'cancelled')->count(),
        ];

        $statuses = [
            'all' => 'All Statuses',
            'pending' => 'Pending',
            'confirmed' => 'Confirmed',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
            'rejected' => 'Rejected',
        ];

        return view('admin.amenities.bookings', compact('amenity', 'bookings', 'stats', 'statuses'));
    }

    /**
     * Show booking details.
     */
    public function showBooking($id)
    {
        $facilityId = Auth::user()->facility_id;

        $booking = AmenityBooking::where('facility_id', $facilityId)
            ->with(['amenity', 'bookedBy', 'facility'])
            ->findOrFail($id);

        return view('admin.amenities.booking-show', compact('booking'));
    }

    /**
     * Update booking status.
     */
    public function updateBookingStatus(Request $request, $id)
    {
        $facilityId = Auth::user()->facility_id;

        $booking = AmenityBooking::where('facility_id', $facilityId)
            ->findOrFail($id);

        $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled,completed,rejected',
            'cancellation_reason' => 'nullable|string',
        ]);

        $booking->update([
            'status' => $request->status,
            'cancellation_reason' => $request->cancellation_reason,
            'confirmed_at' => $request->status == 'confirmed' ? now() : $booking->confirmed_at,
            'cancelled_at' => $request->status == 'cancelled' ? now() : $booking->cancelled_at,
            'completed_at' => $request->status == 'completed' ? now() : $booking->completed_at,
        ]);

        // Send notification to the resident
        $this->notifyResident($booking);

        return redirect()->route('admin.amenities.bookings', $booking->amenity_id)
            ->with('success', 'Booking status updated successfully!');
    }

    /**
     * Notify resident about booking status change.
     */
    protected function notifyResident($booking)
    {
        try {
            $resident = $booking->bookedBy;
            if ($resident && $resident->user) {
                $this->notificationService->sendToUser(
                    $resident->user->id,
                    '📅 Booking Update: ' . $booking->amenity->name,
                    "Status: " . ucfirst($booking->status) . "\nDate: " . $booking->booking_date->format('d/m/Y') . "\nTime: " . $booking->start_time . ' - ' . $booking->end_time,
                    ['url' => '/admin/amenity-bookings/' . $booking->id]
                );
            }
        } catch (\Exception $e) {
            \Log::error('Failed to send booking notification: ' . $e->getMessage());
        }
    }

    /**
     * Resident booking page.
     */
    public function residentBookings()
    {
        $user = Auth::user();
        $person = $user->person;

        if (!$person) {
            return redirect()->back()->with('error', 'No resident profile found.');
        }

        $bookings = AmenityBooking::where('booked_by', $person->id)
            ->with(['amenity'])
            ->orderBy('booking_date', 'desc')
            ->paginate(20);

        return view('resident.amenity-bookings', compact('bookings'));
    }

    /**
     * Get available time slots for an amenity.
     */
    public function getAvailableSlots(Request $request)
    {
        $request->validate([
            'amenity_id' => 'required|exists:amenities,id',
            'date' => 'required|date|after_or_equal:today',
        ]);

        $amenity = Amenity::findOrFail($request->amenity_id);

        // Get day of week
        $dayOfWeek = strtolower(date('l', strtotime($request->date)));

        // Get time slots for this day
        $timeSlots = AmenityTimeSlot::where('amenity_id', $amenity->id)
            ->where('day_of_week', $dayOfWeek)
            ->where('is_active', true)
            ->get();

        // Get existing bookings for this date
        $bookings = AmenityBooking::where('amenity_id', $amenity->id)
            ->where('booking_date', $request->date)
            ->where('status', '!=', 'cancelled')
            ->where('status', '!=', 'rejected')
            ->get();

        // Calculate available slots
        $availableSlots = [];
        foreach ($timeSlots as $slot) {
            $startTime = $slot->start_time;
            $endTime = $slot->end_time;

            // Check if this slot overlaps with existing bookings
            $isAvailable = true;
            foreach ($bookings as $booking) {
                if ($booking->start_time < $endTime && $booking->end_time > $startTime) {
                    $isAvailable = false;
                    break;
                }
            }

            if ($isAvailable) {
                $availableSlots[] = [
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'duration_minutes' => $slot->duration_minutes,
                ];
            }
        }

        return response()->json([
            'amenity' => $amenity->name,
            'date' => $request->date,
            'available_slots' => $availableSlots,
        ]);
    }

    /**
     * Store a booking from the resident portal.
     */
    public function storeBooking(Request $request)
    {
        $user = Auth::user();
        $person = $user->person;

        if (!$person) {
            return response()->json(['error' => 'No resident profile found.'], 422);
        }

        $request->validate([
            'amenity_id' => 'required|exists:amenities,id',
            'booking_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'guests_count' => 'nullable|integer|min:0',
            'notes' => 'nullable|string',
        ]);

        $amenity = Amenity::findOrFail($request->amenity_id);

        // Check availability
        $isAvailable = $amenity->isAvailable($request->booking_date, $request->start_time, $request->end_time);

        if (!$isAvailable) {
            return response()->json(['error' => 'This time slot is not available.'], 422);
        }

        $booking = AmenityBooking::create([
            'facility_id' => $user->facility_id,
            'amenity_id' => $request->amenity_id,
            'booked_by' => $person->id,
            'booking_date' => $request->booking_date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'duration_minutes' => $this->calculateDuration($request->start_time, $request->end_time),
            'guests_count' => $request->guests_count ?? 0,
            'notes' => $request->notes,
            'status' => $amenity->requires_approval ? 'pending' : 'confirmed',
            'confirmed_at' => $amenity->requires_approval ? null : now(),
        ]);

        // Notify management if approval required
        if ($amenity->requires_approval) {
            $this->notifyManagement($booking);
        }

        return response()->json([
            'message' => 'Booking created successfully!',
            'booking' => $booking,
        ]);
    }

    /**
     * Calculate duration in minutes.
     */
    protected function calculateDuration($start, $end)
    {
        $startTime = \Carbon\Carbon::parse($start);
        $endTime = \Carbon\Carbon::parse($end);
        return $startTime->diffInMinutes($endTime);
    }

    /**
     * Notify management about new booking.
     */
    protected function notifyManagement($booking)
    {
        try {
            $managementUsers = \App\Models\User::where('facility_id', $booking->facility_id)
                ->whereHas('roles', function ($query) {
                    $query->where('name', 'Facility Manager');
                })
                ->get();

            foreach ($managementUsers as $user) {
                $this->notificationService->sendToUser(
                    $user->id,
                    '📅 New Booking Request: ' . $booking->amenity->name,
                    "Resident: " . ($booking->bookedBy->full_name ?? 'N/A') . "\nDate: " . $booking->booking_date->format('d/m/Y'),
                    ['url' => '/admin/amenities/bookings/' . $booking->id]
                );
            }
        } catch (\Exception $e) {
            \Log::error('Failed to send booking notification: ' . $e->getMessage());
        }
    }
}