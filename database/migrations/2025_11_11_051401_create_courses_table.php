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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('subtitle')->nullable();
            $table->longText('description')->nullable();
            $table->text('short_description')->nullable();
            $table->foreignId('instructor_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->enum('level', ['beginner', 'intermediate', 'advanced', 'all_levels'])->nullable();
            $table->string('language')->default('english');
            $table->string('thumbnail')->nullable();
            $table->string('preview_video')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->decimal('sale_price', 10, 2)->nullable();
            $table->string('currency')->default('USD');
            $table->boolean('is_free')->default(false);
            $table->decimal('duration_hours', 8, 2)->nullable();
            $table->integer('total_lectures')->default(0);
            $table->integer('total_quizzes')->default(0);
            $table->integer('total_assignments')->default(0);
            $table->integer('enrollment_limit')->nullable();
            $table->integer('enrolled_count')->default(0);
            $table->decimal('average_rating', 3, 2)->default(0.00);
            $table->integer('total_reviews')->default(0);
            $table->decimal('completion_rate', 5, 2)->default(0.00);
            $table->json('requirements')->nullable();
            $table->json('learning_outcomes')->nullable();
            $table->json('target_audience')->nullable();
            $table->json('features')->nullable();
            $table->enum('status', ['draft', 'review', 'published', 'archived'])->default('draft');
            $table->enum('visibility', ['public', 'private', 'unlisted'])->default('public');
            $table->boolean('certificate_enabled')->default(true);
            $table->foreignId('certificate_template_id')->nullable()->constrained('certificate_templates')->nullOnDelete();
            $table->boolean('drip_content')->default(false);
            $table->json('drip_schedule')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_keywords')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('slug');
            $table->index('instructor_id');
            $table->index('category_id');
            $table->index('status');
            $table->index('visibility');
            $table->index('is_free');
            $table->index('published_at');
            $table->index(['status', 'visibility']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
