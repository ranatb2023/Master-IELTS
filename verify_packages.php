<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Package;

echo "=== Package Verification ===\n\n";

$packages = Package::with(['courses', 'features'])->get();

foreach ($packages as $package) {
    echo "Package: " . $package->name . "\n";
    echo "  Status: " . $package->status . "\n";
    echo "  Price: $" . $package->price;
    if ($package->sale_price) {
        echo " (Sale: $" . $package->sale_price . ")";
    }
    echo "\n";

    if ($package->is_lifetime) {
        echo "  Duration: Lifetime\n";
    } else {
        echo "  Duration: " . $package->duration_days . " days\n";
    }

    echo "  Featured: " . ($package->is_featured ? "Yes" : "No") . "\n";

    $featureCount = $package->features->count();
    echo "  Features in Pivot: " . $featureCount . "\n";

    foreach ($package->features as $feature) {
        $enabled = $feature->pivot->is_enabled ? "[ON]" : "[OFF]";
        echo "    " . $enabled . " " . $feature->feature_name . " (" . $feature->type . ")\n";
    }

    $courseCount = $package->courses->count();
    echo "  Courses: " . $courseCount . "\n";

    foreach ($package->courses as $course) {
        echo "    - " . $course->title . "\n";
    }

    echo "\n";
}

echo "=== Summary ===\n";
echo "Total Packages: " . $packages->count() . "\n";
echo "Features in Pivot: " . DB::table('package_package_features')->count() . "\n";
echo "Course Assignments: " . DB::table('package_courses')->count() . "\n";
