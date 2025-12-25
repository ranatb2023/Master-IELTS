<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseCategory;
use Illuminate\Http\Request;

class PublicCourseController extends Controller
{
    /**
     * Display all courses (public catalog)
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

        // Filter by category
        if ($request->filled('category')) {
            $query->whereHas('courseCategories', function ($q) use ($request) {
                $q->where('course_categories.id', $request->category);
            });
        }

        // Filter by level
        if ($request->filled('level')) {
            $query->where('level', $request->level);
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
        $sortBy = $request->get('sort', 'latest');
        switch ($sortBy) {
            case 'popular':
                $query->orderBy('total_enrollments', 'desc');
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
            default:
                $query->latest('published_at');
        }

        $courses = $query->paginate(12);
        $categories = CourseCategory::active()
            ->withCount('courses')
            ->orderBy('order')
            ->get();

        return view('courses.index', compact('courses', 'categories'));
    }

    /**
     * Display course details (public view)
     */
    public function show(Course $course)
    {
        // Check if course is published
        if ($course->status !== 'published') {
            abort(404);
        }

        $course->load([
            'instructor.profile',
            'courseCategories',
            'courseTags',
            'topics' => function ($query) {
                $query->orderBy('order');
            },
            'topics.lessons' => function ($query) {
                $query->where('is_preview', true)->orderBy('order');
            },
            'reviews' => function ($query) {
                $query->with('user')->latest()->take(10);
            },
            'quizzes' => function ($query) {
                $query->where('quizzes.is_published', true);
            }
        ]);

        // Check if user is enrolled (if authenticated)
        $enrollment = null;
        if (auth()->check()) {
            $enrollment = auth()->user()->enrollments()
                ->where('course_id', $course->id)
                ->first();
        }

        // Get related courses (courses sharing the same primary category)
        $primaryCategory = $course->category;
        $relatedCourses = $primaryCategory
            ? Course::published()
                ->whereHas('courseCategories', function ($q) use ($primaryCategory) {
                    $q->where('course_categories.id', $primaryCategory->id);
                })
                ->where('id', '!=', $course->id)
                ->with(['instructor', 'courseCategories'])
                ->take(4)
                ->get()
            : collect();

        // Get instructor's other courses
        $instructorCourses = Course::published()
            ->where('instructor_id', $course->instructor_id)
            ->where('id', '!=', $course->id)
            ->take(4)
            ->get();

        return view('courses.show', compact(
            'course',
            'enrollment',
            'relatedCourses',
            'instructorCourses'
        ));
    }

    /**
     * Display courses by category
     */
    public function byCategory(CourseCategory $category)
    {
        $courses = Course::published()
            ->whereHas('courseCategories', function ($q) use ($category) {
                $q->where('course_categories.id', $category->id);
            })
            ->with(['instructor', 'courseCategories', 'reviews'])
            ->paginate(12);

        return view('courses.by-category', compact('category', 'courses'));
    }

    /**
     * Display courses by instructor
     */
    public function byInstructor($instructorId)
    {
        $instructor = \App\Models\User::role('tutor')->findOrFail($instructorId);

        $courses = Course::published()
            ->where('instructor_id', $instructor->id)
            ->with(['courseCategories', 'reviews'])
            ->paginate(12);

        $stats = [
            'total_courses' => $instructor->createdCourses()->count(),
            'total_students' => $instructor->createdCourses()->withCount('enrollments')->get()->sum('enrollments_count'),
            'average_rating' => $instructor->createdCourses()->avg('average_rating') ?? 0,
        ];

        return view('courses.by-instructor', compact('instructor', 'courses', 'stats'));
    }
}
