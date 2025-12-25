<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Course;
use App\Models\Topic;
use App\Models\Lesson;
use App\Models\TextContent;
use Illuminate\Support\Facades\DB;

echo "=== Testing Soft Delete Cascade ===\n\n";

DB::beginTransaction();
try {
    // 1. Create a complete course structure
    echo "1. Creating test course structure...\n";

    $course = Course::create([
        'title' => 'Test Course for Soft Delete',
        'slug' => 'test-course-soft-delete',
        'description' => 'Testing soft delete cascade',
        'instructor_id' => 1,
        'level' => 'beginner',
        'language' => 'English',
        'status' => 'published',
    ]);
    echo "   Course created (ID: {$course->id})\n";

    $topic = Topic::create([
        'course_id' => $course->id,
        'title' => 'Test Topic',
        'description' => 'Test topic description',
        'order' => 1,
        'is_published' => true,
    ]);
    echo "   Topic created (ID: {$topic->id})\n";

    $content = TextContent::create([
        'body' => 'Test lesson content',
        'reading_time' => 1,
    ]);

    $lesson = Lesson::create([
        'topic_id' => $topic->id,
        'title' => 'Test Lesson',
        'description' => 'Test lesson description',
        'content_type' => 'text',
        'contentable_type' => get_class($content),
        'contentable_id' => $content->id,
        'duration_minutes' => 10,
        'order' => 1,
        'is_published' => true,
    ]);
    echo "   Lesson created (ID: {$lesson->id})\n";
    echo "   Content created (ID: {$content->id})\n\n";

    // 2. Verify all records exist
    echo "2. Before soft delete:\n";
    echo "   Courses: " . Course::where('id', $course->id)->count() . "\n";
    echo "   Topics: " . Topic::where('id', $topic->id)->count() . "\n";
    echo "   Lessons: " . Lesson::where('id', $lesson->id)->count() . "\n";
    echo "   Content: " . TextContent::where('id', $content->id)->count() . "\n\n";

    // 3. SOFT DELETE the course
    echo "3. Soft deleting course...\n";
    $course->delete();
    echo "   Course soft deleted!\n\n";

    // 4. Check what happened
    echo "4. After soft delete:\n";
    echo "   Courses (active): " . Course::where('id', $course->id)->count() . "\n";
    echo "   Courses (with trashed): " . Course::withTrashed()->where('id', $course->id)->count() . "\n";
    echo "   Topics (active): " . Topic::where('id', $topic->id)->count() . "\n";
    echo "   Topics (with trashed): " . Topic::withTrashed()->where('id', $topic->id)->count() . "\n";
    echo "   Lessons (active): " . Lesson::where('id', $lesson->id)->count() . "\n";
    echo "   Lessons (with trashed): " . Lesson::withTrashed()->where('id', $lesson->id)->count() . "\n";
    echo "   Content: " . TextContent::where('id', $content->id)->count() . " (should still exist)\n\n";

    // 5. RESTORE the course
    echo "5. Restoring course...\n";
    $course->restore();
    echo "   Course restored!\n\n";

    // 6. Check if everything was restored
    echo "6. After restore:\n";
    echo "   Courses: " . Course::where('id', $course->id)->count() . "\n";
    echo "   Topics: " . Topic::where('id', $topic->id)->count() . "\n";
    echo "   Lessons: " . Lesson::where('id', $lesson->id)->count() . "\n";
    echo "   Content: " . TextContent::where('id', $content->id)->count() . "\n\n";

    // 7. FORCE DELETE (permanent delete)
    echo "7. Force deleting course (permanent)...\n";
    $course->forceDelete();
    echo "   Course force deleted!\n\n";

    // 8. Check if everything was permanently deleted
    echo "8. After force delete:\n";
    echo "   Courses (with trashed): " . Course::withTrashed()->where('id', $course->id)->count() . "\n";
    echo "   Topics (with trashed): " . Topic::withTrashed()->where('id', $topic->id)->count() . "\n";
    echo "   Lessons (with trashed): " . Lesson::withTrashed()->where('id', $lesson->id)->count() . "\n";
    echo "   Content: " . TextContent::where('id', $content->id)->count() . " (should be 0)\n\n";

    // Verify results
    $allDeleted = (
        Course::withTrashed()->where('id', $course->id)->count() === 0 &&
        Topic::withTrashed()->where('id', $topic->id)->count() === 0 &&
        Lesson::withTrashed()->where('id', $lesson->id)->count() === 0 &&
        TextContent::where('id', $content->id)->count() === 0
    );

    if ($allDeleted) {
        echo "✓ SUCCESS! All records were properly deleted.\n";
    } else {
        echo "✗ FAILED! Some records still exist.\n";
    }

    DB::rollBack(); // Don't save test data

} catch (\Exception $e) {
    DB::rollBack();
    echo "\n✗ ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "\nStack trace:\n" . $e->getTraceAsString() . "\n";
}
