<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Get all packages with features
        $packages = DB::table('packages')->get();

        $migratedCount = 0;
        $totalFeatures = 0;

        foreach ($packages as $package) {
            $displayFeatures = json_decode($package->display_features, true) ?? [];
            $functionalFeatures = json_decode($package->functional_features, true) ?? [];

            $packageFeatureCount = 0;

            // Insert display features
            foreach ($displayFeatures as $featureKey) {
                // Check if feature exists
                $featureExists = DB::table('package_features')
                    ->where('feature_key', $featureKey)
                    ->exists();

                if ($featureExists) {
                    // Check if already in pivot (avoid duplicates)
                    $alreadyExists = DB::table('package_package_features')
                        ->where('package_id', $package->id)
                        ->where('feature_key', $featureKey)
                        ->exists();

                    if (!$alreadyExists) {
                        DB::table('package_package_features')->insert([
                            'package_id' => $package->id,
                            'feature_key' => $featureKey,
                            'is_enabled' => true,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                        $packageFeatureCount++;
                    }
                }
            }

            // Insert functional features
            foreach ($functionalFeatures as $featureKey) {
                // Check if feature exists
                $featureExists = DB::table('package_features')
                    ->where('feature_key', $featureKey)
                    ->exists();

                // Check if already inserted (from display features)
                $alreadyInserted = DB::table('package_package_features')
                    ->where('package_id', $package->id)
                    ->where('feature_key', $featureKey)
                    ->exists();

                if ($featureExists && !$alreadyInserted) {
                    DB::table('package_package_features')->insert([
                        'package_id' => $package->id,
                        'feature_key' => $featureKey,
                        'is_enabled' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $packageFeatureCount++;
                }
            }

            if ($packageFeatureCount > 0) {
                $migratedCount++;
                $totalFeatures += $packageFeatureCount;
            }
        }

        \Log::info('Migrated package features to pivot table', [
            'packages_processed' => $packages->count(),
            'packages_with_features' => $migratedCount,
            'total_features_migrated' => $totalFeatures
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove all pivot table data
        DB::table('package_package_features')->truncate();

        \Log::info('Rolled back package feature migration - pivot table truncated');
    }
};
