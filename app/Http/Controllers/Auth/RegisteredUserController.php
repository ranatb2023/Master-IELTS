<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Auto-enroll user in courses that have auto-enrollment enabled
        $autoEnrollCourses = \App\Models\Course::where('auto_enroll_enabled', true)
            ->where('status', 'published')
            ->get();

        foreach ($autoEnrollCourses as $course) {
            \App\Models\Enrollment::create([
                'user_id' => $user->id,
                'course_id' => $course->id,
                'status' => 'active',
                'enrolled_at' => now(),
                'enrollment_source' => 'auto_enroll',
                'payment_status' => 'free',
            ]);
        }

        // Grant basic feature access for all new students
        // This allows them to access quizzes and assignments in auto-enrolled courses
        $basicFeatures = [
            'quiz_access',
            'assignment_submission',
            'certificate_generation',
        ];

        foreach ($basicFeatures as $featureKey) {
            \App\Models\UserFeatureAccess::create([
                'user_id' => $user->id,
                'feature_key' => $featureKey,
                'has_access' => true,
                'access_granted_at' => now(),
                'access_expires_at' => null, // Lifetime access
            ]);
        }


        event(new Registered($user));

        Auth::login($user);

        // Notify admins about new user registration
        $admins = User::role('super_admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(new \App\Notifications\NewUserRegisteredNotification($user));
        }

        // Redirect based on user role
        if ($user->hasAnyAdminRole()) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->hasRole('tutor')) {
            return redirect()->route('tutor.dashboard');
        } else {
            return redirect()->route('student.dashboard');
        }
    }
}
