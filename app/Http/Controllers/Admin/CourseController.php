<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\CourseTag;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class CourseController extends Controller
{
    /**
     * Display a listing of courses
     */
    public function index(Request $request)
    {
        $query = Course::with(['instructor', 'courseCategories'])
            ->withCount('enrollments');

        // Search
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                    ->orWhere('slug', 'like', '%' . $request->search . '%');
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Filter by level
        if ($request->filled('level')) {
            $query->where('level', $request->level);
        }

        // Filter by instructor
        if ($request->filled('instructor')) {
            $query->where('instructor_id', $request->instructor);
        }

        $courses = $query->latest()->paginate(20);
        $categories = CourseCategory::active()->orderBy('order')->get();
        $instructors = User::role('tutor')->orderBy('name')->get();

        return view('admin.courses.index', compact('courses', 'categories', 'instructors'));
    }

    /**
     * Show the form for creating a new course
     */
    public function create()
    {
        $courseCategories = CourseCategory::active()->orderBy('order')->get();
        $courseTags = CourseTag::active()->orderBy('name')->get();
        $instructors = User::role('tutor')->orderBy('name')->get();

        return view('admin.courses.create', compact('courseCategories', 'courseTags', 'instructors'));
    }

    /**
     * Store a newly created course
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:courses,slug',
            'subtitle' => 'nullable|string|max:255',
            'instructor_id' => 'required|exists:users,id',
            'level' => 'nullable|in:beginner,intermediate,advanced,all_levels',
            'description' => 'nullable|string',
            'short_description' => 'nullable|string|max:500',
            'price' => 'nullable|numeric|min:0|max:99999999.99',
            'sale_price' => 'nullable|numeric|min:0|max:99999999.99|lt:price',
            'currency' => 'nullable|string|max:10',
            'is_free' => 'nullable|boolean',
            'status' => 'nullable|in:draft,review,published,archived',
            'visibility' => 'nullable|in:public,private,unlisted',
            'language' => 'nullable|string|max:50',
            'duration_hours' => 'nullable|numeric|min:0|max:999999.99',
            'enrollment_limit' => 'nullable|integer|min:1|max:999999',
            'learning_outcomes' => 'nullable|array',
            'learning_outcomes.*' => 'nullable|string|max:500',
            'requirements' => 'nullable|array',
            'requirements.*' => 'nullable|string|max:500',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'preview_video' => 'nullable|url|max:500',
            'meta_title' => 'nullable|string|max:60',
            'meta_description' => 'nullable|string|max:160',
            'meta_keywords' => 'nullable|string|max:500',
            'certificate_enabled' => 'nullable|boolean',
            'drip_content' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'published_at' => 'nullable|date',
            'allow_single_purchase' => 'nullable|boolean',
            'package_only' => 'nullable|boolean',
            'single_purchase_price' => 'nullable|numeric|min:0|max:99999999.99',
            'auto_enroll_enabled' => 'nullable|boolean',
            'course_categories' => 'nullable|array',
            'course_categories.*' => 'exists:course_categories,id',
            'course_tags' => 'nullable|array',
            'course_tags.*' => 'exists:course_tags,id',
        ], [
            'title.required' => 'The course title is required.',
            'title.max' => 'The course title cannot exceed 255 characters.',
            'instructor_id.required' => 'Please select an instructor for this course.',
            'instructor_id.exists' => 'The selected instructor does not exist.',
            'price.numeric' => 'The price must be a valid number.',
            'price.min' => 'The price cannot be negative.',
            'sale_price.lt' => 'The sale price must be less than the regular price.',
            'thumbnail.image' => 'The thumbnail must be an image file.',
            'thumbnail.max' => 'The thumbnail size cannot exceed 5MB.',
            'preview_video.url' => 'The preview video must be a valid URL.',
            'course_categories.*.exists' => 'One or more selected categories do not exist.',
            'course_tags.*.exists' => 'One or more selected tags do not exist.',
        ]);

        // Auto-generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        // Convert arrays to JSON for storage
        if (isset($validated['learning_outcomes'])) {
            $validated['learning_outcomes'] = json_encode(array_filter($validated['learning_outcomes'], fn($item) => !empty($item)));
        }

        if (isset($validated['requirements'])) {
            $validated['requirements'] = json_encode(array_filter($validated['requirements'], fn($item) => !empty($item)));
        }

        // Handle thumbnail upload
        if ($request->hasFile('thumbnail')) {
            $path = $request->file('thumbnail')->store('courses/thumbnails', 'public');
            $validated['thumbnail'] = $path;
        }

        // Handle boolean values (checkboxes don't send value if unchecked)
        $validated['is_free'] = $request->has('is_free');
        $validated['certificate_enabled'] = $request->has('certificate_enabled');
        $validated['drip_content'] = $request->has('drip_content');
        $validated['is_featured'] = $request->has('is_featured');
        $validated['auto_enroll_enabled'] = $request->has('auto_enroll_enabled');

        // Set default values
        $validated['status'] = $validated['status'] ?? 'draft';
        $validated['visibility'] = $validated['visibility'] ?? 'public';
        $validated['currency'] = $validated['currency'] ?? 'USD';

        // Auto-set published_at if status is published
        if (isset($validated['status']) && $validated['status'] === 'published') {
            if (empty($validated['published_at'])) {
                $validated['published_at'] = now();
            }
        }

        DB::beginTransaction();
        try {
            // Remove non-fillable fields from validated data
            $courseCategories = $validated['course_categories'] ?? [];
            $courseTags = $validated['course_tags'] ?? [];
            unset($validated['course_categories'], $validated['course_tags']);

            $course = Course::create($validated);

            // Attach course categories if provided
            if (!empty($courseCategories)) {
                $course->courseCategories()->sync($courseCategories);
            }

            // Attach course tags if provided
            if (!empty($courseTags)) {
                $course->courseTags()->sync($courseTags);
            }

            // Handle auto-enrollment if enabled during creation
            $enrollmentMessage = '';
            if ($validated['auto_enroll_enabled'] ?? false) {
                $allStudents = User::role('student')->get();
                $enrolledCount = 0;

                foreach ($allStudents as $user) {
                    \App\Models\Enrollment::create([
                        'user_id' => $user->id,
                        'course_id' => $course->id,
                        'status' => 'active',
                        'enrolled_at' => now(),
                        'enrollment_source' => 'auto_enroll',
                        'payment_status' => 'free',
                    ]);

                    // Send notification to the user
                    $user->notify(new \App\Notifications\CourseAutoEnrolledNotification($course));
                    $enrolledCount++;
                }

                if ($enrolledCount > 0) {
                    $enrollmentMessage = " {$enrolledCount} users have been automatically enrolled.";
                }
            }

            DB::commit();

            return redirect()
                ->route('admin.courses.show', $course)
                ->with('success', 'Course created successfully!' . $enrollmentMessage);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Failed to create course: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified course
     */
    public function show(Course $course)
    {
        $course->load([
            'instructor',
            'courseCategories',
            'courseTags',
            'topics.lessons',
            'topics.quizzes',
            'topics.assignments',
            'enrollments',
            'reviews'
        ]);

        // Calculate stats for the view
        $stats = [
            'topics_count' => $course->topics()->count(),
            'lessons_count' => $course->topics()->withCount('lessons')->get()->sum('lessons_count'),
            'enrollments_count' => $course->enrollments()->count(),
            'active_students' => $course->enrollments()->where('status', 'active')->count(),
            'completed_students' => $course->enrollments()->whereNotNull('completed_at')->count(),
            'average_progress' => $course->enrollments()->avg('progress_percentage') ?? 0,
            'total_quizzes' => $course->topics()->withCount('quizzes')->get()->sum('quizzes_count'),
            'total_assignments' => $course->topics()->withCount('assignments')->get()->sum('assignments_count'),
        ];

        return view('admin.courses.show', compact('course', 'stats'));
    }

    /**
     * Show the form for editing the specified course
     */
    public function edit(Course $course)
    {
        $course->load(['courseCategories', 'courseTags']);
        $courseCategories = CourseCategory::active()->orderBy('order')->get();
        $courseTags = CourseTag::active()->orderBy('name')->get();
        $instructors = User::role('tutor')->orderBy('name')->get();

        return view('admin.courses.edit', compact('course', 'courseCategories', 'courseTags', 'instructors'));
    }

    /**
     * Update the specified course
     */
    public function update(Request $request, Course $course)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:courses,slug,' . $course->id,
            'subtitle' => 'nullable|string|max:255',
            'instructor_id' => 'required|exists:users,id',
            'level' => 'nullable|in:beginner,intermediate,advanced,all_levels',
            'description' => 'nullable|string',
            'short_description' => 'nullable|string|max:500',
            'price' => 'nullable|numeric|min:0|max:99999999.99',
            'sale_price' => 'nullable|numeric|min:0|max:99999999.99|lt:price',
            'currency' => 'nullable|string|max:10',
            'is_free' => 'nullable|boolean',
            'status' => 'nullable|in:draft,review,published,archived',
            'visibility' => 'nullable|in:public,private,unlisted',
            'language' => 'nullable|string|max:50',
            'duration_hours' => 'nullable|numeric|min:0|max:999999.99',
            'enrollment_limit' => 'nullable|integer|min:1|max:999999',
            'learning_outcomes' => 'nullable|array',
            'learning_outcomes.*' => 'nullable|string|max:500',
            'requirements' => 'nullable|array',
            'requirements.*' => 'nullable|string|max:500',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'preview_video' => 'nullable|url|max:500',
            'meta_title' => 'nullable|string|max:60',
            'meta_description' => 'nullable|string|max:160',
            'meta_keywords' => 'nullable|string|max:500',
            'certificate_enabled' => 'nullable|boolean',
            'drip_content' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'published_at' => 'nullable|date',
            'allow_single_purchase' => 'nullable|boolean',
            'package_only' => 'nullable|boolean',
            'single_purchase_price' => 'nullable|numeric|min:0|max:99999999.99',
            'auto_enroll_enabled' => 'nullable|boolean',
            'course_categories' => 'nullable|array',
            'course_categories.*' => 'exists:course_categories,id',
            'course_tags' => 'nullable|array',
            'course_tags.*' => 'exists:course_tags,id',
        ], [
            'title.required' => 'The course title is required.',
            'title.max' => 'The course title cannot exceed 255 characters.',
            'instructor_id.required' => 'Please select an instructor for this course.',
            'instructor_id.exists' => 'The selected instructor does not exist.',
            'price.numeric' => 'The price must be a valid number.',
            'price.min' => 'The price cannot be negative.',
            'sale_price.lt' => 'The sale price must be less than the regular price.',
            'thumbnail.image' => 'The thumbnail must be an image file.',
            'thumbnail.max' => 'The thumbnail size cannot exceed 5MB.',
            'preview_video.url' => 'The preview video must be a valid URL.',
            'course_categories.*.exists' => 'One or more selected categories do not exist.',
            'course_tags.*.exists' => 'One or more selected tags do not exist.',
        ]);

        // Auto-generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        // Convert arrays to JSON for storage
        if (isset($validated['learning_outcomes'])) {
            $validated['learning_outcomes'] = json_encode(array_filter($validated['learning_outcomes'], fn($item) => !empty($item)));
        }

        if (isset($validated['requirements'])) {
            $validated['requirements'] = json_encode(array_filter($validated['requirements'], fn($item) => !empty($item)));
        }

        // Handle thumbnail upload
        if ($request->hasFile('thumbnail')) {
            // Delete old thumbnail if exists
            if ($course->thumbnail && \Storage::disk('public')->exists($course->thumbnail)) {
                \Storage::disk('public')->delete($course->thumbnail);
            }

            $path = $request->file('thumbnail')->store('courses/thumbnails', 'public');
            $validated['thumbnail'] = $path;
        }

        // Handle boolean values (checkboxes don't send value if unchecked)
        $validated['is_free'] = $request->has('is_free');
        $validated['certificate_enabled'] = $request->has('certificate_enabled');
        $validated['drip_content'] = $request->has('drip_content');
        $validated['is_featured'] = $request->has('is_featured');
        $validated['allow_single_purchase'] = $request->has('allow_single_purchase');
        $validated['package_only'] = $request->has('package_only');

        // Track previous auto-enroll state to handle bulk enrollment/unenrollment
        $previousAutoEnrollEnabled = $course->auto_enroll_enabled;
        $validated['auto_enroll_enabled'] = $request->has('auto_enroll_enabled');

        // Auto-set published_at if status changed to published
        if (isset($validated['status']) && $validated['status'] === 'published') {
            if (empty($course->published_at) && empty($validated['published_at'])) {
                $validated['published_at'] = now();
            }
        }

        DB::beginTransaction();
        try {
            // Remove non-fillable fields from validated data
            $courseCategories = $validated['course_categories'] ?? [];
            $courseTags = $validated['course_tags'] ?? [];
            unset($validated['course_categories'], $validated['course_tags']);

            $course->update($validated);

            // Sync course categories
            $course->courseCategories()->sync($courseCategories);

            // Sync course tags
            $course->courseTags()->sync($courseTags);

            // Handle auto-enrollment toggle changes
            $enrollmentMessage = '';
            $wasAutoEnrollChanged = $validated['auto_enroll_enabled'] != $previousAutoEnrollEnabled;

            if ($wasAutoEnrollChanged) {
                if ($validated['auto_enroll_enabled'] && !$previousAutoEnrollEnabled) {
                    // Auto-enrollment was just enabled - dispatch background job
                    \App\Jobs\BulkAutoEnrollStudentsJob::dispatch($course->id);
                    $enrollmentMessage = ' Auto-enrollment process started in background. Check progress below.';
                } elseif (!$validated['auto_enroll_enabled'] && $previousAutoEnrollEnabled) {
                    // Auto-enrollment was just disabled - dispatch unenrollment job
                    \App\Jobs\BulkUnenrollStudentsJob::dispatch($course->id);
                    $enrollmentMessage = ' Auto-unenrollment process started in background. Check progress below.';
                }
            }


            DB::commit();

            // If auto-enrollment was changed, redirect to edit page to show progress modal
            // Otherwise redirect to show page
            $redirectRoute = $wasAutoEnrollChanged
                ? route('admin.courses.edit', $course)
                : route('admin.courses.show', $course);

            return redirect($redirectRoute)
                ->with('success', 'Course updated successfully!' . $enrollmentMessage);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Failed to update course: ' . $e->getMessage());
        }
    }

    /**
     * Check auto-enrollment progress
     */
    public function checkEnrollmentProgress(Course $course)
    {
        $enrollmentProgress = Cache::get("auto_enrollment_{$course->id}");
        $unenrollmentProgress = Cache::get("auto_unenrollment_{$course->id}");

        // Return whichever is active
        if ($enrollmentProgress && $enrollmentProgress['status'] === 'processing') {
            return response()->json([
                'type' => 'enrollment',
                'progress' => $enrollmentProgress
            ]);
        }

        if ($unenrollmentProgress && $unenrollmentProgress['status'] === 'processing') {
            return response()->json([
                'type' => 'unenrollment',
                'progress' => $unenrollmentProgress
            ]);
        }

        // Check for completed status
        if ($enrollmentProgress && $enrollmentProgress['status'] === 'completed') {
            return response()->json([
                'type' => 'enrollment',
                'progress' => $enrollmentProgress
            ]);
        }

        if ($unenrollmentProgress && $unenrollmentProgress['status'] === 'completed') {
            return response()->json([
                'type' => 'unenrollment',
                'progress' => $unenrollmentProgress
            ]);
        }

        return response()->json([
            'type' => 'none',
            'progress' => null
        ]);
    }

    /**
     * Remove the specified course
     */
    public function destroy(Course $course)
    {
        try {
            $course->delete();

            return redirect()
                ->route('admin.courses.index')
                ->with('success', 'Course deleted successfully!');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Failed to delete course: ' . $e->getMessage());
        }
    }

    /**
     * Restore a soft-deleted course
     */
    public function restore($id)
    {
        $course = Course::withTrashed()->findOrFail($id);
        $course->restore();

        return redirect()
            ->route('admin.courses.show', $course)
            ->with('success', 'Course restored successfully!');
    }

    /**
     * Bulk actions
     */
    public function bulkAction(Request $request)
    {
        $validated = $request->validate([
            'action' => 'required|in:delete,publish,archive,draft',
            'course_ids' => 'required|array',
            'course_ids.*' => 'exists:courses,id',
        ]);

        $courses = Course::whereIn('id', $validated['course_ids']);

        switch ($validated['action']) {
            case 'delete':
                $courses->delete();
                $message = 'Courses deleted successfully!';
                break;
            case 'publish':
                $courses->update(['status' => 'published']);
                $message = 'Courses published successfully!';
                break;
            case 'archive':
                $courses->update(['status' => 'archived']);
                $message = 'Courses archived successfully!';
                break;
            case 'draft':
                $courses->update(['status' => 'draft']);
                $message = 'Courses moved to draft successfully!';
                break;
        }

        return back()->with('success', $message);
    }

    /**
     * Clone/Duplicate a course
     */
    public function duplicate(Course $course)
    {
        DB::beginTransaction();
        try {
            $newCourse = $course->replicate();
            $newCourse->title = $course->title . ' (Copy)';
            $newCourse->slug = Str::slug($newCourse->title) . '-' . time();
            $newCourse->status = 'draft';
            $newCourse->enrolled_count = 0;
            $newCourse->published_at = null;
            $newCourse->save();

            // Copy relationships
            $newCourse->courseTags()->attach($course->courseTags->pluck('id'));
            $newCourse->courseCategories()->attach($course->courseCategories->pluck('id'));

            DB::commit();

            return redirect()
                ->route('admin.courses.edit', $newCourse)
                ->with('success', 'Course duplicated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->with('error', 'Failed to duplicate course: ' . $e->getMessage());
        }
    }

    /**
     * Display trashed courses
     */
    public function trash(Request $request)
    {
        $query = Course::onlyTrashed()->with(['instructor', 'courseCategories']);

        // Search
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                    ->orWhere('slug', 'like', '%' . $request->search . '%');
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by level
        if ($request->filled('level')) {
            $query->where('level', $request->level);
        }

        // Filter by instructor
        if ($request->filled('instructor')) {
            $query->where('instructor_id', $request->instructor);
        }

        $courses = $query->latest('deleted_at')->paginate(20);
        $instructors = User::role('tutor')->orderBy('name')->get();

        return view('admin.courses.trash', compact('courses', 'instructors'));
    }

    /**
     * Restore a soft-deleted course
     */
    public function restoreCourse($id)
    {
        $course = Course::onlyTrashed()->findOrFail($id);
        $course->restore();

        return redirect()
            ->route('admin.courses.index')
            ->with('success', 'Course restored successfully!');
    }

    /**
     * Permanently delete a course from database
     */
    public function forceDelete($id)
    {
        $course = Course::onlyTrashed()->findOrFail($id);

        // Delete thumbnail if exists
        if ($course->thumbnail && \Storage::disk('public')->exists($course->thumbnail)) {
            \Storage::disk('public')->delete($course->thumbnail);
        }

        $course->forceDelete();

        return redirect()
            ->route('admin.courses.trash')
            ->with('success', 'Course permanently deleted from database!');
    }

    /**
     * Get topics for a specific course (AJAX helper)
     */
    public function getTopics(Course $course)
    {
        $topics = $course->topics()
            ->orderBy('order')
            ->get(['id', 'title', 'order']);

        return response()->json($topics);
    }

    /**
     * Reorder topics within a course
     */
    public function reorderTopics(Request $request, Course $course)
    {
        $request->validate([
            'topics' => 'required|array',
            'topics.*.id' => 'required|exists:topics,id',
            'topics.*.order' => 'required|integer|min:0',
        ]);

        try {
            DB::beginTransaction();

            foreach ($request->topics as $topicData) {
                DB::table('topics')
                    ->where('id', $topicData['id'])
                    ->where('course_id', $course->id) // Security: ensure topic belongs to this course
                    ->update(['order' => $topicData['order']]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Topics reordered successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to reorder topics: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reorder content items (lessons, quizzes, assignments) within a topic
     */
    public function reorderContent(Request $request, Course $course)
    {
        $request->validate([
            'topic_id' => 'required|exists:topics,id',
            'items' => 'required|array',
            'items.*.id' => 'required|integer',
            'items.*.type' => 'required|in:lesson,quiz,assignment',
            'items.*.order' => 'required|integer|min:0',
        ]);

        try {
            DB::beginTransaction();

            // Verify topic belongs to this course
            $topic = DB::table('topics')
                ->where('id', $request->topic_id)
                ->where('course_id', $course->id)
                ->first();

            if (!$topic) {
                return response()->json([
                    'success' => false,
                    'message' => 'Topic not found or does not belong to this course'
                ], 404);
            }

            foreach ($request->items as $itemData) {
                $table = match ($itemData['type']) {
                    'lesson' => 'lessons',
                    'quiz' => 'quizzes',
                    'assignment' => 'assignments',
                };

                DB::table($table)
                    ->where('id', $itemData['id'])
                    ->where('topic_id', $request->topic_id) // Security: ensure item belongs to this topic
                    ->update(['order' => $itemData['order']]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Content reordered successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to reorder content: ' . $e->getMessage()
            ], 500);
        }
    }
}
