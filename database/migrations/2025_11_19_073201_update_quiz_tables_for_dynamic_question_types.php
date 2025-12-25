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
        // Update questions table to use dynamic question types
        Schema::table('questions', function (Blueprint $table) {
            // Drop the old type enum column
            $table->dropColumn('type');
        });

        Schema::table('questions', function (Blueprint $table) {
            // Add foreign key to question_types table
            $table->foreignId('question_type_id')->after('quiz_id')->constrained('question_types')->onDelete('restrict');

            // Add settings column for question-specific configuration
            $table->longText('settings')->nullable()->after('explanation')->comment('JSON settings specific to this question instance');

            // Add index
            $table->index('question_type_id');
        });

        // Add course_id to quizzes for direct course access
        Schema::table('quizzes', function (Blueprint $table) {
            $table->foreignId('course_id')->after('id')->nullable()->constrained('courses')->onDelete('cascade');
            $table->index('course_id');
        });

        // Update quiz_attempts table
        Schema::table('quiz_attempts', function (Blueprint $table) {
            // Add enrollment relationship
            $table->foreignId('enrollment_id')->after('user_id')->nullable()->constrained('enrollments')->onDelete('cascade');

            // Add status enum for better tracking
            $table->enum('status', ['in_progress', 'submitted', 'graded', 'abandoned'])->after('submitted_at')->default('in_progress');

            // Add completed_at timestamp
            $table->timestamp('completed_at')->after('submitted_at')->nullable();

            // Add manual review tracking
            $table->boolean('requires_manual_grading')->after('graded_at')->default(false);
            $table->foreignId('graded_by')->after('graded_at')->nullable()->constrained('users')->onDelete('set null');

            // Add indexes
            $table->index('enrollment_id');
            $table->index('status');
            $table->index('requires_manual_grading');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quiz_attempts', function (Blueprint $table) {
            $table->dropForeign(['enrollment_id']);
            $table->dropForeign(['graded_by']);
            $table->dropColumn(['enrollment_id', 'status', 'completed_at', 'requires_manual_grading', 'graded_by']);
        });

        Schema::table('quizzes', function (Blueprint $table) {
            $table->dropForeign(['course_id']);
            $table->dropColumn('course_id');
        });

        Schema::table('questions', function (Blueprint $table) {
            $table->dropForeign(['question_type_id']);
            $table->dropColumn(['question_type_id', 'settings']);
        });

        Schema::table('questions', function (Blueprint $table) {
            $table->enum('type', ['mcq_single', 'mcq_multiple', 'true_false', 'short_answer', 'passage_mcq'])->after('quiz_id');
        });
    }
};
