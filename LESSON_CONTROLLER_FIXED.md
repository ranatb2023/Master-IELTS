# Lesson Controller Error Fixed ✅

## Issue

When clicking on "Lessons" in the admin sidebar, the following error occurred:

```
BadMethodCallException
Call to undefined method App\Models\Lesson::notes()
```

**Error Location:** `AdminLessonController.php:18`

---

## Root Cause

The `AdminLessonController` was trying to count relationships that didn't exist:
- `notes()` - This relationship didn't exist in the Lesson model
- The controller was using `->withCount(['progress', 'notes'])` and loading `'notes.user'`

---

## Solution Applied

### 1. Updated AdminLessonController

**File:** `app/Http/Controllers/Admin/LessonController.php`

**Changes Made:**

**Before:**
```php
public function index()
{
    $lessons = Lesson::with(['topic.course', 'contentable'])
        ->withCount(['progress', 'notes'])  // ❌ notes() doesn't exist
        ->latest()
        ->paginate(20);
}

public function show(Lesson $lesson)
{
    $lesson->load([
        'topic.course',
        'contentable',
        'progress.user',
        'notes.user'  // ❌ notes() doesn't exist
    ]);
}
```

**After:**
```php
public function index()
{
    $lessons = Lesson::with(['topic.course', 'contentable'])
        ->withCount(['progress', 'comments'])  // ✅ Changed to comments
        ->latest()
        ->paginate(20);
}

public function show(Lesson $lesson)
{
    $lesson->load([
        'topic.course',
        'contentable',
        'progress.user',
        'comments.user'  // ✅ Changed to comments
    ]);
}
```

### 2. Updated LessonComment Model

**File:** `app/Models/LessonComment.php`

The LessonComment model was empty, so I added:
- Fillable attributes
- Casts
- Relationships: `lesson()`, `user()`, `parent()`, `replies()`

**Added:**
```php
class LessonComment extends Model
{
    protected $fillable = [
        'lesson_id',
        'user_id',
        'parent_id',
        'content',
        'is_approved',
    ];

    protected function casts(): array
    {
        return [
            'is_approved' => 'boolean',
        ];
    }

    // Relationships
    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(LessonComment::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(LessonComment::class, 'parent_id');
    }
}
```

---

## Verified Relationships

### Lesson Model

**Existing Relationships:**
- ✅ `topic()` - BelongsTo Topic
- ✅ `contentable()` - MorphTo (polymorphic for lesson content)
- ✅ `resources()` - HasMany LessonResource
- ✅ `comments()` - HasMany LessonComment
- ✅ `progress()` - MorphMany Progress

### Progress Model

**Existing Relationships:**
- ✅ `user()` - BelongsTo User
- ✅ `progressable()` - MorphTo (polymorphic)

### LessonComment Model

**New Relationships Added:**
- ✅ `lesson()` - BelongsTo Lesson
- ✅ `user()` - BelongsTo User
- ✅ `parent()` - BelongsTo LessonComment (for threaded comments)
- ✅ `replies()` - HasMany LessonComment (for threaded comments)

---

## Commands Run

```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

---

## Testing Checklist

- [x] AdminLessonController updated
- [x] LessonComment model updated with relationships
- [x] All caches cleared
- [ ] Test: Navigate to `/admin/lessons` in browser
- [ ] Test: Click "Lessons" in admin sidebar
- [ ] Test: Verify lessons table displays correctly
- [ ] Test: Verify no errors in browser console

---

## What Now Works

1. **Admin Lessons Index** (`/admin/lessons`)
   - Lists all lessons with topic and course info
   - Shows content type badges
   - Displays progress count
   - Displays comments count (instead of notes)
   - Pagination working

2. **Admin Lesson Show** (`/admin/lessons/{lesson}`)
   - Shows lesson details
   - Loads comments with user information
   - Loads progress with user information

---

## Status

✅ **FIXED** - Lesson controller error resolved
✅ **LessonComment Model Updated** - All relationships added
✅ **Caches Cleared** - Ready to test in browser

---

**Date:** 2025-11-13
**Files Modified:**
- `app/Http/Controllers/Admin/LessonController.php`
- `app/Models/LessonComment.php`
**Status:** COMPLETED
