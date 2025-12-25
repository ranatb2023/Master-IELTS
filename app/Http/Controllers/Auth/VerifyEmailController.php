<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            // Redirect based on user role
            if ($user->hasAnyAdminRole()) {
                return redirect()->intended(route('admin.dashboard').'?verified=1');
            } elseif ($user->hasRole('tutor')) {
                return redirect()->intended(route('tutor.dashboard').'?verified=1');
            } else {
                return redirect()->intended(route('student.dashboard').'?verified=1');
            }
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        // Redirect based on user role
        if ($user->hasAnyAdminRole()) {
            return redirect()->intended(route('admin.dashboard').'?verified=1');
        } elseif ($user->hasRole('tutor')) {
            return redirect()->intended(route('tutor.dashboard').'?verified=1');
        } else {
            return redirect()->intended(route('student.dashboard').'?verified=1');
        }
    }
}
