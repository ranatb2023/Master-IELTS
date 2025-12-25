<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\UserFeatureAccess;
use App\Models\Order;
use App\Models\OrderItem;

class Package extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'sale_price',
        'features',
        'display_features',
        'functional_features',
        'auto_enroll_courses',
        'has_quiz_feature',
        'has_tutor_support',
        'duration_days',
        'is_lifetime',
        'access_expires_at',
        'is_featured',
        'is_subscription_package',
        'subscription_plan_ids',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'sale_price' => 'decimal:2',
            'features' => 'array',
            'display_features' => 'array',
            'functional_features' => 'array',
            'auto_enroll_courses' => 'boolean',
            'has_quiz_feature' => 'boolean',
            'has_tutor_support' => 'boolean',
            'duration_days' => 'integer',
            'is_lifetime' => 'boolean',
            'access_expires_at' => 'datetime',
            'is_featured' => 'boolean',
            'is_subscription_package' => 'boolean',
            'subscription_plan_ids' => 'array',
        ];
    }

    // Relationships
    public function courses()
    {
        return $this->belongsToMany(Course::class, 'package_courses', 'package_id', 'course_id')
            ->withPivot('sort_order')
            ->orderBy('sort_order');
    }

    public function packageCourses()
    {
        return $this->hasMany(PackageCourse::class);
    }

    public function userAccess()
    {
        return $this->hasMany(UserPackageAccess::class);
    }

    /**
     * Get features included in this package (via pivot table)
     */
    public function features()
    {
        return $this->belongsToMany(
            PackageFeature::class,
            'package_package_features',
            'package_id',
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
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'published')
            ->where('is_featured', true);
    }

    public function scopeLifetime($query)
    {
        return $query->where('is_lifetime', true);
    }

    public function scopeTimeLimited($query)
    {
        return $query->where('is_lifetime', false);
    }

    // Helper methods
    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    public function isFeatured(): bool
    {
        return $this->is_featured;
    }

    public function getEffectivePriceAttribute()
    {
        return $this->sale_price ?? $this->price;
    }

    public function getHasSaleAttribute()
    {
        return !is_null($this->sale_price) && $this->sale_price < $this->price;
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Get all features (display + functional)
     */
    public function getAllFeatures(): array
    {
        return array_merge(
            $this->display_features ?? [],
            $this->functional_features ?? []
        );
    }

    /**
     * Check if feature is available in package
     */
    public function hasFeature($featureKey, $checkFunctional = true): bool
    {
        // Check in pivot table first (modern approach)
        $inPivot = $this->features()
            ->where('package_features.feature_key', $featureKey)
            ->wherePivot('is_enabled', true)
            ->exists();

        if ($inPivot) {
            return true;
        }

        // Fallback to JSON fields for backwards compatibility during transition
        if ($checkFunctional) {
            return in_array($featureKey, $this->functional_features ?? []);
        }
        return in_array($featureKey, $this->getAllFeatures());
    }

    /**
     * Process package purchase
     */
    public function processPurchase(User $user, Order $order)
    {
        // Create package access record
        $access = UserPackageAccess::create([
            'user_id' => $user->id,
            'package_id' => $this->id,
            'order_id' => $order->id,
            'access_type' => 'purchase',
            'starts_at' => now(),
            'expires_at' => $this->is_lifetime ? null : now()->addDays($this->duration_days),
            'is_active' => true,
            'features_access' => $this->functional_features,
        ]);

        // Auto-enroll in courses
        if ($this->auto_enroll_courses) {
            $this->enrollUserInCourses($user, $access);
        }

        // Grant feature access
        $this->grantFeatureAccess($user, $access);

        return $access;
    }

    /**
     * Enroll user in all package courses
     */
    protected function enrollUserInCourses(User $user, UserPackageAccess $access)
    {
        foreach ($this->courses as $course) {
            Enrollment::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'course_id' => $course->id,
                ],
                [
                    'package_access_id' => $access->id,
                    'enrollment_source' => 'package',
                    'enrolled_at' => now(),
                    'expires_at' => $access->expires_at,
                    'status' => 'active',
                    'payment_status' => 'paid',
                ]
            );
        }
    }

    /**
     * Grant feature access to user (UPDATED to use modern system)
     */
    protected function grantFeatureAccess(User $user, UserPackageAccess $access)
    {
        // Get functional features from pivot table
        $functionalFeatures = $this->functionalFeatures()->get();

        foreach ($functionalFeatures as $feature) {
            $feature->grantAccessToUser(
                $user,
                packageId: $this->id,
                subscriptionId: null,
                expiresAt: $access->expires_at
            );
        }

        \Log::info("Granted package features", [
            'package_id' => $this->id,
            'user_id' => $user->id,
            'feature_count' => $functionalFeatures->count(),
            'features' => $functionalFeatures->pluck('feature_key')->toArray()
        ]);
    }

    /**
     * Check if package has expired
     */
    public function isExpired(): bool
    {
        if ($this->is_lifetime) {
            return false;
        }

        return $this->access_expires_at && $this->access_expires_at->isPast();
    }

    /**
     * Process package upgrade (user already has another package)
     */
    public function processUpgrade(User $user, Order $order, UserPackageAccess $oldAccess)
    {
        \Log::info('Processing package upgrade', [
            'user_id' => $user->id,
            'old_package_id' => $oldAccess->package_id,
            'new_package_id' => $this->id,
        ]);

        // Deactivate old package access
        $oldAccess->update(['is_active' => false]);

        // Rev oke old package features
        $this->revokePackageFeatures($user, $oldAccess);

        // Create new package access (using existing processPurchase method)
        $newAccess = $this->processPurchase($user, $order);

        \Log::info('Package upgrade completed', [
            'user_id' => $user->id,
            'new_access_id' => $newAccess->id,
        ]);

        return $newAccess;
    }

    /**
     * Revoke features granted by a package
     */
    protected function revokePackageFeatures(User $user, UserPackageAccess $access)
    {
        // Get the old package
        $oldPackage = $access->package;

        if (!$oldPackage) {
            \Log::warning('Old package not found for access', ['access_id' => $access->id]);
            return;
        }

        // Get functional features from the old package
        $functionalFeatures = $oldPackage->functionalFeatures()->get();

        \Log::info('Revoking package features', [
            'package_id' => $oldPackage->id,
            'user_id' => $user->id,
            'feature_count' => $functionalFeatures->count(),
        ]);

        // Revoke each functional feature granted by this package
        foreach ($functionalFeatures as $feature) {
            UserFeatureAccess::where('user_id', $user->id)
                ->where('feature_key', $feature->feature_key)
                ->where('package_id', $oldPackage->id)
                ->delete();
        }

        \Log::info('Package features revoked', [
            'package_id' => $oldPackage->id,
            'user_id' => $user->id,
        ]);
    }

    /**
     * Get total course count
     */
    public function getTotalCoursesAttribute()
    {
        return $this->courses()->count();
    }
}
