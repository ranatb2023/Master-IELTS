<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            // Feature Management
            $table->json('display_features')->nullable()->comment('Features shown but not available')->after('features');
            $table->json('functional_features')->nullable()->comment('Actually available features')->after('display_features');
            $table->boolean('auto_enroll_courses')->default(true)->after('functional_features');

            // Lifetime vs Time-limited
            $table->boolean('is_lifetime')->default(false)->after('duration_days');
            $table->datetime('access_expires_at')->nullable()->after('is_lifetime');

            // Subscription linking
            $table->boolean('is_subscription_package')->default(false)->after('is_featured');
            $table->json('subscription_plan_ids')->nullable()->after('is_subscription_package');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->dropColumn([
                'display_features',
                'functional_features',
                'auto_enroll_courses',
                'is_lifetime',
                'access_expires_at',
                'is_subscription_package',
                'subscription_plan_ids'
            ]);
        });
    }
};
