<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function page(Request $request)
    {
        $user = $request->user();
        $onlyUnread = $request->boolean('unread');

        $query = $onlyUnread ? $user->unreadNotifications() : $user->notifications();
        $notifications = $query->latest()->paginate(20)->withQueryString();

        return view('student.notifications.index', [
            'notifications' => $notifications,
            'unreadCount' => $user->unreadNotifications()->count(),
            'onlyUnread' => $onlyUnread,
        ]);
    }
    public function index(Request $request)
    {
        $user = $request->user();
        return response()->json([
            'data' => $user->notifications()->latest()->paginate(20),
            'unread_count' => $user->unreadNotifications()->count(),
        ]);
    }

    public function unread(Request $request)
    {
        $user = $request->user();
        return response()->json([
            'data' => $user->unreadNotifications()->latest()->paginate(20),
        ]);
    }

    public function markAsRead(Request $request, string $id)
    {
        $user = $request->user();
        $notification = $user->notifications()->whereKey($id)->firstOrFail();
        $notification->markAsRead();

        return response()->json(['status' => 'ok']);
    }

    public function markAllAsRead(Request $request)
    {
        $user = $request->user();
        $user->unreadNotifications->markAsRead();

        return response()->json(['status' => 'ok']);
    }
}