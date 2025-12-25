<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Determine if the user can view any users
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('super_admin');
    }

    /**
     * Determine if the user can view the model
     */
    public function view(User $user, User $model): bool
    {
        // Super admins can view all users
        if ($user->hasRole('super_admin')) {
            return true;
        }

        // Tutors can view students enrolled in their courses
        if ($user->hasRole('tutor')) {
            return $model->hasRole('student') &&
                   $model->enrollments()
                       ->whereHas('course', function ($q) use ($user) {
                           $q->where('instructor_id', $user->id);
                       })
                       ->exists();
        }

        // Users can view their own profile
        return $user->id === $model->id;
    }

    /**
     * Determine if the user can create users
     */
    public function create(User $user): bool
    {
        return $user->hasRole('super_admin');
    }

    /**
     * Determine if the user can update the model
     */
    public function update(User $user, User $model): bool
    {
        // Super admins can update any user
        if ($user->hasRole('super_admin')) {
            return true;
        }

        // Users can update their own profile
        return $user->id === $model->id;
    }

    /**
     * Determine if the user can delete the model
     */
    public function delete(User $user, User $model): bool
    {
        // Super admins can delete any user except themselves
        if ($user->hasRole('super_admin')) {
            return $user->id !== $model->id;
        }

        return false;
    }

    /**
     * Determine if the user can restore the model
     */
    public function restore(User $user, User $model): bool
    {
        return $user->hasRole('super_admin');
    }

    /**
     * Determine if the user can permanently delete the model
     */
    public function forceDelete(User $user, User $model): bool
    {
        return $user->hasRole('super_admin') && $user->id !== $model->id;
    }

    /**
     * Determine if the user can ban users
     */
    public function ban(User $user, User $model): bool
    {
        // Super admins can ban any user except themselves
        return $user->hasRole('super_admin') && $user->id !== $model->id;
    }

    /**
     * Determine if the user can impersonate other users
     */
    public function impersonate(User $user, User $model): bool
    {
        // Super admins can impersonate any user except themselves
        return $user->hasRole('super_admin') && $user->id !== $model->id;
    }

    /**
     * Determine if the user can manage user roles
     */
    public function manageRoles(User $user, User $model): bool
    {
        // Only super admins can manage roles
        return $user->hasRole('super_admin') && $user->id !== $model->id;
    }
}
