<?php

// TEMPORARY DEBUG FILE - Place this in routes/web.php at the end temporarily

Route::get('/test-subscription-debug/{plan}', function (\App\Models\SubscriptionPlan $plan) {
    $user = auth()->user();

    if (!$user) {
        return "Please login first";
    }

    try {
        \Log::info("TEST: Starting subscription creation");

        // Create a test payment method
        $paymentMethod = 'pm_card_visa'; // Stripe test token

        \Log::info("TEST: Plan ID: " . $plan->id);
        \Log::info("TEST: User ID: " . $user->id);
        \Log::info("TEST: Stripe Price ID: " . $plan->stripe_price_id);

        $subscription = $plan->createSubscriptionForUser($user, $paymentMethod);

        return [
            'success' => true,
            'subscription_id' => $subscription->id,
            'stripe_status' => $subscription->stripe_status
        ];

    } catch (\Exception $e) {
        return [
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace_preview' => substr($e->getTraceAsString(), 0, 1000)
        ];
    }
})->middleware('auth')->name('test.subscription.debug');
