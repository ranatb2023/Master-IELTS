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
        // Drop the old course_category pivot table
        Schema::dropIfExists('course_category');

        // Recreate it with the correct structure for course_categories table
        Schema::create('course_category', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_category_id')->constrained('course_categories')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['course_id', 'course_category_id']);
        });

        // Drop the old course_tag pivot table
        Schema::dropIfExists('course_tag');

        // Recreate it with the correct structure for course_tags table
        Schema::create('course_tag', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_tag_id')->constrained('course_tags')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['course_id', 'course_tag_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the new pivot tables
        Schema::dropIfExists('course_tag');
        Schema::dropIfExists('course_category');

        // Recreate the old structure
        Schema::create('course_category', function (Blueprint $table) {
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->primary(['course_id', 'category_id']);
            $table->index('course_id');
            $table->index('category_id');
        });

        Schema::create('course_tag', function (Blueprint $table) {
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->foreignId('tag_id')->constrained('tags')->cascadeOnDelete();
            $table->primary(['course_id', 'tag_id']);
            $table->index('course_id');
            $table->index('tag_id');
        });
    }
};
