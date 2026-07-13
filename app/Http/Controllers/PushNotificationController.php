<?php

namespace App\Http\Controllers;

use App\Models\PushSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PushNotificationController extends Controller
{
    public function subscribe(Request $request)
    {
        $request->validate([
            'endpoint' => 'required|string',
            'public_key' => 'nullable|string',
            'auth_token' => 'nullable|string',
            'device_type' => 'nullable|string',
            'device_name' => 'nullable|string',
        ]);

        $subscription = PushSubscription::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'endpoint' => $request->endpoint,
            ],
            [
                'public_key' => $request->public_key,
                'auth_token' => $request->auth_token,
                'device_type' => $request->device_type ?? 'browser',
                'device_name' => $request->device_name ?? null,
                'is_active' => true,
            ]
        );

        return response()->json([
            'message' => 'Subscribed successfully',
            'subscription' => $subscription,
        ]);
    }

    public function unsubscribe(Request $request)
    {
        $request->validate([
            'endpoint' => 'required|string',
        ]);

        $subscription = PushSubscription::where('user_id', Auth::id())
            ->where('endpoint', $request->endpoint)
            ->first();

        if ($subscription) {
            $subscription->delete();
            return response()->json(['message' => 'Unsubscribed successfully']);
        }

        return response()->json(['message' => 'Subscription not found'], 404);
    }

    public function test(Request $request)
    {
        $userId = $request->user_id ?? Auth::id();
        $subscriptions = PushSubscription::where('user_id', $userId)
            ->where('is_active', true)
            ->get();

        $sent = 0;
        foreach ($subscriptions as $subscription) {
            $sent++;
            Log::info("Test push sent to user {$userId}: endpoint {$subscription->endpoint}");
        }

        return response()->json([
            'message' => "Sent to {$sent} devices",
            'total' => $subscriptions->count(),
            'sent' => $sent,
        ]);
    }

    public function getUserSubscriptions()
    {
        $subscriptions = PushSubscription::where('user_id', Auth::id())
            ->where('is_active', true)
            ->get();

        return response()->json($subscriptions);
    }

    public function getVapidKey()
    {
        $vapidPublicKey = env('VAPID_PUBLIC_KEY');
        return response()->json(['vapid_public_key' => $vapidPublicKey]);
    }
}