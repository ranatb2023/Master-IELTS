<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Lesson;
use App\Models\Topic;
use App\Models\TextContent;
use Illuminate\Support\Facades\DB;

echo "Testing Lesson Content Creation\n";
echo "================================\n\n";

// Get first topic
$topic = Topic::first();
if (!$topic) {
    echo "Error: No topics found. Please create a topic first.\n";
    exit(1);
}

echo "Using topic: {$topic->title}\n";

DB::beginTransaction();
try {
    // Create text content
    echo "\n1. Creating TextContent...\n";
    $content = TextContent::create([
        'body' => 'This is a test lesson content with some text.',
        'reading_time' => 1,
    ]);
    echo "   Content created with ID: {$content->id}\n";
    echo "   Content class: " . get_class($content) . "\n";

    // Create lesson with content relationship
    echo "\n2. Creating Lesson with content relationship...\n";
    $lesson = Lesson::create([
        'topic_id' => $topic->id,
        'title' => 'Test Lesson with Content',
        'description' => 'Testing content creation',
        'content_type' => 'text',
        'contentable_type' => get_class($content),
        'contentable_id' => $content->id,
        'duration_minutes' => 10,
        'order' => 999,
        'is_preview' => false,
        'is_published' => true,
        'requires_previous_completion' => false,
    ]);
    echo "   Lesson created with ID: {$lesson->id}\n";

    // Verify the relationship
    echo "\n3. Verifying relationship...\n";
    $lesson->refresh();
    $lesson->load('contentable');

    echo "   Lesson content_type: {$lesson->content_type}\n";
    echo "   Lesson contentable_type: {$lesson->contentable_type}\n";
    echo "   Lesson contentable_id: {$lesson->contentable_id}\n";
    echo "   Has contentable: " . ($lesson->contentable ? 'YES' : 'NO') . "\n";

    if ($lesson->contentable) {
        echo "   Content body preview: " . substr($lesson->contentable->body, 0, 50) . "...\n";
    }

    DB::commit();
    echo "\n✓ Success! Content creation and relationship works correctly.\n";
    echo "\nCleaning up test data...\n";

    // Delete test lesson and content
    $lesson->delete();
    $content->delete();
    echo "Test data cleaned up.\n";

} catch (\Exception $e) {
    DB::rollBack();
    echo "\n✗ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
