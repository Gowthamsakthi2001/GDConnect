<?php

// app/Http/Controllers/NotificationController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FirebaseNotificationService;

class NotificationController extends Controller
{
    public function sendNotification(Request $request, FirebaseNotificationService $service)
    {
        $validated = $request->validate([
            'dm_id' => 'nullable|required_without:user_id|exists:ev_tbl_delivery_men,id',
            'user_id' => 'nullable|required_without:dm_id|exists:users,id',
            'device_token' => 'required|string',
            'title' => 'required|string|max:100',
            'body' => 'required|string|max:255',
            'data' => 'nullable|array'
        ]);

        try {
            $notification = $service->sendNotification(
                $validated['dm_id'] ?? null,
                $validated['user_id'] ?? null,
                $validated['device_token'],
                $validated['title'],
                $validated['body'],
                $validated['data'] ?? []
            );

            return response()->json([
                'success' => true,
                'notification' => $notification
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}