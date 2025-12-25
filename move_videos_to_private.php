<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\VideoContent;
use Illuminate\Support\Facades\Storage;

echo "=== Moving Videos from Public to Private Storage ===\n\n";

$videos = VideoContent::where('source', 'upload')
    ->whereNotNull('file_path')
    ->get();

echo "Found {$videos->count()} uploaded videos to process\n\n";

$movedCount = 0;
$skippedCount = 0;
$errorCount = 0;

foreach ($videos as $video) {
    echo "Processing video ID {$video->id}: {$video->file_name}\n";

    try {
        $oldPath = $video->file_path;

        // Check if file exists in public storage
        if (Storage::disk('public')->exists($oldPath)) {
            echo "  - Found in public storage\n";

            // Check if already exists in local storage
            if (!Storage::disk('local')->exists($oldPath)) {
                // Copy file from public to local storage
                $contents = Storage::disk('public')->get($oldPath);
                Storage::disk('local')->put($oldPath, $contents);
                echo "  - Copied to private storage\n";

                // Delete from public storage
                Storage::disk('public')->delete($oldPath);
                echo "  - Deleted from public storage\n";

                $movedCount++;
            } else {
                echo "  - Already exists in private storage, removing from public\n";
                Storage::disk('public')->delete($oldPath);
                $movedCount++;
            }
        } elseif (Storage::disk('local')->exists($oldPath)) {
            echo "  - Already in private storage ✓\n";
            $skippedCount++;
        } else {
            echo "  - File not found in either location ✗\n";
            $errorCount++;
        }
    } catch (\Exception $e) {
        echo "  - Error: " . $e->getMessage() . " ✗\n";
        $errorCount++;
    }

    echo "\n";
}

echo "=== Summary ===\n";
echo "Moved: {$movedCount}\n";
echo "Already in private storage: {$skippedCount}\n";
echo "Errors: {$errorCount}\n";
echo "\n=== Complete ===\n";
