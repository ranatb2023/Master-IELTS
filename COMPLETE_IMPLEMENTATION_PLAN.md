# 🚀 **COMPLETE IMPLEMENTATION PLAN**
## Packages, Subscriptions & Course Purchase System with Laravel Cashier

---

## **📋 TABLE OF CONTENTS**
1. [New Requirements Analysis](#new-requirements-analysis)
2. [Database Schema Updates](#database-schema-updates)
3. [Laravel Cashier Integration](#laravel-cashier-integration)
4. [Course Purchase Control System](#course-purchase-control-system)
5. [Package Features Management](#package-features-management)
6. [Subscription Tiers & Pricing](#subscription-tiers-pricing)
7. [Implementation Roadmap](#implementation-roadmap)
8. [Code Implementation Examples](#code-implementation-examples)

---

## **🆕 NEW REQUIREMENTS ANALYSIS** {#new-requirements-analysis}

### **Requirements Not in Current System:**

1. **✅ Course Single Purchase Control**
   - Add field to courses table: `allow_single_purchase` (boolean)
   - If false, course only available through package purchase

2. **✅ Package Features Management**
   - Display-only features vs. functional features
   - Feature availability tracking

3. **✅ Time-Limited & Lifetime Packages**
   - Auto-expiration system
   - Access revocation after expiration

4. **✅ Subscription with Variable Pricing**
   - First month different pricing
   - Subsequent months pricing variations
   - Laravel Cashier integration

5. **✅ Auto-Enrollment System**
   - Package purchase → auto-enroll all courses
   - Subscription → auto-enroll based on tier

---

## **📊 DATABASE SCHEMA UPDATES** {#database-schema-updates}

### **1. Update Courses Table**
```php
// New migration: add_purchase_control_to_courses_table.php
Schema::table('courses', function (Blueprint $table) {
    $table->boolean('allow_single_purchase')->default(true)->after('is_published');
    $table->boolean('package_only')->default(false)->after('allow_single_purchase');
    $table->decimal('single_purchase_price', 10, 2)->nullable()->after('price');
    $table->json('allowed_in_packages')->nullable()->comment('Array of package IDs');
});
```

### **2. Enhanced Packages Table**
```php
// New migration: enhance_packages_table.php
Schema::table('packages', function (Blueprint $table) {
    // Feature Management
    $table->json('display_features')->nullable()->comment('Features shown but not available');
    $table->json('functional_features')->nullable()->comment('Actually available features');
    $table->boolean('auto_enroll_courses')->default(true);
    
    // Lifetime vs Time-limited
    $table->boolean('is_lifetime')->default(false);
    $table->datetime('access_expires_at')->nullable();
    
    // Subscription linking
    $table->boolean('is_subscription_package')->default(false);
    $table->json('subscription_plan_ids')->nullable();
});
```

### **3. Subscription Plans Updates**
```php
// New migration: update_subscription_plans_for_cashier.php
Schema::table('subscription_plans', function (Blueprint $table) {
    // Laravel Cashier specific fields
    $table->string('stripe_product_id')->nullable();
    $table->json('stripe_prices')->nullable()->comment('Multiple price points');
    
    // Variable pricing
    $table->decimal('first_month_price', 10, 2)->nullable();
    $table->decimal('regular_price', 10, 2);
    $table->integer('promotional_months')->default(1);
    $table->json('tiered_pricing')->nullable()->comment('Month-by-month pricing');
    
    // Package linking
    $table->json('included_package_ids')->nullable();
    $table->json('included_course_ids')->nullable();
});
```

### **4. New Feature Tracking Table**
```php
// New migration: create_package_features_table.php
Schema::create('package_features', function (Blueprint $table) {
    $table->id();
    $table->string('feature_key')->unique();
    $table->string('feature_name');
    $table->text('description')->nullable();
    $table->enum('type', ['display', 'functional']);
    $table->boolean('is_active')->default(true);
    $table->json('implementation_details')->nullable();
    $table->timestamps();
});
```

### **5. User Feature Access Table**
```php
// New migration: create_user_feature_access_table.php
Schema::create('user_feature_access', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->foreignId('package_id')->nullable()->constrained()->onDelete('cascade');
    $table->foreignId('subscription_id')->nullable()->constrained('user_subscriptions');
    $table->string('feature_key');
    $table->boolean('has_access')->default(true);
    $table->datetime('access_granted_at');
    $table->datetime('access_expires_at')->nullable();
    $table->timestamps();
    
    $table->index(['user_id', 'feature_key']);
});
```

---

## **💳 LARAVEL CASHIER INTEGRATION** {#laravel-cashier-integration}

### **1. Installation & Configuration**

```bash
# Install Laravel Cashier for Stripe
composer require laravel/cashier

# Publish Cashier migrations
php artisan vendor:publish --tag="cashier-migrations"

# Run migrations
php artisan migrate
```

### **2. Update .env Configuration**
```env
CASHIER_CURRENCY=usd
CASHIER_CURRENCY_LOCALE=en_US
STRIPE_KEY=your-stripe-publishable-key
STRIPE_SECRET=your-stripe-secret-key
STRIPE_WEBHOOK_SECRET=your-webhook-secret

# Subscription trial settings
CASHIER_TRIAL_DAYS=7
```

### **3. User Model Updates**
```php
// app/Models/User.php
use Laravel\Cashier\Billable;

class User extends Authenticatable
{
    use Billable;
    
    // Additional methods for subscription management
    public function hasPackageAccess($packageId)
    {
        return $this->userPackageAccess()
            ->where('package_id', $packageId)
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->exists();
    }
    
    public function hasFeatureAccess($featureKey)
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
}
```

---

## **🎯 COURSE PURCHASE CONTROL SYSTEM** {#course-purchase-control-system}

### **1. Course Model Updates**
```php
// app/Models/Course.php
class Course extends Model
{
    protected $fillable = [
        'allow_single_purchase',
        'package_only',
        'single_purchase_price',
        'allowed_in_packages'
    ];
    
    protected $casts = [
        'allow_single_purchase' => 'boolean',
        'package_only' => 'boolean',
        'allowed_in_packages' => 'array',
    ];
    
    // Check if course can be purchased individually
    public function canBePurchasedIndividually()
    {
        return $this->allow_single_purchase && !$this->package_only;
    }
    
    // Get packages that include this course
    public function availablePackages()
    {
        return $this->packages()
            ->where('status', 'published')
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            });
    }
    
    // Check if user can access course
    public function userCanAccess(User $user)
    {
        // Direct enrollment check
        if ($user->enrollments()->where('course_id', $this->id)->exists()) {
            return true;
        }
        
        // Package access check
        $packageIds = $this->packages()->pluck('packages.id');
        return $user->userPackageAccess()
            ->whereIn('package_id', $packageIds)
            ->where('is_active', true)
            ->exists();
    }
}
```

### **2. Purchase Controller**
```php
// app/Http/Controllers/Student/CoursePurchaseController.php
class CoursePurchaseController extends Controller
{
    public function checkPurchaseEligibility(Course $course)
    {
        $user = auth()->user();
        
        // Check if already enrolled
        if ($user->enrollments()->where('course_id', $course->id)->exists()) {
            return response()->json([
                'can_purchase' => false,
                'reason' => 'already_enrolled',
                'message' => 'You are already enrolled in this course.'
            ]);
        }
        
        // Check single purchase availability
        if (!$course->canBePurchasedIndividually()) {
            $packages = $course->availablePackages();
            return response()->json([
                'can_purchase' => false,
                'reason' => 'package_only',
                'message' => 'This course is only available through package purchase.',
                'available_packages' => $packages
            ]);
        }
        
        return response()->json([
            'can_purchase' => true,
            'price' => $course->single_purchase_price ?? $course->price,
            'checkout_url' => route('course.checkout', $course)
        ]);
    }
    
    public function initiatePurchase(Course $course)
    {
        if (!$course->canBePurchasedIndividually()) {
            return redirect()->route('packages.index')
                ->with('error', 'This course is only available through package purchase.');
        }
        
        $user = auth()->user();
        $price = $course->single_purchase_price ?? $course->price;
        
        // Create Stripe checkout session
        $checkout = $user->checkout([$course->stripe_price_id => 1], [
            'success_url' => route('course.purchase.success', $course),
            'cancel_url' => route('course.purchase.cancel', $course),
            'metadata' => [
                'course_id' => $course->id,
                'purchase_type' => 'single_course'
            ]
        ]);
        
        return redirect($checkout->url);
    }
}
```

---

## **📦 PACKAGE FEATURES MANAGEMENT** {#package-features-management}

### **1. Feature Management System**
```php
// app/Models/PackageFeature.php
class PackageFeature extends Model
{
    protected $fillable = [
        'feature_key',
        'feature_name',
        'description',
        'type',
        'is_active',
        'implementation_details'
    ];
    
    protected $casts = [
        'implementation_details' => 'array',
        'is_active' => 'boolean'
    ];
    
    // Check if feature is functional (not just display)
    public function isFunctional()
    {
        return $this->type === 'functional' && $this->is_active;
    }
    
    // Grant feature access to user
    public function grantAccessToUser(User $user, $packageId = null, $expiresAt = null)
    {
        return UserFeatureAccess::updateOrCreate(
            [
                'user_id' => $user->id,
                'feature_key' => $this->feature_key,
                'package_id' => $packageId
            ],
            [
                'has_access' => true,
                'access_granted_at' => now(),
                'access_expires_at' => $expiresAt
            ]
        );
    }
}
```

### **2. Package Model Feature Methods**
```php
// Add to app/Models/Package.php
class Package extends Model
{
    protected $casts = [
        'display_features' => 'array',
        'functional_features' => 'array',
        'auto_enroll_courses' => 'boolean',
        'is_lifetime' => 'boolean',
        'is_subscription_package' => 'boolean',
        'subscription_plan_ids' => 'array'
    ];
    
    // Get all features (display + functional)
    public function getAllFeatures()
    {
        return array_merge(
            $this->display_features ?? [],
            $this->functional_features ?? []
        );
    }
    
    // Check if feature is available in package
    public function hasFeature($featureKey, $checkFunctional = true)
    {
        if ($checkFunctional) {
            return in_array($featureKey, $this->functional_features ?? []);
        }
        return in_array($featureKey, $this->getAllFeatures());
    }
    
    // Process package purchase
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
            'features_access' => $this->functional_features
        ]);
        
        // Auto-enroll in courses
        if ($this->auto_enroll_courses) {
            $this->enrollUserInCourses($user, $access);
        }
        
        // Grant feature access
        $this->grantFeatureAccess($user, $access);
        
        return $access;
    }
    
    protected function enrollUserInCourses(User $user, UserPackageAccess $access)
    {
        foreach ($this->courses as $course) {
            Enrollment::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'course_id' => $course->id
                ],
                [
                    'package_access_id' => $access->id,
                    'enrollment_source' => 'package',
                    'enrolled_at' => now(),
                    'expires_at' => $access->expires_at,
                    'status' => 'active',
                    'payment_status' => 'paid'
                ]
            );
        }
    }
    
    protected function grantFeatureAccess(User $user, UserPackageAccess $access)
    {
        foreach ($this->functional_features ?? [] as $featureKey) {
            UserFeatureAccess::create([
                'user_id' => $user->id,
                'package_id' => $this->id,
                'feature_key' => $featureKey,
                'has_access' => true,
                'access_granted_at' => now(),
                'access_expires_at' => $access->expires_at
            ]);
        }
    }
}
```

---

## **💰 SUBSCRIPTION TIERS & PRICING** {#subscription-tiers-pricing}

### **1. Subscription Plan Model Updates**
```php
// app/Models/SubscriptionPlan.php
class SubscriptionPlan extends Model
{
    protected $casts = [
        'stripe_prices' => 'array',
        'tiered_pricing' => 'array',
        'included_package_ids' => 'array',
        'included_course_ids' => 'array'
    ];
    
    // Get price for specific month
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
    
    // Create Stripe subscription with variable pricing
    public function createSubscriptionForUser(User $user, $paymentMethod)
    {
        // Create subscription with promotional pricing
        $subscription = $user->newSubscription('default', $this->stripe_price_id)
            ->trialDays($this->trial_days)
            ->create($paymentMethod, [
                'metadata' => [
                    'subscription_plan_id' => $this->id,
                    'first_month_price' => $this->first_month_price
                ]
            ]);
        
        // Store subscription details
        UserSubscription::create([
            'user_id' => $user->id,
            'subscription_plan_id' => $this->id,
            'stripe_subscription_id' => $subscription->stripe_id,
            'stripe_customer_id' => $user->stripe_id,
            'status' => 'active',
            'current_period_start' => $subscription->current_period_start,
            'current_period_end' => $subscription->current_period_end,
            'trial_ends_at' => $subscription->trial_ends_at
        ]);
        
        // Grant access to included packages and courses
        $this->grantSubscriptionAccess($user);
        
        return $subscription;
    }
    
    protected function grantSubscriptionAccess(User $user)
    {
        // Grant access to included packages
        foreach ($this->included_package_ids ?? [] as $packageId) {
            $package = Package::find($packageId);
            if ($package) {
                UserPackageAccess::create([
                    'user_id' => $user->id,
                    'package_id' => $packageId,
                    'subscription_id' => $user->subscription->id,
                    'access_type' => 'subscription',
                    'starts_at' => now(),
                    'is_active' => true
                ]);
                
                // Auto-enroll in package courses
                if ($package->auto_enroll_courses) {
                    $package->enrollUserInCourses($user, null);
                }
            }
        }
        
        // Grant access to individual courses
        foreach ($this->included_course_ids ?? [] as $courseId) {
            Enrollment::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'course_id' => $courseId
                ],
                [
                    'enrollment_source' => 'subscription',
                    'enrolled_at' => now(),
                    'status' => 'active',
                    'payment_status' => 'paid'
                ]
            );
        }
    }
}
```

### **2. Subscription Controller**
```php
// app/Http/Controllers/Student/SubscriptionController.php
class SubscriptionController extends Controller
{
    public function subscribe(Request $request, SubscriptionPlan $plan)
    {
        $user = auth()->user();
        
        // Check if user already has active subscription
        if ($user->subscribed('default')) {
            return redirect()->route('subscriptions.manage')
                ->with('error', 'You already have an active subscription.');
        }
        
        // Create or get Stripe customer
        if (!$user->hasStripeId()) {
            $user->createAsStripeCustomer();
        }
        
        // Create payment intent for first month
        $paymentIntent = $user->createSetupIntent();
        
        return view('student.subscriptions.checkout', [
            'plan' => $plan,
            'intent' => $paymentIntent,
            'firstMonthPrice' => $plan->first_month_price ?? $plan->regular_price,
            'regularPrice' => $plan->regular_price,
            'promotionalMonths' => $plan->promotional_months
        ]);
    }
    
    public function processSubscription(Request $request, SubscriptionPlan $plan)
    {
        $user = auth()->user();
        $paymentMethod = $request->payment_method;
        
        try {
            // Create subscription with variable pricing
            $subscription = $plan->createSubscriptionForUser($user, $paymentMethod);
            
            // Send confirmation email
            Mail::to($user)->send(new SubscriptionConfirmation($subscription));
            
            return redirect()->route('student.dashboard')
                ->with('success', 'Successfully subscribed to ' . $plan->name);
                
        } catch (\Exception $e) {
            return back()->with('error', 'Subscription failed: ' . $e->getMessage());
        }
    }
    
    public function updatePricing(Request $request)
    {
        $user = auth()->user();
        $subscription = $user->subscription('default');
        
        if (!$subscription) {
            return response()->json(['error' => 'No active subscription'], 404);
        }
        
        // Get current billing period
        $monthsSinceStart = $subscription->created_at->diffInMonths(now()) + 1;
        $plan = SubscriptionPlan::find($subscription->metadata['subscription_plan_id']);
        
        // Calculate current price
        $currentPrice = $plan->getPriceForMonth($monthsSinceStart);
        
        // Update Stripe subscription if price changed
        if ($currentPrice != $subscription->metadata['current_price']) {
            // Update subscription price in Stripe
            $stripeSubscription = $subscription->asStripeSubscription();
            $stripeSubscription->items->data[0]->price = $plan->stripe_prices[$monthsSinceStart] ?? $plan->stripe_price_id;
            $stripeSubscription->save();
            
            // Update metadata
            $subscription->updateStripeSubscription([
                'metadata' => [
                    'current_price' => $currentPrice,
                    'billing_month' => $monthsSinceStart
                ]
            ]);
        }
        
        return response()->json([
            'success' => true,
            'current_price' => $currentPrice,
            'billing_month' => $monthsSinceStart
        ]);
    }
}
```

---

## **⏰ ACCESS EXPIRATION SYSTEM**

### **1. Expiration Check Middleware**
```php
// app/Http/Middleware/CheckPackageExpiration.php
class CheckPackageExpiration
{
    public function handle($request, Closure $next)
    {
        if (auth()->check()) {
            $user = auth()->user();
            
            // Check and expire package access
            $expiredPackages = $user->userPackageAccess()
                ->where('is_active', true)
                ->whereNotNull('expires_at')
                ->where('expires_at', '<=', now())
                ->get();
            
            foreach ($expiredPackages as $access) {
                $access->update(['is_active' => false]);
                
                // Expire related enrollments
                Enrollment::where('package_access_id', $access->id)
                    ->update(['status' => 'expired']);
                
                // Expire feature access
                UserFeatureAccess::where('user_id', $user->id)
                    ->where('package_id', $access->package_id)
                    ->update(['has_access' => false]);
            }
        }
        
        return $next($request);
    }
}
```

### **2. Scheduled Command for Expiration**
```php
// app/Console/Commands/ExpirePackageAccess.php
class ExpirePackageAccess extends Command
{
    protected $signature = 'packages:expire-access';
    protected $description = 'Expire package access for users';
    
    public function handle()
    {
        // Find all expired package access
        $expired = UserPackageAccess::where('is_active', true)
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', now())
            ->get();
        
        foreach ($expired as $access) {
            // Deactivate access
            $access->update(['is_active' => false]);
            
            // Update related enrollments
            Enrollment::where('package_access_id', $access->id)
                ->update([
                    'status' => 'expired',
                    'expires_at' => now()
                ]);
            
            // Revoke feature access
            UserFeatureAccess::where('user_id', $access->user_id)
                ->where('package_id', $access->package_id)
                ->update(['has_access' => false]);
            
            // Send expiration notification
            $access->user->notify(new PackageExpiredNotification($access->package));
            
            $this->info("Expired package access for user {$access->user_id}, package {$access->package_id}");
        }
        
        $this->info("Processed {$expired->count()} expired package accesses.");
    }
}

// Add to app/Console/Kernel.php
protected function schedule(Schedule $schedule)
{
    $schedule->command('packages:expire-access')->daily();
}
```

---

## **🗺️ IMPLEMENTATION ROADMAP** {#implementation-roadmap}

### **Phase 1: Foundation (Week 1)**
- [ ] Install Laravel Cashier
- [ ] Run all new migrations
- [ ] Update existing models with new fields
- [ ] Create feature management system
- [ ] Set up Stripe products and prices

### **Phase 2: Course Control (Week 1)**
- [ ] Implement single purchase restrictions
- [ ] Create package-only course logic
- [ ] Build purchase eligibility checker
- [ ] Update course detail pages
- [ ] Add "Package Only" badges

### **Phase 3: Package Features (Week 2)**
- [ ] Create feature management admin panel
- [ ] Implement display vs functional features
- [ ] Build feature access checking
- [ ] Create feature showcase on package pages
- [ ] Add feature comparison table

### **Phase 4: Subscriptions with Variable Pricing (Week 2)**
- [ ] Implement tiered pricing logic
- [ ] Create subscription checkout flow
- [ ] Build pricing update webhook handler
- [ ] Add subscription management dashboard
- [ ] Implement grace period for failed payments

### **Phase 5: Auto-Enrollment & Expiration (Week 3)**
- [ ] Build auto-enrollment system
- [ ] Create expiration checking middleware
- [ ] Set up scheduled expiration command
- [ ] Implement access revocation
- [ ] Add expiration notifications

### **Phase 6: Testing & Optimization (Week 3)**
- [ ] Write comprehensive tests
- [ ] Test all payment scenarios
- [ ] Optimize database queries
- [ ] Add caching for feature checks
- [ ] Performance testing

---

## **📝 WEBHOOK HANDLERS**

### **Stripe Webhook Controller**
```php
// app/Http/Controllers/StripeWebhookController.php
class StripeWebhookController extends Controller
{
    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sig_header = $request->header('Stripe-Signature');
        $endpoint_secret = config('cashier.webhook.secret');
        
        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload, $sig_header, $endpoint_secret
            );
        } catch(\Exception $e) {
            return response('Invalid signature', 400);
        }
        
        switch ($event->type) {
            case 'customer.subscription.created':
                $this->handleSubscriptionCreated($event->data->object);
                break;
                
            case 'customer.subscription.updated':
                $this->handleSubscriptionUpdated($event->data->object);
                break;
                
            case 'customer.subscription.deleted':
                $this->handleSubscriptionCanceled($event->data->object);
                break;
                
            case 'invoice.payment_succeeded':
                $this->handlePaymentSucceeded($event->data->object);
                break;
                
            case 'invoice.payment_failed':
                $this->handlePaymentFailed($event->data->object);
                break;
        }
        
        return response('Webhook handled', 200);
    }
    
    protected function handleSubscriptionUpdated($subscription)
    {
        $userSubscription = UserSubscription::where('stripe_subscription_id', $subscription->id)->first();
        
        if ($userSubscription) {
            // Check if we need to update pricing
            $monthsSinceStart = Carbon::parse($subscription->created)->diffInMonths(now()) + 1;
            $plan = SubscriptionPlan::find($userSubscription->subscription_plan_id);
            
            // Update price if needed
            $newPrice = $plan->getPriceForMonth($monthsSinceStart);
            
            if ($newPrice != $subscription->items->data[0]->price->unit_amount / 100) {
                // Update subscription in Stripe with new price
                $stripe = new \Stripe\StripeClient(config('cashier.secret'));
                $stripe->subscriptions->update($subscription->id, [
                    'items' => [
                        [
                            'id' => $subscription->items->data[0]->id,
                            'price' => $plan->stripe_prices[$monthsSinceStart] ?? $plan->stripe_price_id
                        ]
                    ]
                ]);
            }
        }
    }
}
```

---

## **🎨 FRONTEND COMPONENTS**

### **1. Course Purchase Component**
```blade
{{-- resources/views/components/course-purchase-button.blade.php --}}
@props(['course'])

<div x-data="coursePurchase({{ $course->id }})" class="course-purchase-wrapper">
    @if($course->canBePurchasedIndividually())
        <button @click="purchaseCourse" class="btn btn-primary">
            Buy Now - ${{ $course->single_purchase_price ?? $course->price }}
        </button>
    @else
        <div class="package-only-notice">
            <p class="text-warning">This course is only available through package purchase</p>
            <button @click="viewPackages" class="btn btn-secondary">
                View Available Packages
            </button>
        </div>
    @endif
</div>

<script>
function coursePurchase(courseId) {
    return {
        async purchaseCourse() {
            const response = await fetch(`/api/courses/${courseId}/check-eligibility`);
            const data = await response.json();
            
            if (data.can_purchase) {
                window.location.href = data.checkout_url;
            } else {
                if (data.reason === 'package_only') {
                    this.viewPackages();
                } else {
                    alert(data.message);
                }
            }
        },
        
        viewPackages() {
            window.location.href = '/packages?highlight_course=' + courseId;
        }
    }
}
</script>
```

### **2. Package Features Display**
```blade
{{-- resources/views/components/package-features.blade.php --}}
@props(['package'])

<div class="package-features">
    <h3>Package Features</h3>
    
    <div class="features-grid">
        {{-- Functional Features (Available) --}}
        <div class="functional-features">
            <h4>✅ Included Features</h4>
            <ul>
                @foreach($package->functional_features ?? [] as $feature)
                    <li class="feature-available">
                        <i class="fas fa-check-circle"></i>
                        {{ $feature }}
                    </li>
                @endforeach
            </ul>
        </div>
        
        {{-- Display-only Features (Coming Soon) --}}
        @if($package->display_features)
            <div class="display-features">
                <h4>🔜 Coming Soon</h4>
                <ul>
                    @foreach($package->display_features as $feature)
                        <li class="feature-upcoming">
                            <i class="fas fa-clock"></i>
                            {{ $feature }}
                            <span class="badge badge-info">Coming Soon</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
    
    {{-- Package Duration --}}
    <div class="package-duration mt-3">
        @if($package->is_lifetime)
            <span class="badge badge-success">Lifetime Access</span>
        @else
            <span class="badge badge-warning">
                {{ $package->duration_days }} Days Access
            </span>
        @endif
    </div>
</div>
```

### **3. Subscription Pricing Display**
```blade
{{-- resources/views/components/subscription-pricing.blade.php --}}
@props(['plan'])

<div class="subscription-pricing-card">
    <h3>{{ $plan->name }}</h3>
    
    {{-- Promotional Pricing --}}
    @if($plan->first_month_price && $plan->first_month_price < $plan->regular_price)
        <div class="promotional-price">
            <span class="price-large">${{ $plan->first_month_price }}</span>
            <span class="price-period">/ first {{ $plan->promotional_months }} month(s)</span>
        </div>
        <div class="regular-price">
            <span class="price-small">Then ${{ $plan->regular_price }}/month</span>
        </div>
    @else
        <div class="regular-price">
            <span class="price-large">${{ $plan->regular_price }}</span>
            <span class="price-period">/ month</span>
        </div>
    @endif
    
    {{-- Tiered Pricing if exists --}}
    @if($plan->tiered_pricing)
        <div class="tiered-pricing mt-2">
            <button class="btn btn-sm btn-link" data-toggle="collapse" data-target="#tier-{{ $plan->id }}">
                View pricing tiers →
            </button>
            <div id="tier-{{ $plan->id }}" class="collapse">
                <ul class="list-unstyled">
                    @foreach($plan->tiered_pricing as $tier)
                        <li>
                            Months {{ $tier['from_month'] }}-{{ $tier['to_month'] }}: 
                            ${{ $tier['price'] }}/month
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif
    
    {{-- Included Content --}}
    <div class="included-content mt-3">
        <h4>Includes:</h4>
        <ul>
            @if($plan->included_package_ids)
                <li>{{ count($plan->included_package_ids) }} Premium Packages</li>
            @endif
            @if($plan->included_course_ids)
                <li>{{ count($plan->included_course_ids) }} Courses</li>
            @endif
        </ul>
    </div>
    
    <button class="btn btn-primary btn-block mt-3" 
            onclick="window.location.href='{{ route('subscriptions.checkout', $plan) }}'">
        Start {{ $plan->trial_days }}-Day Free Trial
    </button>
</div>
```

---

## **🔒 SECURITY & VALIDATION**

### **1. Purchase Validation Service**
```php
// app/Services/PurchaseValidationService.php
class PurchaseValidationService
{
    public function validateCoursePurchase(User $user, Course $course)
    {
        $errors = [];
        
        // Check if already enrolled
        if ($user->enrollments()->where('course_id', $course->id)->exists()) {
            $errors[] = 'Already enrolled in this course';
        }
        
        // Check if course allows single purchase
        if (!$course->allow_single_purchase) {
            $errors[] = 'Course is not available for individual purchase';
        }
        
        // Check if course is published
        if (!$course->is_published) {
            $errors[] = 'Course is not available';
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }
    
    public function validatePackagePurchase(User $user, Package $package)
    {
        $errors = [];
        
        // Check if already has access
        if ($user->hasPackageAccess($package->id)) {
            $errors[] = 'Already have access to this package';
        }
        
        // Check if package is published
        if ($package->status !== 'published') {
            $errors[] = 'Package is not available';
        }
        
        // Check expiration
        if ($package->expires_at && $package->expires_at < now()) {
            $errors[] = 'Package offer has expired';
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }
}
```

---

## **📊 REPORTING & ANALYTICS**

### **1. Sales Dashboard Queries**
```php
// app/Services/SalesAnalyticsService.php
class SalesAnalyticsService
{
    public function getPackageSalesMetrics($startDate, $endDate)
    {
        return [
            'total_sales' => Order::where('type', 'package')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->sum('total'),
                
            'units_sold' => OrderItem::where('item_type', Package::class)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count(),
                
            'top_packages' => Package::withCount(['orderItems' => function($query) use ($startDate, $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                }])
                ->orderBy('order_items_count', 'desc')
                ->limit(5)
                ->get(),
                
            'package_conversion_rate' => $this->calculatePackageConversionRate($startDate, $endDate)
        ];
    }
    
    public function getSubscriptionMetrics()
    {
        return [
            'active_subscriptions' => UserSubscription::where('status', 'active')->count(),
            'mrr' => UserSubscription::where('status', 'active')
                ->join('subscription_plans', 'user_subscriptions.subscription_plan_id', '=', 'subscription_plans.id')
                ->sum('subscription_plans.regular_price'),
            'churn_rate' => $this->calculateChurnRate(),
            'average_lifetime_value' => $this->calculateLTV()
        ];
    }
}
```

---

## **✅ TESTING CHECKLIST**

### **Unit Tests**
- [ ] Course purchase eligibility logic
- [ ] Package feature access checks
- [ ] Subscription pricing calculations
- [ ] Auto-enrollment functionality
- [ ] Expiration handling

### **Integration Tests**
- [ ] Stripe checkout flow
- [ ] Webhook processing
- [ ] Package purchase → enrollment creation
- [ ] Subscription lifecycle
- [ ] Feature access revocation

### **E2E Tests**
- [ ] Complete purchase journey
- [ ] Subscription upgrade/downgrade
- [ ] Package expiration flow
- [ ] Refund processing
- [ ] Access control verification

---

## **📁 FILE STRUCTURE**

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Admin/
│   │   │   ├── PackageController.php
│   │   │   ├── SubscriptionPlanController.php
│   │   │   └── PackageFeatureController.php
│   │   └── Student/
│   │       ├── CoursePurchaseController.php
│   │       ├── PackageController.php
│   │       ├── SubscriptionController.php
│   │       └── CheckoutController.php
│   ├── Middleware/
│   │   ├── CheckPackageExpiration.php
│   │   └── VerifyFeatureAccess.php
│   └── Requests/
│       ├── PurchaseCourseRequest.php
│       └── SubscribeRequest.php
├── Models/
│   ├── Package.php (updated)
│   ├── Course.php (updated)
│   ├── SubscriptionPlan.php (updated)
│   ├── PackageFeature.php (new)
│   └── UserFeatureAccess.php (new)
├── Services/
│   ├── PurchaseValidationService.php
│   ├── SubscriptionService.php
│   ├── PackageService.php
│   └── SalesAnalyticsService.php
└── Console/
    └── Commands/
        ├── ExpirePackageAccess.php
        └── UpdateSubscriptionPricing.php

resources/
└── views/
    ├── admin/
    │   ├── packages/
    │   └── features/
    └── student/
        ├── courses/
        │   └── purchase.blade.php
        ├── packages/
        │   ├── index.blade.php
        │   └── show.blade.php
        └── subscriptions/
            ├── plans.blade.php
            └── checkout.blade.php
```

---

## **🎯 SUCCESS METRICS**

1. **Purchase Conversion Rate**: Target 3-5%
2. **Package Attach Rate**: Target 40% of course purchases
3. **Subscription Retention**: Target 85% monthly retention
4. **Feature Utilization**: Track which features drive purchases
5. **Time to First Purchase**: Target < 7 days from registration

---

## **📚 ADDITIONAL RESOURCES**

- [Laravel Cashier Documentation](https://laravel.com/docs/cashier)
- [Stripe Subscription Guide](https://stripe.com/docs/billing/subscriptions/overview)
- [Laravel Best Practices](https://github.com/alexeymezenin/laravel-best-practices)

---

**Last Updated**: December 2025
**Version**: 1.0
**Status**: Ready for Implementation
