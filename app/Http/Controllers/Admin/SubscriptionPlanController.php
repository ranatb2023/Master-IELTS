<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SubscriptionPlanController extends Controller
{
    /**
     * Display a listing of subscription plans
     */
    public function index()
    {
        $plans = SubscriptionPlan::withCount('subscriptions')
            ->orderBy('name')
            ->get();

        return view('admin.subscription-plans.index', compact('plans'));
    }

    /**
     * Show the form for creating a new plan
     */
    public function create()
    {
        $packages = \App\Models\Package::where('status', 'published')->orderBy('name')->get();
        $courses = \App\Models\Course::where('status', 'published')->orderBy('title')->get();

        // Load features for assignment
        $functionalFeatures = \App\Models\PackageFeature::functional()->active()->orderBy('feature_name')->get();
        $displayFeatures = \App\Models\PackageFeature::display()->active()->orderBy('feature_name')->get();

        return view('admin.subscription-plans.create', compact('packages', 'courses', 'functionalFeatures', 'displayFeatures'));
    }

    /**
     * Store a newly created plan
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:subscription_plans,slug',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'first_month_price' => 'nullable|numeric|min:0',
            'regular_price' => 'nullable|numeric|min:0',
            'promotional_months' => 'nullable|integer|min:1',
            'currency' => 'required|string|in:USD,EUR,GBP',
            'interval' => 'required|in:month,year',
            'trial_days' => 'nullable|integer|min:0',
            'stripe_price_id' => 'nullable|string|max:255',
            'stripe_product_id' => 'nullable|string|max:255',
            'paypal_plan_id' => 'nullable|string|max:255',
            'features' => 'nullable|array',
            'features.*' => 'string',
            'included_package_ids' => 'nullable|array',
            'included_package_ids.*' => 'exists:packages,id',
            'included_course_ids' => 'nullable|array',
            'included_course_ids.*' => 'exists:courses,id',
            'is_active' => 'boolean',
        ]);

        // Auto-generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);

            // Ensure uniqueness
            $originalSlug = $validated['slug'];
            $counter = 1;
            while (SubscriptionPlan::where('slug', $validated['slug'])->exists()) {
                $validated['slug'] = $originalSlug . '-' . $counter++;
            }
        }

        // Set default for promotional_months if empty
        if (empty($validated['promotional_months'])) {
            $validated['promotional_months'] = 1;
        }

        // Handle is_active checkbox (defaults to false if not present)
        $validated['is_active'] = $request->has('is_active') && $request->is_active ? true : false;

        // Convert features array if provided
        if ($request->has('features') && is_array($request->features)) {
            $validated['features'] = array_values(array_filter($request->features));
        } else {
            $validated['features'] = [];
        }

        // Handle included packages
        if ($request->has('included_package_ids') && is_array($request->included_package_ids)) {
            $validated['included_package_ids'] = array_values(array_filter($request->included_package_ids));
        } else {
            $validated['included_package_ids'] = [];
        }

        // Handle included courses
        if ($request->has('included_course_ids') && is_array($request->included_course_ids)) {
            $validated['included_course_ids'] = array_values(array_filter($request->included_course_ids));
        } else {
            $validated['included_course_ids'] = [];
        }

        // If regular_price is not set, use the price field
        if (!isset($validated['regular_price']) || $validated['regular_price'] === null) {
            $validated['regular_price'] = $validated['price'];
        }

        $plan = SubscriptionPlan::create($validated);

        // Sync plan features
        if ($request->has('plan_features')) {
            $features = collect($request->plan_features)->mapWithKeys(function ($featureKey) {
                return [$featureKey => ['is_enabled' => true]];
            });
            $plan->features()->sync($features);
        }

        return redirect()
            ->route('admin.subscription-plans.show', $plan)
            ->with('success', 'Subscription plan created successfully!');
    }

    /**
     * Display the specified plan
     */
    public function show(SubscriptionPlan $subscriptionPlan)
    {
        $subscriptionPlan->load(['subscriptions.user']);

        // Calculate estimated revenue based on plan price and active subscriptions
        $activeSubscriptions = $subscriptionPlan->subscriptions()->where('stripe_status', 'active')->get();
        $estimatedRevenue = $activeSubscriptions->count() * $subscriptionPlan->price;

        // Calculate average subscription duration for ended subscriptions
        $endedSubscriptions = $subscriptionPlan->subscriptions()
            ->whereNotNull('ends_at')
            ->whereNotNull('created_at')
            ->get();

        $averageDuration = 0;
        if ($endedSubscriptions->count() > 0) {
            $totalDays = 0;
            foreach ($endedSubscriptions as $subscription) {
                $days = $subscription->created_at->diffInDays($subscription->ends_at);
                $totalDays += $days;
            }
            $averageDuration = round($totalDays / $endedSubscriptions->count());
        }

        $stats = [
            'total_subscriptions' => $subscriptionPlan->subscriptions()->count(),
            'active_subscriptions' => $activeSubscriptions->count(),
            'total_revenue' => $estimatedRevenue,
            'average_duration' => $averageDuration,
        ];

        return view('admin.subscription-plans.show', compact('subscriptionPlan', 'stats'));
    }

    /**
     * Show the form for editing the specified plan
     */
    public function edit(SubscriptionPlan $subscriptionPlan)
    {
        $packages = \App\Models\Package::where('status', 'published')->orderBy('name')->get();
        $courses = \App\Models\Course::where('status', 'published')->orderBy('title')->get();

        // Load features for assignment
        $functionalFeatures = \App\Models\PackageFeature::functional()->active()->orderBy('feature_name')->get();
        $displayFeatures = \App\Models\PackageFeature::display()->active()->orderBy('feature_name')->get();

        return view('admin.subscription-plans.edit', compact('subscriptionPlan', 'packages', 'courses', 'functionalFeatures', 'displayFeatures'));
    }

    /**
     * Update the specified plan
     */
    public function update(Request $request, SubscriptionPlan $subscriptionPlan)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:subscription_plans,slug,' . $subscriptionPlan->id,
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'first_month_price' => 'nullable|numeric|min:0',
            'regular_price' => 'nullable|numeric|min:0',
            'promotional_months' => 'nullable|integer|min:1',
            'currency' => 'required|string|in:USD,EUR,GBP',
            'interval' => 'required|in:month,year',
            'trial_days' => 'nullable|integer|min:0',
            'stripe_price_id' => 'nullable|string|max:255',
            'stripe_product_id' => 'nullable|string|max:255',
            'paypal_plan_id' => 'nullable|string|max:255',
            'features' => 'nullable|array',
            'features.*' => 'string',
            'included_package_ids' => 'nullable|array',
            'included_package_ids.*' => 'exists:packages,id',
            'included_course_ids' => 'nullable|array',
            'included_course_ids.*' => 'exists:courses,id',
            'is_active' => 'boolean',
        ]);

        // Auto-generate slug if not provided or changed
        if (!$request->slug || $request->slug !== $subscriptionPlan->slug) {
            $validated['slug'] = Str::slug($request->name);

            // Ensure uniqueness (excluding current plan)
            $originalSlug = $validated['slug'];
            $counter = 1;
            while (SubscriptionPlan::where('slug', $validated['slug'])->where('id', '!=', $subscriptionPlan->id)->exists()) {
                $validated['slug'] = $originalSlug . '-' . $counter++;
            }
        }

        // Set default for promotional_months if empty
        if (empty($validated['promotional_months'])) {
            $validated['promotional_months'] = 1;
        }

        // Handle is_active checkbox (defaults to false if not present)
        $validated['is_active'] = $request->has('is_active') && $request->is_active ? true : false;

        // Convert features array if provided
        if ($request->has('features') && is_array($request->features)) {
            $validated['features'] = array_values(array_filter($request->features));
        } else {
            $validated['features'] = [];
        }

        // Handle included packages
        if ($request->has('included_package_ids') && is_array($request->included_package_ids)) {
            $validated['included_package_ids'] = array_values(array_filter($request->included_package_ids));
        } else {
            $validated['included_package_ids'] = [];
        }

        // Handle included courses
        if ($request->has('included_course_ids') && is_array($request->included_course_ids)) {
            $validated['included_course_ids'] = array_values(array_filter($request->included_course_ids));
        } else {
            $validated['included_course_ids'] = [];
        }

        $subscriptionPlan->update($validated);

        // Sync plan features
        if ($request->has('plan_features')) {
            $features = collect($request->plan_features)->mapWithKeys(function ($featureKey) {
                return [$featureKey => ['is_enabled' => true]];
            });
            $subscriptionPlan->features()->sync($features);
        } else {
            // If no features selected, clear all
            $subscriptionPlan->features()->sync([]);
        }

        return redirect()
            ->route('admin.subscription-plans.show', $subscriptionPlan)
            ->with('success', 'Subscription plan updated successfully!');
    }

    /**
     * Remove the specified plan
     */
    public function destroy(SubscriptionPlan $subscriptionPlan)
    {
        // Check if plan has active subscriptions with existing users
        // We use whereHas to ensure the user still exists
        $activeSubscriptionsCount = $subscriptionPlan->subscriptions()
            ->where('stripe_status', 'active')
            ->whereHas('user')
            ->count();

        if ($activeSubscriptionsCount > 0) {
            return back()
                ->with('error', 'Cannot delete plan with active subscriptions. Deactivate it instead.');
        }

        // Unlink any remaining (inactive or orphaned) subscriptions to avoid foreign key constraint violation
        // The subscription_plan_id column is nullable, so this is safe
        $subscriptionPlan->subscriptions()->update(['subscription_plan_id' => null]);

        $subscriptionPlan->delete();

        return redirect()
            ->route('admin.subscription-plans.index')
            ->with('success', 'Subscription plan deleted successfully!');
    }

    /**
     * Toggle plan active status
     */
    public function toggleStatus(SubscriptionPlan $subscriptionPlan)
    {
        $subscriptionPlan->update(['is_active' => !$subscriptionPlan->is_active]);

        return back()->with('success', 'Plan status updated successfully!');
    }

    /**
     * Toggle plan featured status
     */
    public function toggleFeatured(SubscriptionPlan $subscriptionPlan)
    {
        $subscriptionPlan->update(['is_featured' => !$subscriptionPlan->is_featured]);

        return back()->with('success', 'Plan featured status updated successfully!');
    }

    /**
     * Reorder plans
     */
    public function reorder(Request $request)
    {
        $validated = $request->validate([
            'plans' => 'required|array',
            'plans.*.id' => 'required|exists:subscription_plans,id',
            'plans.*.sort_order' => 'required|integer|min:0',
        ]);

        foreach ($validated['plans'] as $planData) {
            SubscriptionPlan::where('id', $planData['id'])
                ->update(['sort_order' => $planData['sort_order']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Plans reordered successfully!'
        ]);
    }

    /**
     * View plan subscribers
     */
    public function subscribers(SubscriptionPlan $subscriptionPlan)
    {
        $subscribers = $subscriptionPlan->subscriptions()
            ->with('user')
            ->latest()
            ->paginate(20);

        return view('admin.subscription-plans.subscribers', compact('subscriptionPlan', 'subscribers'));
    }

    /**
     * Delete a user's subscription and remove associated enrollments
     */
    public function destroySubscription($subscriptionId)
    {
        $subscription = \Laravel\Cashier\Subscription::findOrFail($subscriptionId);

        // Get user before deletion
        $user = $subscription->user;

        // Cancel subscription in Stripe first if it's active and user still exists
        if ($user && $subscription->stripe_status === 'active' && !empty($subscription->stripe_id)) {
            try {
                $subscription->cancel();
            } catch (\Exception $e) {
                \Log::warning("Failed to cancel subscription in Stripe: " . $e->getMessage());
            }
        }

        // Remove all enrollments created by this subscription
        if ($user) {
            $enrollments = \App\Models\Enrollment::where('subscription_id', $subscription->id)->get();
            foreach ($enrollments as $enrollment) {
                \Log::info("Removing enrollment {$enrollment->id} for subscription {$subscription->id}");
                $enrollment->delete();
            }

            // Remove package access created by this subscription
            $packageAccess = \App\Models\UserPackageAccess::where('subscription_id', $subscription->id)->get();
            foreach ($packageAccess as $access) {
                \Log::info("Removing package access {$access->id} for subscription {$subscription->id}");
                $access->delete();
            }
        }

        // Delete the subscription record
        $subscription->delete();

        return redirect()
            ->back()
            ->with('success', 'Subscription and associated enrollments deleted successfully!');
    }
}
