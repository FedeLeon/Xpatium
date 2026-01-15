<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        $notifications = $request->user()
            ->notifications()
            ->latest()
            ->get();

        $view = $request->user()->role === User::ROLE_TEACHER
            ? 'teacher.notifications.index'
            : 'student.notifications.index';

        return view($view, compact('notifications'));
    }

    public function destroy(Request $request, string $notificationId): RedirectResponse
    {
        $notification = $request->user()->notifications()->where('id', $notificationId)->firstOrFail();
        $notification->delete();

        return back();
    }

    public function destroyAll(Request $request): RedirectResponse
    {
        $request->user()->notifications()->delete();

        return back();
    }

    public function markAllRead(Request $request): RedirectResponse
    {
        $request->user()->unreadNotifications->markAsRead();

        return back();
    }
}
