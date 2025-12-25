<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\SubscriptionPlan;
use App\Models\Package;
use App\Models\Enrollment;
use App\Models\UserPackageAccess;
use Illuminate\Console\Command;

class GrantSubscriptionAccess extends Command
{
    protected $signature = 'subscription:grant-access {user_id}';
    protected $description = 'Grant course/package access to a user based on their subscription plan';

    public function handle()
    {
        $userId = $this->argument('user_id');
        $user = User::find($userId);

        if (!$user) {
            $this->error("User with ID {$userId} not found!");
            return;
        }

        $subscription = $user->subscription('default');

        if (!$subscription) {
            $this->error("User has no active subscription!");
            return;
        }

        if (!$subscription->subscription_plan_id) {
            $this->error("Subscription has no plan ID!");
            return;
        }

        $plan = SubscriptionPlan::find($subscription->subscription_plan_id);

        if (!$plan) {
            $this->error("Subscription plan not found!");
            return;
        }

        $this->info("Granting access for plan: {$plan->name}");
        $this->info("User: {$user->name} (#{$user->id})");

        $coursesEnrolled = 0;
        $packagesGranted = 0;

        // Grant access to included packages
        foreach ($plan->included_package_ids ?? [] as $packageId) {
            $package = Package::find($packageId);
            if (!$package) {
                $this->warn("Package #{$packageId} not found, skipping");
                continue;
            }

            $this->info("\nProcessing package: {$package->name}");

            // Check if access already exists
            $existingAccess = UserPackageAccess::where('user_id', $user->id)
                ->where('package_id', $packageId)
                ->where('subscription_id', $subscription->id)
                ->first();

            if (!$existingAccess) {
                $packageAccess = UserPackageAccess::create([
                    'user_id' => $user->id,
                    'package_id' => $packageId,
                    'subscription_id' => $subscription->id,
                    'access_type' => 'subscription',
                    'starts_at' => now(),
                    'is_active' => true,
                ]);

                $this->info("✓ Package access granted");
                $packagesGranted++;

                // Auto-enroll in package courses
                if ($package->auto_enroll_courses) {
                    foreach ($package->courses as $course) {
                        $enrollment = Enrollment::updateOrCreate(
                            [
                                'user_id' => $user->id,
                                'course_id' => $course->id,
                            ],
                            [
                                'package_id' => $packageId,
                                'package_access_id' => $packageAccess->id,
                                'subscription_id' => $subscription->id,
                                'enrollment_source' => 'subscription',
                                'enrolled_at' => now(),
                                'status' => 'active',
                                'payment_status' => 'paid',
                            ]
                        );

                        $this->line("  ✓ Enrolled in course: {$course->title}");
                        $coursesEnrolled++;
                    }
                } else {
                    $this->warn("  Package has auto_enroll_courses = false");
                }
            } else {
                $this->info("✓ Package access already exists");
            }
        }

        // Grant access to individual courses
        foreach ($plan->included_course_ids ?? [] as $courseId) {
            $course = \App\Models\Course::find($courseId);
            if (!$course) {
                $this->warn("Course #{$courseId} not found, skipping");
                continue;
            }

            $existingEnrollment = Enrollment::where('user_id', $user->id)
                ->where('course_id', $courseId)
                ->first();

            if (!$existingEnrollment) {
                Enrollment::create([
                    'user_id' => $user->id,
                    'course_id' => $courseId,
                    'subscription_id' => $subscription->id,
                    'enrollment_source' => 'subscription',
                    'enrolled_at' => now(),
                    'status' => 'active',
                    'payment_status' => 'paid',
                ]);

                $this->line("✓ Enrolled in course: {$course->title}");
                $coursesEnrolled++;
            } else {
                $this->info("✓ Already enrolled in: {$course->title}");
            }
        }

        $this->info("\n=== Summary ===");
        $this->info("Packages granted: {$packagesGranted}");
        $this->info("Courses enrolled: {$coursesEnrolled}");
        $this->info("\n✅ Done! User can now access their courses.");
    }
}
