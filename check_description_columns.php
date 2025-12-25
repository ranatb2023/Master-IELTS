<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

echo "=== Checking Description Column Types ===\n\n";

$tables = [
    'courses' => ['description'],
    'topics' => ['description'],
    'lessons' => ['description'],
    'text_contents' => ['body'],
];

foreach ($tables as $table => $columns) {
    echo "Table: {$table}\n";

    if (!Schema::hasTable($table)) {
        echo "  Table does not exist!\n\n";
        continue;
    }

    foreach ($columns as $column) {
        if (Schema::hasColumn($table, $column)) {
            $type = DB::select("SELECT DATA_TYPE, COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = '{$table}' AND COLUMN_NAME = '{$column}'");
            if (!empty($type)) {
                echo "  ✓ {$column}: {$type[0]->COLUMN_TYPE}\n";
            }
        } else {
            echo "  ✗ {$column}: COLUMN DOES NOT EXIST\n";
        }
    }
    echo "\n";
}

echo "=== Recommendations ===\n";
echo "For Quill HTML content, columns should be TEXT or LONGTEXT.\n";
echo "SQLite uses TEXT type which can store unlimited length.\n";
