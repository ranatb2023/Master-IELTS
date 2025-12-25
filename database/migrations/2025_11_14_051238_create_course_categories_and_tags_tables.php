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
        // Create course_categories table
        Schema::create('course_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->string('color')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->foreignId('parent_id')->nullable()->constrained('course_categories')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['is_active', 'order']);
            $table->index('parent_id');
        });

        // Create course_tags table
        Schema::create('course_tags', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('color')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index('is_active');
        });

        // Ensure course_category pivot table exists (if not created by seeder)
        if (!Schema::hasTable('course_category')) {
            Schema::create('course_category', function (Blueprint $table) {
                $table->id();
                $table->foreignId('course_id')->constrained()->cascadeOnDelete();
                $table->foreignId('course_category_id')->constrained('course_categories')->cascadeOnDelete();
                $table->timestamps();

                $table->unique(['course_id', 'course_category_id']);
            });
        }

        // Ensure course_tag pivot table exists (if not created by seeder)
        if (!Schema::hasTable('course_tag')) {
            Schema::create('course_tag', function (Blueprint $table) {
                $table->id();
                $table->foreignId('course_id')->constrained()->cascadeOnDelete();
                $table->foreignId('course_tag_id')->constrained('course_tags')->cascadeOnDelete();
                $table->timestamps();

                $table->unique(['course_id', 'course_tag_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_category');
        Schema::dropIfExists('course_tag');
        Schema::dropIfExists('course_tags');
        Schema::dropIfExists('course_categories');
    }
};
