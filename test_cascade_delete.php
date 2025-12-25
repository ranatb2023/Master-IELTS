<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Lesson;
use App\Models\Topic;
use App\Models\TextContent;
use Illuminate\Support\Facades\DB;

echo "=== Testing Cascade Delete ===\n\n";

// Get first topic
$topic = Topic::first();
if (!$topic) {
    echo "ERROR: No topics found!\n";
    exit(1);
}

DB::beginTransaction();
try {
    // 1. Create content
    echo "1. Creating TextContent...\n";
    $content = TextContent::create([
        'body' => 'Test content for cascade delete testing.',
        'reading_time' => 1,
    ]);
    echo "   Content created with ID: {$content->id}\n\n";

    // 2. Create lesson with content
    echo "2. Creating Lesson...\n";
    $lesson = Lesson::create([
        'topic_id' => $topic->id,
        'title' => 'Test Cascade Delete Lesson',
        'description' => 'Testing cascade delete',
        'content_type' => 'text',
        'contentable_type' => get_class($content),
        'contentable_id' => $content->id,
        'duration_minutes' => 10,
        'order' => 999,
        'is_preview' => false,
        'is_published' => true,
        'requires_previous_completion' => false,
    ]);
    echo "   Lesson created with ID: {$lesson->id}\n\n";

    // 3. Verify content exists
    echo "3. Before deletion:\n";
    echo "   - Lessons with ID {$lesson->id}: " . Lesson::where('id', $lesson->id)->count() . "\n";
    echo "   - TextContents with ID {$content->id}: " . TextContent::where('id', $content->id)->count() . "\n\n";

    // 4. Delete the lesson
    echo "4. Deleting lesson...\n";
    $lesson->delete();
    echo "   Lesson deleted!\n\n";

    // 5. Verify content is also deleted
    echo "5. After deletion:\n";
    echo "   - Lessons with ID {$lesson->id}: " . Lesson::where('id', $lesson->id)->count() . "\n";
    echo "   - TextContents with ID {$content->id}: " . TextContent::where('id', $content->id)->count() . "\n\n";

    $contentCount = TextContent::where('id', $content->id)->count();

    if ($contentCount === 0) {
        echo "✓ SUCCESS! Content was automatically deleted when lesson was deleted.\n";
    } else {
        echo "✗ FAILED! Content still exists after lesson deletion.\n";
    }

    DB::rollBack(); // Don't actually save changes

} catch (\Exception $e) {
    DB::rollBack();
    echo "\n✗ ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
