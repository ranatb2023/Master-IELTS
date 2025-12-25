<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $stats = [];

        // User stats - only if user has user.view permission
        if ($user->can('user.view')) {
            $stats['total_users'] = User::count();
            $stats['total_students'] = User::role('student')->count();
            $stats['total_tutors'] = User::role('tutor')->count();
            $stats['active_users'] = User::active()->count();
        }

        // Course stats - only if user has course.view permission
        if ($user->can('course.view')) {
            $stats['total_courses'] = \App\Models\Course::count() ?? 0;
            $stats['active_courses'] = \App\Models\Course::where('status', 'published')->count() ?? 0;
        }

        // Enrollment and Order stats - only if user has order.view permission
        if ($user->can('order.view')) {
            $stats['total_enrollments'] = \App\Models\Enrollment::count() ?? 0;
            $stats['total_revenue'] = \App\Models\Order::where('status', 'completed')->sum('total') ?? 0;
        }

        // Recent enrollments - only if user has order.view permission
        $recentEnrollments = collect();
        if ($user->can('order.view')) {
            $recentEnrollments = \App\Models\Enrollment::with(['user', 'course'])
                ->latest('enrolled_at')
                ->take(5)
                ->get();
        }

        // Popular courses - only if user has course.view permission
        $popularCourses = collect();
        if ($user->can('course.view')) {
            $popularCourses = \App\Models\Course::withCount('enrollments')
                ->orderBy('enrollments_count', 'desc')
                ->take(5)
                ->get();
        }

        // Refund statistics - only if user has order.view permission
        $refundStats = null;
        if ($user->can('order.view')) {
            $refundReport = new \App\Services\RefundReport();
            $refundStats = $refundReport->getDashboardStats();
        }

        return view('admin.dashboard', compact('stats', 'recentEnrollments', 'popularCourses', 'refundStats'));
    }
}