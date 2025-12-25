<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\Certificate;
use App\Models\QuizAttempt;
use App\Models\AssignmentSubmission;
use App\Models\CourseProgress;
use App\Models\Progress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $student = Auth::user();

        // Get enrollments (excluding canceled/refunded)
        $enrollments = $student->enrollments()
            ->where('status', '!=', 'canceled')
            ->with('course')
            ->get();

        $activeEnrollments = $student->enrollments()
            ->where('status', 'active')
            ->where('payment_status', '!=', 'refunded')
            ->with('course')
            ->orderBy('last_accessed_at', 'desc')
            ->take(6)
            ->get();

        // Calculate stats
        $stats = [
            'enrolled_courses' => $enrollments->count(),
            'completed_courses' => $enrollments->where('status', 'completed')->count(),
            'certificates_earned' => Certificate::where('user_id', $student->id)->count(),
            'average_progress' => $enrollments->count() > 0
                ? $enrollments->avg('progress_percentage')
                : 0,
        ];

        // Recent activity
        $recentQuizAttempts = QuizAttempt::where('user_id', $student->id)
            ->with('quiz')
            ->whereNotNull('completed_at')
            ->orderBy('completed_at', 'desc')
            ->take(5)
            ->get();

        $recentAssignments = AssignmentSubmission::where('user_id', $student->id)
            ->with('assignment')
            ->whereNotNull('submitted_at')
            ->orderBy('submitted_at', 'desc')
            ->take(5)
            ->get();

        return view('student.dashboard', compact(
            'stats',
            'activeEnrollments',
            'recentQuizAttempts',
            'recentAssignments'
        ));
    }

    /**
     * Progress tracking dashboard
     */
    public function progress()
    {
        $student = Auth::user();

        // Get all course progress
        $courseProgress = CourseProgress::where('user_id', $student->id)
            ->with('course')
            ->orderBy('last_accessed_at', 'desc')
            ->get();

        // Calculate overall statistics
        $totalTimeSpent = $courseProgress->sum('total_time_spent');
        $totalLessonsCompleted = $courseProgress->sum('completed_lessons');
        $totalQuizzesCompleted = $courseProgress->sum('completed_quizzes');
        $totalAssignmentsCompleted = $courseProgress->sum('completed_assignments');

        // Get average quiz and assignment scores
        $avgQuizScore = $courseProgress->whereNotNull('average_quiz_score')->avg('average_quiz_score');
        $avgAssignmentScore = $courseProgress->whereNotNull('average_assignment_score')->avg('average_assignment_score');

        // Get recent progress activity (lessons, quizzes, assignments)
        $recentLessons = Progress::where('user_id', $student->id)
            ->where('progressable_type', 'App\\Models\\Lesson')
            ->where('status', 'completed')
            ->with('progressable')
            ->orderBy('completed_at', 'desc')
            ->take(10)
            ->get();

        // Learning streaks - days with activity
        $learningDays = Progress::where('user_id', $student->id)
            ->whereNotNull('completed_at')
            ->select(DB::raw('DATE(completed_at) as date'))
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->take(30)
            ->pluck('date');

        // Calculate current streak
        $currentStreak = 0;
        $today = now()->startOfDay();
        foreach ($learningDays as $day) {
            $dayDate = \Carbon\Carbon::parse($day)->startOfDay();
            $daysDiff = $today->diffInDays($dayDate);

            if ($daysDiff === $currentStreak) {
                $currentStreak++;
            } else {
                break;
            }
        }

        // Weekly progress - lessons completed per day in last 7 days
        $weeklyProgress = Progress::where('user_id', $student->id)
            ->where('progressable_type', 'App\\Models\\Lesson')
            ->where('status', 'completed')
            ->where('completed_at', '>=', now()->subDays(7))
            ->select(DB::raw('DATE(completed_at) as date, COUNT(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('count', 'date');

        // Time spent per course
        $timeByCourse = $courseProgress->map(function ($cp) {
            return [
                'course' => $cp->course->title,
                'time' => $cp->total_time_spent,
                'time_formatted' => $this->formatTime($cp->total_time_spent)
            ];
        });

        // Overall stats
        $stats = [
            'total_time_spent' => $this->formatTime($totalTimeSpent),
            'total_time_seconds' => $totalTimeSpent,
            'total_lessons_completed' => $totalLessonsCompleted,
            'total_quizzes_completed' => $totalQuizzesCompleted,
            'total_assignments_completed' => $totalAssignmentsCompleted,
            'avg_quiz_score' => $avgQuizScore ? round($avgQuizScore, 1) : null,
            'avg_assignment_score' => $avgAssignmentScore ? round($avgAssignmentScore, 1) : null,
            'current_streak' => $currentStreak,
            'total_courses' => $courseProgress->count(),
            'completed_courses' => $courseProgress->where('progress_percentage', 100)->count(),
        ];

        return view('student.progress', compact(
            'courseProgress',
            'stats',
            'recentLessons',
            'weeklyProgress',
            'timeByCourse',
            'learningDays'
        ));
    }

    /**
     * Format seconds to human readable time
     */
    private function formatTime($seconds)
    {
        if ($seconds < 60) {
            return $seconds . 's';
        } elseif ($seconds < 3600) {
            $minutes = floor($seconds / 60);
            return $minutes . 'm';
        } else {
            $hours = floor($seconds / 3600);
            $minutes = floor(($seconds % 3600) / 60);
            return $hours . 'h ' . ($minutes > 0 ? $minutes . 'm' : '');
        }
    }
}