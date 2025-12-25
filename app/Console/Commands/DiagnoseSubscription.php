<?php

namespace App\Console\Commands;

use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Console\Command;

class DiagnoseSubscription extends Command
{
    protected $signature = 'subscription:diagnose {email}';
    protected $description = 'Diagnose subscription and enrollment issues for a user';

    public function handle()
    {
        $email = $this->argument('email');
        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("User with email {$email} not found!");
            return;
        }

        $this->info("=== User Information ===");
        $this->info("ID: {$user->id}");
        $this->info("Name: {$user->name}");
        $this->info("Email: {$user->email}");

        $this->info("\n=== Subscription Status ===");
        $subscription = $user->subscription('default');

        if ($subscription) {
            $this->info("✓ Has active subscription");
            $this->info("Subscription ID: {$subscription->id}");
            $this->info("Stripe Status: {$subscription->stripe_status}");
            $this->info("Subscription Plan ID: {$subscription->subscription_plan_id}");

            if ($subscription->subscription_plan_id) {
                $plan = SubscriptionPlan::find($subscription->subscription_plan_id);
                if ($plan) {
                    $this->info("Plan Name: {$plan->name}");
                    $this->info("Included Course IDs: " . json_encode($plan->included_course_ids));
                    $this->info("Included Package IDs: " . json_encode($plan->included_package_ids));
                }
            }
        } else {
            $this->warn("✗ No active subscription found");
        }

        $this->info("\n=== Enrollments ===");
        $enrollments = $user->enrollments;
        $this->info("Total Enrollments: {$enrollments->count()}");

        foreach ($enrollments as $enrollment) {
            $this->line("- Course: {$enrollment->course->title} (Status: {$enrollment->status})");
        }

        $this->info("\n=== Package Access ===");
        $packageAccess = $user->userPackageAccess()->where('is_active', true)->get();
        $this->info("Active Package Access: {$packageAccess->count()}");

        foreach ($packageAccess as $access) {
            $package = $access->package;
            $this->line("- Package: {$package->name} (ID: {$package->id})");
        }

        $this->info("\n=== Recommendations ===");
        if ($subscription && $subscription->subscription_plan_id) {
            $plan = SubscriptionPlan::find($subscription->subscription_plan_id);
            if ($plan) {
                if (empty($plan->included_course_ids) && empty($plan->included_package_ids)) {
                    $this->warn("⚠ The subscription plan has NO courses or packages assigned!");
                    $this->warn("Go to Admin → Subscription Plans → Edit '{$plan->name}' and add courses/packages");
                } elseif ($enrollments->count() === 0) {
                    $this->warn("⚠ Plan has courses/packages but user has NO enrollments!");
                    $this->warn("Run: php artisan subscription:grant-access {$user->id}");
                }
            }
        }
    }
}
