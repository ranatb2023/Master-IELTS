<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    /**
     * Display all enrollments (My Courses)
     */
    public function index(Request $request)
    {
        $query = auth()->user()->enrollments()
            ->with(['course.instructor', 'course.courseCategories']);

        // Filter by status
        $status = $request->get('status', 'all');
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        // Sort
        $sortBy = $request->get('sort', 'recent');
        switch ($sortBy) {
            case 'progress':
                $query->orderBy('progress_percentage', 'desc');
                break;
            case 'title':
                $query->join('courses', 'enrollments.course_id', '=', 'courses.id')
                    ->orderBy('courses.title');
                break;
            default:
                $query->latest('last_accessed_at');
        }

        $enrollments = $query->paginate(12);

        return view('student.enrollments.index', compact('enrollments', 'status', 'sortBy'));
    }

    /**
     * Display enrollment details
     */
    public function show(Enrollment $enrollment)
    {
        // Ensure user owns this enrollment
        if ($enrollment->user_id !== auth()->id()) {
            abort(403);
        }

        $enrollment->load([
            'course.instructor',
            'course.topics.lessons',
            'course.topics.quizzes',
            'course.topics.assignments',
        ]);

        // Get user's progress, quiz attempts, and assignment submissions for this course
        $progress = $enrollment->course_progress;
        $quizAttempts = $enrollment->course_quiz_attempts;
        $assignmentSubmissions = $enrollment->course_assignment_submissions;

        $stats = [
            'topics_count' => $enrollment->course->topics()->count(),
            'lessons_count' => $enrollment->course->topics()
                ->withCount('lessons')
                ->get()
                ->sum('lessons_count'),
            'completed_lessons' => $progress->where('status', 'completed')->count(),
            'quizzes_count' => $enrollment->course->topics()
                ->withCount('quizzes')
                ->get()
                ->sum('quizzes_count'),
            'quizzes_taken' => $quizAttempts->unique('quiz_id')->count(),
            'assignments_count' => $enrollment->course->topics()
                ->withCount('assignments')
                ->get()
                ->sum('assignments_count'),
            'assignments_submitted' => $assignmentSubmissions->whereNotNull('submitted_at')->count(),
            'average_quiz_score' => $quizAttempts->whereNotNull('percentage')->avg('percentage'),
        ];

        return view('student.enrollments.show', compact('enrollment', 'stats'));
    }

    /**
     * Display learning progress
     */
    public function progress(Enrollment $enrollment)
    {
        // Ensure user owns this enrollment
        if ($enrollment->user_id !== auth()->id()) {
            abort(403);
        }

        $enrollment->load([
            'course.topics.lessons',
            'course.topics.quizzes',
            'course.topics.assignments',
        ]);

        // Get user's progress, quiz attempts, and assignment submissions for this course
        $progress = $enrollment->course_progress->load('progressable');
        $quizAttempts = $enrollment->course_quiz_attempts->load('quiz');
        $assignmentSubmissions = $enrollment->course_assignment_submissions->load('assignment');

        // Overall stats
        $stats = [
            'total_lessons' => $enrollment->course->topics()
                ->withCount('lessons')
                ->get()
                ->sum('lessons_count'),
            'completed_lessons' => $progress->where('status', 'completed')->count(),
            'total_quiz_attempts' => $quizAttempts->count(),
            'average_quiz_score' => $quizAttempts->whereNotNull('percentage')->avg('percentage'),
            'total_assignments' => $enrollment->course->topics()
                ->withCount('assignments')
                ->get()
                ->sum('assignments_count'),
            'submitted_assignments' => $assignmentSubmissions->whereNotNull('submitted_at')->count(),
            'total_time_spent_hours' => round($progress->sum('time_spent') / 60, 1),
        ];

        // Recent activity
        $recentActivity = collect();

        // Add recent lesson completions
        $progress->where('status', 'completed')
            ->sortByDesc('updated_at')
            ->take(5)
            ->each(function ($progressItem) use ($recentActivity) {
                $recentActivity->push((object)[
                    'type' => 'lesson',
                    'title' => 'Lesson Completed',
                    'description' => $progressItem->progressable->title ?? 'Lesson',
                    'created_at' => $progressItem->updated_at,
                ]);
            });

        // Add recent quiz attempts
        $quizAttempts->sortByDesc('completed_at')
            ->take(5)
            ->each(function ($attempt) use ($recentActivity) {
                $recentActivity->push((object)[
                    'type' => 'quiz',
                    'title' => 'Quiz Completed',
                    'description' => ($attempt->quiz->title ?? 'Quiz') . ' - Score: ' . number_format($attempt->percentage, 0) . '%',
                    'created_at' => $attempt->completed_at,
                ]);
            });

        // Add recent assignment submissions
        $assignmentSubmissions->whereNotNull('submitted_at')
            ->sortByDesc('submitted_at')
            ->take(5)
            ->each(function ($submission) use ($recentActivity) {
                $recentActivity->push((object)[
                    'type' => 'assignment',
                    'title' => 'Assignment Submitted',
                    'description' => $submission->assignment->title ?? 'Assignment',
                    'created_at' => $submission->submitted_at,
                ]);
            });

        $recentActivity = $recentActivity->sortByDesc('created_at')->take(10);

        // Topic-wise progress
        $topicProgress = $enrollment->course->topics->map(function ($topic) use ($progress, $quizAttempts, $assignmentSubmissions) {
            $totalLessons = $topic->lessons->count();
            $totalQuizzes = $topic->quizzes->count();
            $totalAssignments = $topic->assignments->count();
            $totalItems = $totalLessons + $totalQuizzes + $totalAssignments;

            $completedLessons = $progress->filter(function ($p) use ($topic) {
                return $p->status === 'completed' &&
                       $p->progressable_type === 'App\\Models\\Lesson' &&
                       $p->progressable &&
                       $p->progressable->topic_id === $topic->id;
            })->count();

            $completedQuizzes = $quizAttempts->filter(function ($attempt) use ($topic) {
                return $attempt->quiz && $attempt->quiz->topic_id === $topic->id;
            })->unique('quiz_id')->count();

            $completedAssignments = $assignmentSubmissions->filter(function ($submission) use ($topic) {
                return $submission->submitted_at &&
                       $submission->assignment &&
                       $submission->assignment->topic_id === $topic->id;
            })->count();

            $completedItems = $completedLessons + $completedQuizzes + $completedAssignments;
            $progressPercentage = $totalItems > 0 ? ($completedItems / $totalItems * 100) : 0;

            return (object)[
                'title' => $topic->title,
                'total_items' => $totalItems,
                'completed_items' => $completedItems,
                'progress_percentage' => $progressPercentage,
                'total_lessons' => $totalLessons,
                'completed_lessons' => $completedLessons,
                'total_quizzes' => $totalQuizzes,
                'completed_quizzes' => $completedQuizzes,
                'total_assignments' => $totalAssignments,
                'completed_assignments' => $completedAssignments,
            ];
        });

        // Quiz attempts for table (grouped by quiz)
        $quizAttempts = $quizAttempts->sortByDesc('completed_at')
            ->groupBy('quiz_id')
            ->map(function ($attempts) {
                $latest = $attempts->first();
                $latest->best_score = $attempts->max('percentage');
                $latest->attempt_number = $attempts->count();
                return $latest;
            });

        // Assignment submissions for table
        $assignmentSubmissions = $assignmentSubmissions->sortByDesc('submitted_at');

        return view('student.enrollments.progress', compact('enrollment', 'stats', 'recentActivity', 'topicProgress', 'quizAttempts', 'assignmentSubmissions'));
    }
}
