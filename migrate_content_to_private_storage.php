<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Lesson;
use Illuminate\Support\Facades\Storage;

echo "=== Migrating Content Files to Private Storage ===\n\n";

$contentTypes = ['audio', 'document', 'presentation'];
$moved = 0;
$errors = 0;
$skipped = 0;

foreach ($contentTypes as $type) {
    echo "Processing {$type} content...\n";

    $lessons = Lesson::where('content_type', $type)
        ->with('contentable')
        ->get();

    foreach ($lessons as $lesson) {
        if (!$lesson->contentable || !$lesson->contentable->file_path) {
            continue;
        }

        $filePath = $lesson->contentable->file_path;

        // Check if file exists in public disk
        if (!Storage::disk('public')->exists($filePath)) {
            // Check if already in local disk
            if (Storage::disk('local')->exists($filePath)) {
                echo "  ✓ Already in private storage: {$filePath}\n";
                $skipped++;
                continue;
            }

            echo "  ✗ File not found: {$filePath}\n";
            $errors++;
            continue;
        }

        try {
            // Get file content from public disk
            $fileContent = Storage::disk('public')->get($filePath);

            // Write to local (private) disk
            Storage::disk('local')->put($filePath, $fileContent);

            // Verify the file was copied successfully
            if (Storage::disk('local')->exists($filePath)) {
                // Delete from public disk
                Storage::disk('public')->delete($filePath);

                echo "  ✓ Moved: {$filePath}\n";
                $moved++;
            } else {
                echo "  ✗ Failed to verify: {$filePath}\n";
                $errors++;
            }
        } catch (\Exception $e) {
            echo "  ✗ Error moving {$filePath}: {$e->getMessage()}\n";
            $errors++;
        }
    }
}

echo "\n=== Migration Complete ===\n";
echo "Files moved: {$moved}\n";
echo "Already in private storage: {$skipped}\n";
echo "Errors: {$errors}\n";
echo "\nNote: Video files were already in private storage and don't need migration.\n";
