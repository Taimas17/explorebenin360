<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * GET /api/v1/notifications
     */
    public function index(Request $request)
    {
        $data = $request->validate([
            'unread_only' => ['nullable', 'boolean'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $user = $request->user();
        $limit = $data['limit'] ?? 20;
        $unreadOnly = $data['unread_only'] ?? false;

        $query = $user->notifications();
        
        if ($unreadOnly) {
            $query->whereNull('read_at');
        }

        $notifications = $query->limit($limit)->get();
        
        $unreadCount = $unreadOnly ? $notifications->count() : $user->unreadNotifications->count();

        return response()->json([
            'data' => $notifications->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'type' => class_basename($notification->type),
                    'data' => $notification->data,
                    'read_at' => $notification->read_at,
                    'created_at' => $notification->created_at,
                ];
            }),
            'meta' => [
                'unread_count' => $unreadCount,
            ]
        ]);
    }

    /**
     * PATCH /api/v1/notifications/{id}/read
     */
    public function markAsRead(Request $request, string $id)
    {
        $user = $request->user();
        $notification = $user->notifications()->where('id', $id)->first();
        
        if (!$notification) {
            return response()->json(['message' => 'Notification not found'], 404);
        }
        
        $notification->markAsRead();
        
        return response()->json(['message' => 'Marked as read']);
    }

    /**
     * POST /api/v1/notifications/mark-all-read
     */
    public function markAllAsRead(Request $request)
    {
        $user = $request->user();
        $user->unreadNotifications->markAsRead();
        
        return response()->json(['message' => 'All notifications marked as read']);
    }

    /**
     * DELETE /api/v1/notifications/{id}
     */
    public function destroy(Request $request, string $id)
    {
        $user = $request->user();
        $notification = $user->notifications()->where('id', $id)->first();
        
        if (!$notification) {
            return response()->json(['message' => 'Notification not found'], 404);
        }
        
        $notification->delete();
        
        return response()->json(['message' => 'Notification deleted']);
    }
}
