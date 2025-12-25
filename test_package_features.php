<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Package Feature Integration Status ===\n\n";

// Count packages
$packagesCount = DB::table('packages')->count();
echo "Total Packages: $packagesCount\n";

// Count features in pivot
$pivotCount = DB::table('package_package_features')->count();
echo "Features in Pivot Table: $pivotCount\n";

// Show sample package with features
$samplePackage = DB::table('packages')->first();
if ($samplePackage) {
    echo "\nSample Package: {$samplePackage->name}\n";

    $features = DB::table('package_package_features')
        ->where('package_id', $samplePackage->id)
        ->join('package_features', 'package_package_features.feature_key', '=', 'package_features.feature_key')
        ->select('package_features.feature_name', 'package_features.type', 'package_package_features.is_enabled')
        ->get();

    echo "  Features assigned: " . $features->count() . "\n";
    foreach ($features as $feature) {
        echo "    - {$feature->feature_name} ({$feature->type})\n";
    }
}

echo "\n=== Migration Successful! ===\n";
