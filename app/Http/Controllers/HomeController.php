<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\Package;
use App\Models\Review;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Display the homepage
     */
    public function index()
    {
        // Featured courses
        $featuredCourses = Course::published()
            ->where('is_featured', true)
            ->with(['instructor', 'courseCategories', 'reviews'])
            ->take(8)
            ->get();

        // Popular courses
        $popularCourses = Course::published()
            ->orderBy('enrolled_count', 'desc')
            ->with(['instructor', 'courseCategories'])
            ->take(8)
            ->get();

        // Latest courses
        $latestCourses = Course::published()
            ->latest('published_at')
            ->with(['instructor', 'courseCategories'])
            ->take(8)
            ->get();

        // Categories
        $categories = CourseCategory::active()
            ->withCount('courses')
            ->orderBy('order')
            ->take(8)
            ->get();

        // Featured packages
        $featuredPackages = Package::where('status', 'published')
            ->where('is_active', true)
            ->where('is_featured', true)
            ->with('courses')
            ->take(3)
            ->get();

        // Featured subscription plans
        $featuredSubscriptions = \App\Models\SubscriptionPlan::where('is_active', true)
            ->orderBy('price')
            ->take(3)
            ->get();

        // Statistics
        $stats = [
            'total_courses' => Course::published()->count(),
            'total_students' => \App\Models\User::role('student')->count(),
            'total_instructors' => \App\Models\User::role('tutor')->count(),
            'total_enrollments' => \App\Models\Enrollment::count(),
        ];

        // Latest blog posts
        // TODO: Implement BlogPost model
        $blogPosts = collect([]);

        // Testimonials/Reviews
        $testimonials = Review::where('rating', '>=', 4)
            ->with(['user', 'course'])
            ->latest()
            ->take(6)
            ->get();

        return view('home', compact(
            'featuredCourses',
            'popularCourses',
            'latestCourses',
            'categories',
            'featuredPackages',
            'featuredSubscriptions',
            'stats',
            'blogPosts',
            'testimonials'
        ));
    }

    /**
     * Display about page
     */
    public function about()
    {
        return view('pages.about');
    }

    /**
     * Display contact page
     */
    public function contact()
    {
        return view('pages.contact');
    }

    /**
     * Handle contact form submission
     */
    public function contactSubmit(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:2000',
        ]);

        // TODO: Send email to admin
        // TODO: Store in contact_messages table if exists

        return back()->with('success', 'Thank you for contacting us! We will get back to you soon.');
    }

    /**
     * Search courses
     */
    public function search(Request $request)
    {
        $query = $request->get('q');

        if (empty($query)) {
            return redirect()->route('courses.index');
        }

        $courses = Course::published()
            ->where(function ($q) use ($query) {
                $q->where('title', 'like', '%' . $query . '%')
                    ->orWhere('description', 'like', '%' . $query . '%')
                    ->orWhere('short_description', 'like', '%' . $query . '%');
            })
            ->with(['instructor', 'courseCategories'])
            ->paginate(12);

        return view('search', compact('courses', 'query'));
    }

    /**
     * Display reading page
     */
    public function reading()
    {
        return view('pages.reading');
    }

    public function listening()
    {
        return view('pages.listening');
    }

    public function writing()
    {
        return view('pages.writing');
    }

    public function speaking()
    {
        return view('pages.speaking');
    }
}
