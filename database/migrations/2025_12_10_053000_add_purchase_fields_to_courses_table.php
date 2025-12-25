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
            // Only add columns that don't exist (allow_single_purchase and package_only already exist)
            if (!Schema::hasColumn('courses', 'single_purchase_price')) {
                $table->decimal('single_purchase_price', 10, 2)->nullable();
            }
            if (!Schema::hasColumn('courses', 'allowed_in_packages')) {
                $table->json('allowed_in_packages')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn(['single_purchase_price', 'allowed_in_packages']);
        });
    }
};
