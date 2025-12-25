# Course Management System - Complete Implementation Guide

## Overview
This document provides a comprehensive guide for implementing the complete course management system for the Master IELTS platform.

## ✅ Completed Views

### Admin Views
- ✅ `admin/courses/index.blade.php` - Course listing with filters
- ✅ `admin/courses/create.blade.php` - Create new course
- ✅ `admin/courses/show.blade.php` - Course details and stats
- ✅ `admin/courses/edit.blade.php` - Edit course information

### Tutor Views
- ✅ `tutor/courses/index.blade.php` - My courses listing
- ✅ `tutor/courses/create.blade.php` - Create course
- ✅ `tutor/courses/show.blade.php` - Course management dashboard with expandable topics
- ✅ `tutor/courses/edit.blade.php` - Edit course

## 🔨 Views Still Needed

### 1. Tutor Topics Management

#### Directory: `resources/views/tutor/topics/`

**create.blade.php** - Create Topic Form
```blade
Form Fields:
- course_id (hidden, passed via query string)
- title (required)
- description (textarea)
- order (number, default to next available)
- is_published (checkbox)

Route: tutor.topics.store
Method: POST
Redirect: Back to course management page
```

**edit.blade.php** - Edit Topic Form
```blade
Form Fields: Same as create
Route: tutor.topics.update
Method: PUT
Redirect: Back to course management page
```

---

### 2. Tutor Lessons Management

#### Directory: `resources/views/tutor/lessons/`

**create.blade.php** - Create Lesson Form
```blade
Form Sections:
1. Basic Information:
   - topic_id (hidden)
   - title (required)
   - description (textarea)
   - duration_minutes (number)
   - order (number)
   - is_preview (checkbox)
   - is_published (checkbox)
   - requires_previous_completion (checkbox)

2. Content Type Selection (Radio):
   - video
   - text
   - document
   - audio
   - presentation
   - embed

3. Content Details (Show/hide based on type):

   FOR VIDEO:
   - vimeo_id or url
   - captions (file upload)
   - transcript (textarea)

   FOR TEXT:
   - body (rich text editor)
   - reading_time (calculated or manual)

   FOR DOCUMENT:
   - file (PDF, Word, etc.)
   - pages (number)

   FOR AUDIO:
   - file (MP3, etc.)
   - transcript (textarea)

   FOR PRESENTATION:
   - file (PPT, etc.)
   - slides (number)

   FOR EMBED:
   - provider (YouTube, Vimeo, etc.)
   - embed_url
   - metadata (JSON)

Route: tutor.lessons.store
Method: POST
Redirect: Back to course management page
```

**edit.blade.php** - Edit Lesson Form
```blade
Form Fields: Same as create, with current content loaded
Route: tutor.lessons.update
Method: PUT
Redirect: Back to course management page

Additional: Show current content with ability to replace
```

---

### 3. Tutor Quizzes Management

#### Directory: `resources/views/tutor/quizzes/`

**create.blade.php** - Create Quiz Form
```blade
Form Sections:
1. Basic Information:
   - topic_id (hidden)
   - title (required)
   - description
   - instructions
   - order
   - is_published

2. Settings:
   - time_limit (minutes, nullable)
   - passing_score (percentage)
   - max_attempts (nullable = unlimited)
   - shuffle_questions (checkbox)
   - shuffle_answers (checkbox)
   - show_answers (checkbox)
   - show_correct_answers (checkbox)
   - require_passing (checkbox)
   - certificate_eligible (checkbox)

Route: tutor.quizzes.store
Method: POST
Redirect: To quiz edit page to add questions
```

**edit.blade.php** - Edit Quiz with Questions
```blade
Top Section: Quiz settings (same as create)

Bottom Section: Questions Management
- List all questions with order
- Add Question button
- Edit/Delete for each question
- Drag & drop reordering

Route: tutor.quizzes.update
Method: PUT
```

**questions/create.blade.php** - Add Question Modal/Page
```blade
Form Fields:
1. Question Details:
   - quiz_id (hidden)
   - type (select):
     * mcq_single (Multiple Choice - Single Answer)
     * mcq_multiple (Multiple Choice - Multiple Answers)
     * true_false
     * short_answer
     * passage_mcq (Reading passage with MCQ)
   - question (textarea/rich text)
   - description (additional info)
   - points (number)
   - order (number)
   - difficulty (easy/medium/hard)

2. Media (optional):
   - media_type (image/audio/video)
   - media_url (file upload or URL)

3. Options (for MCQ):
   - Option text
   - Is correct (checkbox/radio)
   - Explanation (why correct/incorrect)
   - Add Option button (for multiple options)

4. Explanation:
   - General explanation shown after answer

Route: tutor.questions.store
Method: POST
Redirect: Back to quiz edit page
```

**questions/edit.blade.php** - Edit Question
```blade
Form Fields: Same as create
Route: tutor.questions.update
Method: PUT
```

---

### 4. Tutor Assignments Management

#### Directory: `resources/views/tutor/assignments/`

**create.blade.php** - Create Assignment
```blade
Form Sections:
1. Basic Information:
   - topic_id (hidden)
   - title (required)
   - description (rich text)
   - instructions (rich text)
   - order
   - is_published

2. Grading:
   - max_points (number)
   - passing_points (number)
   - auto_grade (checkbox - for specific types)
   - require_passing (checkbox)

3. Submission Settings:
   - due_date (datetime picker)
   - allow_late_submission (checkbox)
   - late_penalty (percentage per day)

4. File Upload Settings:
   - max_file_size (MB)
   - allowed_file_types (checkboxes: PDF, DOC, ZIP, etc.)
   - max_files (number)

Route: tutor.assignments.store
Method: POST
Redirect: To assignment edit page to add rubrics
```

**edit.blade.php** - Edit Assignment
```blade
Top Section: Assignment settings (same as create)

Bottom Section: Rubrics (Optional)
- Add criteria for grading
- Each criterion has:
  * Description
  * Points possible
  * Levels (Excellent, Good, Fair, Poor with points)

Route: tutor.assignments.update
Method: PUT
```

**submissions/index.blade.php** - View & Grade Submissions
```blade
Display:
- List of all student submissions
- Filter: All / Pending / Graded / Late
- For each submission:
  * Student name & photo
  * Submission date
  * Status (pending/graded)
  * Files submitted
  * Current score (if graded)
  * Grade button

Grade Modal/Page:
- View student files
- Rubric scoring (if exists)
- Score input (number)
- Feedback (rich text)
- Status (pass/fail auto-calculated)

Route: tutor.assignment-submissions.grade
Method: POST
```

---

### 5. Student Course Views

#### Directory: `resources/views/student/courses/`

**index.blade.php** - Browse All Courses
```blade
Display:
- Hero section with search
- Filters sidebar:
  * Categories
  * Level
  * Price (Free/Paid)
  * Rating
  * Duration
- Course grid with cards showing:
  * Thumbnail
  * Title
  * Instructor name
  * Rating & reviews
  * Price
  * Enrollment count
  * Duration
- Pagination
- Sort options (Popular, Newest, Highest Rated, Price)

Route: student.courses.index
```

**show.blade.php** - Course Preview/Details
```blade
Sections:
1. Hero Section:
   - Course thumbnail/video preview
   - Title & subtitle
   - Rating & reviews
   - Instructor info
   - Enroll button (if not enrolled)
   - Continue Learning button (if enrolled)

2. What You'll Learn:
   - List of learning outcomes

3. Course Content:
   - Expandable topics list
   - Show lesson titles (with lock icon if not enrolled)
   - Total lectures, duration, quizzes, assignments

4. Requirements:
   - Prerequisites list

5. Description:
   - Full course description

6. Instructor:
   - Name, photo, bio
   - Other courses by instructor

7. Student Reviews:
   - Review list with ratings
   - Review form (if enrolled & completed)

8. Sticky Sidebar:
   - Price card
   - Enroll button
   - Course includes (lectures, quizzes, certificate, etc.)
   - Share buttons

Route: student.courses.show
Method: GET
```

---

## 📊 Routes Reference

### Tutor Routes (to be added to routes/web.php)

```php
Route::middleware(['auth', 'role:tutor'])->prefix('tutor')->name('tutor.')->group(function () {

    // Topics
    Route::resource('topics', TopicController::class)->except(['index', 'show']);

    // Lessons
    Route::resource('lessons', LessonController::class)->except(['index', 'show']);

    // Quizzes
    Route::resource('quizzes', QuizController::class);
    Route::resource('quizzes.questions', QuestionController::class)->shallow();

    // Assignments
    Route::resource('assignments', AssignmentController::class);
    Route::get('assignments/{assignment}/submissions', [AssignmentSubmissionController::class, 'index'])->name('assignment-submissions.index');
    Route::post('assignment-submissions/{submission}/grade', [AssignmentSubmissionController::class, 'grade'])->name('assignment-submissions.grade');

});
```

### Student Routes

```php
Route::middleware(['auth', 'role:student'])->prefix('student')->name('student.')->group(function () {

    Route::get('courses', [StudentCourseController::class, 'index'])->name('courses.index');
    Route::get('courses/{course}', [StudentCourseController::class, 'show'])->name('courses.show');
    Route::post('courses/{course}/enroll', [StudentCourseController::class, 'enroll'])->name('courses.enroll');

});
```

---

## 🎨 UI Components & Patterns

### Common Components to Use

1. **Form Input Groups** - Consistent styling for all forms
2. **File Upload with Preview** - Show image/file previews
3. **Rich Text Editor** - For descriptions, content, etc. (TinyMCE or similar)
4. **Drag & Drop Ordering** - For reordering topics, lessons, questions
5. **Modal Dialogs** - For quick actions (delete confirmations, etc.)
6. **Toast Notifications** - Success/error messages
7. **Loading States** - Spinners for async actions

### Alpine.js Patterns Used

```javascript
// Expandable sections
x-data="{ open: false }"
@click="open = !open"
x-show="open"
x-collapse

// Form sections based on selection
x-data="{ contentType: 'video' }"
x-show="contentType === 'video'"
```

---

## 🔐 Authorization

### Gates & Policies to Check

All views should check appropriate permissions:

```blade
@can('course.create')
@can('course.update', $course)
@can('course.delete', $course)
```

For tutors:
- Can only manage their own courses
- Can only see their own students
- Cannot delete published courses without admin approval

---

## 📝 Form Validation

### Key Validation Rules

**Courses:**
- title: required|string|max:255
- slug: unique:courses,slug|nullable
- instructor_id: required|exists:users,id
- price: nullable|numeric|min:0
- status: required|in:draft,review,published,archived

**Topics:**
- course_id: required|exists:courses,id
- title: required|string|max:255
- order: required|integer|min:0

**Lessons:**
- topic_id: required|exists:topics,id
- title: required|string|max:255
- content_type: required|in:video,text,document,audio,presentation,embed
- duration_minutes: nullable|integer|min:0

**Quizzes:**
- topic_id: required|exists:topics,id
- title: required|string|max:255
- time_limit: nullable|integer|min:1
- passing_score: required|numeric|min:0|max:100
- max_attempts: nullable|integer|min:1

**Questions:**
- quiz_id: required|exists:quizzes,id
- type: required|in:mcq_single,mcq_multiple,true_false,short_answer,passage_mcq
- question: required|string
- points: required|numeric|min:0

**Assignments:**
- topic_id: required|exists:topics,id
- title: required|string|max:255
- max_points: required|numeric|min:0
- due_date: nullable|date|after:now

---

## 🚀 Implementation Order

### Phase 1: Topics (CRITICAL - DO THIS FIRST)
1. Create `tutor/topics/create.blade.php`
2. Create `tutor/topics/edit.blade.php`
3. Test topic creation and editing

### Phase 2: Lessons (HIGH PRIORITY)
1. Create `tutor/lessons/create.blade.php` with content type selection
2. Create `tutor/lessons/edit.blade.php`
3. Test all content types

### Phase 3: Quizzes (HIGH PRIORITY)
1. Create `tutor/quizzes/create.blade.php`
2. Create `tutor/quizzes/edit.blade.php`
3. Create `tutor/quizzes/questions/create.blade.php`
4. Create `tutor/quizzes/questions/edit.blade.php`
5. Test quiz creation with various question types

### Phase 4: Assignments (MEDIUM PRIORITY)
1. Create `tutor/assignments/create.blade.php`
2. Create `tutor/assignments/edit.blade.php`
3. Create `tutor/assignments/submissions/index.blade.php`
4. Test assignment creation and grading workflow

### Phase 5: Student Views (MEDIUM PRIORITY)
1. Create `student/courses/index.blade.php`
2. Create `student/courses/show.blade.php`
3. Test course browsing and enrollment

---

## 📦 Additional Features to Consider

### Nice-to-Have Enhancements

1. **Course Builder Interface** - Drag & drop visual course builder
2. **Bulk Actions** - Bulk publish/unpublish lessons
3. **Course Templates** - Save course structure as template
4. **Content Import** - Import from other platforms
5. **Analytics Dashboard** - Detailed course performance metrics
6. **Student Progress Tracking** - Visual progress indicators
7. **Certificate Designer** - Custom certificate templates
8. **Course Preview Mode** - Preview as student before publishing
9. **Version Control** - Track changes to course content
10. **Collaboration** - Multiple instructors per course

---

## 🐛 Common Issues & Solutions

### Issue: Topics not showing in course management
**Solution:** Check that topics are loaded with relationships in controller:
```php
$course->load(['topics.lessons', 'topics.quizzes', 'topics.assignments']);
```

### Issue: File uploads not working
**Solution:** Ensure form has `enctype="multipart/form-data"` and storage is linked:
```bash
php artisan storage:link
```

### Issue: Permission errors
**Solution:** Check middleware and policies are properly set up

### Issue: Alpine.js not working
**Solution:** Ensure Alpine.js is loaded in layout file

---

## 📚 Database Queries for Common Operations

### Get course with all content
```php
$course = Course::with([
    'topics' => function($query) {
        $query->orderBy('order');
    },
    'topics.lessons' => function($query) {
        $query->orderBy('order');
    },
    'topics.quizzes' => function($query) {
        $query->orderBy('order');
    },
    'topics.assignments' => function($query) {
        $query->orderBy('order');
    }
])->findOrFail($id);
```

### Get student's course progress
```php
$progress = CourseProgress::with([
    'enrollment',
    'completedLessons',
    'quizAttempts',
    'assignmentSubmissions'
])->where('user_id', $userId)
  ->where('course_id', $courseId)
  ->first();
```

---

## ✅ Testing Checklist

### For each view created:
- [ ] Form validation works correctly
- [ ] Success/error messages display properly
- [ ] File uploads work (if applicable)
- [ ] Redirects to correct page after action
- [ ] Authorization checks work
- [ ] Mobile responsive
- [ ] Alpine.js interactions work
- [ ] No console errors
- [ ] Database records created correctly
- [ ] Relationships maintained

---

## 📞 Support & Resources

- Laravel Documentation: https://laravel.com/docs
- Tailwind CSS: https://tailwindcss.com/docs
- Alpine.js: https://alpinejs.dev/
- Spatie Permission: https://spatie.be/docs/laravel-permission

---

**Last Updated:** {{ now()->format('Y-m-d H:i:s') }}
**Version:** 1.0
**Author:** Claude Code Assistant
