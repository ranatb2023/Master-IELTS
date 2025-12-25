<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Cashier\Billable;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, HasApiTokens, HasRoles, SoftDeletes, LogsActivity, Billable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'avatar',
        'bio',
        'date_of_birth',
        'gender',
        'country',
        'city',
        'address',
        'timezone',
        'language',
        'stripe_customer_id',
        'is_active',
        'is_verified',
        'email_verified_at',
        'last_login_at',
        'last_login_ip',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'date_of_birth' => 'date',
            'is_active' => 'boolean',
            'is_verified' => 'boolean',
            'last_login_at' => 'datetime',
            'two_factor_confirmed_at' => 'datetime',
        ];
    }

    // Activity Log Configuration
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'email', 'is_active'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    // Relationships
    public function profile()
    {
        return $this->hasOne(UserProfile::class);
    }

    public function preferences()
    {
        return $this->hasOne(UserPreference::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }

    public function learningSessions()
    {
        return $this->hasMany(LearningSession::class);
    }

    public function createdCourses()
    {
        return $this->hasMany(Course::class, 'instructor_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function quizAttempts()
    {
        return $this->hasMany(QuizAttempt::class);
    }

    public function assignmentSubmissions()
    {
        return $this->hasMany(AssignmentSubmission::class);
    }

    public function progress()
    {
        return $this->hasMany(Progress::class);
    }

    public function activities()
    {
        return $this->hasMany(\Spatie\Activitylog\Models\Activity::class, 'causer_id');
    }

    public function userPackageAccess()
    {
        return $this->hasMany(UserPackageAccess::class);
    }

    public function userFeatureAccess()
    {
        return $this->hasMany(UserFeatureAccess::class);
    }

    /**
     * Get all Cashier subscriptions for this user
     * Using Cashier's subscriptions relationship
     */
    public function planSubscriptions()
    {
        return $this->subscriptions();
    }

    // Accessor for avatar URL
    public function getAvatarUrlAttribute()
    {
        return $this->avatar
            ? asset('storage/' . $this->avatar)
            : asset('images/default-avatar.png');
    }

    // Check if user has two-factor authentication enabled
    public function hasTwoFactorEnabled(): bool
    {
        return !is_null($this->two_factor_secret) && !is_null($this->two_factor_confirmed_at);
    }

    // Helper method to check if user is admin
    public function isAdmin(): bool
    {
        return $this->hasRole('super_admin');
    }

    // Helper method to check if user has any admin role (including custom admin roles)
    public function hasAnyAdminRole(): bool
    {
        // Get all roles except tutor and student
        $roles = $this->getRoleNames();
        return $roles->contains(function ($role) {
            return !in_array($role, ['tutor', 'student']);
        });
    }

    // Helper method to check if user is tutor
    public function isTutor(): bool
    {
        return $this->hasRole('tutor');
    }

    // Helper method to check if user is student
    public function isStudent(): bool
    {
        return $this->hasRole('student');
    }

    // Scope for active users
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope for verified users
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    // Update last login information
    public function updateLastLogin()
    {
        $this->update([
            'last_login_at' => now(),
            'last_login_ip' => request()->ip(),
        ]);
    }

    // Package and Feature Access Methods
    public function hasPackageAccess($packageId): bool
    {
        return $this->userPackageAccess()
            ->where('package_id', $packageId)
            ->where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->exists();
    }

    public function hasFeatureAccess($featureKey): bool
    {
        return $this->userFeatureAccess()
            ->where('feature_key', $featureKey)
            ->where('has_access', true)
            ->where(function ($query) {
                $query->whereNull('access_expires_at')
                    ->orWhere('access_expires_at', '>', now());
            })
            ->exists();
    }

    /**
     * Check if user has access to a feature (alias for cleaner code)
     */
    public function canAccessFeature(string $featureKey): bool
    {
        return $this->hasFeatureAccess($featureKey);
    }

    /**
     * Get user's current active subscription plan
     */
    public function getCurrentPlan()
    {
        if (!$this->subscribed('default')) {
            return null;
        }

        $subscription = $this->subscription('default');
        return $subscription && $subscription->subscription_plan_id
            ? \App\Models\SubscriptionPlan::find($subscription->subscription_plan_id)
            : null;
    }

    /**
     * Get list of locked features (features user doesn't have)
     */
    public function getLockedFeatures()
    {
        $allFeatures = \App\Models\PackageFeature::functional()->active()->get();

        return $allFeatures->filter(function ($feature) {
            return !$this->hasFeatureAccess($feature->feature_key);
        });
    }

    public function getActivePackages()
    {
        return $this->userPackageAccess()
            ->with('package')
            ->where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->get()
            ->pluck('package');
    }

    public function getActiveFeatures()
    {
        return $this->userFeatureAccess()
            ->with('feature')
            ->where('has_access', true)
            ->where(function ($query) {
                $query->whereNull('access_expires_at')
                    ->orWhere('access_expires_at', '>', now());
            })
            ->get()
            ->pluck('feature');
    }

    /**
     * Boot function to handle model events
     */
    protected static function boot()
    {
        parent::boot();

        // When a user is deleted (soft delete), clean up all related student data
        static::deleted(function ($user) {
            \Log::info("Cascade deleting related data for user ID: {$user->id}", [
                'email' => $user->email,
                'name' => $user->name,
            ]);

            // Check if this is a force delete (user already trashed)
            // If so, skip subscription operations as they were already handled during soft delete
            $isForceDelete = $user->trashed();

            // Soft delete enrollments (this will trigger enrollment cascade delete we built earlier)
            // This cascades to: quiz attempts, assignment submissions, progress, course progress
            $enrollmentsDeleted = $user->enrollments()->delete();
            \Log::info("Soft deleted {$enrollmentsDeleted} enrollments for user {$user->id}");

            // Delete learning sessions
            $learningSessionsDeleted = $user->learningSessions()->delete();
            \Log::info("Deleted {$learningSessionsDeleted} learning sessions for user {$user->id}");

            // Delete certificates
            $certificatesDeleted = $user->certificates()->delete();
            \Log::info("Deleted {$certificatesDeleted} certificates for user {$user->id}");

            // Delete user profile
            if ($user->profile) {
                $user->profile->delete();
                \Log::info("Deleted profile for user {$user->id}");
            }

            // Delete user preferences
            if ($user->preferences) {
                $user->preferences->delete();
                \Log::info("Deleted preferences for user {$user->id}");
            }

            // Only handle subscriptions during initial soft delete, not force delete
            if (!$isForceDelete) {
                // Cancel active Stripe subscriptions (Cashier)
                try {
                    if (method_exists($user, 'subscribed') && $user->subscribed('default')) {
                        $stripeSubscription = $user->subscription('default');
                        if ($stripeSubscription) {
                            $stripeSubscription->cancel();
                            \Log::info("Cancelled Stripe subscription for user {$user->id}");
                        }
                    }
                } catch (\Exception $e) {
                    \Log::warning("Could not cancel Stripe subscription for user {$user->id}: " . $e->getMessage());
                }

                // Cashier subscriptions will be automatically handled
            } else {
                \Log::info("Skipping subscription cleanup for force delete (already handled during soft delete)");
            }

            // Note: Orders, transactions, and created courses are preserved
            // - Orders & transactions: Legal/accounting requirement
            // - Created courses: Content belongs to platform

            \Log::info("Cascade delete completed for user ID: {$user->id}");
        });

        // When a user is force deleted (permanent), log the action
        static::forceDeleted(function ($user) {
            \Log::warning("User permanently deleted (force delete): {$user->id}", [
                'email' => $user->email,
                'name' => $user->name,
            ]);
        });
    }
}