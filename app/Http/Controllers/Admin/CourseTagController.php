<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CourseTag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CourseTagController extends Controller
{
    public function index()
    {
        $tags = CourseTag::withCount('courses')
            ->orderBy('name')
            ->paginate(20);

        return view('admin.course-tags.index', compact('tags'));
    }

    public function create()
    {
        return view('admin.course-tags.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:course_tags,slug',
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:50',
            'is_active' => 'boolean',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $validated['is_active'] = $request->has('is_active');

        CourseTag::create($validated);

        return redirect()
            ->route('admin.course-tags.index')
            ->with('success', 'Course tag created successfully!');
    }

    public function edit(CourseTag $courseTag)
    {
        return view('admin.course-tags.edit', compact('courseTag'));
    }

    public function update(Request $request, CourseTag $courseTag)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:course_tags,slug,' . $courseTag->id,
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:50',
            'is_active' => 'boolean',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $validated['is_active'] = $request->has('is_active');

        $courseTag->update($validated);

        return redirect()
            ->route('admin.course-tags.index')
            ->with('success', 'Course tag updated successfully!');
    }

    public function destroy(CourseTag $courseTag)
    {
        $courseTag->delete();

        return redirect()
            ->route('admin.course-tags.index')
            ->with('success', 'Course tag deleted successfully!');
    }
}
