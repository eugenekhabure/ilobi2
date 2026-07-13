<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use App\Models\VisitingDetails;
use App\Models\Visitor;
use App\Models\Person;
use App\Models\Invitation;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeedbackController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
        $this->middleware('auth')->except(['showForm', 'store']);
    }

    /**
     * Show the feedback form for a visitor.
     */
    public function showForm(Request $request)
    {
        $visitingDetailId = $request->visiting_detail_id;

        // Find the visiting detail
        $visitingDetail = VisitingDetails::with(['visitor', 'employee.user'])
            ->find($visitingDetailId);

        if (!$visitingDetail) {
            return redirect('/')->with('error', 'Invalid visit reference.');
        }

        // Check if feedback already exists
        $existingFeedback = Feedback::where('visiting_detail_id', $visitingDetailId)->first();
        if ($existingFeedback) {
            return view('feedback.already-done', ['visitingDetail' => $visitingDetail]);
        }

        return view('feedback.form', compact('visitingDetail'));
    }

    /**
     * Store feedback.
     */
    public function store(Request $request)
    {
        $request->validate([
            'visiting_detail_id' => 'required|exists:visiting_details,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500',
            'host_rating' => 'nullable|integer|min:1|max:5',
            'security_rating' => 'nullable|integer|min:1|max:5',
            'cleanliness_rating' => 'nullable|integer|min:1|max:5',
            'overall_rating' => 'nullable|integer|min:1|max:5',
            'would_recommend' => 'nullable|boolean',
        ]);

        $visitingDetail = VisitingDetails::with(['visitor', 'employee.user'])
            ->find($request->visiting_detail_id);

        if (!$visitingDetail) {
            return back()->with('error', 'Invalid visit reference.');
        }

        // Check if feedback already exists
        $existingFeedback = Feedback::where('visiting_detail_id', $request->visiting_detail_id)->first();
        if ($existingFeedback) {
            return back()->with('error', 'Feedback already submitted for this visit.');
        }

        // Get host person ID if available
        $hostPersonId = null;
        if ($visitingDetail->employee) {
            $person = Person::where('email', $visitingDetail->employee->user->email ?? null)->first();
            if ($person) {
                $hostPersonId = $person->id;
            }
        }

        // Create feedback
        $feedback = Feedback::create([
            'facility_id' => $visitingDetail->facility_id ?? null,
            'visiting_detail_id' => $request->visiting_detail_id,
            'visitor_id' => $visitingDetail->visitor_id,
            'host_person_id' => $hostPersonId,
            'rating' => $request->rating,
            'comment' => $request->comment,
            'host_rating' => $request->host_rating,
            'security_rating' => $request->security_rating,
            'cleanliness_rating' => $request->cleanliness_rating,
            'overall_rating' => $request->overall_rating ?? $request->rating,
            'would_recommend' => $request->has('would_recommend'),
            'submitted_at' => now(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        // Flag low ratings
        if ($feedback->rating <= 2) {
            $feedback->update([
                'is_flagged' => true,
                'flag_reason' => 'Low rating: ' . $feedback->rating . '/5',
            ]);

            // Notify facility management
            $this->notifyManagement($feedback);
        }

        return redirect()->route('feedback.thankyou', $feedback->id)
            ->with('success', 'Thank you for your feedback!');
    }

    /**
     * Show thank you page.
     */
    public function thankYou($id)
    {
        $feedback = Feedback::with(['facility', 'visitor'])->findOrFail($id);
        return view('feedback.thankyou', compact('feedback'));
    }

    /**
     * Display feedback dashboard (admin).
     */
    public function index(Request $request)
    {
        $facilityId = Auth::user()->facility_id;

        $query = Feedback::where('facility_id', $facilityId)
            ->with(['visitor', 'facility', 'host']);

        // Filters
        if ($request->has('rating') && $request->rating != 'all') {
            $query->where('rating', $request->rating);
        }

        if ($request->has('date_from')) {
            $query->whereDate('submitted_at', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('submitted_at', '<=', $request->date_to);
        }

        if ($request->has('flagged') && $request->flagged) {
            $query->where('is_flagged', true);
        }

        $feedback = $query->orderBy('submitted_at', 'desc')
            ->paginate(20);

        // Stats
        $stats = [
            'total' => Feedback::where('facility_id', $facilityId)->count(),
            'average_rating' => Feedback::where('facility_id', $facilityId)->avg('rating') ?? 0,
            'high_rated' => Feedback::where('facility_id', $facilityId)->where('rating', '>=', 4)->count(),
            'low_rated' => Feedback::where('facility_id', $facilityId)->where('rating', '<=', 2)->count(),
            'flagged' => Feedback::where('facility_id', $facilityId)->where('is_flagged', true)->count(),
            'would_recommend' => Feedback::where('facility_id', $facilityId)->where('would_recommend', true)->count(),
        ];

        $ratings = [
            'all' => 'All Ratings',
            5 => '⭐ 5 Stars',
            4 => '⭐ 4 Stars',
            3 => '⭐ 3 Stars',
            2 => '⭐ 2 Stars',
            1 => '⭐ 1 Star',
        ];

        return view('admin.feedback.index', compact('feedback', 'stats', 'ratings'));
    }

    /**
     * Show feedback details.
     */
    public function show($id)
    {
        $facilityId = Auth::user()->facility_id;

        $feedback = Feedback::where('facility_id', $facilityId)
            ->with(['visitor', 'facility', 'host', 'visitingDetail.employee.user'])
            ->findOrFail($id);

        return view('admin.feedback.show', compact('feedback'));
    }

    /**
     * Toggle flag status.
     */
    public function toggleFlag($id)
    {
        $facilityId = Auth::user()->facility_id;

        $feedback = Feedback::where('facility_id', $facilityId)->findOrFail($id);

        $feedback->update([
            'is_flagged' => !$feedback->is_flagged,
            'flag_reason' => $feedback->is_flagged ? null : 'Manually flagged',
        ]);

        return redirect()->route('admin.feedback.show', $feedback->id)
            ->with('success', 'Flag status updated!');
    }

    /**
     * Delete feedback.
     */
    public function destroy($id)
    {
        $facilityId = Auth::user()->facility_id;

        $feedback = Feedback::where('facility_id', $facilityId)->findOrFail($id);
        $feedback->delete();

        return redirect()->route('admin.feedback.index')
            ->with('success', 'Feedback deleted successfully!');
    }

    /**
     * Notify management about low ratings.
     */
    protected function notifyManagement($feedback)
    {
        try {
            $facility = $feedback->facility;
            $managementUsers = \App\Models\User::where('facility_id', $facility->id)
                ->whereHas('roles', function ($query) {
                    $query->where('name', 'Facility Manager');
                })
                ->get();

            foreach ($managementUsers as $user) {
                // Send notification
                $this->notificationService->sendToUser(
                    $user->id,
                    '⚠️ Low Rating Alert',
                    "Visitor rated {$feedback->rating}/5 stars. Comment: " . ($feedback->comment ?? 'No comment provided.'),
                    ['url' => '/admin/feedback/' . $feedback->id]
                );
            }
        } catch (\Exception $e) {
            Log::error('Failed to send low rating alert: ' . $e->getMessage());
        }
    }

    /**
     * Get feedback stats for API (PWA).
     */
    public function getStatsApi(Request $request)
    {
        $facilityId = $request->facility_id ?? Auth::user()->facility_id;

        $stats = [
            'total' => Feedback::where('facility_id', $facilityId)->count(),
            'average_rating' => round(Feedback::where('facility_id', $facilityId)->avg('rating') ?? 0, 1),
            'rating_distribution' => [
                1 => Feedback::where('facility_id', $facilityId)->where('rating', 1)->count(),
                2 => Feedback::where('facility_id', $facilityId)->where('rating', 2)->count(),
                3 => Feedback::where('facility_id', $facilityId)->where('rating', 3)->count(),
                4 => Feedback::where('facility_id', $facilityId)->where('rating', 4)->count(),
                5 => Feedback::where('facility_id', $facilityId)->where('rating', 5)->count(),
            ],
        ];

        return response()->json($stats);
    }
}