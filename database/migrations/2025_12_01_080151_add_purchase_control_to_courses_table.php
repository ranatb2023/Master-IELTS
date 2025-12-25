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
        Schema::table('courses', function (Blueprint $table) {
            $table->boolean('allow_single_purchase')->default(true)->after('status');
            $table->boolean('package_only')->default(false)->after('allow_single_purchase');
            $table->decimal('single_purchase_price', 10, 2)->nullable()->after('price');
            $table->json('allowed_in_packages')->nullable()->comment('Array of package IDs')->after('single_purchase_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn(['allow_single_purchase', 'package_only', 'single_purchase_price', 'allowed_in_packages']);
        });
    }
};
