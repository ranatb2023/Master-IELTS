# Admin Routes Added - Complete ✅

## Problem Solved
Fixed the error: `Route [admin.lessons.index] not defined` by adding missing admin routes.

---

## Routes Added to `routes/web.php`

### 1. Topics Routes
```php
Route::prefix('topics')->name('topics.')->group(function () {
    Route::get('/', [AdminTopicController::class, 'index'])->name('index');
    Route::get('/{topic}', [AdminTopicController::class, 'show'])->name('show');
    Route::get('/{topic}/edit', [AdminTopicController::class, 'edit'])->name('edit');
    Route::put('/{topic}', [AdminTopicController::class, 'update'])->name('update');
    Route::delete('/{topic}', [AdminTopicController::class, 'destroy'])->name('destroy');
});
```

**Route Names:**
- `admin.topics.index` - List all topics
- `admin.topics.show` - View single topic
- `admin.topics.edit` - Edit topic form
- `admin.topics.update` - Update topic (PUT)
- `admin.topics.destroy` - Delete topic

---

### 2. Lessons Routes
```php
Route::prefix('lessons')->name('lessons.')->group(function () {
    Route::get('/', [AdminLessonController::class, 'index'])->name('index');
    Route::get('/{lesson}', [AdminLessonController::class, 'show'])->name('show');
    Route::get('/{lesson}/edit', [AdminLessonController::class, 'edit'])->name('edit');
    Route::put('/{lesson}', [AdminLessonController::class, 'update'])->name('update');
    Route::delete('/{lesson}', [AdminLessonController::class, 'destroy'])->name('destroy');
});
```

**Route Names:**
- `admin.lessons.index` - List all lessons
- `admin.lessons.show` - View single lesson
- `admin.lessons.edit` - Edit lesson form
- `admin.lessons.update` - Update lesson (PUT)
- `admin.lessons.destroy` - Delete lesson

---

### 3. Quizzes Routes (Admin)
```php
Route::prefix('quizzes')->name('quizzes.')->group(function () {
    Route::get('/', [AdminQuizController::class, 'index'])->name('index');
    Route::get('/{quiz}', [AdminQuizController::class, 'show'])->name('show');
    Route::get('/{quiz}/edit', [AdminQuizController::class, 'edit'])->name('edit');
    Route::put('/{quiz}', [AdminQuizController::class, 'update'])->name('update');
    Route::delete('/{quiz}', [AdminQuizController::class, 'destroy'])->name('destroy');
});
```

**Route Names:**
- `admin.quizzes.index` - List all quizzes
- `admin.quizzes.show` - View single quiz
- `admin.quizzes.edit` - Edit quiz form
- `admin.quizzes.update` - Update quiz (PUT)
- `admin.quizzes.destroy` - Delete quiz

---

### 4. Assignments Routes
```php
Route::prefix('assignments')->name('assignments.')->group(function () {
    Route::get('/', [AdminAssignmentController::class, 'index'])->name('index');
    Route::get('/{assignment}', [AdminAssignmentController::class, 'show'])->name('show');
    Route::get('/{assignment}/edit', [AdminAssignmentController::class, 'edit'])->name('edit');
    Route::put('/{assignment}', [AdminAssignmentController::class, 'update'])->name('update');
    Route::delete('/{assignment}', [AdminAssignmentController::class, 'destroy'])->name('destroy');
});
```

**Route Names:**
- `admin.assignments.index` - List all assignments
- `admin.assignments.show` - View single assignment
- `admin.assignments.edit` - Edit assignment form
- `admin.assignments.update` - Update assignment (PUT)
- `admin.assignments.destroy` - Delete assignment

---

## Controller Imports Added

Added to the top of `routes/web.php`:

```php
use App\Http\Controllers\Admin\TopicController as AdminTopicController;
use App\Http\Controllers\Admin\LessonController as AdminLessonController;
use App\Http\Controllers\Admin\AssignmentController as AdminAssignmentController;
```

**Note:** `AdminQuizController` was already imported.

---

## Next Steps - Create Controllers

These controllers need to be created in `app/Http/Controllers/Admin/`:

### 1. TopicController
```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Topic;
use Illuminate\Http\Request;

class TopicController extends Controller
{
    public function index()
    {
        $topics = Topic::with(['course', 'lessons', 'quizzes', 'assignments'])
            ->latest()
            ->paginate(20);

        return view('admin.topics.index', compact('topics'));
    }

    public function show(Topic $topic)
    {
        $topic->load(['course', 'lessons', 'quizzes', 'assignments']);
        return view('admin.topics.show', compact('topic'));
    }

    public function edit(Topic $topic)
    {
        $topic->load('course');
        return view('admin.topics.edit', compact('topic'));
    }

    public function update(Request $request, Topic $topic)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'required|integer|min:0',
            'is_published' => 'boolean',
        ]);

        $topic->update($validated);

        return redirect()->route('admin.topics.index')
            ->with('success', 'Topic updated successfully');
    }

    public function destroy(Topic $topic)
    {
        $topic->delete();

        return redirect()->route('admin.topics.index')
            ->with('success', 'Topic deleted successfully');
    }
}
```

---

### 2. LessonController
```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use Illuminate\Http\Request;

class LessonController extends Controller
{
    public function index()
    {
        $lessons = Lesson::with(['topic.course', 'contentable'])
            ->latest()
            ->paginate(20);

        return view('admin.lessons.index', compact('lessons'));
    }

    public function show(Lesson $lesson)
    {
        $lesson->load(['topic.course', 'contentable']);
        return view('admin.lessons.show', compact('lesson'));
    }

    public function edit(Lesson $lesson)
    {
        $lesson->load(['topic.course', 'contentable']);
        return view('admin.lessons.edit', compact('lesson'));
    }

    public function update(Request $request, Lesson $lesson)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration_minutes' => 'nullable|integer|min:1',
            'order' => 'required|integer|min:0',
            'is_preview' => 'boolean',
            'is_published' => 'boolean',
            'requires_previous_completion' => 'boolean',
        ]);

        $lesson->update($validated);

        return redirect()->route('admin.lessons.index')
            ->with('success', 'Lesson updated successfully');
    }

    public function destroy(Lesson $lesson)
    {
        $lesson->delete();

        return redirect()->route('admin.lessons.index')
            ->with('success', 'Lesson deleted successfully');
    }
}
```

---

### 3. AssignmentController
```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use Illuminate\Http\Request;

class AssignmentController extends Controller
{
    public function index()
    {
        $assignments = Assignment::with(['topic.course', 'submissions'])
            ->latest()
            ->paginate(20);

        return view('admin.assignments.index', compact('assignments'));
    }

    public function show(Assignment $assignment)
    {
        $assignment->load(['topic.course', 'submissions.student']);
        return view('admin.assignments.show', compact('assignment'));
    }

    public function edit(Assignment $assignment)
    {
        $assignment->load(['topic.course']);
        return view('admin.assignments.edit', compact('assignment'));
    }

    public function update(Request $request, Assignment $assignment)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'instructions' => 'nullable|string',
            'max_points' => 'required|numeric|min:0',
            'passing_points' => 'required|numeric|min:0',
            'due_date' => 'nullable|date',
            'allow_late_submission' => 'boolean',
            'late_penalty' => 'nullable|numeric|min:0|max:100',
            'is_published' => 'boolean',
        ]);

        $assignment->update($validated);

        return redirect()->route('admin.assignments.index')
            ->with('success', 'Assignment updated successfully');
    }

    public function destroy(Assignment $assignment)
    {
        $assignment->delete();

        return redirect()->route('admin.assignments.index')
            ->with('success', 'Assignment deleted successfully');
    }
}
```

---

## Required Admin Views

Create these index views:

### 1. admin/topics/index.blade.php
Simple listing of all topics with course names, lesson/quiz/assignment counts, edit/delete buttons.

### 2. admin/lessons/index.blade.php
Simple listing of all lessons with topic/course names, content type, duration, edit/delete buttons.

### 3. admin/assignments/index.blade.php
Simple listing of all assignments with topic/course names, due dates, submission counts, edit/delete buttons.

---

## Testing Checklist

- [x] Routes defined in web.php
- [x] Controller imports added
- [ ] Create AdminTopicController
- [ ] Create AdminLessonController
- [ ] Create AdminAssignmentController
- [ ] Create admin/topics/index view
- [ ] Create admin/lessons/index view
- [ ] Create admin/assignments/index view
- [ ] Test sidebar navigation
- [ ] Test permissions

---

## Status

✅ **Routes Fixed** - Dashboard should now load without errors
⏳ **Controllers Needed** - Must create the 3 admin controllers
⏳ **Views Needed** - Must create the 3 index views

---

**Date:** {{ now()->format('Y-m-d H:i:s') }}
**Status:** Routes Complete, Controllers & Views Pending
