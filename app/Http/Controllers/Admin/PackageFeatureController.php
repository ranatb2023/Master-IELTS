<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PackageFeature;
use Illuminate\Http\Request;

class PackageFeatureController extends Controller
{
    /**
     * Display listing of all package features
     */
    public function index(Request $request)
    {
        $query = PackageFeature::query();

        // Search
        if ($request->has('search') && $request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('feature_name', 'like', '%' . $request->search . '%')
                    ->orWhere('feature_key', 'like', '%' . $request->search . '%')
                    ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        // Filter by type
        if ($request->has('type') && $request->type !== 'all') {
            $query->where('type', $request->type);
        }

        // Filter by status
        if ($request->has('status')) {
            $isActive = $request->status === 'active';
            $query->where('is_active', $isActive);
        }

        // Sorting
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        $features = $query->withCount('userAccess')->paginate(20)->withQueryString();

        return view('admin.package-features.index', compact('features'));
    }

    /**
     * Show the form for creating a new package feature
     */
    public function create()
    {
        return view('admin.package-features.create');
    }

    /**
     * Store a newly created package feature
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'feature_key' => 'required|string|max:255|unique:package_features,feature_key|regex:/^[a-z0-9_-]+$/',
            'feature_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:display,functional',
            'is_active' => 'boolean',
            'implementation_details' => 'nullable|string', // Changed from 'array' to 'string'
        ]);

        // Handle is_active checkbox (defaults to false if not present)
        $validated['is_active'] = $request->has('is_active') && $request->is_active ? true : false;

        // Convert JSON string to array if provided
        if (!empty($validated['implementation_details'])) {
            $jsonData = json_decode($validated['implementation_details'], true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return back()
                    ->withInput()
                    ->withErrors(['implementation_details' => 'Invalid JSON format. Please check your syntax.']);
            }

            $validated['implementation_details'] = $jsonData;
        } else {
            $validated['implementation_details'] = null;
        }

        $feature = PackageFeature::create($validated);

        return redirect()
            ->route('admin.package-features.show', $feature)
            ->with('success', 'Package feature created successfully!');
    }

    /**
     * Display the specified package feature
     */
    public function show(PackageFeature $packageFeature)
    {
        $feature = $packageFeature;

        // Get users with access to this feature with pagination
        $users = $feature->userAccess()
            ->with('user')
            ->latest()
            ->paginate(20);

        return view('admin.package-features.show', compact('feature', 'users'));
    }

    /**
     * Show the form for editing the package feature
     */
    public function edit(PackageFeature $packageFeature)
    {
        $feature = $packageFeature;
        return view('admin.package-features.edit', compact('feature'));
    }

    /**
     * Update the specified package feature
     */
    public function update(Request $request, PackageFeature $packageFeature)
    {
        $validated = $request->validate([
            'feature_key' => 'required|string|max:255|unique:package_features,feature_key,' . $packageFeature->id . '|regex:/^[a-z0-9_-]+$/',
            'feature_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:display,functional',
            'is_active' => 'boolean',
            'implementation_details' => 'nullable|string', // Changed from 'array' to 'string'
        ]);

        // Handle is_active checkbox (defaults to false if not present)
        $validated['is_active'] = $request->has('is_active') && $request->is_active ? true : false;

        // Convert JSON string to array if provided
        if (!empty($validated['implementation_details'])) {
            $jsonData = json_decode($validated['implementation_details'], true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return back()
                    ->withInput()
                    ->withErrors(['implementation_details' => 'Invalid JSON format. Please check your syntax.']);
            }

            $validated['implementation_details'] = $jsonData;
        } else {
            $validated['implementation_details'] = null;
        }

        $packageFeature->update($validated);

        return redirect()
            ->route('admin.package-features.show', $packageFeature)
            ->with('success', 'Package feature updated successfully!');
    }

    /**
     * Remove the specified package feature
     */
    public function destroy(PackageFeature $packageFeature)
    {
        // Check if feature is being used
        $usersCount = $packageFeature->userAccess()->count();

        if ($usersCount > 0) {
            return back()->with('error', 'Cannot delete feature that is assigned to users. Please deactivate it instead.');
        }

        $packageFeature->delete();

        return redirect()
            ->route('admin.package-features.index')
            ->with('success', 'Package feature deleted successfully!');
    }

    /**
     * Toggle feature active status
     */
    public function toggleStatus(PackageFeature $packageFeature)
    {
        $packageFeature->update(['is_active' => !$packageFeature->is_active]);

        $status = $packageFeature->is_active ? 'activated' : 'deactivated';

        return back()->with('success', "Feature {$status} successfully!");
    }

    /**
     * Grant feature access to a user
     */
    public function grantAccess(Request $request, PackageFeature $packageFeature)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'package_id' => 'nullable|exists:packages,id',
            'subscription_id' => 'nullable|exists:user_subscriptions,id',
            'expires_at' => 'nullable|date|after:now',
        ]);

        $user = \App\Models\User::findOrFail($validated['user_id']);

        $packageFeature->grantAccessToUser(
            $user,
            $validated['package_id'] ?? null,
            $validated['subscription_id'] ?? null,
            isset($validated['expires_at']) ? \Carbon\Carbon::parse($validated['expires_at']) : null
        );

        return back()->with('success', 'Feature access granted to user successfully!');
    }

    /**
     * Revoke feature access from a user
     */
    public function revokeAccess(Request $request, PackageFeature $packageFeature)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $packageFeature->userAccess()
            ->where('user_id', $validated['user_id'])
            ->update(['has_access' => false]);

        return back()->with('success', 'Feature access revoked from user successfully!');
    }

    /**
     * Get all users with access to this feature
     */
    public function getUsers(PackageFeature $packageFeature)
    {
        $users = $packageFeature->userAccess()
            ->with('user')
            ->active()
            ->paginate(50);

        return response()->json($users);
    }
}
