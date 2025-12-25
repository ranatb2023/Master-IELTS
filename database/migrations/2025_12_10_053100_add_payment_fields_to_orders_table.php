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
        Schema::table('orders', function (Blueprint $table) {
            $table->json('metadata')->nullable();
            $table->string('payment_intent_id')->nullable();
            $table->timestamp('paid_at')->nullable();

            // Add index for payment_intent_id for faster lookups
            $table->index('payment_intent_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['payment_intent_id']);
            $table->dropColumn(['metadata', 'payment_intent_id', 'paid_at']);
        });
    }
};
