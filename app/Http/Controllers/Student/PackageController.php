<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\UserPackageAccess;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PackageController extends Controller
{
    /**
     * Display all available packages for students to browse
     */
    public function index(Request $request)
    {
        $query = Package::query()
            ->where('status', 'published')
            ->where('is_active', true);

        // Filter by category if provided
        if ($request->has('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        // Search by name or description
        if ($request->has('search') && $request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        // Sort options
        $sort = $request->get('sort', 'newest');

        switch ($sort) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'popular':
                $query->withCount('userAccesses')
                    ->orderBy('user_accesses_count', 'desc');
                break;
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'newest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        // Get featured packages
        $featuredPackages = Package::where('status', 'published')
            ->where('is_active', true)
            ->where('is_featured', true)
            ->take(3)
            ->get();

        $packages = $query->with('courses')->paginate(12)->withQueryString();

        // Get user's purchased packages
        $purchasedPackageIds = [];
        if (Auth::check()) {
            $purchasedPackageIds = UserPackageAccess::where('user_id', Auth::id())
                ->where('is_active', true)
                ->where(function ($q) {
                    $q->whereNull('expires_at')
                        ->orWhere('expires_at', '>', now());
                })
                ->pluck('package_id')
                ->toArray();
        }

        $categories = Package::select('category')
            ->where('status', 'published')
            ->where('is_active', true)
            ->distinct()
            ->pluck('category')
            ->filter();

        return view('student.packages.index', compact(
            'packages',
            'featuredPackages',
            'purchasedPackageIds',
            'categories'
        ));
    }

    /**
     * Show detailed package information
     */
    public function show(Package $package)
    {
        // Check if package is available
        if ($package->status !== 'published' || !$package->is_active) {
            abort(404);
        }

        // Load relationships
        $package->load([
            'courses' => function ($query) {
                $query->where('status', 'published');
            }
        ]);

        // Check if user already has access
        $hasAccess = false;
        $accessDetails = null;
        $currentPackage = null;

        if (Auth::check()) {
            $accessDetails = UserPackageAccess::where('user_id', Auth::id())
                ->where('package_id', $package->id)
                ->where('is_active', true)
                ->where(function ($q) {
                    $q->whereNull('expires_at')
                        ->orWhere('expires_at', '>', now());
                })
                ->first();

            $hasAccess = $accessDetails !== null;

            // Check if user has a different active package
            $currentPackageAccess = UserPackageAccess::where('user_id', Auth::id())
                ->where('package_id', '!=', $package->id)
                ->where('is_active', true)
                ->where(function ($q) {
                    $q->whereNull('expires_at')
                        ->orWhere('expires_at', '>', now());
                })
                ->with('package')
                ->first();

            if ($currentPackageAccess) {
                $currentPackage = $currentPackageAccess->package;
            }
        }

        // Get related packages (same category, exclude current)
        $relatedPackages = Package::where('status', 'published')
            ->where('is_active', true)
            ->where('category', $package->category)
            ->where('id', '!=', $package->id)
            ->take(3)
            ->get();

        // Check for active subscription (for warning banner)
        $activeSubscription = null;
        if (Auth::check()) {
            $activeSubscription = Auth::user()->subscriptions()->active()->first();
        }

        return view('student.packages.show', compact(
            'package',
            'hasAccess',
            'accessDetails',
            'relatedPackages',
            'currentPackage',
            'activeSubscription'
        ));
    }

    /**
     * Initialize package purchase checkout
     */
    public function checkout(Package $package)
    {
        // Check if package is available
        if ($package->status !== 'published' || !$package->is_active) {
            return redirect()
                ->route('student.packages.index')
                ->with('error', 'This package is not available for purchase.');
        }

        // Check if user already has access
        $hasAccess = UserPackageAccess::where('user_id', Auth::id())
            ->where('package_id', $package->id)
            ->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->exists();

        if ($hasAccess) {
            return redirect()
                ->route('student.packages.show', $package)
                ->with('info', 'You already have access to this package.');
        }

        // For subscription packages, redirect to subscription flow
        if ($package->is_subscription_package) {
            return redirect()
                ->route('student.subscriptions.index')
                ->with('info', 'This is a subscription package. Please choose a subscription plan.');
        }

        // Load courses for display
        $package->load([
            'courses' => function ($query) {
                $query->where('status', 'published');
            }
        ]);

        return view('student.packages.checkout', compact('package'));
    }

    /**
     * Create Stripe payment intent for package purchase
     */
    public function createPaymentIntent(Request $request, Package $package)
    {
        // Ensure we always return JSON
        $request->headers->set('Accept', 'application/json');

        try {
            $validated = $request->validate([
                'amount' => 'required|numeric|min:1',
                'package_id' => 'required|exists:packages,id',
            ]);

            // Verify package is available
            if ($package->status !== 'published' || !$package->is_active) {
                return response()->json([
                    'error' => 'Package is no longer available.'
                ], 400);
            }

            // Check if user already has access
            $hasAccess = UserPackageAccess::where('user_id', Auth::id())
                ->where('package_id', $package->id)
                ->where('is_active', true)
                ->where(function ($q) {
                    $q->whereNull('expires_at')
                        ->orWhere('expires_at', '>', now());
                })
                ->exists();

            if ($hasAccess) {
                return response()->json([
                    'error' => 'You already have access to this package.'
                ], 400);
            }

            // Check if Stripe secret is configured
            $stripeSecret = config('services.stripe.secret');
            if (!$stripeSecret) {
                \Log::error('Stripe secret key not configured');
                return response()->json([
                    'error' => 'Payment system not configured. Please contact support.'
                ], 500);
            }

            // Initialize Stripe
            \Stripe\Stripe::setApiKey($stripeSecret);

            // Create payment intent
            $paymentIntent = \Stripe\PaymentIntent::create([
                'amount' => $validated['amount'], // Amount in cents
                'currency' => 'usd',
                'metadata' => [
                    'package_id' => $package->id,
                    'package_name' => $package->name,
                    'user_id' => Auth::id(),
                    'user_email' => Auth::user()->email,
                ],
                'description' => "Package: {$package->name}",
            ]);

            return response()->json([
                'clientSecret' => $paymentIntent->client_secret,
            ]);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            \Log::error('Stripe API Error in createPaymentIntent: ' . $e->getMessage());
            return response()->json([
                'error' => 'Payment processing error: ' . $e->getMessage()
            ], 500);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Validation failed',
                'details' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error in createPaymentIntent: ' . $e->getMessage());
            return response()->json([
                'error' => 'An unexpected error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process package purchase (called after successful Stripe payment)
     */
    public function processPurchase(Request $request, Package $package)
    {
        // Ensure we always return JSON
        $request->headers->set('Accept', 'application/json');

        try {
            $validated = $request->validate([
                'payment_method' => 'required|in:stripe,paypal',
                'payment_intent_id' => 'required|string',
                'amount_paid' => 'required|numeric',
            ]);

            // Verify the package is still available
            if ($package->status !== 'published' || !$package->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'Package is no longer available.'
                ], 400);
            }

            // Check if user already has access
            $existingAccess = UserPackageAccess::where('user_id', Auth::id())
                ->where('package_id', $package->id)
                ->where('is_active', true)
                ->where(function ($q) {
                    $q->whereNull('expires_at')
                        ->orWhere('expires_at', '>', now());
                })
                ->first();

            if ($existingAccess) {
                return response()->json([
                    'success' => false,
                    'message' => 'You already have access to this package.'
                ], 400);
            }

            // Create Order record
            $order = Order::create([
                'user_id' => Auth::id(),
                'type' => 'package',
                'subtotal' => $package->price,
                'discount' => 0,
                'tax' => 0,
                'total' => $validated['amount_paid'],
                'currency' => 'USD',
                'status' => 'completed',
                'payment_method' => $validated['payment_method'],
                'payment_id' => $validated['payment_intent_id'],
            ]);

            // Create OrderItem linking to the package
            OrderItem::create([
                'order_id' => $order->id,
                'item_type' => Package::class,
                'item_id' => $package->id,
                'name' => $package->name,
                'quantity' => 1,
                'unit_price' => $package->price,
                'total' => $package->price,
            ]);

            // Calculate expiration date
            $expiresAt = null;
            if (!$package->is_lifetime) {
                $expiresAt = now()->addDays($package->duration_days);
            }

            // Check if user has ANY OTHER active package (for upgrade handling)
            $otherActivePackage = UserPackageAccess::where('user_id', Auth::id())
                ->where('package_id', '!=', $package->id)
                ->where('is_active', true)
                ->where(function ($q) {
                    $q->whereNull('expires_at')
                        ->orWhere('expires_at', '>', now());
                })
                ->first();

            // Check for ANY active subscription (exclusive access model)
            $activeSubscription = Auth::user()->subscriptions()->active()->first();

            // Cancel active subscription FIRST, in its own transaction (exclusive access - no refund)
            // This must happen before the package transaction to avoid rollback
            if ($activeSubscription) {
                \Log::info('Cancelling subscription due to package purchase', [
                    'user_id' => Auth::id(),
                    'subscription_id' => $activeSubscription->id,
                    'package_id' => $package->id,
                ]);

                // Run in separate transaction that commits immediately
                \DB::transaction(function () use ($activeSubscription) {
                    $this->cancelSubscriptionForPackage(Auth::user(), $activeSubscription);
                });

                \Log::info('Subscription cancelled and committed before package creation');
            }

            // Use DB transaction for package access and enrollments
            \DB::beginTransaction();
            try {
                // If user has another active package, handle upgrade
                if ($otherActivePackage) {
                    \Log::info('Package upgrade detected', [
                        'user_id' => Auth::id(),
                        'old_package_id' => $otherActivePackage->package_id,
                        'new_package_id' => $package->id,
                    ]);

                    // Use the Package model's processUpgrade method
                    $access = $package->processUpgrade(Auth::user(), $order, $otherActivePackage);
                } else {
                    // New purchase (no existing package) - use Package model's processPurchase
                    $access = $package->processPurchase(Auth::user(), $order);
                }

                \DB::commit();
            } catch (\Exception $e) {
                \DB::rollBack();
                throw $e;
            }

            // Generate Invoice
            $invoice = $this->generateInvoice($order);

            // Send purchase confirmation email with invoice PDF
            try {
                \Mail::to(Auth::user())->send(new \App\Mail\PackagePurchased($order, $invoice));
                \Log::info("Purchase confirmation email sent to user {$order->user_id} for order {$order->id}");
            } catch (\Exception $e) {
                \Log::error("Failed to send purchase email: " . $e->getMessage());
                // Continue even if email fails
            }

            // Send package purchased notification
            Auth::user()->notify(new \App\Notifications\PackagePurchasedNotification($order, $package));

            // Notify admins about package purchase
            $admins = \App\Models\User::role('super_admin')->get();
            foreach ($admins as $admin) {
                $admin->notify(new \App\Notifications\PaymentReceivedNotification($order, null));
            }

            return response()->json([
                'success' => true,
                'message' => 'Package purchased successfully!',
                'redirect_url' => route('student.packages.purchase-complete', $package),
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error in processPurchase: ' . $e->getMessage(), [
                'package_id' => $package->id,
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing your purchase: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display purchase success page
     */
    public function purchaseComplete(Package $package)
    {
        // Verify user has access
        $access = UserPackageAccess::where('user_id', Auth::id())
            ->where('package_id', $package->id)
            ->where('is_active', true)
            ->latest()
            ->first();

        if (!$access) {
            return redirect()
                ->route('student.packages.index')
                ->with('error', 'Purchase not found.');
        }

        $package->load([
            'courses' => function ($query) {
                $query->where('status', 'published');
            }
        ]);

        return view('student.packages.success', compact('package', 'access'));
    }

    /**
     * Display user's purchased packages
     */
    public function myPackages(Request $request)
    {
        $sort = $request->get('sort', 'newest');

        // Get active packages
        $activeQuery = UserPackageAccess::where('user_id', Auth::id())
            ->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->with(['package.courses', 'order.invoices', 'enrollments']);

        // Get expired packages
        $expiredQuery = UserPackageAccess::where('user_id', Auth::id())
            ->where('is_active', true)
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', now())
            ->with(['package.courses', 'order.invoices', 'enrollments']);

        // Apply sorting
        switch ($sort) {
            case 'oldest':
                $activeQuery->orderBy('created_at', 'asc');
                $expiredQuery->orderBy('created_at', 'asc');
                break;
            case 'expires_soon':
                $activeQuery->orderBy('expires_at', 'asc');
                break;
            case 'name':
                $activeQuery->join('packages', 'user_package_accesses.package_id', '=', 'packages.id')
                    ->orderBy('packages.name', 'asc')
                    ->select('user_package_accesses.*');
                break;
            default: // newest
                $activeQuery->orderBy('created_at', 'desc');
                $expiredQuery->orderBy('created_at', 'desc');
        }

        $activePackages = $activeQuery->get();
        $expiredPackages = $expiredQuery->get();

        return view('student.packages.my-packages', compact('activePackages', 'expiredPackages'));
    }

    /**
     * Auto-enroll user in package courses
     */
    protected function enrollInPackageCourses(Package $package, UserPackageAccess $packageAccess)
    {
        $courses = $package->courses()->where('status', 'published')->get();
        $courseCount = $courses->count();

        // Get the order to retrieve payment information
        $order = $packageAccess->order;

        // Calculate pro-rated amount per course
        $amountPerCourse = $courseCount > 0 ? ($order->total / $courseCount) : 0;

        foreach ($courses as $course) {
            // Check if already enrolled
            $alreadyEnrolled = \App\Models\Enrollment::where('user_id', Auth::id())
                ->where('course_id', $course->id)
                ->exists();

            if (!$alreadyEnrolled) {
                \App\Models\Enrollment::create([
                    'user_id' => Auth::id(),
                    'course_id' => $course->id,
                    'package_access_id' => $packageAccess->id,
                    'enrolled_at' => now(),
                    'expires_at' => $packageAccess->expires_at,
                    'status' => 'active',
                    'payment_status' => 'completed',
                    'amount_paid' => $amountPerCourse,
                    'enrollment_source' => 'package',
                ]);
            }
        }
    }

    /**
     * Generate invoice for order
     */
    protected function generateInvoice(Order $order)
    {
        // Generate unique invoice number
        $year = date('Y');
        $lastInvoice = \App\Models\Invoice::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        $sequence = $lastInvoice
            ? ((int) substr($lastInvoice->number, -5)) + 1
            : 1;

        $invoiceNumber = "INV-{$year}-" . str_pad($sequence, 5, '0', STR_PAD_LEFT);

        // Create invoice with data stored in JSON field
        $invoice = \App\Models\Invoice::create([
            'user_id' => $order->user_id,
            'order_id' => $order->id,
            'number' => $invoiceNumber,
            'data' => [
                'amount' => $order->subtotal,
                'tax' => $order->tax,
                'total' => $order->total,
                'currency' => $order->currency,
                'status' => 'paid',
                'paid_at' => now()->toDateTimeString(),
            ],
            'issued_at' => now(),
        ]);

        \Log::info("Invoice generated: {$invoiceNumber} for order {$order->id}");

        return $invoice;
    }

    /**
     * Cancel user''s subscription when purchasing a package (exclusive access model)
     */
    protected function cancelSubscriptionForPackage($user, $activeSubscription)
    {
        try {
            // Revoke subscription features first
            $subscriptionPlan = $activeSubscription->subscriptionPlan;

            if ($subscriptionPlan) {
                \Log::info('Revoking subscription features', [
                    'user_id' => $user->id,
                    'subscription_id' => $activeSubscription->id,
                    'plan_name' => $subscriptionPlan->name,
                ]);

                // Get features and revoke them
                foreach ($subscriptionPlan->features as $feature) {
                    \App\Models\UserFeatureAccess::where('user_id', $user->id)
                        ->where('feature_key', $feature->feature_key)
                        ->where('subscription_id', $activeSubscription->id)
                        ->delete();
                }
            }

            // Cancel the subscription immediately (no refund)
            if ($activeSubscription->stripe_id) {
                \Log::info('Cancelling Stripe subscription', [
                    'user_id' => $user->id,
                    'subscription_name' => $activeSubscription->name,
                    'stripe_id' => $activeSubscription->stripe_id,
                ]);

                // Use 'default' as the subscription name (Cashier convention)
                $subscriptionName = $activeSubscription->name ?? 'default';
                $user->subscription($subscriptionName)->cancelNow();

                // Explicitly update the database to ensure it's marked as cancelled
                // Set ends_at to past to bypass grace period check in active() scope
                $activeSubscription->update([
                    'stripe_status' => 'canceled',
                    'ends_at' => now()->subDay(),  // Yesterday, so it's not on grace period
                ]);
            } else {
                // For non-Stripe subscriptions, just delete
                $activeSubscription->delete();
            }

            \Log::info('Subscription cancelled successfully for package purchase', [
                'user_id' => $user->id,
                'subscription_id' => $activeSubscription->id,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error cancelling subscription for package', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}




