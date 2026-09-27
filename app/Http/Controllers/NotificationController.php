<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    public function index()
    {
        $userId = session('user_id');

        $sql = "
            SELECT * FROM notifications
            WHERE (user_id = ? OR user_id IS NULL)
            ORDER BY id DESC
        ";

        $notifications = raw_paginate($sql, [$userId], 20);

        return view('admin.notifications.index', compact('notifications'));
    }

    public function markAsRead($id)
    {
        $userId = session('user_id');

        DB::update("
            UPDATE notifications
            SET is_read = 1, updated_at = NOW()
            WHERE id = ? AND (user_id = ? OR user_id IS NULL)
        ", [$id, $userId]);

        return back()->with('success', 'Notification marked as read.');
    }

    public function markAllAsRead()
    {
        $userId = session('user_id');

        DB::update("
            UPDATE notifications
            SET is_read = 1, updated_at = NOW()
            WHERE (user_id = ? OR user_id IS NULL) AND is_read = 0
        ", [$userId]);

        return back()->with('success', 'All notifications marked as read.');
    }

    /**
     * JSON endpoint for top navbar badge and dropdown.
     */
    public function getUnread()
    {
        $userId = session('user_id');

        $notifications = DB::select("
            SELECT * FROM notifications
            WHERE (user_id = ? OR user_id IS NULL) AND is_read = 0
            ORDER BY id DESC
            LIMIT 5
        ", [$userId]);

        $unreadCount = DB::select("
            SELECT COUNT(*) as cnt FROM notifications
            WHERE (user_id = ? OR user_id IS NULL) AND is_read = 0
        ", [$userId])[0]->cnt;

        return response()->json([
            'unread_count' => $unreadCount,
            'notifications' => $notifications
        ]);
    }
}
