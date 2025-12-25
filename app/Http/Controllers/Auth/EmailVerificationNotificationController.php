<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EmailVerificationNotificationController extends Controller
{
    /**
     * Send a new email verification notification.
     */
    public function store(Request $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            $user = $request->user();

            // Redirect based on user role
            if ($user->hasAnyAdminRole()) {
                return redirect()->intended(route('admin.dashboard'));
            } elseif ($user->hasRole('tutor')) {
                return redirect()->intended(route('tutor.dashboard'));
            } else {
                return redirect()->intended(route('student.dashboard'));
            }
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('status', 'verification-link-sent');
    }
}
