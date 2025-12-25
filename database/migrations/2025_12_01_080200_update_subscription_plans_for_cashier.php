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
        Schema::table('subscription_plans', function (Blueprint $table) {
            // Laravel Cashier specific fields
            $table->string('stripe_product_id')->nullable()->after('stripe_price_id');
            $table->json('stripe_prices')->nullable()->comment('Multiple price points')->after('stripe_product_id');

            // Variable pricing
            $table->decimal('first_month_price', 10, 2)->nullable()->after('price');
            $table->decimal('regular_price', 10, 2)->after('first_month_price');
            $table->integer('promotional_months')->default(1)->after('regular_price');
            $table->json('tiered_pricing')->nullable()->comment('Month-by-month pricing')->after('promotional_months');

            // Package linking
            $table->json('included_package_ids')->nullable()->after('features');
            $table->json('included_course_ids')->nullable()->after('included_package_ids');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscription_plans', function (Blueprint $table) {
            $table->dropColumn([
                'stripe_product_id',
                'stripe_prices',
                'first_month_price',
                'regular_price',
                'promotional_months',
                'tiered_pricing',
                'included_package_ids',
                'included_course_ids'
            ]);
        });
    }
};
