<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmailVerificationPromptController extends Controller
{
    /**
     * Display the email verification prompt.
     */
    public function __invoke(Request $request): RedirectResponse|View
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

        return view('auth.verify-email');
    }
}
