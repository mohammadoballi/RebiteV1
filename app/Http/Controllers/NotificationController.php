<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    public function index(): JsonResponse
    {
        $notifications = auth()->user()
            ->notifications()
            ->latest()
            ->paginate(20);

        return response()->json($notifications);
    }

    public function markAsRead(string $id): JsonResponse
    {
        auth()->user()
            ->notifications()
            ->where('id', $id)
            ->first()
            ?->markAsRead();

        return response()->json(['message' => __('Notification marked as read.')]);
    }

    public function markAllAsRead(): JsonResponse
    {
        auth()->user()->unreadNotifications->markAsRead();

        return response()->json(['message' => __('All notifications marked as read.')]);
    }

    public function unreadCount(): JsonResponse
    {
        return response()->json([
            'count' => auth()->user()->unreadNotifications()->count(),
        ]);
    }
}
