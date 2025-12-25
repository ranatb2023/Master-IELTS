<?php

namespace App\Http\Controllers\Tutor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AssignmentController extends Controller
{
    /**
     * Display assignments for tutor's courses
     */
    public function index()
    {
        $assignments = Assignment::whereHas('course', function ($query) {
            $query->where('instructor_id', auth()->id());
        })
            ->with(['course', 'topic'])
            ->withCount(['submissions'])
            ->latest()
            ->paginate(20);

        return view('tutor.assignments.index', compact('assignments'));
    }

    /**
     * Show form to create assignment
     */
    public function create()
    {
        $courses = auth()->user()->createdCourses()
            ->where('status', '!=', 'archived')
            ->orderBy('title')
            ->get();

        return view('tutor.assignments.create', compact('courses'));
    }

    /**
     * Store a new assignment
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'topic_id' => 'nullable|exists:topics,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'instructions' => 'nullable|string',
            'type' => 'required|in:essay,project,presentation,code,file_upload',
            'max_points' => 'required|integer|min:1',
            'passing_points' => 'required|integer|min:0',
            'due_date' => 'nullable|date|after:now',
            'allow_late_submission' => 'boolean',
            'late_penalty_percentage' => 'nullable|integer|min:0|max:100',
            'max_file_size_mb' => 'nullable|integer|min:1',
            'allowed_file_types' => 'nullable|json',
        ]);

        // Ensure tutor owns this course
        $course = Course::findOrFail($validated['course_id']);
        if ($course->instructor_id !== auth()->id()) {
            abort(403);
        }

        $assignment = Assignment::create($validated);

        return redirect()
            ->route('tutor.assignments.show', $assignment)
            ->with('success', 'Assignment created successfully!');
    }

    /**
     * Display assignment details
     */
    public function show(Assignment $assignment)
    {
        // Ensure tutor owns the course
        if ($assignment->course->instructor_id !== auth()->id()) {
            abort(403);
        }

        $assignment->load(['course', 'topic', 'rubric']);

        $stats = [
            'total_submissions' => $assignment->submissions()->count(),
            'pending_review' => $assignment->submissions()->where('status', 'submitted')->count(),
            'graded' => $assignment->submissions()->where('status', 'graded')->count(),
            'average_score' => round($assignment->submissions()->whereNotNull('points_earned')->avg('points_earned') ?? 0, 2),
        ];

        return view('tutor.assignments.show', compact('assignment', 'stats'));
    }

    /**
     * Show form to edit assignment
     */
    public function edit(Assignment $assignment)
    {
        // Ensure tutor owns the course
        if ($assignment->course->instructor_id !== auth()->id()) {
            abort(403);
        }

        $courses = auth()->user()->createdCourses()
            ->where('status', '!=', 'archived')
            ->orderBy('title')
            ->get();

        return view('tutor.assignments.edit', compact('assignment', 'courses'));
    }

    /**
     * Update assignment
     */
    public function update(Request $request, Assignment $assignment)
    {
        // Ensure tutor owns the course
        if ($assignment->course->instructor_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'instructions' => 'nullable|string',
            'type' => 'required|in:essay,project,presentation,code,file_upload',
            'max_points' => 'required|integer|min:1',
            'passing_points' => 'required|integer|min:0',
            'due_date' => 'nullable|date',
            'allow_late_submission' => 'boolean',
            'late_penalty_percentage' => 'nullable|integer|min:0|max:100',
            'max_file_size_mb' => 'nullable|integer|min:1',
            'allowed_file_types' => 'nullable|json',
        ]);

        $assignment->update($validated);

        return redirect()
            ->route('tutor.assignments.show', $assignment)
            ->with('success', 'Assignment updated successfully!');
    }

    /**
     * View submissions for assignment
     */
    public function submissions(Assignment $assignment)
    {
        // Ensure tutor owns the course
        if ($assignment->course->instructor_id !== auth()->id()) {
            abort(403);
        }

        $submissions = $assignment->submissions()
            ->with(['user', 'files'])
            ->latest('submitted_at')
            ->paginate(20);

        return view('tutor.assignments.submissions', compact('assignment', 'submissions'));
    }

    /**
     * Grade a submission
     */
    public function grade(Request $request, Assignment $assignment, AssignmentSubmission $submission)
    {
        // Ensure tutor owns the course
        if ($assignment->course->instructor_id !== auth()->id()) {
            abort(403);
        }

        if ($submission->assignment_id !== $assignment->id) {
            abort(404);
        }

        $validated = $request->validate([
            'points_earned' => 'required|integer|min:0|max:' . $assignment->max_points,
            'feedback' => 'nullable|string',
        ]);

        $submission->update([
            'points_earned' => $validated['points_earned'],
            'feedback' => $validated['feedback'],
            'status' => 'graded',
            'graded_at' => now(),
            'graded_by' => auth()->id(),
        ]);

        // Send notification to student about grading
        $submission->user->notify(new \App\Notifications\AssignmentGradedNotification($assignment, $submission));

        return back()->with('success', 'Submission graded successfully!');
    }

    /**
     * View individual submission details
     */
    public function viewSubmission(Assignment $assignment, AssignmentSubmission $submission)
    {
        // Ensure tutor owns the course
        if ($assignment->course->instructor_id !== auth()->id()) {
            abort(403);
        }

        if ($submission->assignment_id !== $assignment->id) {
            abort(404);
        }

        $submission->load(['user', 'files', 'gradedBy']);

        return view('tutor.assignments.view-submission', compact('assignment', 'submission'));
    }
}
