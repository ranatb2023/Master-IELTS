<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PackageFeature extends Model
{
    protected $fillable = [
        'feature_key',
        'feature_name',
        'description',
        'type',
        'is_active',
        'implementation_details',
    ];

    protected function casts(): array
    {
        return [
            'implementation_details' => 'array',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Check if feature is functional (not just display)
     */
    public function isFunctional(): bool
    {
        return $this->type === 'functional' && $this->is_active;
    }

    /**
     * Grant feature access to user
     */
    public function grantAccessToUser(User $user, $packageId = null, $subscriptionId = null, $expiresAt = null)
    {
        return UserFeatureAccess::updateOrCreate(
            [
                'user_id' => $user->id,
                'feature_key' => $this->feature_key,
                'package_id' => $packageId,
                'subscription_id' => $subscriptionId,
            ],
            [
                'has_access' => true,
                'access_granted_at' => now(),
                'access_expires_at' => $expiresAt,
            ]
        );
    }

    /**
     * Get all users with access to this feature
     */
    public function userAccess()
    {
        return $this->hasMany(UserFeatureAccess::class, 'feature_key', 'feature_key');
    }

    /**
     * Get subscription plans that include this feature
     */
    public function subscriptionPlans()
    {
        return $this->belongsToMany(
            SubscriptionPlan::class,
            'subscription_plan_features',
            'feature_key',
            'subscription_plan_id',
            'feature_key',
            'id'
        )->withPivot('is_enabled')
            ->withTimestamps();
    }

    /**
     * Scope to only active features
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to only functional features
     */
    public function scopeFunctional($query)
    {
        return $query->where('type', 'functional');
    }

    /**
     * Scope to only display features
     */
    public function scopeDisplay($query)
    {
        return $query->where('type', 'display');
    }
}
