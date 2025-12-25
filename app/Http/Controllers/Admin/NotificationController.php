<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\NotificationController as BaseNotificationController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class NotificationController extends BaseNotificationController
{
    /**
     * Get all notifications for admin with filters
     */
    public function index(Request $request)
    {
        $query = Auth::user()->notifications();

        // Filter by type
        if ($request->has('type') && $request->type !== 'all') {
            $query->where('type', 'like', '%\\' . $request->type . '%');
        }

        // Filter by read status
        if ($request->has('status')) {
            if ($request->status === 'unread') {
                $query->whereNull('read_at');
            } elseif ($request->status === 'read') {
                $query->whereNotNull('read_at');
            }
        }

        $notifications = $query->paginate(20);

        return view('admin.notifications.index', compact('notifications'));
    }

    /**
     * Get system-wide notifications
     */
    public function systemNotifications()
    {
        // This could be extended to return system-level alerts/announcements
        $systemNotifications = Auth::user()
            ->notifications()
            ->whereIn('type', [
                'App\\Notifications\\PaymentReceivedNotification',
                'App\\Notifications\\RefundRequestedNotification',
                'App\\Notifications\\CourseSubmittedForApprovalNotification',
            ])
            ->paginate(20);

        return view('admin.notifications.system', compact('systemNotifications'));
    }

    /**
     * Broadcast notification to users
     */
    public function broadcastNotification(Request $request)
    {
        $request->validate([
            'role' => 'required|in:all,student,tutor',
            'title' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        // Get users based on role
        if ($request->role === 'all') {
            $users = User::all();
        } else {
            $users = User::role($request->role)->get();
        }

        // Create a simple broadcast notification
        foreach ($users as $user) {
            $user->notify(new \App\Notifications\BroadcastNotification(
                $request->title,
                $request->message
            ));
        }

        return redirect()->back()->with('success', 'Notification sent to ' . $users->count() . ' users');
    }

    protected function getViewPrefix()
    {
        return 'admin';
    }
}
