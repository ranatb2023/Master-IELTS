<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\NotificationController as BaseNotificationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends BaseNotificationController
{
    /**
     * Get all notifications for student with filters
     */
    public function index(Request $request)
    {
        $query = Auth::user()->notifications();

        // Filter by category
        if ($request->has('category') && $request->category !== 'all') {
            $categoryMap = [
                'courses' => ['CourseEnrolledNotification', 'NewCourseContentNotification'],
                'assignments' => ['AssignmentDueNotification', 'AssignmentGradedNotification'],
                'payments' => ['PackagePurchasedNotification', 'SubscriptionUpdatedNotification', 'PaymentFailedNotification'],
                'certificates' => ['CertificateEarnedNotification'],
            ];

            if (isset($categoryMap[$request->category])) {
                $query->where(function ($q) use ($categoryMap, $request) {
                    foreach ($categoryMap[$request->category] as $type) {
                        $q->orWhere('type', 'like', '%\\' . $type);
                    }
                });
            }
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

        return view('student.notifications.index', compact('notifications'));
    }

    protected function getViewPrefix()
    {
        return 'student';
    }
}
