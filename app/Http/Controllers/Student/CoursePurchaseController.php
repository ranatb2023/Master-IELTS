<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Package;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeCheckoutSession;

class CoursePurchaseController extends Controller
{
    /**
     * Check if user can purchase this course
     */
    public function checkPurchaseEligibility(Course $course)
    {
        $user = Auth::user();

        // Check if user already has access
        if ($course->userCanAccess($user)) {
            return response()->json([
                'can_purchase' => false,
                'reason' => 'already_enrolled',
                'message' => 'You already have access to this course.',
            ]);
        }

        // Check if course allows individual purchase
        if (!$course->canBePurchasedIndividually()) {
            $availablePackages = $course->availablePackages()->get();

            return response()->json([
                'can_purchase' => false,
                'reason' => 'package_only',
                'message' => 'This course is only available as part of a package.',
                'available_packages' => $availablePackages->map(function ($package) {
                    return [
                        'id' => $package->id,
                        'name' => $package->name,
                        'slug' => $package->slug,
                        'price' => $package->effective_price,
                        'has_sale' => $package->has_sale,
                        'course_count' => $package->courses()->count(),
                    ];
                }),
            ]);
        }

        // Check if course has restricted packages
        if ($course->allowed_in_packages && count($course->allowed_in_packages) > 0) {
            $restrictedPackages = Package::whereIn('id', $course->allowed_in_packages)
                ->published()
                ->get();

            return response()->json([
                'can_purchase' => true,
                'has_restrictions' => true,
                'message' => 'This course can be purchased individually or as part of specific packages.',
                'price' => $course->getEffectivePurchasePrice(),
                'recommended_packages' => $restrictedPackages->map(function ($package) {
                    return [
                        'id' => $package->id,
                        'name' => $package->name,
                        'slug' => $package->slug,
                        'price' => $package->effective_price,
                        'has_sale' => $package->has_sale,
                        'course_count' => $package->courses()->count(),
                    ];
                }),
            ]);
        }

        // Course can be purchased individually
        return response()->json([
            'can_purchase' => true,
            'has_restrictions' => false,
            'price' => $course->getEffectivePurchasePrice(),
            'message' => 'This course is available for individual purchase.',
        ]);
    }

    /**
     * Initiate course purchase via Stripe
     */
    public function initiatePurchase(Request $request, Course $course)
    {
        $user = Auth::user();

        // Verify purchase eligibility
        if ($course->userCanAccess($user)) {
            return back()->with('error', 'You already have access to this course.');
        }

        if (!$course->canBePurchasedIndividually()) {
            return back()->with('error', 'This course cannot be purchased individually. Please select a package.');
        }

        // Get effective purchase price
        $price = $course->getEffectivePurchasePrice();

        if (!$price || $price <= 0) {
            return back()->with('error', 'This course does not have a valid purchase price.');
        }

        // Create pending order
        $order = Order::create([
            'user_id' => $user->id,
            'type' => 'course',
            'subtotal' => $price,
            'discount' => 0,
            'tax' => 0,
            'total' => $price,
            'currency' => config('cashier.currency', 'usd'),
            'status' => 'pending',
            'payment_method' => 'stripe',
            'metadata' => [
                'course_id' => $course->id,
                'course_name' => $course->title,
                'price' => $price,
            ],
        ]);

        // Create OrderItem for the course
        \App\Models\OrderItem::create([
            'order_id' => $order->id,
            'item_type' => \App\Models\Course::class,
            'item_id' => $course->id,
            'name' => $course->title,
            'quantity' => 1,
            'unit_price' => $price,
            'total' => $price,
        ]);

        // Create Stripe Checkout Session
        Stripe::setApiKey(config('services.stripe.secret'));

        try {
            $checkoutSession = StripeCheckoutSession::create([
                'payment_method_types' => ['card'],
                'line_items' => [
                    [
                        'price_data' => [
                            'currency' => config('cashier.currency', 'usd'),
                            'product_data' => [
                                'name' => $course->title,
                                'description' => strip_tags($course->description ? substr($course->description, 0, 200) : 'Course purchase'),
                                'images' => $course->thumbnail ? [asset('storage/' . $course->thumbnail)] : [],
                            ],
                            'unit_amount' => $price * 100, // Convert to cents
                        ],
                        'quantity' => 1,
                    ]
                ],
                'mode' => 'payment',
                'success_url' => route('student.courses.purchase.success', ['order' => $order->id]) . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('student.courses.purchase.cancel', ['order' => $order->id]),
                'client_reference_id' => $order->id,
                'customer_email' => $user->email,
                'metadata' => [
                    'order_id' => $order->id,
                    'course_id' => $course->id,
                    'user_id' => $user->id,
                ],
            ]);

            // Update order with Stripe session ID
            $order->update([
                'payment_intent_id' => $checkoutSession->id,
            ]);

            return redirect($checkoutSession->url);
        } catch (\Exception $e) {
            // Mark order as failed
            $order->update(['status' => 'failed']);

            return back()->with('error', 'Failed to initiate payment: ' . $e->getMessage());
        }
    }

    /**
     * Handle successful course purchase
     */
    public function purchaseSuccess(Request $request, Order $order)
    {
        $user = Auth::user();

        // Verify order belongs to user
        if ($order->user_id !== $user->id) {
            abort(403, 'Unauthorized');
        }

        // Verify Stripe session
        $sessionId = $request->get('session_id');
        if (!$sessionId) {
            return redirect()->route('student.dashboard')->with('error', 'Invalid payment session.');
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        try {
            $session = StripeCheckoutSession::retrieve($sessionId);

            if ($session->payment_status === 'paid') {
                // Update order status
                $order->update([
                    'status' => 'completed',
                    'paid_at' => now(),
                ]);

                // Enroll user in course
                $courseId = $order->metadata['course_id'] ?? null;
                if ($courseId) {
                    $course = Course::find($courseId);
                    if ($course) {
                        $course->enrollments()->updateOrCreate(
                            [
                                'user_id' => $user->id,
                                'course_id' => $course->id,
                            ],
                            [
                                'order_id' => $order->id,
                                'enrollment_source' => 'purchase',
                                'enrolled_at' => now(),
                                'status' => 'active',
                            ]
                        );

                        return redirect()
                            ->route('student.courses.show', $course->slug)
                            ->with('success', 'Course purchased successfully! You now have access.');
                    }
                }

                return redirect()
                    ->route('student.dashboard')
                    ->with('success', 'Payment successful!');
            }

            return redirect()
                ->route('student.dashboard')
                ->with('error', 'Payment was not completed. Please try again.');
        } catch (\Exception $e) {
            return redirect()
                ->route('student.dashboard')
                ->with('error', 'Failed to verify payment: ' . $e->getMessage());
        }
    }

    /**
     * Handle cancelled course purchase
     */
    public function purchaseCancel(Order $order)
    {
        $user = Auth::user();

        // Verify order belongs to user
        if ($order->user_id !== $user->id) {
            abort(403, 'Unauthorized');
        }

        // Update order status
        $order->update(['status' => 'cancelled']);

        $courseId = $order->metadata['course_id'] ?? null;
        if ($courseId) {
            $course = Course::find($courseId);
            if ($course) {
                return redirect()
                    ->route('student.courses.show', $course->slug)
                    ->with('info', 'Payment was cancelled. You can try again when ready.');
            }
        }

        return redirect()
            ->route('student.dashboard')
            ->with('info', 'Payment was cancelled.');
    }
}
