<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Package;
use App\Models\PackageFeature;
use App\Models\Course;

echo "=== Creating Sample Packages ===\n\n";

// Get all features
$allFeatures = PackageFeature::all();
$displayFeatures = $allFeatures->where('type', 'display')->pluck('feature_key')->toArray();
$functionalFeatures = $allFeatures->where('type', 'functional')->pluck('feature_key')->toArray();

// Get courses
$courses = Course::where('status', 'published')->pluck('id')->toArray();

if (empty($courses)) {
    echo "❌ No published courses found. Please create some courses first.\n";
    exit(1);
}

echo "Found " . count($displayFeatures) . " display features\n";
echo "Found " . count($functionalFeatures) . " functional features\n";
echo "Found " . count($courses) . " courses\n\n";

// Package 1: Basic Package
echo "Creating Package 1: IELTS Starter Package...\n";
$package1 = Package::create([
    'name' => 'IELTS Starter Package',
    'slug' => 'ielts-starter-package',
    'description' => 'Perfect for beginners starting their IELTS preparation journey. Includes essential courses and basic features to get you started.',
    'price' => 99.00,
    'sale_price' => 79.00,
    'display_features' => array_slice($displayFeatures, 0, 2), // First 2 display features
    'functional_features' => array_slice($functionalFeatures, 0, 2), // First 2 functional features
    'auto_enroll_courses' => true,
    'has_quiz_feature' => true,
    'has_tutor_support' => false,
    'duration_days' => 90,
    'is_lifetime' => false,
    'is_featured' => true,
    'is_subscription_package' => false,
    'status' => 'published',
    'category' => 'Beginner',
    'is_active' => true,
]);

// Attach features to pivot
$package1Features = array_merge(
    array_slice($displayFeatures, 0, 2),
    array_slice($functionalFeatures, 0, 2)
);
foreach ($package1Features as $featureKey) {
    $package1->features()->attach($featureKey, ['is_enabled' => true]);
}

// Attach first course
if (count($courses) > 0) {
    $package1->courses()->attach($courses[0], ['sort_order' => 1]);
}

echo "✓ Created with " . count($package1Features) . " features and 1 course\n\n";

// Package 2: Professional Package  
echo "Creating Package 2: IELTS Professional Package...\n";
$package2 = Package::create([
    'name' => 'IELTS Professional Package',
    'slug' => 'ielts-professional-package',
    'description' => 'Comprehensive IELTS preparation for serious students. Includes all features, tutor support, and full access to course library.',
    'price' => 199.00,
    'sale_price' => null,
    'display_features' => $displayFeatures, // All display features
    'functional_features' => $functionalFeatures, // All functional features
    'auto_enroll_courses' => true,
    'has_quiz_feature' => true,
    'has_tutor_support' => true,
    'duration_days' => 180,
    'is_lifetime' => false,
    'is_featured' => true,
    'is_subscription_package' => false,
    'status' => 'published',
    'category' => 'Professional',
    'is_active' => true,
]);

// Attach all features to pivot
$package2Features = array_merge($displayFeatures, $functionalFeatures);
foreach ($package2Features as $featureKey) {
    $package2->features()->attach($featureKey, ['is_enabled' => true]);
}

// Attach all courses
foreach ($courses as $index => $courseId) {
    $package2->courses()->attach($courseId, ['sort_order' => $index + 1]);
}

echo "✓ Created with " . count($package2Features) . " features and " . count($courses) . " courses\n\n";

// Package 3: Lifetime Master Package
echo "Creating Package 3: IELTS Lifetime Master Package...\n";
$package3 = Package::create([
    'name' => 'IELTS Lifetime Master Package',
    'slug' => 'ielts-lifetime-master',
    'description' => 'Ultimate IELTS package with lifetime access! Get all current and future courses, premium features, priority tutor support, and never pay again.',
    'price' => 499.00,
    'sale_price' => 399.00,
    'display_features' => $displayFeatures,
    'functional_features' => $functionalFeatures,
    'auto_enroll_courses' => true,
    'has_quiz_feature' => true,
    'has_tutor_support' => true,
    'duration_days' => null,
    'is_lifetime' => true,
    'is_featured' => true,
    'is_subscription_package' => false,
    'status' => 'published',
    'category' => 'Premium',
    'is_active' => true,
]);

// Attach all features to pivot
$package3Features = array_merge($displayFeatures, $functionalFeatures);
foreach ($package3Features as $featureKey) {
    $package3->features()->attach($featureKey, ['is_enabled' => true]);
}

// Attach all courses
foreach ($courses as $index => $courseId) {
    $package3->courses()->attach($courseId, ['sort_order' => $index + 1]);
}

echo "✓ Created with " . count($package3Features) . " features and " . count($courses) . " courses (LIFETIME)\n\n";

// Summary
echo "=== Summary ===\n";
echo "✓ Package 1: {$package1->name} - \${$package1->effective_price} (90 days)\n";
echo "✓ Package 2: {$package2->name} - \${$package2->effective_price} (180 days)\n";
echo "✓ Package 3: {$package3->name} - \${$package3->effective_price} (Lifetime)\n";
echo "\nAll packages created successfully!\n";
echo "Check them in: Admin → Packages\n";
