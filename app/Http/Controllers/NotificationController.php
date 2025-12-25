<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Get all notifications for authenticated user (paginated)
     */
    public function index(Request $request)
    {
        $notifications = Auth::user()
            ->notifications()
            ->paginate(20);

        return view($this->getViewPrefix() . '.notifications.index', compact('notifications'));
    }

    /**
     * Get unread notifications count (API endpoint)
     */
    public function unread()
    {
        $count = Auth::user()->unreadNotifications->count();
        return response()->json(['count' => $count]);
    }

    /**
     * Mark single notification as read
     */
    public function markAsRead($id)
    {
        $notification = Auth::user()
            ->notifications()
            ->findOrFail($id);

        $notification->markAsRead();

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Notification marked as read');
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'All notifications marked as read');
    }

    /**
     * Delete single notification
     */
    public function destroy($id)
    {
        $notification = Auth::user()
            ->notifications()
            ->findOrFail($id);

        $notification->delete();

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Notification deleted');
    }

    /**
     * Clear all read notifications
     */
    public function clearAll()
    {
        Auth::user()
            ->notifications()
            ->whereNotNull('read_at')
            ->delete();

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'All read notifications cleared');
    }

    /**
     * Get the view prefix based on user role
     */
    protected function getViewPrefix()
    {
        $user = Auth::user();

        if ($user->hasRole('super_admin')) {
            return 'admin';
        } elseif ($user->hasRole('tutor')) {
            return 'tutor';
        } elseif ($user->hasRole('student')) {
            return 'student';
        }

        return 'student'; // default
    }
}
