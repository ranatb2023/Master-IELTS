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
            // Drop the existing foreign key with wrong table reference
            $table->dropForeign(['package_access_id']);

            // Re-add with correct table name (user_package_accesses plural)
            $table->foreign('package_access_id')
                ->references('id')
                ->on('user_package_accesses')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            // Drop the corrected foreign key
            $table->dropForeign(['package_access_id']);

            // Restore the old (incorrect) foreign key
            $table->foreign('package_access_id')
                ->references('id')
                ->on('user_package_access')
                ->nullOnDelete();
        });
    }
};
