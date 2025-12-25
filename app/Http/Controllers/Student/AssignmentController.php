<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AssignmentController extends Controller
{
    /**
     * Display assignment details
     */
    public function show(Assignment $assignment)
    {
        // Check enrollment
        $course = $assignment->topic->course;
        $enrollment = auth()->user()->enrollments()
            ->where('course_id', $course->id)
            ->where('status', 'active')
            ->firstOrFail();

        // Load course with topics, lessons, quizzes, assignments for sidebar
        $course->load([
            'topics.lessons',
            'topics.quizzes',
            'topics.assignments'
        ]);

        // Get student's submissions
        $submissions = auth()->user()->assignmentSubmissions()
            ->where('assignment_id', $assignment->id)
            ->with('assignmentFiles')
            ->orderBy('submitted_at', 'desc')
            ->get();

        $latestSubmission = $submissions->first();

        // Get completed lessons for progress tracking (for sidebar checkmarks)
        $lessonIds = $course->topics->flatMap(function ($topic) {
            return $topic->lessons->pluck('id');
        })->toArray();

        $progress = auth()->user()->progress()
            ->where('progressable_type', 'App\\Models\\Lesson')
            ->whereIn('progressable_id', $lessonIds)
            ->get()
            ->keyBy('progressable_id');

        $completedLessons = $progress->where('status', 'completed')->pluck('progressable_id')->toArray();

        // Get accurate progress percentage from CourseProgress model (includes lessons, quizzes, assignments)
        $courseProgress = \App\Models\CourseProgress::where('user_id', auth()->id())
            ->where('course_id', $course->id)
            ->first();

        $progressPercentage = $courseProgress ? $courseProgress->progress_percentage : 0;

        // Get graded assignments for sidebar checkmarks
        $assignmentIds = $course->topics->flatMap(fn($topic) => $topic->assignments->pluck('id'))->toArray();
        $gradedSubmissions = auth()->user()->assignmentSubmissions()
            ->where('status', 'graded')
            ->whereIn('assignment_id', $assignmentIds)
            ->get();
        $completedAssignments = $gradedSubmissions->pluck('assignment_id')->unique()->toArray();

        // Get completed quizzes for sidebar checkmarks
        $quizIds = $course->topics->flatMap(fn($topic) => $topic->quizzes->pluck('id'))->toArray();
        $userQuizAttempts = auth()->user()->quizAttempts()
            ->whereIn('quiz_id', $quizIds)
            ->where('passed', true)
            ->get();
        $completedQuizzes = $userQuizAttempts->pluck('quiz_id')->unique()->toArray();

        // Build navigation (previous/next)
        $allContent = $course->topics->flatMap(function ($topic) {
            $contentItems = collect();

            foreach ($topic->lessons as $lesson) {
                $contentItems->push([
                    'type' => 'lesson',
                    'order' => $lesson->order ?? 0,
                    'item' => $lesson,
                    'topic_id' => $topic->id,
                ]);
            }

            foreach ($topic->quizzes as $quiz) {
                $contentItems->push([
                    'type' => 'quiz',
                    'order' => $quiz->order ?? 0,
                    'item' => $quiz,
                    'topic_id' => $topic->id,
                ]);
            }

            foreach ($topic->assignments as $assn) {
                $contentItems->push([
                    'type' => 'assignment',
                    'order' => $assn->order ?? 0,
                    'item' => $assn,
                    'topic_id' => $topic->id,
                ]);
            }

            return $contentItems->sortBy('order');
        });

        // Find current assignment index
        $currentIndex = $allContent->search(function ($content) use ($assignment) {
            return $content['type'] === 'assignment' && $content['item']->id === $assignment->id;
        });

        // Get previous and next content
        $previousContent = $currentIndex > 0 ? $allContent[$currentIndex - 1] : null;
        $nextContent = $currentIndex !== false && $currentIndex < $allContent->count() - 1 ? $allContent[$currentIndex + 1] : null;

        $previousItem = null;
        $nextItem = null;

        if ($previousContent) {
            $previousItem = [
                'type' => $previousContent['type'],
                'item' => $previousContent['item'],
                'topic_id' => $previousContent['topic_id'],
            ];
        }

        if ($nextContent) {
            $nextItem = [
                'type' => $nextContent['type'],
                'item' => $nextContent['item'],
                'topic_id' => $nextContent['topic_id'],
            ];
        }

        return view('student.assignments.show', compact(
            'course',
            'assignment',
            'enrollment',
            'submissions',
            'latestSubmission',
            'completedLessons',
            'completedQuizzes',
            'completedAssignments',
            'progressPercentage',
            'previousItem',
            'nextItem'
        ));
    }

    /**
     * Show submission form
     */
    public function create(Assignment $assignment)
    {
        // Check enrollment
        $course = $assignment->topic->course;
        $enrollment = auth()->user()->enrollments()
            ->where('course_id', $course->id)
            ->where('status', 'active')
            ->firstOrFail();

        // Check if due date passed and late submission not allowed
        if ($assignment->due_date && now()->gt($assignment->due_date) && !$assignment->allow_late_submission) {
            return back()->with('error', 'The deadline for this assignment has passed.');
        }

        return view('student.assignments.create', compact('course', 'assignment', 'enrollment'));
    }

    /**
     * Submit assignment
     */
    public function store(Request $request, Assignment $assignment)
    {
        // Check enrollment
        $course = $assignment->topic->course;
        $enrollment = auth()->user()->enrollments()
            ->where('course_id', $course->id)
            ->where('status', 'active')
            ->firstOrFail();

        // Check if due date passed
        $isLate = $assignment->due_date && now()->gt($assignment->due_date);
        if ($isLate && !$assignment->allow_late_submission) {
            return back()->with('error', 'The deadline for this assignment has passed.');
        }

        $validated = $request->validate([
            'submission_text' => 'nullable|string',
            'files' => 'nullable|array',
            'files.*' => 'file|max:' . ($assignment->max_file_size * 1024),
        ]);

        // Custom validation: require at least text or files
        if (empty($validated['submission_text']) && (!$request->hasFile('files') || count($request->file('files')) === 0)) {
            return back()
                ->withInput()
                ->withErrors(['submission' => 'Please provide either text submission or upload at least one file.']);
        }

        DB::beginTransaction();
        try {
            // Create submission
            $submission = AssignmentSubmission::create([
                'assignment_id' => $assignment->id,
                'user_id' => auth()->id(),
                'enrollment_id' => $enrollment->id,
                'submission_text' => $validated['submission_text'] ?? null,
                'submitted_at' => now(),
                'status' => 'submitted',
                'is_late' => $isLate,
            ]);

            // Handle file uploads
            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    // Store file in storage/app/assignments
                    $filename = time() . '_' . uniqid() . '_' . $file->getClientOriginalName();
                    $path = $file->storeAs('assignments', $filename, 'private');

                    // Create AssignmentFile record
                    $submission->assignmentFiles()->create([
                        'file_name' => $filename,
                        'original_filename' => $file->getClientOriginalName(),
                        'file_path' => $path,
                        'file_type' => $file->getClientOriginalExtension(),
                        'file_size' => $file->getSize(),
                        'mime_type' => $file->getMimeType(),
                    ]);
                }
            }

            DB::commit();

            // Notify course instructor about new submission
            $instructor = $course->instructor;
            if ($instructor) {
                $instructor->notify(new \App\Notifications\AssignmentSubmittedNotification($assignment, $submission));
            }

            return redirect()
                ->route('student.assignments.show', $assignment)
                ->with('success', 'Assignment submitted successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Failed to submit assignment: ' . $e->getMessage());
        }
    }

    /**
     * View submission details
     */
    public function viewSubmission(Assignment $assignment, AssignmentSubmission $submission)
    {
        // Check ownership
        if ($submission->user_id !== auth()->id() || $submission->assignment_id !== $assignment->id) {
            abort(403);
        }

        $course = $assignment->topic->course;
        $submission->load(['assignmentFiles', 'grader', 'rubricScores.rubric']);

        return view('student.assignments.view-submission', compact('course', 'assignment', 'submission'));
    }

    /**
     * View/preview assignment file
     */
    public function viewFile(Assignment $assignment, $fileId)
    {
        $file = \App\Models\AssignmentFile::findOrFail($fileId);

        // Check ownership - file must belong to this user's submission
        $submission = $file->submission;
        if ($submission->user_id !== auth()->id() || $submission->assignment_id !== $assignment->id) {
            abort(403);
        }

        $path = storage_path('app/private/' . $file->file_path);

        if (!file_exists($path)) {
            abort(404);
        }

        return response()->file($path, [
            'Content-Type' => $file->mime_type ?? 'application/octet-stream',
            'Content-Disposition' => 'inline; filename="' . ($file->original_filename ?? $file->file_name) . '"'
        ]);
    }

    /**
     * Download assignment file
     */
    public function downloadFile(Assignment $assignment, $fileId)
    {
        $file = \App\Models\AssignmentFile::findOrFail($fileId);

        // Check ownership - file must belong to this user's submission
        $submission = $file->submission;
        if ($submission->user_id !== auth()->id() || $submission->assignment_id !== $assignment->id) {
            abort(403);
        }

        return response()->download(storage_path('app/private/' . $file->file_path), $file->original_filename ?? $file->file_name);
    }
}
