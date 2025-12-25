<?php

namespace App\Http\Controllers\Tutor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\QuizAttempt;
use App\Models\AssignmentSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    /**
     * Display analytics dashboard.
     */
    public function dashboard()
    {
        $tutorId = auth()->id();

        // Get courses owned by tutor
        $courses = Course::where('instructor_id', $tutorId)->get();
        $courseIds = $courses->pluck('id');

        // Overall Statistics
        $stats = [
            'total_courses' => $courses->count(),
            'published_courses' => $courses->where('status', 'published')->count(),
            'total_students' => Enrollment::whereIn('course_id', $courseIds)
                ->distinct('user_id')
                ->count('user_id'),
            'total_enrollments' => Enrollment::whereIn('course_id', $courseIds)->count(),
            'active_students' => Enrollment::whereIn('course_id', $courseIds)
                ->where('status', 'active')
                ->distinct('user_id')
                ->count('user_id'),
            'total_revenue' => Enrollment::whereIn('course_id', $courseIds)
                ->where('status', 'active')
                ->sum('amount_paid'),
        ];

        // Recent enrollments (last 30 days)
        $recentEnrollments = Enrollment::whereIn('course_id', $courseIds)
            ->where('enrolled_at', '>=', now()->subDays(30))
            ->selectRaw('DATE(enrolled_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Top performing courses
        $topCourses = Course::where('instructor_id', $tutorId)
            ->withCount('enrollments')
            ->with('enrollments')
            ->get()
            ->map(function ($course) {
                $course->avg_rating = $course->average_rating ?? 0;
                $course->revenue = $course->enrollments->where('status', 'active')->sum('amount_paid');
                return $course;
            })
            ->sortByDesc('enrollments_count')
            ->take(5);

        return view('tutor.analytics.dashboard', compact('stats', 'recentEnrollments', 'topCourses'));
    }

    /**
     * Display course performance analytics.
     */
    public function coursePerformance(Request $request)
    {
        $tutorId = auth()->id();

        $query = Course::where('instructor_id', $tutorId)
            ->withCount(['enrollments', 'quizAttempts', 'assignmentSubmissions']);

        if ($request->filled('course_id')) {
            $query->where('id', $request->course_id);
        }

        $courses = $query->get()->map(function ($course) {
            // Calculate completion rate
            $totalLessons = $course->topics->sum(function ($topic) {
                return $topic->lessons->count();
            });

            $courseEnrollments = $course->enrollments;
            $completedLessons = DB::table('progress')
                ->whereIn('enrollment_id', $courseEnrollments->pluck('id'))
                ->where('is_completed', true)
                ->count();

            $course->completion_rate = $totalLessons > 0 && $courseEnrollments->count() > 0
                ? round(($completedLessons / ($totalLessons * $courseEnrollments->count())) * 100, 2)
                : 0;

            // Calculate average quiz score
            $course->avg_quiz_score = QuizAttempt::whereHas('quiz.lesson.topic.course', function ($q) use ($course) {
                $q->where('id', $course->id);
            })->avg('score') ?? 0;

            // Calculate average assignment grade
            $course->avg_assignment_grade = AssignmentSubmission::whereHas('assignment.lesson.topic.course', function ($q) use ($course) {
                $q->where('id', $course->id);
            })->whereNotNull('grade')->avg('grade') ?? 0;

            $course->average_rating = $course->average_rating ?? 0;

            return $course;
        });

        return view('tutor.analytics.course-performance', compact('courses'));
    }

    /**
     * Display student engagement analytics.
     */
    public function studentEngagement(Request $request)
    {
        $tutorId = auth()->id();
        $courseIds = Course::where('instructor_id', $tutorId)->pluck('id');

        // Active vs Inactive students
        $activeStudents = Enrollment::whereIn('course_id', $courseIds)
            ->where('status', 'active')
            ->where('last_accessed_at', '>=', now()->subDays(7))
            ->distinct('user_id')
            ->count('user_id');

        $inactiveStudents = Enrollment::whereIn('course_id', $courseIds)
            ->where('status', 'active')
            ->where(function ($q) {
                $q->where('last_accessed_at', '<', now()->subDays(7))
                    ->orWhereNull('last_accessed_at');
            })
            ->distinct('user_id')
            ->count('user_id');

        // Average time spent per course
        $avgTimePerCourse = DB::table('learning_sessions')
            ->whereIn('course_id', $courseIds)
            ->selectRaw('course_id, AVG(duration_minutes) as avg_duration')
            ->groupBy('course_id')
            ->get();

        // Student activity over time (last 30 days)
        $studentActivity = DB::table('enrollments')
            ->whereIn('course_id', $courseIds)
            ->where('last_accessed_at', '>=', now()->subDays(30))
            ->selectRaw('DATE(last_accessed_at) as date, COUNT(DISTINCT user_id) as active_students')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Most engaged students
        $topStudents = Enrollment::whereIn('course_id', $courseIds)
            ->with(['user', 'course'])
            ->where('status', 'active')
            ->get()
            ->map(function ($enrollment) {
                $totalLessons = $enrollment->course->topics->sum(function ($topic) {
                    return $topic->lessons->count();
                });

                $completedLessons = $enrollment->progress()->where('is_completed', true)->count();

                $enrollment->progress_percentage = $totalLessons > 0
                    ? round(($completedLessons / $totalLessons) * 100, 2)
                    : 0;

                $enrollment->quiz_attempts_count = $enrollment->quizAttempts()->count();
                $enrollment->assignment_submissions_count = $enrollment->assignmentSubmissions()->count();

                return $enrollment;
            })
            ->sortByDesc('progress_percentage')
            ->take(10);

        return view('tutor.analytics.student-engagement', compact(
            'activeStudents',
            'inactiveStudents',
            'avgTimePerCourse',
            'studentActivity',
            'topStudents'
        ));
    }

    /**
     * Display revenue analytics.
     */
    public function revenue(Request $request)
    {
        $tutorId = auth()->id();
        $courseIds = Course::where('instructor_id', $tutorId)->pluck('id');

        // Total revenue statistics
        $totalRevenue = Enrollment::whereIn('course_id', $courseIds)
            ->where('status', 'active')
            ->sum('amount_paid');

        $monthlyRevenue = Enrollment::whereIn('course_id', $courseIds)
            ->where('status', 'active')
            ->where('enrolled_at', '>=', now()->startOfMonth())
            ->sum('amount_paid');

        $yearlyRevenue = Enrollment::whereIn('course_id', $courseIds)
            ->where('status', 'active')
            ->where('enrolled_at', '>=', now()->startOfYear())
            ->sum('amount_paid');

        // Revenue by course
        $revenueByCourse = Course::where('instructor_id', $tutorId)
            ->with('enrollments')
            ->get()
            ->map(function ($course) {
                $course->total_revenue = $course->enrollments->where('status', 'active')->sum('amount_paid');
                $course->total_enrollments = $course->enrollments->where('status', 'active')->count();
                $course->avg_revenue_per_student = $course->total_enrollments > 0
                    ? round($course->total_revenue / $course->total_enrollments, 2)
                    : 0;
                return $course;
            })
            ->sortByDesc('total_revenue');

        // Revenue over time (last 12 months)
        $revenueOverTime = Enrollment::whereIn('course_id', $courseIds)
            ->where('status', 'active')
            ->where('enrolled_at', '>=', now()->subMonths(12))
            ->selectRaw('DATE_FORMAT(enrolled_at, "%Y-%m") as month, SUM(amount_paid) as revenue, COUNT(*) as enrollments')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return view('tutor.analytics.revenue', compact(
            'totalRevenue',
            'monthlyRevenue',
            'yearlyRevenue',
            'revenueByCourse',
            'revenueOverTime'
        ));
    }
}
