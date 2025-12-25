<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserFeatureAccess extends Model
{
    protected $fillable = [
        'user_id',
        'package_id',
        'subscription_id',
        'feature_key',
        'has_access',
        'access_granted_at',
        'access_expires_at',
    ];

    protected function casts(): array
    {
        return [
            'has_access' => 'boolean',
            'access_granted_at' => 'datetime',
            'access_expires_at' => 'datetime',
        ];
    }

    /**
     * Get the user that owns the feature access
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the package that granted this access
     */
    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    /**
     * Get the subscription that granted this access
     */
    public function subscription()
    {
        return $this->belongsTo(UserSubscription::class, 'subscription_id');
    }

    /**
     * Get the feature details
     */
    public function feature()
    {
        return $this->belongsTo(PackageFeature::class, 'feature_key', 'feature_key');
    }

    /**
     * Check if access is still valid (not expired)
     */
    public function isValid(): bool
    {
        if (!$this->has_access) {
            return false;
        }

        if ($this->access_expires_at === null) {
            return true; // Lifetime access
        }

        return $this->access_expires_at->isFuture();
    }

    /**
     * Revoke access
     */
    public function revoke()
    {
        $this->update(['has_access' => false]);
    }

    /**
     * Extend access expiration
     */
    public function extend($days)
    {
        if ($this->access_expires_at) {
            $this->update([
                'access_expires_at' => $this->access_expires_at->addDays($days),
            ]);
        }
    }

    /**
     * Scope to only active/valid access
     */
    public function scopeActive($query)
    {
        return $query->where('has_access', true)
            ->where(function ($q) {
                $q->whereNull('access_expires_at')
                  ->orWhere('access_expires_at', '>', now());
            });
    }

    /**
     * Scope to expired access
     */
    public function scopeExpired($query)
    {
        return $query->where('has_access', true)
            ->whereNotNull('access_expires_at')
            ->where('access_expires_at', '<=', now());
    }
}
