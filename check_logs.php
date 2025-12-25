<?php

$logFile = __DIR__ . '/storage/logs/laravel.log';

if (!file_exists($logFile)) {
    echo "Log file not found!\n";
    exit(1);
}

echo "=== Latest Laravel Log Entries ===\n\n";

// Get last 100 lines
$lines = file($logFile);
$totalLines = count($lines);
$start = max(0, $totalLines - 100);

echo "Showing last " . min(100, $totalLines) . " lines:\n";
echo str_repeat("=", 80) . "\n\n";

for ($i = $start; $i < $totalLines; $i++) {
    echo $lines[$i];
}
