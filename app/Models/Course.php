<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Course extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'title',
        'slug',
        'subtitle',
        'description',
        'short_description',
        'instructor_id',
        'level',
        'language',
        'thumbnail',
        'preview_video',
        'price',
        'sale_price',
        'currency',
        'is_free',
        'is_featured',
        'duration_hours',
        'total_lectures',
        'total_quizzes',
        'total_assignments',
        'enrollment_limit',
        'enrolled_count',
        'average_rating',
        'total_reviews',
        'completion_rate',
        'requirements',
        'learning_outcomes',
        'target_audience',
        'features',
        'status',
        'visibility',
        'certificate_enabled',
        'certificate_template_id',
        'drip_content',
        'drip_schedule',
        'published_at',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'allow_single_purchase',
        'package_only',
        'single_purchase_price',
        'allowed_in_packages',
        'auto_enroll_enabled',
    ];

    protected function casts(): array
    {
        return [
            // JSON fields
            'requirements' => 'array',
            'learning_outcomes' => 'array',
            'target_audience' => 'array',
            'features' => 'array',
            'drip_schedule' => 'array',

            // Decimal fields
            'price' => 'decimal:2',
            'sale_price' => 'decimal:2',
            'single_purchase_price' => 'decimal:2',
            'duration_hours' => 'decimal:2',
            'average_rating' => 'decimal:2',
            'completion_rate' => 'decimal:2',

            // Integer fields
            'total_lectures' => 'integer',
            'total_quizzes' => 'integer',
            'total_assignments' => 'integer',
            'enrollment_limit' => 'integer',
            'enrolled_count' => 'integer',
            'total_reviews' => 'integer',

            // Boolean fields
            'is_free' => 'boolean',
            'is_featured' => 'boolean',
            'certificate_enabled' => 'boolean',
            'drip_content' => 'boolean',
            'allow_single_purchase' => 'boolean',
            'package_only' => 'boolean',
            'auto_enroll_enabled' => 'boolean',
            'allowed_in_packages' => 'array',

            // Date fields
            'published_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    // Activity Log Configuration
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'title',
                'status',
                'visibility',
                'price',
                'sale_price',
                'is_free',
                'published_at'
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    // ========== Relationships ==========

    /**
     * Get the primary instructor of the course.
     */
    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    /**
     * Get the primary category of the course (first category).
     * Note: This is an accessor, not a relationship. Use courseCategories() for the actual relationship.
     * This is for backward compatibility with views that expect a single category.
     */
    public function getCategoryAttribute()
    {
        // Check if courseCategories relationship is already loaded
        if ($this->relationLoaded('courseCategories')) {
            return $this->courseCategories->first();
        }

        // Otherwise, query for the first category
        return $this->courseCategories()->first();
    }

    /**
     * Get the certificate template for the course.
     */
    public function certificateTemplate()
    {
        return $this->belongsTo(CertificateTemplate::class);
    }

    /**
     * Get all topics for the course.
     */
    public function topics()
    {
        return $this->hasMany(Topic::class)->orderBy('order');
    }

    /**
     * Get all packages that include this course.
     */
    public function packages()
    {
        return $this->belongsToMany(Package::class, 'package_courses', 'course_id', 'package_id')
            ->withTimestamps();
    }

    /**
     * Get all course categories through pivot table (many-to-many).
     */
    public function courseCategories()
    {
        return $this->belongsToMany(CourseCategory::class, 'course_category', 'course_id', 'course_category_id');
    }

    /**
     * Get all course tags through pivot table (many-to-many).
     */
    public function courseTags()
    {
        return $this->belongsToMany(CourseTag::class, 'course_tag', 'course_id', 'course_tag_id');
    }

    /**
     * Legacy: Get all categories through pivot table (many-to-many).
     * @deprecated Use courseCategories() instead
     */
    public function categories()
    {
        return $this->courseCategories();
    }

    /**
     * Legacy: Get all tags through pivot table (many-to-many).
     * @deprecated Use courseTags() instead
     */
    public function tags()
    {
        return $this->courseTags();
    }

    /**
     * Get all course instructors (co-instructors).
     */
    public function courseInstructors()
    {
        return $this->hasMany(CourseInstructor::class);
    }

    /**
     * Get co-instructors through course_instructors table.
     */
    public function coInstructors()
    {
        return $this->hasManyThrough(
            User::class,
            CourseInstructor::class,
            'course_id',
            'id',
            'id',
            'user_id'
        )->where('role', 'co_instructor');
    }

    /**
     * Get all instructors (primary + co-instructors).
     */
    public function instructors()
    {
        return $this->courseInstructors();
    }

    /**
     * Get all resources for the course.
     */
    public function resources()
    {
        return $this->hasMany(CourseResource::class)->orderBy('order');
    }

    /**
     * Get all enrollments for the course.
     */
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    /**
     * Get all active enrollments.
     */
    public function activeEnrollments()
    {
        return $this->hasMany(Enrollment::class)->where('status', 'active');
    }

    /**
     * Get all reviews (polymorphic).
     */
    public function reviews()
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    /**
     * Get all wishlists (polymorphic).
     */
    public function wishlists()
    {
        return $this->morphMany(Wishlist::class, 'wishable');
    }

    /**
     * Get all students enrolled in the course.
     */
    public function students()
    {
        return $this->hasManyThrough(
            User::class,
            Enrollment::class,
            'course_id',
            'id',
            'id',
            'user_id'
        );
    }

    /**
     * Get all quizzes through topics.
     */
    public function quizzes()
    {
        return $this->hasManyThrough(
            Quiz::class,
            Topic::class,
            'course_id',
            'topic_id',
            'id',
            'id'
        );
    }

    /**
     * Get all assignments through topics.
     */
    public function assignments()
    {
        return $this->hasManyThrough(
            Assignment::class,
            Topic::class,
            'course_id',
            'topic_id',
            'id',
            'id'
        );
    }

    // ========== Scopes ==========

    /**
     * Scope a query to only include published courses.
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    /**
     * Scope a query to only include active courses.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'published')
            ->where('visibility', 'public');
    }

    /**
     * Scope a query to only include free courses.
     */
    public function scopeFree($query)
    {
        return $query->where('is_free', true);
    }

    /**
     * Scope a query to only include paid courses.
     */
    public function scopePaid($query)
    {
        return $query->where('is_free', false);
    }

    /**
     * Scope a query to filter courses by level.
     */
    public function scopeByLevel($query, $level)
    {
        return $query->where('level', $level);
    }

    /**
     * Scope a query to filter courses by category.
     */
    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Scope a query to filter courses by instructor.
     */
    public function scopeByInstructor($query, $instructorId)
    {
        return $query->where('instructor_id', $instructorId);
    }

    /**
     * Scope a query to filter courses by language.
     */
    public function scopeByLanguage($query, $language)
    {
        return $query->where('language', $language);
    }

    /**
     * Scope a query to filter courses by status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to filter courses by visibility.
     */
    public function scopeByVisibility($query, $visibility)
    {
        return $query->where('visibility', $visibility);
    }

    /**
     * Scope a query to filter courses with available enrollment slots.
     */
    public function scopeAvailableForEnrollment($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('enrollment_limit')
                ->orWhereColumn('enrolled_count', '<', 'enrollment_limit');
        });
    }

    /**
     * Scope a query to filter featured courses (high rating).
     */
    public function scopeFeatured($query)
    {
        return $query->where('average_rating', '>=', 4.5)
            ->where('total_reviews', '>=', 10);
    }

    /**
     * Scope a query to search courses by keyword.
     */
    public function scopeSearch($query, $keyword)
    {
        return $query->where(function ($q) use ($keyword) {
            $q->where('title', 'like', "%{$keyword}%")
                ->orWhere('description', 'like', "%{$keyword}%")
                ->orWhere('short_description', 'like', "%{$keyword}%");
        });
    }

    /**
     * Scope a query to order by popularity.
     */
    public function scopePopular($query)
    {
        return $query->orderBy('enrolled_count', 'desc');
    }

    /**
     * Scope a query to order by rating.
     */
    public function scopeTopRated($query)
    {
        return $query->orderBy('average_rating', 'desc')
            ->orderBy('total_reviews', 'desc');
    }

    /**
     * Scope a query to order by newest.
     */
    public function scopeNewest($query)
    {
        return $query->orderBy('published_at', 'desc');
    }

    // ========== Accessors ==========

    /**
     * Get the formatted price.
     */
    public function getFormattedPriceAttribute()
    {
        if ($this->is_free) {
            return 'Free';
        }

        $price = $this->sale_price ?? $this->price;

        return $this->formatCurrency($price);
    }

    /**
     * Get the original formatted price.
     */
    public function getOriginalPriceAttribute()
    {
        return $this->formatCurrency($this->price);
    }

    /**
     * Get the sale formatted price.
     */
    public function getSalePriceFormattedAttribute()
    {
        return $this->sale_price ? $this->formatCurrency($this->sale_price) : null;
    }

    /**
     * Check if course has a sale.
     */
    public function getHasSaleAttribute()
    {
        return !is_null($this->sale_price) && $this->sale_price < $this->price;
    }

    /**
     * Get the discount percentage.
     */
    public function getDiscountPercentageAttribute()
    {
        if (!$this->has_sale || $this->price <= 0) {
            return 0;
        }

        return round((($this->price - $this->sale_price) / $this->price) * 100);
    }

    /**
     * Get the effective price (sale price or regular price).
     */
    public function getEffectivePriceAttribute()
    {
        return $this->sale_price ?? $this->price;
    }

    /**
     * Get the thumbnail URL.
     */
    public function getThumbnailUrlAttribute()
    {
        return $this->thumbnail
            ? asset('storage/' . $this->thumbnail)
            : asset('images/default-course.png');
    }

    /**
     * Get the preview video URL.
     */
    public function getPreviewVideoUrlAttribute()
    {
        return $this->preview_video
            ? asset('storage/' . $this->preview_video)
            : null;
    }

    /**
     * Check if course is published.
     */
    public function getIsPublishedAttribute()
    {
        return $this->status === 'published'
            && !is_null($this->published_at)
            && $this->published_at <= now();
    }

    /**
     * Check if course is enrollable.
     */
    public function getIsEnrollableAttribute()
    {
        if (!$this->is_published) {
            return false;
        }

        if (is_null($this->enrollment_limit)) {
            return true;
        }

        return $this->enrolled_count < $this->enrollment_limit;
    }

    /**
     * Get remaining enrollment slots.
     */
    public function getRemainingEnrollmentSlotsAttribute()
    {
        if (is_null($this->enrollment_limit)) {
            return null;
        }

        return max(0, $this->enrollment_limit - $this->enrolled_count);
    }

    /**
     * Check if enrollment is full.
     */
    public function getIsEnrollmentFullAttribute()
    {
        if (is_null($this->enrollment_limit)) {
            return false;
        }

        return $this->enrolled_count >= $this->enrollment_limit;
    }

    // ========== Helper Methods ==========

    /**
     * Format currency value.
     */
    protected function formatCurrency($amount)
    {
        $currencySymbols = [
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            'INR' => '₹',
        ];

        $symbol = $currencySymbols[$this->currency] ?? $this->currency . ' ';

        return $symbol . number_format($amount, 2);
    }

    /**
     * Check if a user has purchased this course.
     */
    public function isPurchased($userId = null)
    {
        $userId = $userId ?? auth()->id();

        if (!$userId) {
            return false;
        }

        return $this->enrollments()
            ->where('user_id', $userId)
            ->whereIn('status', ['active', 'completed'])
            ->exists();
    }

    /**
     * Check if a user is enrolled in this course.
     */
    public function isEnrolled($userId = null)
    {
        return $this->isPurchased($userId);
    }

    /**
     * Check if a user is on the instructor team.
     */
    public function isInstructedBy($userId = null)
    {
        $userId = $userId ?? auth()->id();

        if (!$userId) {
            return false;
        }

        if ($this->instructor_id == $userId) {
            return true;
        }

        return $this->courseInstructors()
            ->where('user_id', $userId)
            ->exists();
    }

    /**
     * Check if a user has wishlisted this course.
     */
    public function isWishlisted($userId = null)
    {
        $userId = $userId ?? auth()->id();

        if (!$userId) {
            return false;
        }

        return $this->wishlists()
            ->where('user_id', $userId)
            ->exists();
    }

    /**
     * Increment enrolled count.
     */
    public function incrementEnrolledCount()
    {
        $this->increment('enrolled_count');
    }

    /**
     * Decrement enrolled count.
     */
    public function decrementEnrolledCount()
    {
        if ($this->enrolled_count > 0) {
            $this->decrement('enrolled_count');
        }
    }

    /**
     * Update average rating and total reviews.
     */
    public function updateRatings()
    {
        $reviews = $this->reviews()->get();

        $this->update([
            'average_rating' => $reviews->avg('rating') ?? 0,
            'total_reviews' => $reviews->count(),
        ]);
    }

    /**
     * Publish the course.
     */
    public function publish()
    {
        $this->update([
            'status' => 'published',
            'published_at' => now(),
        ]);
    }

    /**
     * Unpublish the course.
     */
    public function unpublish()
    {
        $this->update([
            'status' => 'draft',
        ]);
    }

    /**
     * Archive the course.
     */
    public function archive()
    {
        $this->update([
            'status' => 'archived',
        ]);
    }

    /**
     * Get the route key name for Laravel.
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Check if course can be purchased individually
     */
    public function canBePurchasedIndividually(): bool
    {
        return $this->allow_single_purchase && !$this->package_only;
    }

    /**
     * Get packages that include this course
     */
    public function availablePackages()
    {
        return $this->packages()
            ->where('status', 'published')
            ->where(function ($query) {
                $query->whereNull('access_expires_at')
                    ->orWhere('access_expires_at', '>', now());
            });
    }

    /**
     * Check if user can access course
     */
    public function userCanAccess(User $user): bool
    {
        // Direct enrollment check
        if ($user->enrollments()->where('course_id', $this->id)->where('status', 'active')->exists()) {
            return true;
        }

        // Package access check
        $packageIds = $this->packages()->pluck('packages.id');
        return $user->userPackageAccess()
            ->whereIn('package_id', $packageIds)
            ->where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->exists();
    }

    /**
     * Get effective purchase price
     */
    public function getEffectivePurchasePrice()
    {
        return $this->single_purchase_price ?? $this->sale_price ?? $this->price;
    }
}
