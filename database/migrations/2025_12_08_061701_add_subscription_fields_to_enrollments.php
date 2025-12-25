<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            // Add subscription_id column (references Cashier subscriptions table)
            $table->foreignId('subscription_id')->nullable()->after('package_access_id')
                ->constrained('subscriptions')->onDelete('set null');

            // Add package_id column (direct reference, complements package_access_id)
            $table->foreignId('package_id')->nullable()->after('course_id')
                ->constrained('packages')->onDelete('set null');

            // Add indexes for performance
            $table->index('subscription_id');
            $table->index('package_id');
            $table->index(['user_id', 'subscription_id']);
            $table->index(['user_id', 'package_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropForeign(['subscription_id']);
            $table->dropForeign(['package_id']);
            $table->dropColumn(['subscription_id', 'package_id']);
        });
    }
};
