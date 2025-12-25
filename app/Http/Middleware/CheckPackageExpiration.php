<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckPackageExpiration
{
    /**
     * Handle an incoming request.
     *
     * Check if user's package and feature access has expired
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();

            // Check and update expired package access
            $expiredPackages = $user->userPackageAccess()
                ->where('is_active', true)
                ->whereNotNull('expires_at')
                ->where('expires_at', '<=', now())
                ->get();

            foreach ($expiredPackages as $access) {
                // Deactivate expired package access
                $access->update(['is_active' => false]);

                // Deactivate associated enrollments
                $user->enrollments()
                    ->where('package_access_id', $access->id)
                    ->where('status', 'active')
                    ->update([
                        'status' => 'expired',
                        'expires_at' => now(),
                    ]);

                // Log expiration
                \Log::info('Package access expired', [
                    'user_id' => $user->id,
                    'package_id' => $access->package_id,
                    'access_id' => $access->id,
                ]);
            }

            // Check and update expired feature access
            $expiredFeatures = $user->userFeatureAccess()
                ->where('has_access', true)
                ->whereNotNull('access_expires_at')
                ->where('access_expires_at', '<=', now())
                ->get();

            foreach ($expiredFeatures as $featureAccess) {
                $featureAccess->update(['has_access' => false]);

                // Log expiration
                \Log::info('Feature access expired', [
                    'user_id' => $user->id,
                    'feature_key' => $featureAccess->feature_key,
                ]);
            }

            // Check and update expired subscriptions
            $expiredSubscriptions = $user->subscriptions()
                ->where('status', 'active')
                ->whereNotNull('ends_at')
                ->where('ends_at', '<=', now())
                ->get();

            foreach ($expiredSubscriptions as $subscription) {
                $subscription->update(['status' => 'expired']);

                // Revoke subscription-based package access
                $user->userPackageAccess()
                    ->where('subscription_id', $subscription->id)
                    ->where('is_active', true)
                    ->update(['is_active' => false]);

                // Revoke subscription-based feature access
                $user->userFeatureAccess()
                    ->where('subscription_id', $subscription->id)
                    ->where('has_access', true)
                    ->update(['has_access' => false]);

                // Log expiration
                \Log::info('Subscription expired', [
                    'user_id' => $user->id,
                    'subscription_id' => $subscription->id,
                ]);
            }

            // If any access expired, add flash message
            if ($expiredPackages->count() > 0 || $expiredFeatures->count() > 0 || $expiredSubscriptions->count() > 0) {
                session()->flash('warning', 'Some of your access has expired. Please renew to continue accessing content.');
            }
        }

        return $next($request);
    }
}
