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
        Schema::table('courses', function (Blueprint $table) {
            // Add auto-enrollment flag after package_only field
            $table->boolean('auto_enroll_enabled')->default(false)->after('package_only');

            // Add index for better performance when querying auto-enroll courses
            $table->index('auto_enroll_enabled');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            // Drop the index first
            $table->dropIndex(['auto_enroll_enabled']);

            // Then drop the column
            $table->dropColumn('auto_enroll_enabled');
        });
    }
};
