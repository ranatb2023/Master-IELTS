<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPackageAccess extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'package_id',
        'order_id',
        'subscription_id',
        'access_type',
        'starts_at',
        'expires_at',
        'is_active',
        'features_access',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'expires_at' => 'datetime',
            'is_active' => 'boolean',
            'features_access' => 'array',
        ];
    }

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function subscription()
    {
        return $this->belongsTo(\Laravel\Cashier\Subscription::class, 'subscription_id');
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class, 'package_access_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where('starts_at', '<=', now())
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            });
    }

    public function scopeExpired($query)
    {
        return $query->whereNotNull('expires_at')
            ->where('expires_at', '<=', now());
    }

    // Methods
    public function isActive(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->starts_at && $this->starts_at->isFuture()) {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        return true;
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function grantAccess(array $features = null)
    {
        $this->update([
            'is_active' => true,
            'starts_at' => now(),
            'features_access' => $features,
        ]);

        return $this;
    }

    public function revokeAccess()
    {
        $this->update([
            'is_active' => false,
            'expires_at' => now(),
        ]);

        return $this;
    }

    public function hasFeatureAccess(string $feature): bool
    {
        if (!$this->isActive()) {
            return false;
        }

        if (empty($this->features_access)) {
            return true;
        }

        return in_array($feature, $this->features_access);
    }
}
