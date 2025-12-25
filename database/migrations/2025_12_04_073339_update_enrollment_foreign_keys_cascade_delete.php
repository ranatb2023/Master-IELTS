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
        // Update assignment_submissions foreign key to cascade on delete
        // This complements the model event but provides database-level consistency
        Schema::table('assignment_submissions', function (Blueprint $table) {
            // Drop existing foreign key constraint
            $table->dropForeign(['enrollment_id']);

            // Re-add with cascade delete
            $table->foreign('enrollment_id')
                ->references('id')
                ->on('enrollments')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to nullOnDelete
        Schema::table('assignment_submissions', function (Blueprint $table) {
            $table->dropForeign(['enrollment_id']);

            $table->foreign('enrollment_id')
                ->references('id')
                ->on('enrollments')
                ->nullOnDelete();
        });
    }
};
