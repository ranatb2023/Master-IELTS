<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseCategory;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    /**
     * Browse all courses
     */
    public function index(Request $request)
    {
        $query = Course::published()
            ->with(['instructor', 'courseCategories', 'reviews']);

        // Search
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                    ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        // Filter by category (many-to-many relationship)
        if ($request->filled('category')) {
            $query->whereHas('courseCategories', function ($q) use ($request) {
                $q->where('course_categories.id', $request->category);
            });
        }

        // Filter by level
        if ($request->filled('level')) {
            $query->where('level', $request->level);
        }

        // Filter by rating
        if ($request->filled('rating')) {
            $query->where('average_rating', '>=', $request->rating);
        }

        // Filter by price
        if ($request->filled('price_type')) {
            if ($request->price_type === 'free') {
                $query->where('is_free', true);
            } elseif ($request->price_type === 'paid') {
                $query->where('is_free', false);
            }
        }

        // Sort
        $sortBy = $request->get('sort', 'newest');
        switch ($sortBy) {
            case 'popular':
                $query->orderBy('enrolled_count', 'desc');
                break;
            case 'rating':
                $query->orderBy('average_rating', 'desc');
                break;
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'newest':
            default:
                $query->latest('published_at');
        }

        $courses = $query->paginate(12);
        $categories = CourseCategory::active()->orderBy('order')->get();

        return view('student.courses.index', compact('courses', 'categories'));
    }

    /**
     * Display course details
     */
    public function show(Course $course)
    {
        // Check if course is published
        if ($course->status !== 'published') {
            abort(404);
        }

        $course->load([
            'instructor.profile',
            'instructor.createdCourses' => function ($query) {
                $query->select('id', 'instructor_id', 'enrolled_count')->published();
            },
            'courseCategories',
            'courseTags',
            'topics.lessons' => function ($query) {
                $query->orderBy('order');
            },
            'reviews.user',
            'quizzes' => function ($query) {
                $query->where('quizzes.is_published', true);
            }
        ]);

        // Check if user is enrolled
        $enrollment = auth()->user()->enrollments()
            ->where('course_id', $course->id)
            ->first();

        // Check if user can access (enrolled or free preview lessons)
        $canAccess = $enrollment && $enrollment->status === 'active';

        // Get related courses (from same categories)
        $categoryIds = $course->courseCategories->pluck('id')->toArray();

        $relatedCourses = Course::published()
            ->where('id', '!=', $course->id)
            ->when(!empty($categoryIds), function ($query) use ($categoryIds) {
                $query->whereHas('courseCategories', function ($q) use ($categoryIds) {
                    $q->whereIn('course_categories.id', $categoryIds);
                });
            })
            ->take(4)
            ->get();

        return view('student.courses.show', compact('course', 'enrollment', 'canAccess', 'relatedCourses'));
    }

    /**
     * Enroll in a course
     */
    public function enroll(Course $course)
    {
        // Check if already enrolled
        $existingEnrollment = auth()->user()->enrollments()
            ->where('course_id', $course->id)
            ->first();

        if ($existingEnrollment) {
            return redirect()
                ->route('student.courses.learn', $course)
                ->with('info', 'You are already enrolled in this course.');
        }

        // Check if course is free
        if (!$course->is_free) {
            return redirect()
                ->route('student.courses.purchase', $course)
                ->with('info', 'Please complete payment to enroll.');
        }

        // Free course - enroll immediately
        $enrollment = auth()->user()->enrollments()->create([
            'course_id' => $course->id,
            'enrolled_at' => now(),
            'status' => 'active',
            'payment_status' => 'free',
            'amount_paid' => 0,
            'enrollment_source' => 'web',
            'expires_at' => $course->has_lifetime_access ? null : now()->addDays($course->access_duration_days ?? 365),
        ]);

        return redirect()
            ->route('student.courses.learn', $course)
            ->with('success', 'Successfully enrolled in the course!');
    }

    /**
     * Course learning interface
     */
    public function learn(Course $course)
    {
        // Check enrollment
        $enrollment = auth()->user()->enrollments()
            ->where('course_id', $course->id)
            ->where('status', 'active')
            ->firstOrFail();

        // Load all content types
        $course->load([
            'topics.lessons' => fn($q) => $q->orderBy('order'),
            'topics.quizzes' => fn($q) => $q->where('is_published', 1)->orderBy('order'),
            'topics.assignments' => fn($q) => $q->orderBy('order'),
        ]);

        // Check user feature access
        $canAccessQuizzes = auth()->user()->canAccessFeature('quiz_access');
        $canAccessAssignments = auth()->user()->canAccessFeature('assignment_submission');

        // Get user progress for lessons
        $lessonIds = $course->topics->flatMap(fn($topic) => $topic->lessons->pluck('id'))->toArray();
        $progress = auth()->user()->progress()
            ->where('progressable_type', 'App\\Models\\Lesson')
            ->whereIn('progressable_id', $lessonIds)
            ->get()
            ->keyBy('progressable_id');

        // Get user's passed quiz attempts (Step 1: Query existing data)
        $quizAttempts = auth()->user()->quizAttempts()
            ->where('status', 'completed')
            ->where('passed', true)
            ->get()
            ->keyBy('quiz_id');

        // Get user's graded assignment submissions (Step 2: Query existing data)
        $assignmentSubmissions = auth()->user()->assignmentSubmissions()
            ->where('status', 'graded')
            ->get()
            ->keyBy('assignment_id');

        // Build unified content collection with all types
        $allContent = collect();
        foreach ($course->topics as $topic) {
            // Add lessons
            foreach ($topic->lessons as $lesson) {
                $allContent->push([
                    'type' => 'lesson',
                    'type_priority' => 1, // Lessons come first
                    'topic_id' => $topic->id,
                    'topic_order' => $topic->order ?? 0,
                    'item' => $lesson,
                    'order' => $lesson->order ?? 0,
                    'completed' => isset($progress[$lesson->id]) && $progress[$lesson->id]->status === 'completed',
                ]);
            }

            // Add quizzes if accessible
            if ($canAccessQuizzes) {
                foreach ($topic->quizzes as $quiz) {
                    $allContent->push([
                        'type' => 'quiz',
                        'type_priority' => 2, // Quizzes come after lessons
                        'topic_id' => $topic->id,
                        'topic_order' => $topic->order ?? 0,
                        'item' => $quiz,
                        'order' => $quiz->order ?? 0,
                        'completed' => isset($quizAttempts[$quiz->id]), // Step 3: Check if quiz passed
                    ]);
                }
            }

            // Add assignments if accessible
            if ($canAccessAssignments) {
                foreach ($topic->assignments as $assignment) {
                    $allContent->push([
                        'type' => 'assignment',
                        'type_priority' => 3, // Assignments come last
                        'topic_id' => $topic->id,
                        'topic_order' => $topic->order ?? 0,
                        'item' => $assignment,
                        'order' => $assignment->order ?? 0,
                        'completed' => isset($assignmentSubmissions[$assignment->id]), // Step 4: Check if assignment graded
                    ]);
                }
            }
        }

        // Sort by: 1) topic order, 2) type priority (lesson→quiz→assignment), 3) item order
        $allContent = $allContent->sortBy([
            ['topic_order', 'asc'],
            ['type_priority', 'asc'],
            ['order', 'asc'],
        ])->values();

        // Debug logging for empty content issue
        \Log::info('Learn method content check', [
            'user_id' => auth()->id(),
            'course_id' => $course->id,
            'content_count' => $allContent->count(),
            'can_access_quizzes' => $canAccessQuizzes,
            'can_access_assignments' => $canAccessAssignments,
            'lesson_count' => $course->topics->flatMap(fn($t) => $t->lessons)->count(),
            'quiz_count' => $course->topics->flatMap(fn($t) => $t->quizzes)->count(),
            'assignment_count' => $course->topics->flatMap(fn($t) => $t->assignments)->count(),
        ]);

        // Check if course has any content at all
        if ($allContent->isEmpty()) {
            \Log::warning('No accessible content for user in course', [
                'user_id' => auth()->id(),
                'course_id' => $course->id,
            ]);

            return redirect()->route('student.courses.show', $course)
                ->with('error', 'You do not have access to view this course content. Please contact support if you believe this is an error.');
        }

        // Find first incomplete content (or first item if all completed)
        $nextContent = $allContent->first(fn($content) => !$content['completed']) ?? $allContent->first();

        // Redirect to appropriate content type
        switch ($nextContent['type']) {
            case 'lesson':
                return redirect()->route('student.courses.view-lesson', [
                    $course,
                    $nextContent['topic_id'],
                    $nextContent['item']->id
                ]);

            case 'quiz':
                return redirect()->route('student.quizzes.show', $nextContent['item']);

            case 'assignment':
                return redirect()->route('student.assignments.show', $nextContent['item']);

            default:
                // Fallback (should never happen)
                return redirect()->route('student.courses.show', $course)
                    ->with('info', 'Continue learning from the course page.');
        }
    }

    /**
     * View lesson content
     */
    public function viewLesson(Course $course, $topicId, $lessonId)
    {
        // Check enrollment
        $enrollment = auth()->user()->enrollments()
            ->where('course_id', $course->id)
            ->where('status', 'active')
            ->firstOrFail();

        // Load course topics with lessons, quizzes, and assignments
        $course->load([
            'topics.lessons' => function ($query) {
                $query->orderBy('order');
            },
            'topics.quizzes' => function ($query) {
                $query->where('is_published', true)->orderBy('order');
            },
            'topics.quizzes.questions',
            'topics.quizzes.attempts' => function ($query) {
                $query->where('user_id', auth()->id());
            },
            'topics.assignments' => function ($query) {
                $query->where('is_published', true)->orderBy('order');
            },
            'topics.assignments.submissions' => function ($query) {
                $query->where('user_id', auth()->id());
            }
        ]);

        // Find the current lesson
        $currentLesson = null;
        $currentTopic = null;
        foreach ($course->topics as $topic) {
            if ($topic->id == $topicId) {
                $currentTopic = $topic;
                foreach ($topic->lessons as $lesson) {
                    if ($lesson->id == $lessonId) {
                        $currentLesson = $lesson;
                        break 2;
                    }
                }
            }
        }

        if (!$currentLesson) {
            abort(404, 'Lesson not found');
        }

        // Set topic_id on current lesson for use in forms
        $currentLesson->topic_id = $topicId;

        // Load the contentable relationship (TextContent, VideoContent, etc.) and resources
        $currentLesson->load(['contentable', 'resources']);

        // Get user progress for all lessons in this course
        $lessonIds = $course->topics->flatMap(function ($topic) {
            return $topic->lessons->pluck('id');
        })->toArray();

        $progress = auth()->user()->progress()
            ->where('progressable_type', 'App\\Models\\Lesson')
            ->whereIn('progressable_id', $lessonIds)
            ->get()
            ->keyBy('progressable_id');

        // Mark current lesson as started if not already
        if (!isset($progress[$currentLesson->id])) {
            $lessonProgress = auth()->user()->progress()->create([
                'progressable_type' => 'App\\Models\\Lesson',
                'progressable_id' => $currentLesson->id,
                'status' => 'in_progress',
            ]);
            $progress[$currentLesson->id] = $lessonProgress;
        } elseif ($progress[$currentLesson->id]->status === 'not_started') {
            $progress[$currentLesson->id]->update(['status' => 'in_progress']);
        }

        // Get completed lessons for marking
        $completedLessons = $progress->where('status', 'completed')->pluck('progressable_id')->toArray();

        // Get all content (lessons, quizzes, assignments) in order for navigation
        $allContent = $course->topics->flatMap(function ($topic) {
            $contentItems = collect();

            // Add lessons
            foreach ($topic->lessons as $lesson) {
                $contentItems->push([
                    'type' => 'lesson',
                    'order' => $lesson->order ?? 0,
                    'item' => $lesson,
                    'topic_id' => $topic->id,
                ]);
            }

            // Add quizzes
            foreach ($topic->quizzes as $quiz) {
                $contentItems->push([
                    'type' => 'quiz',
                    'order' => $quiz->order ?? 0,
                    'item' => $quiz,
                    'topic_id' => $topic->id,
                ]);
            }

            // Add assignments
            foreach ($topic->assignments as $assignment) {
                $contentItems->push([
                    'type' => 'assignment',
                    'order' => $assignment->order ?? 0,
                    'item' => $assignment,
                    'topic_id' => $topic->id,
                ]);
            }

            // Sort by order within topic
            return $contentItems->sortBy('order');
        });

        // Find current item index in the unified content list
        $currentIndex = $allContent->search(function ($content) use ($currentLesson) {
            return $content['type'] === 'lesson' && $content['item']->id === $currentLesson->id;
        });

        // Get previous and next content items
        $previousContent = $currentIndex > 0 ? $allContent[$currentIndex - 1] : null;
        $nextContent = $currentIndex !== false && $currentIndex < $allContent->count() - 1 ? $allContent[$currentIndex + 1] : null;

        // Prepare navigation data for view
        $previousLesson = null;
        $nextLesson = null;
        $previousItem = null;
        $nextItem = null;

        if ($previousContent) {
            if ($previousContent['type'] === 'lesson') {
                $previousLesson = $previousContent['item'];
                $previousLesson->topic_id_for_nav = $previousContent['topic_id'];
            } else {
                $previousItem = [
                    'type' => $previousContent['type'],
                    'item' => $previousContent['item'],
                ];
            }
        }

        if ($nextContent) {
            if ($nextContent['type'] === 'lesson') {
                $nextLesson = $nextContent['item'];
                $nextLesson->topic_id_for_nav = $nextContent['topic_id'];
            } else {
                $nextItem = [
                    'type' => $nextContent['type'],
                    'item' => $nextContent['item'],
                ];
            }
        }

        // Get all lessons for progress calculation
        $allLessons = $course->topics->flatMap(function ($topic) {
            return $topic->lessons;
        });


        // Get accurate progress percentage from CourseProgress model (includes all content types)
        $courseProgress = \App\Models\CourseProgress::where('user_id', auth()->id())
            ->where('course_id', $course->id)
            ->first();

        $progressPercentage = $courseProgress ? $courseProgress->progress_percentage : 0;


        // Load lesson comments (private - students see only their own + instructor replies)
        $lessonComments = $currentLesson->comments()
            ->with([
                'user',
                'replies' => function ($query) {
                    // For students, only load admin/tutor replies
                    $query->where('is_from_tutor', true)->with('user');
                }
            ])
            ->whereNull('parent_id')
            ->where('user_id', auth()->id()) // Only current student's comments
            ->latest()
            ->get();

        return view('student.courses.learn', compact(
            'course',
            'enrollment',
            'currentLesson',
            'progress',
            'completedLessons',
            'previousLesson',
            'nextLesson',
            'previousItem',
            'nextItem',
            'progressPercentage',
            'lessonComments'
        ));
    }

    /**
     * Update lesson progress (time and position)
     */
    public function updateLessonProgress(Request $request, Course $course, $topicId, $lessonId)
    {
        // Check enrollment
        $enrollment = auth()->user()->enrollments()
            ->where('course_id', $course->id)
            ->where('status', 'active')
            ->firstOrFail();

        $validated = $request->validate([
            'time_spent' => 'nullable|integer|min:0',
            'last_position' => 'nullable|string|max:255',
        ]);

        // Update or create progress
        $progress = auth()->user()->progress()
            ->where('progressable_type', 'App\\Models\\Lesson')
            ->where('progressable_id', $lessonId)
            ->first();

        if ($progress) {
            // Increment time spent if provided
            if (isset($validated['time_spent'])) {
                $progress->increment('time_spent', $validated['time_spent']);
            }

            // Update last position if provided
            if (isset($validated['last_position'])) {
                $progress->update(['last_position' => $validated['last_position']]);
            }

            // Update course progress total time
            $courseProgress = \App\Models\CourseProgress::where('user_id', auth()->id())
                ->where('course_id', $course->id)
                ->first();

            if ($courseProgress && isset($validated['time_spent'])) {
                $courseProgress->increment('total_time_spent', $validated['time_spent']);
            }
        }

        return response()->json(['success' => true]);
    }

    /**
     * Mark lesson as complete
     */
    public function completeLesson(Request $request, Course $course, $topicId, $lessonId)
    {
        // Check enrollment
        $enrollment = auth()->user()->enrollments()
            ->where('course_id', $course->id)
            ->where('status', 'active')
            ->firstOrFail();

        $lesson = $course->topics()
            ->findOrFail($topicId)
            ->lessons()
            ->findOrFail($lessonId);

        // Mark lesson as completed using Progress model
        $progress = auth()->user()->progress()
            ->updateOrCreate(
                [
                    'user_id' => auth()->id(),
                    'progressable_type' => 'App\\Models\\Lesson',
                    'progressable_id' => $lesson->id,
                ],
                [
                    'status' => 'completed',
                    'completed_at' => now(),
                ]
            );

        // Update course progress
        $this->updateCourseProgress($course, $enrollment);

        // Get accurate progress percentage from CourseProgress model (includes all content types)
        $courseProgress = \App\Models\CourseProgress::where('user_id', auth()->id())
            ->where('course_id', $course->id)
            ->first();

        $progressPercentage = $courseProgress ? $courseProgress->progress_percentage : 0;

        return response()->json([
            'success' => true,
            'message' => 'Lesson completed!',
            'progress' => $progressPercentage,
        ]);
    }

    /**
     * Update course progress
     */
    private function updateCourseProgress(Course $course, $enrollment)
    {
        // Get all lessons, quizzes, and assignments for this course
        $course->load(['topics.lessons', 'topics.quizzes', 'topics.assignments']);

        $lessonIds = $course->topics->flatMap(fn($t) => $t->lessons->pluck('id'))->toArray();
        $quizIds = $course->topics->flatMap(fn($t) => $t->quizzes->pluck('id'))->toArray();
        $assignmentIds = $course->topics->flatMap(fn($t) => $t->assignments->pluck('id'))->toArray();

        // Count completed lessons
        $completedLessons = auth()->user()->progress()
            ->where('progressable_type', 'App\\Models\\Lesson')
            ->whereIn('progressable_id', $lessonIds)
            ->where('status', 'completed')
            ->count();

        // Count completed quizzes (passed attempts)
        $completedQuizzes = auth()->user()->quizAttempts()
            ->whereIn('quiz_id', $quizIds)
            ->where('passed', true)
            ->distinct('quiz_id')
            ->count('quiz_id');

        // Count completed assignments (graded submissions)
        $completedAssignments = auth()->user()->assignmentSubmissions()
            ->whereIn('assignment_id', $assignmentIds)
            ->where('status', 'graded')
            ->distinct('assignment_id')
            ->count('assignment_id');

        // Calculate average scores
        $avgQuizScore = auth()->user()->quizAttempts()
            ->whereIn('quiz_id', $quizIds)
            ->where('status', 'graded')
            ->avg('score');

        $avgAssignmentScore = auth()->user()->assignmentSubmissions()
            ->whereIn('assignment_id', $assignmentIds)
            ->where('status', 'graded')
            ->whereNotNull('score')
            ->avg('score');

        // Update or create course progress
        $courseProgress = \App\Models\CourseProgress::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'course_id' => $course->id,
            ],
            [
                'completed_lessons' => $completedLessons,
                'total_lessons' => count($lessonIds),
                'completed_quizzes' => $completedQuizzes,
                'total_quizzes' => count($quizIds),
                'completed_assignments' => $completedAssignments,
                'total_assignments' => count($assignmentIds),
                'average_quiz_score' => $avgQuizScore ? round($avgQuizScore, 2) : null,
                'average_assignment_score' => $avgAssignmentScore ? round($avgAssignmentScore, 2) : null,
                'last_accessed_at' => now(),
                'started_at' => $courseProgress->started_at ?? now(),
            ]
        );

        // Update overall progress percentage
        $courseProgress->updateProgress();

        // Update enrollment status
        if ($courseProgress->isCompleted() && $enrollment->status !== 'completed') {
            $enrollment->update([
                'status' => 'completed',
                'completed_at' => now(),
                'progress_percentage' => 100,
            ]);
        } else {
            $enrollment->update([
                'progress_percentage' => $courseProgress->progress_percentage,
                'last_accessed_at' => now(),
            ]);
        }
    }

}
