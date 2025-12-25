<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\VideoContent;
use Illuminate\Support\Facades\DB;

echo "=== Testing Video Upload Feature ===\n\n";

// Test 1: Check if new columns exist
echo "Test 1: Checking if video_contents table has new columns...\n";
$columns = DB::select("SHOW COLUMNS FROM video_contents");
$columnNames = array_map(fn($col) => $col->Field, $columns);

$requiredColumns = ['file_path', 'file_name', 'file_type', 'file_size', 'duration_seconds', 'source'];
$missingColumns = array_diff($requiredColumns, $columnNames);

if (empty($missingColumns)) {
    echo "✓ All required columns exist!\n";
    foreach ($requiredColumns as $col) {
        echo "  - $col\n";
    }
} else {
    echo "✗ Missing columns: " . implode(', ', $missingColumns) . "\n";
}

// Test 2: Create a URL-based video content
echo "\n\nTest 2: Creating URL-based video content...\n";
try {
    $urlVideo = VideoContent::create([
        'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        'source' => 'url',
        'transcript' => 'Test URL video transcript'
    ]);
    echo "✓ URL-based video created successfully (ID: {$urlVideo->id})\n";
    echo "  - URL: {$urlVideo->url}\n";
    echo "  - Source: {$urlVideo->source}\n";
} catch (\Exception $e) {
    echo "✗ Failed to create URL-based video: " . $e->getMessage() . "\n";
}

// Test 3: Create an upload-based video content
echo "\n\nTest 3: Creating upload-based video content...\n";
try {
    $uploadVideo = VideoContent::create([
        'file_path' => 'lessons/videos/test-video.mp4',
        'file_name' => 'test-video.mp4',
        'file_type' => 'mp4',
        'file_size' => 1024000,
        'duration_seconds' => 120,
        'source' => 'upload',
        'transcript' => 'Test uploaded video transcript'
    ]);
    echo "✓ Upload-based video created successfully (ID: {$uploadVideo->id})\n";
    echo "  - File Path: {$uploadVideo->file_path}\n";
    echo "  - File Name: {$uploadVideo->file_name}\n";
    echo "  - File Type: {$uploadVideo->file_type}\n";
    echo "  - File Size: " . number_format($uploadVideo->file_size) . " bytes\n";
    echo "  - Duration: {$uploadVideo->duration_seconds} seconds\n";
    echo "  - Source: {$uploadVideo->source}\n";
} catch (\Exception $e) {
    echo "✗ Failed to create upload-based video: " . $e->getMessage() . "\n";
}

// Test 4: Verify both records in database
echo "\n\nTest 4: Verifying records in database...\n";
$allVideos = VideoContent::all();
echo "Total video contents: {$allVideos->count()}\n";
echo "URL-based videos: " . VideoContent::where('source', 'url')->count() . "\n";
echo "Upload-based videos: " . VideoContent::where('source', 'upload')->count() . "\n";

// Clean up test data
echo "\n\nCleaning up test data...\n";
if (isset($urlVideo)) {
    $urlVideo->forceDelete();
    echo "✓ Deleted URL-based test video\n";
}
if (isset($uploadVideo)) {
    $uploadVideo->forceDelete();
    echo "✓ Deleted upload-based test video\n";
}

echo "\n=== Test Complete ===\n";
