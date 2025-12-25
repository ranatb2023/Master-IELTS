<?php

namespace App\Observers;

use App\Models\User;
use App\Models\UserProfile;
use App\Models\UserPreference;

class UserObserver
{
    public function created(User $user): void
    {
        // Create user profile
        UserProfile::create([
            'user_id' => $user->id,
        ]);

        // Create user preferences
        UserPreference::create([
            'user_id' => $user->id,
        ]);

        // Assign default student role if no role assigned
        if (!$user->roles()->exists()) {
            $user->assignRole('student');
        }
    }

    public function updated(User $user): void
    {
        //
    }

    public function deleted(User $user): void
    {
        // Profile and preferences will be deleted via cascade
    }
}