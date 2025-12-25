<?php

namespace App\Http\Controllers;

use App\Mail\SubscriptionEnded;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Laravel\Cashier\Subscription;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;

class StripeWebhookController extends Controller
{
    /**
     * Handle incoming Stripe webhook
     */
    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $webhookSecret = config('services.stripe.webhook_secret');

        try {
            // Verify webhook signature
            $event = Webhook::constructEvent(
                $payload,
                $sigHeader,
                $webhookSecret
            );
        } catch (SignatureVerificationException $e) {
            Log::error('Stripe webhook signature verification failed', [
                'error' => $e->getMessage()
            ]);
            return response()->json(['error' => 'Invalid signature'], 400);
        } catch (\Exception $e) {
            Log::error('Stripe webhook error', [
                'error' => $e->getMessage()
            ]);
            return response()->json(['error' => 'Webhook error'], 400);
        }

        // Log the event
        Log::info('Stripe webhook received', [
            'type' => $event->type,
            'id' => $event->id
        ]);

        // Route to appropriate handler
        try {
            switch ($event->type) {
                case 'customer.subscription.deleted':
                    $this->handleSubscriptionDeleted($event->data->object);
                    break;

                case 'customer.subscription.updated':
                    $this->handleSubscriptionUpdated($event->data->object);
                    break;

                case 'invoice.payment_failed':
                    $this->handlePaymentFailed($event->data->object);
                    break;

                default:
                    Log::info('Unhandled webhook event type', ['type' => $event->type]);
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Error processing webhook', [
                'type' => $event->type,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Processing error'], 500);
        }
    }

    /**
     * Handle subscription deleted event
     */
    protected function handleSubscriptionDeleted($stripeSubscription)
    {
        $subscription = Subscription::where('stripe_id', $stripeSubscription->id)->first();

        if (!$subscription) {
            Log::warning('Subscription not found for deleted event', [
                'stripe_id' => $stripeSubscription->id
            ]);
            return;
        }

        $user = $subscription->user;

        Log::info('Processing subscription deletion', [
            'user_id' => $user->id,
            'subscription_id' => $subscription->id,
            'stripe_id' => $stripeSubscription->id
        ]);

        // Suspend enrollments
        $suspendedCount = $user->enrollments()
            ->where('subscription_id', $subscription->id)
            ->where('enrollment_source', 'subscription')
            ->where('status', 'active')
            ->update([
                'status' => 'suspended',
                'notes' => 'Subscription ended on ' . now()->format('Y-m-d H:i') . '. Suspended via webhook.'
            ]);

        // Deactivate package access
        $user->userPackageAccess()
            ->where('subscription_id', $subscription->id)
            ->update(['is_active' => false]);

        // Revoke all feature access from this subscription
        \App\Models\UserFeatureAccess::where('user_id', $user->id)
            ->where('subscription_id', $subscription->id)
            ->update(['has_access' => false]);

        Log::info('Subscription ended via webhook', [
            'user_id' => $user->id,
            'subscription_id' => $subscription->id,
            'suspended_enrollments' => $suspendedCount
        ]);

        Log::info('Revoked subscription features', [
            'user_id' => $user->id,
            'subscription_id' => $subscription->id
        ]);

        // Send email notification
        try {
            Mail::to($user)->send(new SubscriptionEnded($user, $subscription));
        } catch (\Exception $e) {
            Log::error('Failed to send subscription ended email', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle subscription updated event
     */
    protected function handleSubscriptionUpdated($stripeSubscription)
    {
        $subscription = Subscription::where('stripe_id', $stripeSubscription->id)->first();

        if (!$subscription) {
            Log::warning('Subscription not found for updated event', [
                'stripe_id' => $stripeSubscription->id
            ]);
            return;
        }

        // Check if subscription was cancelled
        if ($stripeSubscription->cancel_at_period_end && !$subscription->ends_at) {
            Log::info('Subscription marked for cancellation via webhook', [
                'subscription_id' => $subscription->id,
                'ends_at' => $stripeSubscription->current_period_end
            ]);

            // Update ends_at if not already set
            $subscription->update([
                'ends_at' => \Carbon\Carbon::createFromTimestamp($stripeSubscription->current_period_end)
            ]);
        }

        // Check if subscription was resumed
        if (!$stripeSubscription->cancel_at_period_end && $subscription->ends_at) {
            Log::info('Subscription resumed via webhook', [
                'subscription_id' => $subscription->id
            ]);

            // Clear ends_at
            $subscription->update(['ends_at' => null]);
        }
    }

    /**
     * Handle payment failed event
     */
    protected function handlePaymentFailed($invoice)
    {
        Log::warning('Payment failed for invoice', [
            'invoice_id' => $invoice->id,
            'customer' => $invoice->customer,
            'amount' => $invoice->amount_due / 100
        ]);

        // You can add additional logic here:
        // - Send notification to user
        // - Update subscription status
        // - Trigger retry logic
    }
}
