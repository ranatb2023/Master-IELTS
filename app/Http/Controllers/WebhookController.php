<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Laravel\Cashier\Http\Controllers\WebhookController as CashierController;

class WebhookController extends CashierController
{
    /**
     * Handle subscription created event
     */
    public function handleCustomerSubscriptionCreated(array $payload)
    {
        \Log::info('Webhook: customer.subscription.created', ['payload' => $payload]);

        $subscription = $payload['data']['object'];
        $customerId = $subscription['customer'];

        // Find user by Stripe customer ID
        $user = User::where('stripe_id', $customerId)->first();

        if (!$user) {
            \Log::warning('User not found for Stripe customer', ['customer_id' => $customerId]);
            return;
        }

        // Get subscription plan
        $stripePriceId = $subscription['items']['data'][0]['price']['id'] ?? null;
        $plan = SubscriptionPlan::where('stripe_price_id', $stripePriceId)->first();

        if (!$plan) {
            \Log::warning('Subscription plan not found', ['price_id' => $stripePriceId]);
            return;
        }

        // Get the Cashier subscription model
        $cashierSubscription = $user->subscriptions()
            ->where('stripe_id', $subscription['id'])
            ->first();

        if ($cashierSubscription) {
            // Update with subscription plan ID and metadata
            $cashierSubscription->update([
                'subscription_plan_id' => $plan->id,
                'payment_method' => 'stripe',
                'metadata' => json_encode([
                    'subscription_plan_id' => $plan->id,
                    'plan_name' => $plan->name,
                ]),
            ]);

            // Grant subscription access
            try {
                $plan->grantSubscriptionAccess($user, $cashierSubscription);

                \Log::info('Subscription access granted successfully via webhook', [
                    'user_id' => $user->id,
                    'subscription_id' => $cashierSubscription->id,
                ]);
            } catch (\Exception $e) {
                \Log::error('Failed to grant subscription access via webhook', [
                    'user_id' => $user->id,
                    'subscription_id' => $cashierSubscription->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }

        \Log::info('Subscription created successfully', [
            'user_id' => $user->id,
            'subscription_id' => $subscription['id'],
        ]);
    }

    /**
     * Handle subscription updated event
     */
    public function handleCustomerSubscriptionUpdated(array $payload)
    {
        \Log::info('Webhook: customer.subscription.updated', ['payload' => $payload]);

        $subscription = $payload['data']['object'];

        // Let Cashier handle the update
        // Additional custom logic can be added here if needed

        \Log::info('Subscription updated successfully', [
            'subscription_id' => $subscription['id'],
            'status' => $subscription['status'],
        ]);
    }

    /**
     * Handle subscription deleted/cancelled event
     */
    public function handleCustomerSubscriptionDeleted(array $payload)
    {
        \Log::info('Webhook: customer.subscription.deleted', ['payload' => $payload]);

        $subscription = $payload['data']['object'];
        $customerId = $subscription['customer'];

        // Find user
        $user = User::where('stripe_id', $customerId)->first();

        if (!$user) {
            \Log::warning('User not found for Stripe customer', ['customer_id' => $customerId]);
            return;
        }

        // Find local subscription
        $cashierSubscription = $user->subscriptions()
            ->where('stripe_id', $subscription['id'])
            ->first();

        if (!$cashierSubscription) {
            \Log::warning('Local subscription not found', ['subscription_id' => $subscription['id']]);
            return;
        }

        // Revoke package access
        $user->userPackageAccess()
            ->where('subscription_id', $cashierSubscription->id)
            ->update(['is_active' => false]);

        // Update enrollments
        $enrollmentsUpdated = $user->enrollments()
            ->where('subscription_id', $cashierSubscription->id)
            ->where('status', 'active')
            ->update([
                'status' => 'expired',
                'expires_at' => now(),
            ]);

        \Log::info('Subscription cancelled successfully', [
            'subscription_id' => $subscription['id'],
            'user_id' => $user->id,
            'enrollments_expired' => $enrollmentsUpdated,
        ]);
    }

    /**
     * Handle successful payment
     */
    public function handleInvoicePaymentSucceeded(array $payload)
    {
        \Log::info('Webhook: invoice.payment_succeeded', ['payload' => $payload]);

        $invoice = $payload['data']['object'];
        $subscriptionId = $invoice['subscription'] ?? null;

        if (!$subscriptionId) {
            return;
        }

        // Find local subscription
        $cashierSubscription = \Laravel\Cashier\Subscription::where('stripe_id', $subscriptionId)->first();

        if (!$cashierSubscription) {
            \Log::warning('Local subscription not found', ['subscription_id' => $subscriptionId]);
            return;
        }

        // Ensure access is active
        if ($cashierSubscription->stripe_status !== 'active') {
            $user = $cashierSubscription->user;

            $user->userPackageAccess()
                ->where('subscription_id', $cashierSubscription->id)
                ->update(['is_active' => true]);

            $user->enrollments()
                ->where('subscription_id', $cashierSubscription->id)
                ->where('status', 'expired')
                ->update([
                    'status' => 'active',
                    'expires_at' => null,
                ]);
        }

        \Log::info('Subscription payment succeeded', [
            'subscription_id' => $subscriptionId,
            'user_id' => $cashierSubscription->user_id,
            'amount' => ($invoice['amount_paid'] / 100),
        ]);

        // Notify admins about payment received
        $admins = User::role('super_admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(new \App\Notifications\PaymentReceivedNotification($invoice, $cashierSubscription));
        }
    }

    /**
     * Handle failed payment
     */
    public function handleInvoicePaymentFailed(array $payload)
    {
        \Log::error('Webhook: invoice.payment_failed', ['payload' => $payload]);

        $invoice = $payload['data']['object'];
        $subscriptionId = $invoice['subscription'] ?? null;

        if (!$subscriptionId) {
            return;
        }

        // Find local subscription
        $cashierSubscription = \Laravel\Cashier\Subscription::where('stripe_id', $subscriptionId)->first();

        if (!$cashierSubscription) {
            \Log::warning('Local subscription not found', ['subscription_id' => $subscriptionId]);
            return;
        }

        // Send notification to user about failed payment
        $cashierSubscription->user->notify(new \App\Notifications\PaymentFailedNotification($invoice));

        \Log::info('Payment failed notification sent', [
            'subscription_id' => $subscriptionId,
            'user_id' => $cashierSubscription->user_id,
        ]);
    }

    /**
     * Handle customer updated event
     */
    public function handleCustomerUpdated(array $payload)
    {
        \Log::info('Webhook: customer.updated', ['payload' => $payload]);

        $customer = $payload['data']['object'];

        // Find user by Stripe customer ID
        $user = User::where('stripe_id', $customer['id'])->first();

        if (!$user) {
            \Log::warning('User not found for Stripe customer', ['customer_id' => $customer['id']]);
            return;
        }

        // Update user's payment method if default changed
        if (isset($customer['invoice_settings']['default_payment_method'])) {
            try {
                $user->updateDefaultPaymentMethod($customer['invoice_settings']['default_payment_method']);
            } catch (\Exception $e) {
                \Log::warning('Could not update default payment method: ' . $e->getMessage());
            }
        }

        \Log::info('Customer updated successfully', ['user_id' => $user->id]);
    }

    /**
     * Handle payment method attached
     */
    public function handlePaymentMethodAttached(array $payload)
    {
        \Log::info('Webhook: payment_method.attached', ['payload' => $payload]);

        $paymentMethod = $payload['data']['object'];
        $customerId = $paymentMethod['customer'] ?? null;

        if (!$customerId) {
            return;
        }

        $user = User::where('stripe_id', $customerId)->first();

        if (!$user) {
            \Log::warning('User not found for Stripe customer', ['customer_id' => $customerId]);
            return;
        }

        \Log::info('Payment method attached', [
            'user_id' => $user->id,
            'payment_method_id' => $paymentMethod['id'],
        ]);
    }

    /**
     * Handle all other webhook events
     */
    public function handleWebhook(Request $request)
    {
        // Verify webhook signature
        $payload = json_decode($request->getContent(), true);
        $eventType = $payload['type'] ?? null;

        \Log::info('Webhook received', ['type' => $eventType]);

        // Call parent to handle standard Cashier webhooks
        return parent::handleWebhook($request);
    }
}
