<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Show the user's profile.
     */
    public function show(): View
    {
        $user = Auth::user()->load(['profile', 'preferences']);
        return view('profile.show', compact('user'));
    }

    /**
     * Edit profile form.
     */
    public function edit(): View
    {
        $user = Auth::user()->load(['profile', 'preferences']);
        return view('profile.edit', compact('user'));
    }

    /**
     * Update basic user info + avatar.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'date_of_birth' => ['nullable', 'date'],
            'gender' => ['nullable', Rule::in(['male', 'female', 'other'])],
            'country' => ['nullable', 'string', 'max:100'],
            'city' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string', 'max:500'],
            'timezone' => ['nullable', 'string'],
            'language' => ['nullable', 'string', 'max:10'],
            'avatar' => ['nullable', 'image', 'max:2048'],
        ]);

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            $path = $request->file('avatar')->store('avatars', 'public');
            $validated['avatar'] = $path;
        }

        $user->update($validated);

        return redirect()->route('profile.edit')
            ->with('status', 'profile-updated');
    }

    /**
     * Update extended profile information (social links, headline, skills, interests).
     */
    public function updateProfile(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'headline' => ['nullable', 'string', 'max:255'],
            'website' => ['nullable', 'url', 'max:255'],
            'twitter' => ['nullable', 'string', 'max:255'],
            'facebook' => ['nullable', 'string', 'max:255'],
            'linkedin' => ['nullable', 'string', 'max:255'],
            'youtube' => ['nullable', 'string', 'max:255'],
            'github' => ['nullable', 'string', 'max:255'],
            'interests' => ['nullable', 'array'],
            'skills' => ['nullable', 'array'],
        ]);

        $user->profile->update($validated);

        return redirect()->route('profile.show')
            ->with('success', 'Profile information updated successfully!');
    }

    /**
     * Update user notification & preference settings.
     */
    public function updatePreferences(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'email_notifications' => ['boolean'],
            'push_notifications' => ['boolean'],
            'sms_notifications' => ['boolean'],
            'course_updates' => ['boolean'],
            'assignment_reminders' => ['boolean'],
            'message_notifications' => ['boolean'],
            'marketing_emails' => ['boolean'],
            'weekly_digest' => ['boolean'],
            'theme' => ['required', Rule::in(['light', 'dark', 'auto'])],
        ]);

        $user->preferences->update($validated);

        return redirect()->route('profile.show')
            ->with('success', 'Preferences updated successfully!');
    }

    /**
     * Remove avatar image.
     */
    public function deleteAvatar(): RedirectResponse
    {
        $user = Auth::user();

        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
            $user->update(['avatar' => null]);
        }

        return redirect()->route('profile.edit')
            ->with('success', 'Avatar removed successfully!');
    }

    /**
     * Delete account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
