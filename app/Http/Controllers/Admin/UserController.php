<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of users
     */
    public function index(Request $request)
    {
        $query = User::with(['roles', 'profile']);

        // Search
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        // Filter by role
        if ($request->filled('role')) {
            $query->role($request->role);
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        // Filter by email verification
        if ($request->filled('verified')) {
            if ($request->verified === 'yes') {
                $query->whereNotNull('email_verified_at');
            } else {
                $query->whereNull('email_verified_at');
            }
        }

        $users = $query->latest()->paginate(20);
        $roles = Role::all();

        return view('admin.users.index', compact('users', 'roles'));
    }

    /**
     * Show the form for creating a new user
     */
    public function create()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    /**
     * Store a newly created user
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Password::defaults()],
            'role' => 'required|exists:roles,name',
            'country_code' => 'nullable|string|max:5',
            'phone' => 'nullable|string|regex:/^[0-9]{10,15}$/',
            'country' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
            'date_of_birth' => 'nullable|date|before:today',
            'gender' => 'nullable|in:male,female,other',
            'bio' => 'nullable|string|max:1000',
            'timezone' => 'nullable|string|max:255',
            'language' => 'nullable|string|max:10',
            'is_active' => 'boolean',
            'email_verified' => 'boolean',
        ]);

        // Combine country code and phone
        $phone = null;
        if (!empty($validated['phone']) && !empty($validated['country_code'])) {
            $phone = $validated['country_code'] . ' ' . $validated['phone'];
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $phone,
            'country' => $validated['country'] ?? null,
            'city' => $validated['city'] ?? null,
            'address' => $validated['address'] ?? null,
            'date_of_birth' => $validated['date_of_birth'] ?? null,
            'gender' => $validated['gender'] ?? null,
            'bio' => $validated['bio'] ?? null,
            'timezone' => $validated['timezone'] ?? 'UTC',
            'language' => $validated['language'] ?? 'en',
            'is_active' => $validated['is_active'] ?? true,
            'email_verified_at' => ($validated['email_verified'] ?? false) ? now() : null,
        ]);

        $user->assignRole($validated['role']);

        return redirect()
            ->route('admin.users.show', $user)
            ->with('success', 'User created successfully!');
    }

    /**
     * Display the specified user
     */
    public function show(User $user)
    {
        $user->load([
            'roles',
            'profile',
            'preferences',
            'enrollments.course',
            'createdCourses',
            'orders'
        ]);

        $stats = [];

        if ($user->hasRole('tutor')) {
            $stats = [
                'total_courses' => $user->createdCourses()->count(),
                'published_courses' => $user->createdCourses()->where('status', 'published')->count(),
                'total_students' => $user->createdCourses()->withCount('enrollments')->get()->sum('enrollments_count'),
                'total_revenue' => 0, // TODO: Calculate revenue from orders
            ];
        } elseif ($user->hasRole('student')) {
            $stats = [
                'enrolled_courses' => $user->enrollments()->count(),
                'active_enrollments' => $user->enrollments()->where('status', 'active')->count(),
                'completed_courses' => $user->enrollments()->where('status', 'completed')->count(),
                'certificates_earned' => $user->certificates()->count(),
                'total_spent' => $user->orders()->where('status', 'completed')->sum('total'),
                'average_progress' => $user->enrollments()->avg('progress_percentage') ?? 0,
            ];
        }

        return view('admin.users.show', compact('user', 'stats'));
    }

    /**
     * Show the form for editing the specified user
     */
    public function edit(User $user)
    {
        $user->load('roles');
        $roles = Role::all();

        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified user
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'role' => 'required|exists:roles,name',
            'country_code' => 'nullable|string|max:5',
            'phone' => 'nullable|string|regex:/^[0-9]{10,15}$/',
            'country' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
            'date_of_birth' => 'nullable|date|before:today',
            'gender' => 'nullable|in:male,female,other',
            'bio' => 'nullable|string|max:1000',
            'is_active' => 'nullable|boolean',
            'email_verified' => 'nullable|boolean',
        ]);

        // Combine country code and phone
        $phone = null;
        if (!empty($validated['phone']) && !empty($validated['country_code'])) {
            $phone = $validated['country_code'] . ' ' . $validated['phone'];
        } elseif (empty($validated['phone'])) {
            $phone = null;
        } else {
            $phone = $validated['phone'];
        }

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $phone,
            'country' => $validated['country'] ?? null,
            'city' => $validated['city'] ?? null,
            'address' => $validated['address'] ?? null,
            'date_of_birth' => $validated['date_of_birth'] ?? null,
            'gender' => $validated['gender'] ?? null,
            'bio' => $validated['bio'] ?? null,
            'is_active' => isset($validated['is_active']) && $validated['is_active'] ? true : false,
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        // Handle email verification checkbox
        // Checkbox sends value only when checked, so check the validated data
        if (isset($validated['email_verified']) && $validated['email_verified']) {
            $updateData['email_verified_at'] = now();
        } else {
            $updateData['email_verified_at'] = null;
        }

        $user->update($updateData);

        // Update role
        $user->syncRoles([$validated['role']]);

        return redirect()
            ->route('admin.users.show', $user)
            ->with('success', 'User updated successfully!');
    }

    /**
     * Remove the specified user
     */
    public function destroy(User $user)
    {
        // Prevent deleting own account
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User deleted successfully!');
    }

    /**
     * Toggle user active status
     */
    public function toggleStatus(User $user)
    {
        $user->update(['is_active' => !$user->is_active]);

        return back()->with('success', 'User status updated successfully!');
    }

    /**
     * Verify user email manually
     */
    public function verifyEmail(User $user)
    {
        $user->update(['email_verified_at' => now()]);

        return back()->with('success', 'User email verified successfully!');
    }

    /**
     * Display trashed users
     */
    public function trash(Request $request)
    {
        $query = User::onlyTrashed()->with(['roles']);

        // Search
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        // Filter by role
        if ($request->filled('role')) {
            $query->role($request->role);
        }

        $users = $query->latest('deleted_at')->paginate(20);
        $roles = Role::all();

        return view('admin.users.trash', compact('users', 'roles'));
    }

    /**
     * Restore a soft-deleted user
     */
    public function restore($id)
    {
        $user = User::onlyTrashed()->findOrFail($id);
        $user->restore();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User restored successfully!');
    }

    /**
     * Permanently delete a user from database
     */
    public function forceDelete($id)
    {
        $user = User::onlyTrashed()->findOrFail($id);

        // Prevent force deleting own account
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot permanently delete your own account.');
        }

        $user->forceDelete();

        return redirect()
            ->route('admin.users.trash')
            ->with('success', 'User permanently deleted from database!');
    }

    /**
     * Bulk actions
     */
    public function bulkAction(Request $request)
    {
        $validated = $request->validate([
            'action' => 'required|in:activate,deactivate,delete,verify_email',
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        // Prevent bulk action on own account
        if (in_array(auth()->id(), $validated['user_ids'])) {
            return back()->with('error', 'You cannot perform bulk actions on your own account.');
        }

        $users = User::whereIn('id', $validated['user_ids']);

        switch ($validated['action']) {
            case 'activate':
                $users->update(['is_active' => true]);
                $message = 'Users activated successfully!';
                break;
            case 'deactivate':
                $users->update(['is_active' => false]);
                $message = 'Users deactivated successfully!';
                break;
            case 'delete':
                $users->delete();
                $message = 'Users deleted successfully!';
                break;
            case 'verify_email':
                $users->update(['email_verified_at' => now()]);
                $message = 'User emails verified successfully!';
                break;
        }

        return back()->with('success', $message);
    }

    /**
     * Impersonate a user
     */
    public function impersonate(User $user)
    {
        // Prevent impersonating own account
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot impersonate yourself.');
        }

        session(['impersonating' => auth()->id()]);
        auth()->login($user);

        return redirect()->route('dashboard')
            ->with('success', 'Now impersonating ' . $user->name);
    }

    /**
     * Stop impersonating
     */
    public function stopImpersonating()
    {
        if (!session()->has('impersonating')) {
            return redirect()->route('dashboard');
        }

        $originalUserId = session('impersonating');
        session()->forget('impersonating');

        auth()->loginUsingId($originalUserId);

        return redirect()->route('admin.users.index')
            ->with('success', 'Stopped impersonating.');
    }
}
