<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index() {
        $notifications = auth()->user()->notifications()->latest()->paginate(20);
        return view('notifications.index', compact('notifications'));
    }

    public function markAllRead() {
        auth()->user()->unreadNotifications->markAsRead();
        return response()->noContent();
    }

    public function markRead($uuid) {
        auth()->user()->unreadNotifications->where('uuid', $uuid)->markAsRead();
        return response()->noContent();
    }
}
