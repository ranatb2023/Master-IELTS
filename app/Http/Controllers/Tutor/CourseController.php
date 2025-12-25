<?php

namespace App\Http\Controllers\Tutor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\CourseTag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CourseController extends Controller
{
    /**
     * Display a listing of tutor's courses
     */
    public function index(Request $request)
    {
        $query = auth()->user()->createdCourses()
            ->with(['courseCategories', 'enrollments']);

        // Search
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $courses = $query->latest()->paginate(12);

        return view('tutor.courses.index', compact('courses'));
    }

    /**
     * Show the form for creating a new course
     */
    public function create()
    {
        $categories = CourseCategory::active()->orderBy('order')->get();
        $tags = CourseTag::active()->orderBy('name')->get();

        return view('tutor.courses.create', compact('categories', 'tags'));
    }

    /**
     * Store a newly created course
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:courses,slug',
            'category_id' => 'required|exists:categories,id',
            'level' => 'required|in:beginner,intermediate,advanced,all_levels',
            'description' => 'required|string',
            'short_description' => 'nullable|string|max:500',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'is_free' => 'boolean',
            'language' => 'nullable|string',
            'duration_hours' => 'nullable|integer|min:0',
            'requirements' => 'nullable|json',
            'learning_outcomes' => 'nullable|json',
            'target_audience' => 'nullable|json',
            'thumbnail' => 'nullable|string',
            'preview_video_url' => 'nullable|url',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
        ]);

        // Auto-generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
            // Ensure uniqueness
            $originalSlug = $validated['slug'];
            $counter = 1;
            while (Course::where('slug', $validated['slug'])->exists()) {
                $validated['slug'] = $originalSlug . '-' . $counter++;
            }
        }

        // Handle thumbnail upload
        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = $request->file('thumbnail')->store('courses/thumbnails', 'public');
        }

        // Convert arrays to JSON for database
        if (isset($validated['requirements']) && is_array($validated['requirements'])) {
            $validated['requirements'] = array_values(array_filter($validated['requirements']));
        }
        if (isset($validated['learning_outcomes']) && is_array($validated['learning_outcomes'])) {
            $validated['learning_outcomes'] = array_values(array_filter($validated['learning_outcomes']));
        }
        if (isset($validated['target_audience']) && is_array($validated['target_audience'])) {
            $validated['target_audience'] = array_values(array_filter($validated['target_audience']));
        }

        // Set defaults
        $validated['instructor_id'] = auth()->id();
        $validated['is_free'] = $request->has('is_free');
        $validated['is_featured'] = $request->has('is_featured');
        $validated['visibility'] = $validated['visibility'] ?? 'public';
        $status = $validated['status'] ?? 'draft';

        // Auto-set published_at if publishing
        if ($status === 'published') {
            if (empty($validated['published_at'])) {
                $validated['published_at'] = now();
            }
        }
        $validated['status'] = $status;

        DB::beginTransaction();
        try {
            // Separate category and tag data
            $categoryIds = $validated['category_ids'] ?? [];
            $tagIds = $validated['tag_ids'] ?? [];
            unset($validated['category_ids'], $validated['tag_ids']);

            $course = Course::create($validated);

            // Attach categories (many-to-many)
            if (!empty($categoryIds)) {
                $course->courseCategories()->attach($categoryIds);
            }

            // Attach tags (many-to-many)
            if (!empty($tagIds)) {
                $course->courseTags()->attach($tagIds);
            }

            DB::commit();

            return redirect()
                ->route('tutor.courses.show', $course)
                ->with('success', 'Course created successfully! Now add topics and lessons.');
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
        // Ensure tutor owns this course
        $this->authorize('update', $course);

        $course->load([
            'courseCategories',
            'courseTags',
            'topics.lessons',
            'enrollments',
            'reviews',
            'quizzes',
            'assignments'
        ]);

        $stats = [
            'total_enrollments' => $course->enrollments()->count(),
            'active_students' => $course->enrollments()->where('status', 'active')->count(),
            'completed_students' => $course->enrollments()->where('status', 'completed')->count(),
            'average_progress' => $course->enrollments()->avg('progress_percentage') ?? 0,
            'total_revenue' => $course->enrollments()->where('payment_status', 'paid')->sum('amount_paid'),
            'total_topics' => $course->topics()->count(),
            'total_lessons' => $course->topics()->withCount('lessons')->get()->sum('lessons_count'),
            'average_rating' => $course->reviews()->avg('rating') ?? 0,
            'total_reviews' => $course->reviews()->count(),
        ];

        return view('tutor.courses.show', compact('course', 'stats'));
    }

    /**
     * Show the form for editing the specified course
     */
    public function edit(Course $course)
    {
        // Ensure tutor owns this course
        $this->authorize('update', $course);

        $course->load(['courseCategories', 'courseTags']);
        $categories = CourseCategory::active()->orderBy('order')->get();
        $tags = CourseTag::active()->orderBy('name')->get();

        return view('tutor.courses.edit', compact('course', 'categories', 'tags'));
    }

    /**
     * Update the specified course
     */
    public function update(Request $request, Course $course)
    {
        // Ensure tutor owns this course
        $this->authorize('update', $course);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:courses,slug,' . $course->id,
            'subtitle' => 'nullable|string|max:255',
            'category_id' => 'nullable|exists:course_categories,id',
            'level' => 'required|in:beginner,intermediate,advanced,all_levels',
            'description' => 'required|string',
            'short_description' => 'nullable|string|max:500',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'is_free' => 'boolean',
            'is_featured' => 'boolean',
            'language' => 'nullable|string',
            'duration_hours' => 'nullable|numeric|min:0',
            'requirements' => 'nullable|array',
            'learning_outcomes' => 'nullable|array',
            'target_audience' => 'nullable|array',
            'thumbnail' => 'nullable|image|max:2048',
            'preview_video' => 'nullable|string',
            'visibility' => 'nullable|in:public,private,unlisted',
            'status' => 'nullable|in:draft,published,review,archived',
            'published_at' => 'nullable|date',
            'category_ids' => 'nullable|array',
            'category_ids.*' => 'exists:course_categories,id',
            'tag_ids' => 'nullable|array',
            'tag_ids.*' => 'exists:course_tags,id',
        ]);

        // Handle thumbnail upload
        if ($request->hasFile('thumbnail')) {
            // Delete old thumbnail if exists
            if ($course->thumbnail) {
                \Storage::disk('public')->delete($course->thumbnail);
            }
            $validated['thumbnail'] = $request->file('thumbnail')->store('courses/thumbnails', 'public');
        }

        // Convert arrays to JSON for database
        if (isset($validated['requirements']) && is_array($validated['requirements'])) {
            $validated['requirements'] = array_values(array_filter($validated['requirements']));
        }
        if (isset($validated['learning_outcomes']) && is_array($validated['learning_outcomes'])) {
            $validated['learning_outcomes'] = array_values(array_filter($validated['learning_outcomes']));
        }
        if (isset($validated['target_audience']) && is_array($validated['target_audience'])) {
            $validated['target_audience'] = array_values(array_filter($validated['target_audience']));
        }

        // Handle checkboxes
        $validated['is_free'] = $request->has('is_free');
        $validated['is_featured'] = $request->has('is_featured');

        // Auto-set published_at if status changed to published
        if (isset($validated['status']) && $validated['status'] === 'published') {
            if (empty($course->published_at) && empty($validated['published_at'])) {
                $validated['published_at'] = now();
            }
        }

        DB::beginTransaction();
        try {
            // Separate category and tag data
            $categoryIds = $validated['category_ids'] ?? null;
            $tagIds = $validated['tag_ids'] ?? null;
            unset($validated['category_ids'], $validated['tag_ids']);

            $course->update($validated);

            // Sync categories (many-to-many)
            if ($categoryIds !== null) {
                $course->courseCategories()->sync($categoryIds);
            }

            // Sync tags (many-to-many)
            if ($tagIds !== null) {
                $course->courseTags()->sync($tagIds);
            }

            DB::commit();

            return redirect()
                ->route('tutor.courses.show', $course)
                ->with('success', 'Course updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Failed to update course: ' . $e->getMessage());
        }
    }

    /**
     * Submit course for review
     */
    public function submitForReview(Course $course)
    {
        // Ensure tutor owns this course
        $this->authorize('update', $course);

        // Validate course has minimum required content
        if ($course->topics()->count() < 1) {
            return back()->with('error', 'Please add at least one topic before submitting for review.');
        }

        if ($course->topics()->withCount('lessons')->get()->sum('lessons_count') < 1) {
            return back()->with('error', 'Please add at least one lesson before submitting for review.');
        }

        $course->update(['status' => 'review']);

        return back()->with('success', 'Course submitted for review!');
    }

    /**
     * Publish course (if allowed)
     */
    public function publish(Course $course)
    {
        // Ensure tutor owns this course
        $this->authorize('update', $course);

        // Check if course meets publish requirements
        if ($course->topics()->count() < 1 || $course->topics()->withCount('lessons')->get()->sum('lessons_count') < 1) {
            return back()->with('error', 'Course must have at least one topic and lesson to publish.');
        }

        $course->update([
            'status' => 'published',
            'published_at' => now(),
        ]);

        return back()->with('success', 'Course published successfully!');
    }

    /**
     * Unpublish/Archive course
     */
    public function archive(Course $course)
    {
        // Ensure tutor owns this course
        $this->authorize('update', $course);

        $course->update(['status' => 'archived']);

        return back()->with('success', 'Course archived successfully!');
    }

    /**
     * View course analytics
     */
    public function analytics(Course $course)
    {
        // Ensure tutor owns this course
        $this->authorize('update', $course);

        $enrollmentTrends = $course->enrollments()
            ->selectRaw('DATE(enrolled_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->take(30)
            ->get();

        $revenueTrends = $course->enrollments()
            ->where('payment_status', 'paid')
            ->selectRaw('DATE(enrolled_at) as date, SUM(amount_paid) as revenue')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->take(30)
            ->get();

        $completionRates = [
            'completed' => $course->enrollments()->where('status', 'completed')->count(),
            'active' => $course->enrollments()->where('status', 'active')->count(),
            'cancelled' => $course->enrollments()->where('status', 'cancelled')->count(),
        ];

        return view('tutor.courses.analytics', compact(
            'course',
            'enrollmentTrends',
            'revenueTrends',
            'completionRates'
        ));
    }

    /**
     * View enrolled students
     */
    public function students(Course $course)
    {
        // Ensure tutor owns this course
        $this->authorize('update', $course);

        $enrollments = $course->enrollments()
            ->with(['user', 'progress'])
            ->latest('enrolled_at')
            ->paginate(20);

        return view('tutor.courses.students', compact('course', 'enrollments'));
    }

    /**
     * View trashed courses
     */
    public function trash(Request $request)
    {
        $query = auth()->user()->createdCourses()->onlyTrashed();

        // Search
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by level
        if ($request->filled('level')) {
            $query->where('level', $request->level);
        }

        $courses = $query->latest('deleted_at')->paginate(15);

        return view('tutor.courses.trash', compact('courses'));
    }

    /**
     * Restore a trashed course
     */
    public function restore($id)
    {
        $course = auth()->user()->createdCourses()->onlyTrashed()->findOrFail($id);

        $course->restore();

        return redirect()
            ->route('tutor.courses.trash')
            ->with('success', 'Course restored successfully!');
    }

    /**
     * Permanently delete a course
     */
    public function forceDelete($id)
    {
        $course = auth()->user()->createdCourses()->onlyTrashed()->findOrFail($id);

        // Delete thumbnail if exists
        if ($course->thumbnail) {
            \Storage::disk('public')->delete($course->thumbnail);
        }

        $course->forceDelete();

        return redirect()
            ->route('tutor.courses.trash')
            ->with('success', 'Course permanently deleted!');
    }
}
