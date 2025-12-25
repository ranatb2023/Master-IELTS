<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Laravel\Cashier\Subscription;
use App\Models\Feature;
use App\Models\SubscriptionPlanFeature;

class SubscriptionPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'first_month_price',
        'regular_price',
        'promotional_months',
        'tiered_pricing',
        'currency',
        'interval',
        'trial_days',
        'stripe_price_id',
        'stripe_product_id',
        'stripe_prices',
        'paypal_plan_id',
        'features',
        'included_package_ids',
        'included_course_ids',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'first_month_price' => 'decimal:2',
            'regular_price' => 'decimal:2',
            'promotional_months' => 'integer',
            'tiered_pricing' => 'array',
            'trial_days' => 'integer',
            'stripe_prices' => 'array',
            'features' => 'array',
            'included_package_ids' => 'array',
            'included_course_ids' => 'array',
            'is_active' => 'boolean',
        ];
    }

    // Relationships
    /**
     * User subscriptions using this plan (Cashier subscriptions)
     */
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class, 'subscription_plan_id');
    }

    /**
     * Alias for subscriptions() - for backward compatibility
     */
    public function planSubscriptions()
    {
        return $this->subscriptions();
    }

    public function includedPackages()
    {
        return Package::whereIn('id', $this->included_package_ids ?? [])->get();
    }

    public function includedCourses()
    {
        return Course::whereIn('id', $this->included_course_ids ?? [])->get();
    }

    /**
     * Get features included in this plan
     */
    public function features()
    {
        return $this->belongsToMany(
            PackageFeature::class,
            'subscription_plan_features',
            'subscription_plan_id',
            'feature_key',
            'id',
            'feature_key'
        )->withPivot('is_enabled')
            ->withTimestamps();
    }

    /**
     * Get only enabled features
     */
    public function enabledFeatures()
    {
        return $this->features()->wherePivot('is_enabled', true);
    }

    /**
     * Check if plan has specific feature
     */
    public function hasFeature(string $featureKey): bool
    {
        return $this->features()
            ->where('package_features.feature_key', $featureKey)
            ->wherePivot('is_enabled', true)
            ->exists();
    }

    /**
     * Get functional features for access control
     */
    public function functionalFeatures()
    {
        return $this->enabledFeatures()->where('type', 'functional');
    }

    /**
     * Get display features for UI badges
     */
    public function displayFeatures()
    {
        return $this->enabledFeatures()->where('type', 'display');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Helper methods
    public function isActive(): bool
    {
        return $this->is_active;
    }

    public function getFormattedPriceAttribute()
    {
        $currencySymbols = [
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
        ];

        $symbol = $currencySymbols[$this->currency] ?? $this->currency . ' ';
        return $symbol . number_format($this->price, 2);
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Get price for specific month
     */
    public function getPriceForMonth($monthNumber)
    {
        // First month promotional price
        if ($monthNumber <= $this->promotional_months) {
            return $this->first_month_price ?? $this->regular_price;
        }

        // Check tiered pricing
        if ($this->tiered_pricing) {
            foreach ($this->tiered_pricing as $tier) {
                if ($monthNumber >= $tier['from_month'] && $monthNumber <= $tier['to_month']) {
                    return $tier['price'];
                }
            }
        }

        // Regular price
        return $this->regular_price;
    }

    /**
     * Create Stripe subscription using Cashier
     */
    public function createSubscriptionForUser(User $user, $paymentMethod)
    {
        return DB::transaction(function () use ($user, $paymentMethod) {
            // Create subscription with promotional pricing
            $subscriptionBuilder = $user->newSubscription('default', $this->stripe_price_id);

            // Only add trial days if they exist
            if ($this->trial_days && $this->trial_days > 0) {
                $subscriptionBuilder->trialDays($this->trial_days);
            }

            // Create Cashier subscription
            $cashierSubscription = $subscriptionBuilder->create($paymentMethod, [
                'metadata' => [
                    'subscription_plan_id' => $this->id,
                    'first_month_price' => $this->first_month_price,
                ],
            ]);

            // Update the subscription record with our custom fields
            $cashierSubscription->update([
                'subscription_plan_id' => $this->id,
                'payment_method' => 'stripe',
                'metadata' => json_encode([
                    'subscription_plan_id' => $this->id,
                    'first_month_price' => $this->first_month_price,
                    'plan_name' => $this->name,
                ]),
            ]);

            // Grant access to included packages and courses
            $this->grantSubscriptionAccess($user, $cashierSubscription);

            \Log::info("Subscription created successfully", [
                'subscription_id' => $cashierSubscription->id,
                'user_id' => $user->id,
                'plan_id' => $this->id
            ]);

            // Return Cashier Subscription
            return $cashierSubscription;
        });
    }

    /**
     * Grant subscription access
     */
    public function grantSubscriptionAccess(User $user, Subscription $cashierSubscription)
    {
        // Grant access to included packages
        foreach ($this->included_package_ids ?? [] as $packageId) {
            $package = Package::find($packageId);
            if ($package) {
                $packageAccess = UserPackageAccess::create([
                    'user_id' => $user->id,
                    'package_id' => $packageId,
                    'subscription_id' => $cashierSubscription->id,
                    'access_type' => 'subscription',
                    'starts_at' => now(),
                    'is_active' => true,
                ]);

                // Auto-enroll in package courses
                if ($package->auto_enroll_courses) {
                    \Log::info("Auto-enrolling courses for package {$packageId}");

                    foreach ($package->courses as $course) {
                        $enrollment = Enrollment::updateOrCreate(
                            [
                                'user_id' => $user->id,
                                'course_id' => $course->id,
                            ],
                            [
                                'package_id' => $packageId,
                                'package_access_id' => $packageAccess->id,
                                'subscription_id' => $cashierSubscription->id,
                                'enrollment_source' => 'subscription',
                                'enrolled_at' => now(),
                                'status' => 'active',
                                'payment_status' => 'paid',
                            ]
                        );

                        // Send enrollment notification to student
                        $user->notify(new \App\Notifications\CourseEnrolledNotification($course, $enrollment));

                        // Notify admins about new enrollment
                        $admins = User::role('super_admin')->get();
                        foreach ($admins as $admin) {
                            $admin->notify(new \App\Notifications\NewEnrollmentNotification($enrollment));
                        }

                        // Notify course instructor about new student
                        if ($course->instructor) {
                            $course->instructor->notify(new \App\Notifications\NewStudentEnrolledNotification($course, $user, $enrollment));
                        }

                        \Log::info("Enrolled user in course", [
                            'user_id' => $user->id,
                            'course_id' => $course->id,
                            'enrollment_id' => $enrollment->id,
                            'subscription_id' => $cashierSubscription->id
                        ]);
                    }
                } else {
                    \Log::warning("Package {$packageId} has auto_enroll_courses = false");
                }
            }
        }

        // Grant access to individual courses
        foreach ($this->included_course_ids ?? [] as $courseId) {
            $course = \App\Models\Course::find($courseId);
            $enrollment = Enrollment::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'course_id' => $courseId,
                ],
                [
                    'subscription_id' => $cashierSubscription->id,
                    'enrollment_source' => 'subscription',
                    'enrolled_at' => now(),
                    'status' => 'active',
                    'payment_status' => 'paid',
                ]
            );

            // Send enrollment notification to student
            if ($course) {
                $user->notify(new \App\Notifications\CourseEnrolledNotification($course, $enrollment));

                // Notify admins about new enrollment
                $admins = User::role('super_admin')->get();
                foreach ($admins as $admin) {
                    $admin->notify(new \App\Notifications\NewEnrollmentNotification($enrollment));
                }

                // Notify course instructor about new student
                if ($course->instructor) {
                    $course->instructor->notify(new \App\Notifications\NewStudentEnrolledNotification($course, $user, $enrollment));
                }
            }
        }
    }

    /**
     * Get effective price (first month or regular)
     */
    public function getEffectivePriceAttribute()
    {
        return $this->first_month_price ?? $this->regular_price ?? $this->price;
    }

    /**
     * Check if has promotional pricing
     */
    public function hasPromotionalPricing(): bool
    {
        return !is_null($this->first_month_price) && $this->first_month_price < $this->regular_price;
    }
}
