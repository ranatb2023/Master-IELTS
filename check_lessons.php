<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Lesson;

echo "=== Checking Lessons ===\n\n";

$lessons = Lesson::with(['contentable', 'topic.course'])->take(5)->get();

echo "Total lessons found: " . Lesson::count() . "\n\n";

foreach ($lessons as $lesson) {
    echo "ID: {$lesson->id}\n";
    echo "Title: {$lesson->title}\n";
    echo "Type: {$lesson->content_type}\n";
    echo "Course: {$lesson->topic->course->title}\n";
    echo "Topic: {$lesson->topic->title}\n";
    echo "Player URL: " . route('lessons.play', $lesson) . "\n";
    echo "---\n\n";
}
