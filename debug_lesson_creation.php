<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Lesson;
use App\Models\Topic;
use App\Models\TextContent;
use Illuminate\Support\Facades\DB;

echo "=== Debugging Lesson Content Creation ===\n\n";

// Check current state
echo "1. Current Database State:\n";
echo "   - Lessons: " . Lesson::count() . "\n";
echo "   - Text Contents: " . TextContent::count() . "\n";
echo "   - Video Contents: " . \App\Models\VideoContent::count() . "\n";
echo "   - Document Contents: " . \App\Models\DocumentContent::count() . "\n\n";

// Get first topic
$topic = Topic::first();
if (!$topic) {
    echo "ERROR: No topics found!\n";
    exit(1);
}

echo "2. Using Topic: {$topic->title} (ID: {$topic->id})\n\n";

// Simulate the controller's createContent method
echo "3. Testing createContent logic:\n";

DB::beginTransaction();
try {
    // Simulate text content creation
    echo "   - Creating TextContent...\n";
    $textContent = TextContent::create([
        'body' => 'This is test content for debugging lesson creation.',
        'reading_time' => 1,
    ]);
    echo "   - TextContent created! ID: {$textContent->id}\n";
    echo "   - Class: " . get_class($textContent) . "\n\n";

    // Now create lesson with the relationship
    echo "4. Creating Lesson with content relationship:\n";
    $lessonData = [
        'topic_id' => $topic->id,
        'title' => 'Debug Test Lesson',
        'description' => 'Testing lesson creation',
        'content_type' => 'text',
        'duration_minutes' => 10,
        'order' => 999,
        'is_preview' => false,
        'is_published' => true,
        'requires_previous_completion' => false,
    ];

    // Add the polymorphic relationship
    if ($textContent) {
        $lessonData['contentable_type'] = get_class($textContent);
        $lessonData['contentable_id'] = $textContent->id;
        echo "   - contentable_type: {$lessonData['contentable_type']}\n";
        echo "   - contentable_id: {$lessonData['contentable_id']}\n";
    }

    $lesson = Lesson::create($lessonData);
    echo "   - Lesson created! ID: {$lesson->id}\n\n";

    // Verify immediately
    echo "5. Verifying saved data:\n";
    $lesson->refresh();
    echo "   - Lesson ID: {$lesson->id}\n";
    echo "   - content_type: {$lesson->content_type}\n";
    echo "   - contentable_type: " . ($lesson->contentable_type ?? 'NULL') . "\n";
    echo "   - contentable_id: " . ($lesson->contentable_id ?? 'NULL') . "\n";

    // Try to load the relationship
    $lesson->load('contentable');
    if ($lesson->contentable) {
        echo "   - Contentable loaded: YES\n";
        echo "   - Content body: " . substr($lesson->contentable->body, 0, 50) . "...\n";
    } else {
        echo "   - Contentable loaded: NO (NULL)\n";
    }

    echo "\n6. Raw database check:\n";
    $rawLesson = DB::table('lessons')->where('id', $lesson->id)->first();
    echo "   - content_type: " . ($rawLesson->content_type ?? 'NULL') . "\n";
    echo "   - contentable_type: " . ($rawLesson->contentable_type ?? 'NULL') . "\n";
    echo "   - contentable_id: " . ($rawLesson->contentable_id ?? 'NULL') . "\n";

    DB::commit();

    echo "\n✓ SUCCESS! Test completed.\n";
    echo "\nCleaning up...\n";
    $lesson->delete();
    $textContent->delete();
    echo "Test data removed.\n";

} catch (\Exception $e) {
    DB::rollBack();
    echo "\n✗ ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    echo "\nStack trace:\n" . $e->getTraceAsString() . "\n";
}
