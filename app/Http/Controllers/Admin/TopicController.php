<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Topic;
use App\Models\Course;
use Illuminate\Http\Request;

class TopicController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Topic::with(['course.courseCategories'])
            ->withCount(['lessons', 'quizzes', 'assignments'])
            ->whereHas('course'); // Only show topics with existing courses

        // Search
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        // Filter by course
        if ($request->filled('course')) {
            $query->where('course_id', $request->course);
        }

        // Filter by published status
        if ($request->filled('status')) {
            if ($request->status === 'published') {
                $query->where('is_published', true);
            } elseif ($request->status === 'draft') {
                $query->where('is_published', false);
            }
        }

        $topics = $query->latest()->paginate(20)->withQueryString();
        $courses = Course::orderBy('title')->get();

        return view('admin.topics.index', compact('topics', 'courses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $courses = Course::orderBy('title')->get();
        $selectedCourse = $request->query('course_id');

        return view('admin.topics.create', compact('courses', 'selectedCourse'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'required|integer|min:0',
            'is_published' => 'boolean',
        ], [
            'course_id.required' => 'Please select a course for this topic.',
            'course_id.exists' => 'The selected course does not exist.',
            'title.required' => 'The topic title is required.',
            'title.max' => 'The topic title cannot exceed 255 characters.',
            'order.required' => 'The order is required.',
            'order.min' => 'The order cannot be negative.',
        ]);

        $validated['is_published'] = $request->has('is_published');

        $topic = Topic::create($validated);

        return redirect()->route('admin.topics.show', $topic)
            ->with('success', 'Topic created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Topic $topic)
    {
        $topic->load([
            'course',
            'lessons' => function($query) {
                $query->with(['contentable'])
                    ->withCount(['progress', 'comments'])
                    ->orderBy('order');
            },
            'quizzes' => function($query) {
                $query->orderBy('order');
            },
            'assignments' => function($query) {
                $query->orderBy('order');
            }
        ]);

        return view('admin.topics.show', compact('topic'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Topic $topic)
    {
        $topic->load('course');
        $courses = Course::orderBy('title')->get();

        return view('admin.topics.edit', compact('topic', 'courses'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Topic $topic)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'required|integer|min:0',
            'is_published' => 'boolean',
        ]);

        $validated['is_published'] = $request->has('is_published');

        $topic->update($validated);

        return redirect()->route('admin.topics.index')
            ->with('success', 'Topic updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Topic $topic)
    {
        $courseName = $topic->course->title;
        $topic->delete();

        return redirect()->route('admin.topics.index')
            ->with('success', "Topic deleted from {$courseName} successfully");
    }

    /**
     * Display trashed topics
     */
    public function trash()
    {
        $topics = Topic::onlyTrashed()
            ->with(['course'])
            ->withCount(['lessons', 'quizzes', 'assignments'])
            ->latest('deleted_at')
            ->paginate(20);

        return view('admin.topics.trash', compact('topics'));
    }

    /**
     * Restore a trashed topic
     */
    public function restore($id)
    {
        $topic = Topic::onlyTrashed()->findOrFail($id);
        $topic->restore();

        return redirect()->route('admin.topics.trash')
            ->with('success', 'Topic restored successfully');
    }

    /**
     * Permanently delete a topic
     */
    public function forceDelete($id)
    {
        $topic = Topic::onlyTrashed()->findOrFail($id);
        $topic->forceDelete();

        return redirect()->route('admin.topics.trash')
            ->with('success', 'Topic permanently deleted');
    }
}
