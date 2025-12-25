<?php

namespace App\Http\Controllers\Tutor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Topic;
use Illuminate\Http\Request;

class TopicController extends Controller
{
    /**
     * Display topics for a course
     */
    public function index(Course $course)
    {
        // Ensure tutor owns this course
        $this->authorize('update', $course);

        $topics = $course->topics()
            ->withCount('lessons')
            ->orderBy('order')
            ->get();

        return view('tutor.topics.index', compact('course', 'topics'));
    }

    /**
     * Store a new topic
     */
    public function store(Request $request, Course $course)
    {
        // Ensure tutor owns this course
        $this->authorize('update', $course);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'nullable|integer|min:0',
            'is_free_preview' => 'boolean',
        ]);

        // Set order if not provided
        if (!isset($validated['order'])) {
            $validated['order'] = $course->topics()->max('order') + 1;
        }

        $validated['course_id'] = $course->id;

        $topic = Topic::create($validated);

        return back()->with('success', 'Topic created successfully!');
    }

    /**
     * Update a topic
     */
    public function update(Request $request, Course $course, Topic $topic)
    {
        // Ensure tutor owns this course and topic belongs to course
        $this->authorize('update', $course);

        if ($topic->course_id !== $course->id) {
            abort(404);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'nullable|integer|min:0',
            'is_free_preview' => 'boolean',
        ]);

        $topic->update($validated);

        return back()->with('success', 'Topic updated successfully!');
    }

    /**
     * Delete a topic
     */
    public function destroy(Course $course, Topic $topic)
    {
        // Ensure tutor owns this course and topic belongs to course
        $this->authorize('update', $course);

        if ($topic->course_id !== $course->id) {
            abort(404);
        }

        // Check if topic has lessons
        if ($topic->lessons()->count() > 0) {
            return back()->with('error', 'Cannot delete topic with lessons. Please delete lessons first.');
        }

        $topic->delete();

        return back()->with('success', 'Topic deleted successfully!');
    }

    /**
     * Reorder topics
     */
    public function reorder(Request $request, Course $course)
    {
        // Ensure tutor owns this course
        $this->authorize('update', $course);

        $validated = $request->validate([
            'topics' => 'required|array',
            'topics.*.id' => 'required|exists:topics,id',
            'topics.*.order' => 'required|integer|min:0',
        ]);

        foreach ($validated['topics'] as $topicData) {
            Topic::where('id', $topicData['id'])
                ->where('course_id', $course->id)
                ->update(['order' => $topicData['order']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Topics reordered successfully!'
        ]);
    }

    /**
     * Display all topics across all tutor's courses
     */
    public function allTopics(Request $request)
    {
        $query = Topic::whereHas('course', function ($q) {
            $q->where('instructor_id', auth()->id());
        })
            ->with(['course'])
            ->withCount(['lessons', 'quizzes', 'assignments']);

        // Search
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        // Filter by course
        if ($request->filled('course')) {
            $query->where('course_id', $request->course);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_published', $request->status === 'published');
        }

        $topics = $query->latest()->paginate(15);

        // Get tutor's courses for filter
        $courses = auth()->user()->createdCourses()->get();

        return view('tutor.topics.index', compact('topics', 'courses'));
    }

    /**
     * Show a single topic
     */
    public function show(Topic $topic)
    {
        // Ensure tutor owns this topic's course
        if ($topic->course->instructor_id !== auth()->id()) {
            abort(403);
        }

        $topic->load(['course', 'lessons', 'quizzes', 'assignments']);

        return view('tutor.topics.show', compact('topic'));
    }

    /**
     * Display trashed topics
     */
    public function trash(Request $request)
    {
        $query = Topic::onlyTrashed()
            ->whereHas('course', function ($q) {
                $q->where('instructor_id', auth()->id());
            })
            ->with('course');

        // Search
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        // Filter by course
        if ($request->filled('course')) {
            $query->where('course_id', $request->course);
        }

        $topics = $query->latest('deleted_at')->paginate(15);

        // Get tutor's courses for filter
        $courses = auth()->user()->createdCourses()->get();

        return view('tutor.topics.trash', compact('topics', 'courses'));
    }

    /**
     * Restore a trashed topic
     */
    public function restore($id)
    {
        $topic = Topic::onlyTrashed()
            ->whereHas('course', function ($q) {
                $q->where('instructor_id', auth()->id());
            })
            ->findOrFail($id);

        $topic->restore();

        return redirect()
            ->route('tutor.topics.trash')
            ->with('success', 'Topic restored successfully!');
    }

    /**
     * Permanently delete a topic
     */
    public function forceDelete($id)
    {
        $topic = Topic::onlyTrashed()
            ->whereHas('course', function ($q) {
                $q->where('instructor_id', auth()->id());
            })
            ->findOrFail($id);

        $topic->forceDelete();

        return redirect()
            ->route('tutor.topics.trash')
            ->with('success', 'Topic permanently deleted!');
    }
}
