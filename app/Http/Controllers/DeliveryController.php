<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Models\Person;
use App\Models\SubUnit;
use App\Models\Facility;
use Illuminate\Http\Request;

class DeliveryController extends Controller
{
    public function index(Request $request)
    {
        $query = Delivery::with(['facility', 'recipient', 'subUnit']);

        if ($request->has('facility_id')) {
            $query->where('facility_id', $request->facility_id);
        }
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        if ($request->has('recipient_person_id')) {
            $query->where('recipient_person_id', $request->recipient_person_id);
        }
        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $deliveries = $query->get();

        if ($request->wantsJson()) {
            return response()->json($deliveries);
        }

        return view('admin.deliveries.index', compact('deliveries'));
    }

    public function create()
    {
        $facilities = Facility::all();
        $people = Person::all();
        $subUnits = SubUnit::all();
        return view('admin.deliveries.create', compact('facilities', 'people', 'subUnits'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'facility_id' => 'required|exists:facilities,id',
            'courier_name' => 'nullable|string|max:100',
            'tracking_number' => 'nullable|string|max:100',
            'recipient_person_id' => 'required|exists:people,id',
            'sub_unit_id' => 'nullable|exists:sub_units,id',
            'status' => 'required|in:pending,received,rejected',
            'notes' => 'nullable|string',
        ]);

        $recipient = Person::find($validated['recipient_person_id']);
        if ($recipient->facility_id != $validated['facility_id']) {
            if ($request->wantsJson()) {
                return response()->json(['error' => 'The recipient does not belong to this facility.'], 422);
            }
            return redirect()->back()->with('error', 'The recipient does not belong to this facility.');
        }

        $delivery = Delivery::create($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Delivery recorded successfully!',
                'delivery' => $delivery->load(['facility', 'recipient', 'subUnit'])
            ], 201);
        }

        return redirect()->route('admin.deliveries.index')
            ->with('success', 'Delivery recorded successfully!');
    }

    public function show(Delivery $delivery)
    {
        return response()->json($delivery->load(['facility', 'recipient', 'subUnit']));
    }

    public function edit(Delivery $delivery)
    {
        $facilities = Facility::all();
        $people = Person::all();
        $subUnits = SubUnit::all();
        return view('admin.deliveries.edit', compact('delivery', 'facilities', 'people', 'subUnits'));
    }

    public function update(Request $request, Delivery $delivery)
    {
        $validated = $request->validate([
            'courier_name' => 'nullable|string|max:100',
            'tracking_number' => 'nullable|string|max:100',
            'status' => 'sometimes|in:pending,received,rejected',
            'notes' => 'nullable|string',
            'delivered_at' => 'nullable|date',
            'facility_id' => 'sometimes|exists:facilities,id',
            'recipient_person_id' => 'sometimes|exists:people,id',
            'sub_unit_id' => 'nullable|exists:sub_units,id',
        ]);

        if (isset($validated['status']) && $validated['status'] === 'received' && !isset($validated['delivered_at'])) {
            $validated['delivered_at'] = now();
        }

        $delivery->update($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Delivery updated successfully!',
                'delivery' => $delivery
            ]);
        }

        return redirect()->route('admin.deliveries.index')
            ->with('success', 'Delivery updated successfully!');
    }

    public function markReceived(Request $request, $id)
    {
        $delivery = Delivery::findOrFail($id);
        
        $delivery->update([
            'status' => 'received',
            'delivered_at' => now(),
            'notes' => $request->input('notes', $delivery->notes)
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Delivery marked as received!',
                'delivery' => $delivery
            ]);
        }

        return redirect()->route('admin.deliveries.index')
            ->with('success', 'Delivery marked as received!');
    }

    public function destroy(Delivery $delivery)
    {
        $delivery->delete();

        if (request()->wantsJson()) {
            return response()->json(['message' => 'Delivery deleted successfully!']);
        }

        return redirect()->route('admin.deliveries.index')
            ->with('success', 'Delivery deleted successfully!');
    }

    public function getByRecipient($personId)
    {
        $deliveries = Delivery::where('recipient_person_id', $personId)
            ->with(['facility', 'subUnit'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($deliveries);
    }

    public function getStats($facilityId)
    {
        $stats = [
            'total' => Delivery::where('facility_id', $facilityId)->count(),
            'pending' => Delivery::where('facility_id', $facilityId)->where('status', 'pending')->count(),
            'received' => Delivery::where('facility_id', $facilityId)->where('status', 'received')->count(),
            'rejected' => Delivery::where('facility_id', $facilityId)->where('status', 'rejected')->count(),
            'today' => Delivery::where('facility_id', $facilityId)
                ->whereDate('created_at', today())
                ->count(),
        ];

        return response()->json($stats);
    }
}