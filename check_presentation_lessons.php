<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Lesson;

echo "=== Checking Content Type Distribution ===\n\n";

$contentTypes = Lesson::select('content_type', \DB::raw('count(*) as count'))
    ->groupBy('content_type')
    ->get();

foreach ($contentTypes as $type) {
    echo "{$type->content_type}: {$type->count}\n";
}

echo "\n=== Presentation Lessons ===\n\n";

$presentations = Lesson::where('content_type', 'presentation')
    ->with(['contentable', 'topic.course'])
    ->get();

if ($presentations->isEmpty()) {
    echo "No presentation lessons found.\n";
} else {
    foreach ($presentations as $lesson) {
        echo "ID: {$lesson->id}\n";
        echo "Title: {$lesson->title}\n";
        echo "Course: {$lesson->topic->course->title}\n";
        echo "File: {$lesson->contentable->file_path}\n";
        echo "Player URL: " . route('lessons.play', $lesson) . "\n";
        echo "---\n\n";
    }
}

echo "\n=== Embed Lessons ===\n\n";

$embeds = Lesson::where('content_type', 'embed')
    ->with(['contentable', 'topic.course'])
    ->get();

if ($embeds->isEmpty()) {
    echo "No embed lessons found.\n";
} else {
    foreach ($embeds as $lesson) {
        echo "ID: {$lesson->id}\n";
        echo "Title: {$lesson->title}\n";
        echo "Course: {$lesson->topic->course->title}\n";

        $embedCode = $lesson->contentable->metadata['embed_code'] ?? null;
        $embedUrl = $lesson->contentable->embed_url ?? null;

        if ($embedCode) {
            echo "Has Embed Code: Yes\n";
        }
        if ($embedUrl) {
            echo "Embed URL: {$embedUrl}\n";
        }

        echo "Player URL: " . route('lessons.play', $lesson) . "\n";
        echo "---\n\n";
    }
}
