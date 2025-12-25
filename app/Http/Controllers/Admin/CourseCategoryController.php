<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CourseCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CourseCategoryController extends Controller
{
    public function index()
    {
        $categories = CourseCategory::with('parent', 'children')
            ->withCount('courses')
            ->orderBy('order')
            ->paginate(20);

        return view('admin.course-categories.index', compact('categories'));
    }

    public function create()
    {
        $parentCategories = CourseCategory::whereNull('parent_id')
            ->orderBy('order')
            ->get();

        return view('admin.course-categories.create', compact('parentCategories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:course_categories,slug',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:50',
            'order' => 'nullable|integer',
            'is_active' => 'boolean',
            'parent_id' => 'nullable|exists:course_categories,id',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $validated['is_active'] = $request->has('is_active');

        CourseCategory::create($validated);

        return redirect()
            ->route('admin.course-categories.index')
            ->with('success', 'Course category created successfully!');
    }

    public function edit(CourseCategory $courseCategory)
    {
        $parentCategories = CourseCategory::whereNull('parent_id')
            ->where('id', '!=', $courseCategory->id)
            ->orderBy('order')
            ->get();

        return view('admin.course-categories.edit', compact('courseCategory', 'parentCategories'));
    }

    public function update(Request $request, CourseCategory $courseCategory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:course_categories,slug,' . $courseCategory->id,
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:50',
            'order' => 'nullable|integer',
            'is_active' => 'boolean',
            'parent_id' => 'nullable|exists:course_categories,id',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $validated['is_active'] = $request->has('is_active');

        $courseCategory->update($validated);

        return redirect()
            ->route('admin.course-categories.index')
            ->with('success', 'Course category updated successfully!');
    }

    public function destroy(CourseCategory $courseCategory)
    {
        $courseCategory->delete();

        return redirect()
            ->route('admin.course-categories.index')
            ->with('success', 'Course category deleted successfully!');
    }
}
