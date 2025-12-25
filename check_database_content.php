<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Checking Database Content ===\n\n";

// Check features
$features = DB::table('package_features')->get();
echo "📋 Available Features: " . $features->count() . "\n";
foreach ($features as $feature) {
    echo "  - {$feature->feature_name} ({$feature->feature_key}) - {$feature->type}\n";
}

// Check courses
$courses = DB::table('courses')->where('status', 'published')->get();
echo "\n📚 Available Published Courses: " . $courses->count() . "\n";
foreach ($courses->take(10) as $course) {
    echo "  - {$course->title} (ID: {$course->id})\n";
}

if ($courses->count() > 10) {
    echo "  ... and " . ($courses->count() - 10) . " more\n";
}
