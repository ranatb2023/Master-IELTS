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
        // Rename categories table to blog_categories
        Schema::rename('categories', 'blog_categories');

        // Rename tags table to blog_tags
        Schema::rename('tags', 'blog_tags');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rename back to original names
        Schema::rename('blog_categories', 'categories');
        Schema::rename('blog_tags', 'tags');
    }
};
