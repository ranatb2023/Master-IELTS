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
        // Add missing fields to assignment_submissions table
        Schema::table('assignment_submissions', function (Blueprint $table) {
            // Add submission_text field (currently using 'content' but student controller uses 'submission_text')
            $table->longText('submission_text')->nullable()->after('user_id');

            // Add enrollment_id for tracking which enrollment this submission belongs to
            $table->foreignId('enrollment_id')->nullable()->after('user_id')->constrained('enrollments')->nullOnDelete();
        });

        // Add missing fields to assignment_files table
        Schema::table('assignment_files', function (Blueprint $table) {
            // Add mime_type field (student controller saves it but column doesn't exist)
            $table->string('mime_type')->nullable()->after('file_type');

            // Add original_filename for display purposes (currently only file_name exists)
            $table->string('original_filename')->nullable()->after('file_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assignment_submissions', function (Blueprint $table) {
            $table->dropForeign(['enrollment_id']);
            $table->dropColumn(['submission_text', 'enrollment_id']);
        });

        Schema::table('assignment_files', function (Blueprint $table) {
            $table->dropColumn(['mime_type', 'original_filename']);
        });
    }
};
