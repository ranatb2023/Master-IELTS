<?php

namespace App\Http\Controllers\Tutor;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    /**
     * Display a listing of enrollments for tutor's courses.
     */
    public function index(Request $request)
    {
        $query = Enrollment::with(['user', 'course'])
            ->whereHas('course', function ($q) {
                $q->where('instructor_id', auth()->id());
            });

        // Apply filters
        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $enrollments = $query->latest()->paginate(20);

        // Get tutor's courses for filter
        $courses = auth()->user()->createdCourses()
            ->select('id', 'title')
            ->orderBy('title')
            ->get();

        return view('tutor.enrollments.index', compact('enrollments', 'courses'));
    }

    /**
     * Display the specified enrollment.
     */
    public function show(Enrollment $enrollment)
    {
        // Ensure enrollment belongs to tutor's course
        if ($enrollment->course->instructor_id !== auth()->id()) {
            abort(403, 'Unauthorized access to this enrollment.');
        }

        $enrollment->load([
            'user',
            'course.topics.lessons',
            'progress',
            'quizAttempts.quiz',
            'assignmentSubmissions.assignment'
        ]);

        // Calculate progress statistics
        $totalLessons = $enrollment->course->topics->sum(function ($topic) {
            return $topic->lessons->count();
        });

        $completedLessons = $enrollment->progress()
            ->where('is_completed', true)
            ->count();

        $progressPercentage = $totalLessons > 0
            ? round(($completedLessons / $totalLessons) * 100)
            : 0;

        // Get quiz statistics
        $quizStats = [
            'total_attempts' => $enrollment->quizAttempts->count(),
            'passed_attempts' => $enrollment->quizAttempts->where('status', 'passed')->count(),
            'average_score' => $enrollment->quizAttempts->avg('score') ?? 0,
        ];

        // Get assignment statistics
        $assignmentStats = [
            'total_submissions' => $enrollment->assignmentSubmissions->count(),
            'graded_submissions' => $enrollment->assignmentSubmissions->whereNotNull('grade')->count(),
            'pending_submissions' => $enrollment->assignmentSubmissions->where('status', 'pending')->count(),
            'average_grade' => $enrollment->assignmentSubmissions->whereNotNull('grade')->avg('grade') ?? 0,
        ];

        return view('tutor.enrollments.show', compact(
            'enrollment',
            'totalLessons',
            'completedLessons',
            'progressPercentage',
            'quizStats',
            'assignmentStats'
        ));
    }
}
