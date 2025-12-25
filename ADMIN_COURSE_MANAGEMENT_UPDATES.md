# Admin Course Management Updates - Complete ✅

## Summary of Changes

All admin course management features have been updated with enhanced forms and proper sidebar navigation.

---

## 📋 Updates Made

### 1. Enhanced Admin Course Create Form
**File:** `resources/views/admin/courses/create.blade.php`

**New Sections Added:**

#### Learning Outcomes (Dynamic Array)
- Add/remove learning outcomes with Alpine.js
- Dynamic field management
- Array submission support

#### Requirements (Dynamic Array)
- Add/remove prerequisites
- Dynamic field management with remove buttons
- Minimum 1 requirement enforced

#### SEO Settings
- Meta Title (60 char max)
- Meta Description (160 char max)
- Meta Keywords (comma-separated)
- Character count recommendations

#### Additional Settings
- Drip content (release lessons gradually)
- Is Featured course
- Allow student reviews
- Enable discussions

**Features:**
- Alpine.js for dynamic fields
- Input validation with character limits
- Old input preservation
- User-friendly placeholders

---

### 2. Updated Admin Sidebar Navigation
**File:** `resources/views/layouts/partials/admin-sidebar.blade.php`

#### Course Management Submenu (NEW)
Replaced single "Courses" link with expandable submenu:

```
Course Management (Dropdown)
├── All Courses
├── Topics
└── Lessons
```

**Permissions:**
- `course.view` - View courses
- `course.create` - Create courses
- `course.update` - Update courses
- `topic.view` - View topics
- `lesson.view` - View lessons

#### Separated Quiz & Assignment Sections (NEW)
- **Quizzes** - Standalone menu item with quiz icon
- **Assignments** - Standalone menu item with assignment icon

**Permissions:**
- `quiz.view` - View quizzes
- `quiz.manage` - Manage quizzes
- `assignment.view` - View assignments
- `assignment.manage` - Manage assignments

#### Features:
- Alpine.js collapsible submenus
- Auto-expand when on relevant route
- Consistent styling with hover effects
- Icon updates (quiz uses question mark icon)
- Both desktop and mobile sidebars updated

---

## 🎨 UI/UX Enhancements

### Dynamic Fields with Alpine.js
```blade
<div x-data="{ outcomes: {{ json_encode(old('learning_outcomes', [''])) }} }">
    <!-- Dynamic outcome inputs -->
    <template x-for="(outcome, index) in outcomes" :key="index">
        <!-- Input field with remove button -->
    </template>
    <!-- Add button -->
</div>
```

### Sidebar Submenu Pattern
```blade
<div x-data="{ open: {{ request()->routeIs('admin.courses.*') ? 'true' : 'false' }} }">
    <button @click="open = !open">
        <!-- Menu title with arrow icon -->
    </button>
    <div x-show="open" x-collapse>
        <!-- Submenu items -->
    </div>
</div>
```

---

## 🔑 Required Permissions

### New Permissions to Create

Run the following in a seeder or admin panel:

```php
// Course Management Permissions
Permission::create(['name' => 'topic.view', 'guard_name' => 'web']);
Permission::create(['name' => 'topic.create', 'guard_name' => 'web']);
Permission::create(['name' => 'topic.update', 'guard_name' => 'web']);
Permission::create(['name' => 'topic.delete', 'guard_name' => 'web']);

Permission::create(['name' => 'lesson.view', 'guard_name' => 'web']);
Permission::create(['name' => 'lesson.create', 'guard_name' => 'web']);
Permission::create(['name' => 'lesson.update', 'guard_name' => 'web']);
Permission::create(['name' => 'lesson.delete', 'guard_name' => 'web']);

Permission::create(['name' => 'assignment.view', 'guard_name' => 'web']);
Permission::create(['name' => 'assignment.manage', 'guard_name' => 'web']);
Permission::create(['name' => 'assignment.grade', 'guard_name' => 'web']);
```

### Assign Permissions to Roles

```php
// Super Admin gets all permissions
$superAdmin = Role::findByName('super_admin');
$superAdmin->givePermissionTo([
    'topic.view', 'topic.create', 'topic.update', 'topic.delete',
    'lesson.view', 'lesson.create', 'lesson.update', 'lesson.delete',
    'assignment.view', 'assignment.manage', 'assignment.grade'
]);

// Course Manager role example
$courseManager = Role::findByName('course_manager');
$courseManager->givePermissionTo([
    'course.view', 'course.create', 'course.update',
    'topic.view', 'topic.create', 'topic.update',
    'lesson.view', 'lesson.create', 'lesson.update',
    'quiz.view', 'quiz.manage',
    'assignment.view', 'assignment.manage'
]);
```

---

## 🛣️ Required Routes

Add these routes to `routes/web.php`:

```php
// Admin Routes
Route::middleware(['auth', 'role:super_admin'])->prefix('admin')->name('admin.')->group(function () {

    // Existing routes...

    // Topics Management
    Route::get('topics', [Admin\TopicController::class, 'index'])->name('topics.index');
    Route::get('topics/{topic}', [Admin\TopicController::class, 'show'])->name('topics.show');
    Route::get('topics/{topic}/edit', [Admin\TopicController::class, 'edit'])->name('topics.edit');
    Route::put('topics/{topic}', [Admin\TopicController::class, 'update'])->name('topics.update');
    Route::delete('topics/{topic}', [Admin\TopicController::class, 'destroy'])->name('topics.destroy');

    // Lessons Management
    Route::get('lessons', [Admin\LessonController::class, 'index'])->name('lessons.index');
    Route::get('lessons/{lesson}', [Admin\LessonController::class, 'show'])->name('lessons.show');
    Route::get('lessons/{lesson}/edit', [Admin\LessonController::class, 'edit'])->name('lessons.edit');
    Route::put('lessons/{lesson}', [Admin\LessonController::class, 'update'])->name('lessons.update');
    Route::delete('lessons/{lesson}', [Admin\LessonController::class, 'destroy'])->name('lessons.destroy');

    // Assignments Management
    Route::get('assignments', [Admin\AssignmentController::class, 'index'])->name('assignments.index');
    Route::get('assignments/{assignment}', [Admin\AssignmentController::class, 'show'])->name('assignments.show');
    Route::get('assignments/{assignment}/edit', [Admin\AssignmentController::class, 'edit'])->name('assignments.edit');
    Route::put('assignments/{assignment}', [Admin\AssignmentController::class, 'update'])->name('assignments.update');
    Route::delete('assignments/{assignment}', [Admin\AssignmentController::class, 'destroy'])->name('assignments.destroy');

});
```

---

## 📊 Database Schema Updates

Ensure these columns exist in the `courses` table:

```php
Schema::table('courses', function (Blueprint $table) {
    // Learning outcomes & requirements (JSON)
    if (!Schema::hasColumn('courses', 'learning_outcomes')) {
        $table->json('learning_outcomes')->nullable();
    }
    if (!Schema::hasColumn('courses', 'requirements')) {
        $table->json('requirements')->nullable();
    }

    // SEO fields
    if (!Schema::hasColumn('courses', 'meta_title')) {
        $table->string('meta_title', 60)->nullable();
    }
    if (!Schema::hasColumn('courses', 'meta_description')) {
        $table->string('meta_description', 160)->nullable();
    }
    if (!Schema::hasColumn('courses', 'meta_keywords')) {
        $table->string('meta_keywords')->nullable();
    }

    // Additional settings
    if (!Schema::hasColumn('courses', 'is_featured')) {
        $table->boolean('is_featured')->default(false);
    }
    if (!Schema::hasColumn('courses', 'allow_reviews')) {
        $table->boolean('allow_reviews')->default(true);
    }
    if (!Schema::hasColumn('courses', 'discussion_enabled')) {
        $table->boolean('discussion_enabled')->default(true);
    }
});
```

---

## 🎯 Controller Updates Required

### Admin Course Controller

Update `store()` method to handle new fields:

```php
public function store(Request $request)
{
    $validated = $request->validate([
        // Existing validations...
        'learning_outcomes.*' => 'nullable|string|max:255',
        'requirements.*' => 'nullable|string|max:255',
        'meta_title' => 'nullable|string|max:60',
        'meta_description' => 'nullable|string|max:160',
        'meta_keywords' => 'nullable|string',
        'drip_content' => 'boolean',
        'is_featured' => 'boolean',
        'allow_reviews' => 'boolean',
        'discussion_enabled' => 'boolean',
    ]);

    // Filter empty values from arrays
    if ($request->has('learning_outcomes')) {
        $validated['learning_outcomes'] = json_encode(
            array_filter($request->learning_outcomes, fn($item) => !empty($item))
        );
    }

    if ($request->has('requirements')) {
        $validated['requirements'] = json_encode(
            array_filter($request->requirements, fn($item) => !empty($item))
        );
    }

    $course = Course::create($validated);

    return redirect()->route('admin.courses.show', $course)
        ->with('success', 'Course created successfully!');
}
```

---

## ✅ Testing Checklist

### Admin Course Create Form
- [ ] Learning outcomes can be added/removed dynamically
- [ ] Requirements can be added/removed dynamically
- [ ] SEO fields validate character limits
- [ ] Additional settings checkboxes work
- [ ] Form submission includes all new fields
- [ ] Old input is preserved on validation errors
- [ ] Empty array items are filtered out

### Admin Sidebar
- [ ] Course Management submenu expands on click
- [ ] Course Management submenu auto-expands on relevant routes
- [ ] All Courses link navigates correctly
- [ ] Topics link shows (with proper permission)
- [ ] Lessons link shows (with proper permission)
- [ ] Quizzes menu item displays with new icon
- [ ] Assignments menu item displays separately
- [ ] Permissions properly restrict access
- [ ] Mobile sidebar mirrors desktop functionality

### Permissions
- [ ] `topic.view` permission works
- [ ] `lesson.view` permission works
- [ ] `assignment.view` permission works
- [ ] `assignment.manage` permission works
- [ ] Users without permissions see limited menu

---

## 📝 Additional Notes

### Field Storage
- `learning_outcomes` and `requirements` are stored as JSON arrays
- Empty values are filtered before storage
- Use `json_decode()` when retrieving

### Example Usage in Views
```blade
@if($course->learning_outcomes)
    @foreach(json_decode($course->learning_outcomes) as $outcome)
        <li>{{ $outcome }}</li>
    @endforeach
@endif
```

### Alpine.js Requirement
Ensure Alpine.js is loaded in admin layout:
```blade
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
```

---

## 🚀 Next Steps

1. **Create Admin Controllers** for Topics, Lessons, Assignments
2. **Create Admin Views** for Topics/Lessons index pages
3. **Update Validation Rules** in form requests
4. **Add Permission Checks** to controllers
5. **Test All Functionality** with different user roles
6. **Update Documentation** for end users

---

**Last Updated:** {{ now()->format('Y-m-d H:i:s') }}
**Status:** ✅ COMPLETED
**Version:** 1.0
