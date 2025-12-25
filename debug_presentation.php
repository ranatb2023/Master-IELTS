<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Lesson;
use Illuminate\Support\Facades\Storage;

echo "=== Debugging Presentation Issue ===\n\n";

$presentation = Lesson::where('content_type', 'presentation')->first();

if (!$presentation) {
    echo "No presentation found!\n";
    exit;
}

echo "Lesson ID: {$presentation->id}\n";
echo "Title: {$presentation->title}\n";
echo "Content Type: {$presentation->content_type}\n\n";

$content = $presentation->contentable;

echo "=== Contentable Data ===\n";
echo "Type: " . get_class($content) . "\n";
echo "File Path: {$content->file_path}\n";

// Get file extension
$extension = pathinfo($content->file_path, PATHINFO_EXTENSION);
echo "File Extension: {$extension}\n";
echo "Extension lowercase: " . strtolower($extension) . "\n\n";

// Check if file exists
echo "=== File Existence Check ===\n";
echo "Checking in 'public' disk...\n";
if (Storage::disk('public')->exists($content->file_path)) {
    echo "✓ File EXISTS in public disk\n";
    echo "Full path: " . Storage::disk('public')->path($content->file_path) . "\n";
    echo "File size: " . Storage::disk('public')->size($content->file_path) . " bytes\n";
    echo "MIME type: " . Storage::disk('public')->mimeType($content->file_path) . "\n";
} else {
    echo "✗ File NOT FOUND in public disk\n";
}

// Check what conditions the blade template would evaluate
echo "\n=== Blade Template Conditions ===\n";
$presentationExtension = strtolower($extension);
echo "Variable \$presentationExtension = '{$presentationExtension}'\n\n";

echo "Checking: in_array('{$presentationExtension}', ['pdf'])\n";
if (in_array($presentationExtension, ['pdf'])) {
    echo "✓ Would show PDF viewer\n";
} else {
    echo "✗ Would NOT show PDF viewer\n";
}

echo "\nChecking: in_array('{$presentationExtension}', ['ppt', 'pptx'])\n";
if (in_array($presentationExtension, ['ppt', 'pptx'])) {
    echo "✓ Would show PowerPoint viewer (Google Docs)\n";
} else {
    echo "✗ Would NOT show PowerPoint viewer\n";
}

echo "\n=== Route Generation ===\n";
echo "Stream URL: " . route('lessons.presentation.stream', $presentation) . "\n";
echo "Google Docs Viewer URL: https://docs.google.com/viewer?url=" . urlencode(route('lessons.presentation.stream', $presentation)) . "&embedded=true\n";

// Check content properties
echo "\n=== All Contentable Properties ===\n";
print_r($content->getAttributes());
