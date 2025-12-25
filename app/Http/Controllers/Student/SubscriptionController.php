<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Mail\SubscriptionCancelled;
use App\Mail\SubscriptionResumed;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SubscriptionController extends Controller
{
    /**
     * Display available subscription plans
     */
    public function index()
    {
        $plans = SubscriptionPlan::active()
            ->orderBy('price')
            ->get();

        // Get current active subscription using Cashier - only active ones
        $currentSubscription = Auth::user()->subscriptions()->active()->where('type', 'default')->first();

        // Load the subscription plan relationship if subscription exists
        if ($currentSubscription && $currentSubscription->subscription_plan_id) {
            $currentSubscription->subscriptionPlan = SubscriptionPlan::find($currentSubscription->subscription_plan_id);
        }

        return view('student.subscriptions.index', compact('plans', 'currentSubscription'));
    }

    /**
     * Show subscription plan details
     */
    public function show(SubscriptionPlan $plan)
    {
        if (!$plan->isActive()) {
            abort(404, 'Subscription plan not available');
        }

        $plan->load('includedPackages', 'includedCourses');

        $currentSubscription = Auth::user()->subscriptions()->active()->where('type', 'default')->first();

        return view('student.subscriptions.show', compact('plan', 'currentSubscription'));
    }

    /**
     * Show checkout page for subscription
     */
    public function checkout(SubscriptionPlan $plan)
    {
        $user = Auth::user();

        if (!$plan->isActive()) {
            return back()->with('error', 'This subscription plan is not available.');
        }

        // Check if user already has active subscription
        if ($user->subscribed('default')) {
            return redirect()
                ->route('student.subscriptions.manage')
                ->with('info', 'You already have an active subscription. Please cancel it before subscribing to a new plan.');
        }

        // Get user's default payment method (if exists)
        $defaultPaymentMethod = null;
        if ($user->hasPaymentMethod()) {
            try {
                $defaultPaymentMethod = $user->defaultPaymentMethod();
            } catch (\Exception $e) {
                \Log::warning("Could not retrieve payment method for user {$user->id}: " . $e->getMessage());
            }
        }

        // Check for active package (for warning banner)
        $activePackage = \App\Models\UserPackageAccess::where('user_id', $user->id)
            ->where('is_active', true)
            ->with('package')
            ->first();

        // Get setup intent for Stripe
        $intent = $user->createSetupIntent();

        return view('student.subscriptions.checkout', compact('plan', 'defaultPaymentMethod', 'intent', 'activePackage'));
    }

    /**
     * Process subscription checkout
     */
    public function subscribe(Request $request, SubscriptionPlan $plan)
    {
        $user = Auth::user();

        if (!$plan->isActive()) {
            return back()->with('error', 'This subscription plan is not available.');
        }

        // Check if user already has active subscription
        if ($user->subscribed('default')) {
            return redirect()
                ->route('student.subscriptions.manage')
                ->with('info', 'You already have an active subscription.');
        }

        $request->validate([
            'payment_method' => 'required|string',
        ]);

        // Check for active packages (exclusive access model)
        $activePackages = \App\Models\UserPackageAccess::where('user_id', $user->id)
            ->where('is_active', true)
            ->get();

        try {
            // Deactivate all active packages if any exist (exclusive access - no refund)
            if ($activePackages->count() > 0) {
                \Log::info('Deactivating packages for subscription', [
                    'user_id' => $user->id,
                    'package_count' => $activePackages->count(),
                    'plan_id' => $plan->id,
                ]);

                foreach ($activePackages as $packageAccess) {
                    $this->deactivatePackageForSubscription($packageAccess);
                }
            }

            // Create subscription using the model method
            \Log::info("Creating subscription for user {$user->id}, plan: {$plan->id}");

            $subscription = $plan->createSubscriptionForUser($user, $request->payment_method);

            Log::info("Subscription created successfully", [
                'user_id' => $user->id,
                'subscription_id' => $subscription->id,
                'stripe_status' => $subscription->stripe_status
            ]);

            // Grant all functional features from the plan
            $functionalFeatures = $plan->functionalFeatures()->get();

            foreach ($functionalFeatures as $feature) {
                $feature->grantAccessToUser(
                    $user,
                    packageId: null,
                    subscriptionId: $subscription->id,
                    expiresAt: null // Features last while subscription active
                );
            }

            Log::info('Granted subscription features', [
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'features_count' => $functionalFeatures->count(),
                'features' => $functionalFeatures->pluck('feature_key')->toArray()
            ]);

            // Notify admins about new subscription
            $admins = \App\Models\User::role('super_admin')->get();
            foreach ($admins as $admin) {
                $admin->notify(new \App\Notifications\NewEnrollmentNotification($subscription->enrollments()->first()));
            }

            return redirect()
                ->route('student.subscriptions.success')
                ->with('success', 'Subscription activated successfully!');
        } catch (\Exception $e) {
            Log::error("Subscription creation failed: " . $e->getMessage(), [
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Failed to create subscription: ' . $e->getMessage());
        }
    }

    /**
     * Subscription success page
     */
    public function success()
    {
        $subscription = Auth::user()->subscription('default');

        if (!$subscription) {
            return redirect()->route('student.subscriptions.index');
        }

        // Load subscription plan
        $subscription->subscriptionPlan = SubscriptionPlan::find($subscription->subscription_plan_id);

        return view('student.subscriptions.success', compact('subscription'));
    }

    /**
     * Manage current subscription
     */
    public function manage()
    {
        $user = Auth::user();

        $subscription = $user->subscription('default');

        if (!$subscription) {
            return redirect()
                ->route('student.subscriptions.index')
                ->with('info', 'You don\'t have an active subscription.');
        }

        // Load subscription plan
        $subscription->subscriptionPlan = SubscriptionPlan::find($subscription->subscription_plan_id);

        // Get package access
        $packageAccess = $user->userPackageAccess()
            ->where('subscription_id', $subscription->id)
            ->where('is_active', true)
            ->with('package.courses')
            ->get();

        // Fetch Stripe subscription once to get billing dates
        try {
            $stripeSubscription = $subscription->asStripeSubscription();

            // Convert Stripe object to array for easier access
            $stripeData = $stripeSubscription->toArray();

            // The billing period dates are in the first subscription item
            $firstItem = $stripeData['items']['data'][0] ?? null;

            $billingDates = [
                'current_period_start' => isset($firstItem['current_period_start']) ? \Carbon\Carbon::createFromTimestamp($firstItem['current_period_start']) : null,
                'current_period_end' => isset($firstItem['current_period_end']) ? \Carbon\Carbon::createFromTimestamp($firstItem['current_period_end']) : null,
            ];
        } catch (\Exception $e) {
            // If Stripe fetch fails, use fallback
            \Log::warning('Failed to fetch Stripe subscription data', [
                'subscription_id' => $subscription->id,
                'error' => $e->getMessage()
            ]);
            $billingDates = [
                'current_period_start' => null,
                'current_period_end' => null,
            ];
        }

        return view('student.subscriptions.manage', compact('subscription', 'packageAccess', 'billingDates'));
    }

    /**
     * Cancel subscription
     */
    public function cancel(Request $request)
    {
        $user = Auth::user();

        $subscription = $user->subscription('default');

        if (!$subscription) {
            return back()->with('error', 'No active subscription found.');
        }

        try {
            // Cancel at period end (not immediately)
            $subscription->cancel();

            Log::info("Subscription cancelled", [
                'user_id' => $user->id,
                'subscription_id' => $subscription->id,
                'ends_at' => $subscription->ends_at
            ]);

            // Send cancellation email
            try {
                Mail::to($user)->send(new SubscriptionCancelled($user, $subscription));
            } catch (\Exception $e) {
                Log::error('Failed to send cancellation email', ['error' => $e->getMessage()]);
            }

            // Notify admins about subscription cancellation
            $admins = \App\Models\User::role('super_admin')->get();
            $plan = \App\Models\SubscriptionPlan::find($subscription->subscription_plan_id);
            foreach ($admins as $admin) {
                $admin->notify(new \App\Notifications\SubscriptionUpdatedNotification($subscription, $plan));
            }

            return redirect()
                ->route('student.subscriptions.manage')
                ->with('success', 'Subscription cancelled. You will retain access until ' . $subscription->ends_at->format('M d, Y'));
        } catch (\Exception $e) {
            Log::error("Subscription cancellation failed: " . $e->getMessage());
            return back()->with('error', 'Failed to cancel subscription: ' . $e->getMessage());
        }
    }

    /**
     * Resume cancelled subscription
     */
    public function resume(Request $request)
    {
        $user = Auth::user();

        try {
            $subscription = $user->subscription('default');

            if ($subscription && $subscription->onGracePeriod()) {
                $subscription->resume();

                // Restore package access
                $user->userPackageAccess()
                    ->where('subscription_id', $subscription->id)
                    ->update(['is_active' => true]);

                // Restore enrollments
                $user->enrollments()
                    ->where('subscription_id', $subscription->id)
                    ->update([
                        'status' => 'active',
                        'expires_at' => null,
                    ]);

                // Restore feature access
                \App\Models\UserFeatureAccess::where('user_id', $user->id)
                    ->where('subscription_id', $subscription->id)
                    ->update(['has_access' => true]);

                Log::info("Subscription resumed", [
                    'user_id' => $user->id,
                    'subscription_id' => $subscription->id
                ]);

                Log::info('Restored subscription features', [
                    'user_id' => $user->id,
                    'subscription_id' => $subscription->id
                ]);

                // Send resume email
                try {
                    Mail::to($user)->send(new SubscriptionResumed($user, $subscription));
                } catch (\Exception $e) {
                    Log::error('Failed to send resume email', ['error' => $e->getMessage()]);
                }

                // Notify admins about subscription resumption
                $admins = \App\Models\User::role('super_admin')->get();
                $plan = \App\Models\SubscriptionPlan::find($subscription->subscription_plan_id);
                foreach ($admins as $admin) {
                    $admin->notify(new \App\Notifications\SubscriptionUpdatedNotification($subscription, $plan));
                }

                return back()->with('success', 'Subscription resumed successfully! Welcome back!');
            }

            return back()->with('error', 'Unable to resume subscription.');
        } catch (\Exception $e) {
            Log::error("Subscription resume failed: " . $e->getMessage());
            return back()->with('error', 'Failed to resume subscription: ' . $e->getMessage());
        }
    }

    /**
     * Show payment method management page
     */
    public function paymentMethod()
    {
        $user = Auth::user();

        // Get setup intent for Stripe Elements
        $intent = $user->createSetupIntent();

        return view('student.subscriptions.payment-method', [
            'intent' => $intent,
            'stripeKey' => config('services.stripe.key')
        ]);
    }

    /**
     * Update payment method
     */
    public function updatePaymentMethod(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|string',
        ]);

        try {
            $user = Auth::user();
            $user->updateDefaultPaymentMethod($request->payment_method);

            return back()->with('success', 'Payment method updated successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update payment method: ' . $e->getMessage());
        }
    }

    /**
     * Delete payment method
     */
    public function deletePaymentMethod(Request $request)
    {
        $user = Auth::user();

        // Check if user has active subscription
        $subscription = $user->subscription('default');

        if ($subscription && $subscription->active()) {
            return back()->with('error', 'Cannot delete payment method while you have an active subscription. Please cancel your subscription first.');
        }

        try {
            // Delete all payment methods
            $user->deletePaymentMethods();

            return back()->with('success', 'Payment method deleted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete payment method: ' . $e->getMessage());
        }
    }

    /**
     * Show invoices
     */
    public function invoices()
    {
        $user = Auth::user();

        $invoices = [];
        if ($user->hasStripeId()) {
            try {
                $invoices = $user->invoices();
            } catch (\Exception $e) {
                // Handle error silently
            }
        }

        return view('student.subscriptions.invoices', compact('invoices'));
    }

    /**
     * Download invoice
     */
    public function downloadInvoice(Request $request, $invoiceId)
    {
        try {
            return $request->user()->downloadInvoice($invoiceId, [
                'vendor' => config('app.name'),
                'product' => 'Subscription',
            ]);
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to download invoice: ' . $e->getMessage());
        }
    }

    /**
     * Preview plan change with proration details
     */
    public function previewPlanChange(SubscriptionPlan $plan)
    {
        $user = Auth::user();

        if (!$plan->isActive()) {
            return back()->with('error', 'This subscription plan is not available.');
        }

        $subscription = $user->subscription('default');

        if (!$subscription) {
            return back()->with('error', 'No active subscription found.');
        }

        // Prevent plan changes during grace period
        if ($subscription->onGracePeriod()) {
            return back()->with('error', 'You cannot change plans while your subscription is cancelled. Please resume your subscription first.');
        }

        // Check if already on this plan
        if ($subscription->subscription_plan_id == $plan->id) {
            return back()->with('info', 'You are already subscribed to this plan.');
        }

        $currentPlan = SubscriptionPlan::find($subscription->subscription_plan_id);

        try {
            // Convert Stripe object to array for easier access
            $stripeData = $subscription->asStripeSubscription()->toArray();

            // Get upcoming invoice to calculate proration
            // Use previewInvoice for subscription swaps (not upcomingInvoice)
            $upcomingInvoice = $subscription->previewInvoice($plan->stripe_price_id);

            // Convert from cents to dollars
            $prorationAmount = $upcomingInvoice->total / 100;
            $subtotal = $upcomingInvoice->subtotal / 100;

            // Determine if upgrade or downgrade
            $isUpgrade = $plan->price > ($currentPlan->price ?? 0);

            // Get line items for detailed breakdown
            $lineItems = [];
            foreach ($upcomingInvoice->lines->data as $line) {
                $lineItems[] = [
                    'description' => $line->description,
                    'amount' => $line->amount / 100,
                    'proration' => $line->proration ?? false,
                ];
            }

            return view('student.subscriptions.preview-change', compact(
                'plan',
                'currentPlan',
                'subscription',
                'prorationAmount',
                'subtotal',
                'isUpgrade',
                'lineItems'
            ));

        } catch (\Exception $e) {
            \Log::error("Failed to preview plan change", [
                'user_id' => $user->id,
                'target_plan' => $plan->id,
                'error' => $e->getMessage()
            ]);
            return back()->with('error', 'Failed to preview plan change. Please try again.');
        }
    }

    /**
     * Confirm and execute plan change after preview
     */
    public function confirmPlanChange(Request $request, SubscriptionPlan $plan)
    {
        // This is essentially the same as changePlan, but we know user has seen the preview
        return $this->changePlan($request, $plan);
    }

    /**
     * Upgrade/Downgrade subscription
     */
    public function changePlan(Request $request, SubscriptionPlan $plan)
    {
        $user = Auth::user();

        if (!$plan->isActive()) {
            return back()->with('error', 'This subscription plan is not available.');
        }

        $subscription = $user->subscription('default');

        if (!$subscription) {
            return back()->with('error', 'No active subscription found.');
        }

        // Get current plan name for logging
        $oldPlanName = 'Unknown';
        if ($subscription->subscription_plan_id) {
            $oldPlan = SubscriptionPlan::find($subscription->subscription_plan_id);
            $oldPlanName = $oldPlan ? $oldPlan->name : 'Unknown';
        }

        try {
            DB::transaction(function () use ($user, $plan, $subscription, $oldPlanName) {
                // 1. Swap Stripe subscription
                $subscription->swap($plan->stripe_price_id);

                // 2. Update subscription plan ID
                $subscription->update([
                    'subscription_plan_id' => $plan->id,
                ]);

                // 3. Suspend old enrollments (don't delete - preserve user progress)
                $suspendedCount = $user->enrollments()
                    ->where('subscription_id', $subscription->id)
                    ->where('enrollment_source', 'subscription')
                    ->where('status', 'active')
                    ->update([
                        'status' => 'suspended',
                        'notes' => 'Suspended due to plan change on ' . now()->format('Y-m-d H:i') .
                            '. Previous plan: ' . $oldPlanName
                    ]);

                \Log::info("Plan change: Suspended {$suspendedCount} enrollments from {$oldPlanName}");

                // 4. Revoke old package access
                $user->userPackageAccess()
                    ->where('subscription_id', $subscription->id)
                    ->update(['is_active' => false]);

                // 5. Revoke old subscription features
                \App\Models\UserFeatureAccess::where('user_id', $user->id)
                    ->where('subscription_id', $subscription->id)
                    ->update(['has_access' => false]);

                \Log::info("Plan change: Revoked old features", [
                    'user_id' => $user->id,
                    'subscription_id' => $subscription->id
                ]);

                // 6. Grant new package access and enrollments
                $plan->grantSubscriptionAccess($user, $subscription);

                // 7. Grant new plan features
                $functionalFeatures = $plan->functionalFeatures()->get();
                foreach ($functionalFeatures as $feature) {
                    \App\Models\PackageFeature::where('feature_key', $feature->feature_key)
                        ->first()?->grantAccessToUser($user, null, $subscription->id);
                }

                \Log::info("Plan change: Granted new features", [
                    'user_id' => $user->id,
                    'feature_count' => $functionalFeatures->count()
                ]);

                \Log::info("Plan change successful", [
                    'user_id' => $user->id,
                    'from_plan' => $oldPlanName,
                    'to_plan' => $plan->name,
                    'suspended_enrollments' => $suspendedCount
                ]);
            });

            // Send notification to user about plan change
            $user->notify(new \App\Notifications\SubscriptionUpdatedNotification($subscription, $plan));

            return redirect()
                ->route('student.subscriptions.manage')
                ->with('success', 'Subscription plan changed successfully! You now have access to courses in the ' . $plan->name . ' plan.');

        } catch (\Exception $e) {
            \Log::error("Plan change failed", [
                'user_id' => $user->id,
                'to_plan' => $plan->name,
                'error' => $e->getMessage()
            ]);
            return back()->with('error', 'Failed to change plan: ' . $e->getMessage());
        }
    }

    /**
     * Deactivate package when user subscribes (exclusive access model)
     */
    protected function deactivatePackageForSubscription($packageAccess)
    {
        try {
            $package = $packageAccess->package;

            // Revoke package features
            if ($package) {
                \Log::info('Revoking package features for subscription', [
                    'user_id' => $packageAccess->user_id,
                    'package_id' => $package->id,
                    'package_name' => $package->name,
                ]);

                foreach ($package->functionalFeatures as $feature) {
                    \App\Models\UserFeatureAccess::where('user_id', $packageAccess->user_id)
                        ->where('feature_key', $feature->feature_key)
                        ->where('package_id', $package->id)
                        ->delete();
                }
            }

            // Deactivate the package access
            $packageAccess->update(['is_active' => false]);

            \Log::info('Package deactivated for subscription', [
                'user_id' => $packageAccess->user_id,
                'package_id' => $package->id ?? null,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error deactivating package for subscription', [
                'user_id' => $packageAccess->user_id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}

