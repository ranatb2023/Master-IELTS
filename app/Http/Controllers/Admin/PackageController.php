<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Requests\Admin\StorePackageRequest;
use App\Http\Requests\Admin\UpdatePackageRequest;

class PackageController extends Controller
{
    /**
     * Display listing of all packages
     */
    public function index(Request $request)
    {
        $query = Package::query();

        // Search
        if ($request->has('search') && $request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter by lifetime/time-limited
        if ($request->has('access_type')) {
            if ($request->access_type === 'lifetime') {
                $query->where('is_lifetime', true);
            } elseif ($request->access_type === 'time_limited') {
                $query->where('is_lifetime', false);
            }
        }

        // Filter by featured
        if ($request->has('featured') && $request->featured === '1') {
            $query->where('is_featured', true);
        }

        // Sorting
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        $packages = $query->withCount('courses')->paginate(15)->withQueryString();

        return view('admin.packages.index', compact('packages'));
    }

    /**
     * Show the form for creating a new package
     */
    public function create()
    {
        $courses = Course::where('status', 'published')->orderBy('title')->get();
        $features = \App\Models\PackageFeature::where('is_active', true)
            ->orderBy('type')
            ->orderBy('feature_name')
            ->get();

        // Get subscription plans
        $subscriptionPlans = \App\Models\SubscriptionPlan::orderBy('name')->get();

        return view('admin.packages.create', compact('courses', 'features', 'subscriptionPlans'));
    }

    /**
     * Store a newly created package
     */
    public function store(StorePackageRequest $request)
    {
        $validated = $request->validated();

        // Store feature keys in the appropriate JSON columns
        // Filter out empty strings from hidden inputs and validate feature keys exist
        $displayKeys = array_values(array_filter(
            $request->input('display_feature_keys', []),
            fn($value) => !empty($value) && $value !== ''
        ));
        $functionalKeys = array_values(array_filter(
            $request->input('functional_feature_keys', []),
            fn($value) => !empty($value) && $value !== ''
        ));

        // Validate that feature keys exist and get valid ones
        $validDisplayKeys = !empty($displayKeys)
            ? \App\Models\PackageFeature::whereIn('feature_key', $displayKeys)
                ->where('type', 'display')
                ->pluck('feature_key')
                ->toArray()
            : [];

        $validFunctionalKeys = !empty($functionalKeys)
            ? \App\Models\PackageFeature::whereIn('feature_key', $functionalKeys)
                ->where('type', 'functional')
                ->pluck('feature_key')
                ->toArray()
            : [];

        // Set JSON fields for backwards compatibility
        $validated['display_features'] = $validDisplayKeys;
        $validated['functional_features'] = $validFunctionalKeys;

        // Generate slug if not provided
        if (!$request->slug) {
            $validated['slug'] = Str::slug($request->name);

            // Ensure slug is unique
            $originalSlug = $validated['slug'];
            $counter = 1;
            while (Package::where('slug', $validated['slug'])->exists()) {
                $validated['slug'] = $originalSlug . '-' . $counter++;
            }
        }

        // Handle boolean checkboxes (default to false if not present)
        $validated['is_lifetime'] = $request->has('is_lifetime') && $request->is_lifetime ? true : false;
        $validated['is_featured'] = $request->has('is_featured') && $request->is_featured ? true : false;
        $validated['is_subscription_package'] = $request->has('is_subscription_package') && $request->is_subscription_package ? true : false;
        $validated['auto_enroll_courses'] = $request->has('auto_enroll_courses') && $request->auto_enroll_courses ? true : false;

        // Handle lifetime access
        if ($validated['is_lifetime']) {
            $validated['duration_days'] = null;
        }

        // Handle subscription plan IDs (convert to JSON array)
        if ($request->has('subscription_plan_ids') && is_array($request->subscription_plan_ids)) {
            $validated['subscription_plan_ids'] = array_values(array_filter($request->subscription_plan_ids));
        } else {
            $validated['subscription_plan_ids'] = null;
        }

        // Create package
        $package = Package::create($validated);

        // Sync features to pivot table (modern approach)
        $allFeatureKeys = array_merge($validDisplayKeys, $validFunctionalKeys);
        if (!empty($allFeatureKeys)) {
            foreach ($allFeatureKeys as $featureKey) {
                $package->features()->attach($featureKey, [
                    'is_enabled' => true
                ]);
            }
        }

        // Attach courses with sort order
        if ($request->has('course_ids') && is_array($request->course_ids)) {
            foreach ($request->course_ids as $index => $courseId) {
                $package->courses()->attach($courseId, ['sort_order' => $index + 1]);
            }
        }

        return redirect()
            ->route('admin.packages.show', $package)
            ->with('success', 'Package created successfully!');
    }

    /**
     * Display the specified package
     */
    public function show(Package $package)
    {
        $package->load([
            'courses',
            'userAccess' => function ($query) {
                $query->where('is_active', true)->latest()->take(10);
            }
        ]);

        $stats = [
            'total_courses' => $package->courses()->count(),
            'active_users' => $package->userAccess()->where('is_active', true)->count(),
            'total_revenue' => 0, // Calculate from orders if available
            'average_rating' => 0, // Calculate from course reviews
        ];

        return view('admin.packages.show', compact('package', 'stats'));
    }

    /**
     * Show the form for editing the package
     */
    public function edit(Package $package)
    {
        $courses = Course::where('status', 'published')->orderBy('title')->get();
        $selectedCourses = $package->courses()->pluck('course_id')->toArray();
        $features = \App\Models\PackageFeature::where('is_active', true)
            ->orderBy('type')
            ->orderBy('feature_name')
            ->get();

        // Get selected feature keys from package
        $selectedDisplayFeatures = is_array($package->display_features) ? $package->display_features : [];
        $selectedFunctionalFeatures = is_array($package->functional_features) ? $package->functional_features : [];

        // Get subscription plans
        $subscriptionPlans = \App\Models\SubscriptionPlan::orderBy('name')->get();
        $selectedPlanIds = is_array($package->subscription_plan_ids) ? $package->subscription_plan_ids : [];

        return view('admin.packages.edit', compact('package', 'courses', 'selectedCourses', 'features', 'selectedDisplayFeatures', 'selectedFunctionalFeatures', 'subscriptionPlans', 'selectedPlanIds'));
    }

    /**
     * Update the specified package
     */
    public function update(UpdatePackageRequest $request, Package $package)
    {
        $validated = $request->validated();

        // Store feature keys in the appropriate JSON columns
        // Filter out empty strings from hidden inputs and validate feature keys exist
        $displayKeys = array_values(array_filter(
            $request->input('display_feature_keys', []),
            fn($value) => !empty($value) && $value !== ''
        ));
        $functionalKeys = array_values(array_filter(
            $request->input('functional_feature_keys', []),
            fn($value) => !empty($value) && $value !== ''
        ));

        // Validate that feature keys exist and get valid ones
        $validDisplayKeys = !empty($displayKeys)
            ? \App\Models\PackageFeature::whereIn('feature_key', $displayKeys)
                ->where('type', 'display')
                ->pluck('feature_key')
                ->toArray()
            : [];

        $validFunctionalKeys = !empty($functionalKeys)
            ? \App\Models\PackageFeature::whereIn('feature_key', $functionalKeys)
                ->where('type', 'functional')
                ->pluck('feature_key')
                ->toArray()
            : [];

        // Set JSON fields for backwards compatibility
        $validated['display_features'] = $validDisplayKeys;
        $validated['functional_features'] = $validFunctionalKeys;

        // Generate slug if not provided or changed
        if (!$request->slug || $request->slug !== $package->slug) {
            $validated['slug'] = Str::slug($request->name);

            // Ensure slug is unique (excluding current package)
            $originalSlug = $validated['slug'];
            $counter = 1;
            while (Package::where('slug', $validated['slug'])->where('id', '!=', $package->id)->exists()) {
                $validated['slug'] = $originalSlug . '-' . $counter++;
            }
        }

        // Handle boolean checkboxes (default to false if not present)
        $validated['is_lifetime'] = $request->has('is_lifetime') && $request->is_lifetime ? true : false;
        $validated['is_featured'] = $request->has('is_featured') && $request->is_featured ? true : false;
        $validated['is_subscription_package'] = $request->has('is_subscription_package') && $request->is_subscription_package ? true : false;
        $validated['auto_enroll_courses'] = $request->has('auto_enroll_courses') && $request->auto_enroll_courses ? true : false;

        // Handle lifetime access
        if ($validated['is_lifetime']) {
            $validated['duration_days'] = null;
        }

        // Handle subscription plan IDs (convert to JSON array)
        if ($request->has('subscription_plan_ids') && is_array($request->subscription_plan_ids)) {
            $validated['subscription_plan_ids'] = array_values(array_filter($request->subscription_plan_ids));
        } else {
            $validated['subscription_plan_ids'] = null;
        }

        // Update package
        $package->update($validated);

        // Sync features in pivot table
        $allFeatureKeys = array_merge($validDisplayKeys, $validFunctionalKeys);
        $syncData = [];
        foreach ($allFeatureKeys as $featureKey) {
            $syncData[$featureKey] = ['is_enabled' => true];
        }
        $package->features()->sync($syncData);

        // Sync courses with sort order
        $courseData = [];
        if ($request->has('course_ids') && is_array($request->course_ids)) {
            foreach ($request->course_ids as $index => $courseId) {
                $courseData[$courseId] = ['sort_order' => $index + 1];
            }
        }
        $package->courses()->sync($courseData);

        return redirect()
            ->route('admin.packages.show', $package)
            ->with('success', 'Package updated successfully!');
    }

    /**
     * Remove the specified package
     */
    public function destroy(Package $package)
    {
        // Check if package has active users
        $activeUsers = $package->userAccess()->where('is_active', true)->count();

        if ($activeUsers > 0) {
            return back()->with('error', 'Cannot delete package with active users. Please archive it instead.');
        }

        $package->delete();

        return redirect()
            ->route('admin.packages.index')
            ->with('success', 'Package deleted successfully!');
    }

    /**
     * Toggle package status
     */
    public function toggleStatus(Package $package)
    {
        $newStatus = $package->status === 'published' ? 'draft' : 'published';
        $package->update(['status' => $newStatus]);

        return back()->with('success', 'Package status updated to ' . $newStatus);
    }

    /**
     * Toggle featured status
     */
    public function toggleFeatured(Package $package)
    {
        $package->update(['is_featured' => !$package->is_featured]);

        return back()->with('success', 'Featured status updated!');
    }

    /**
     * Duplicate package
     */
    public function duplicate(Package $package)
    {
        $newPackage = $package->replicate();
        $newPackage->name = $package->name . ' (Copy)';
        $newPackage->slug = $package->slug . '-copy-' . time();
        $newPackage->status = 'draft';
        $newPackage->save();

        // Copy course relationships
        foreach ($package->courses as $course) {
            $newPackage->courses()->attach($course->id, [
                'sort_order' => $course->pivot->sort_order
            ]);
        }

        return redirect()
            ->route('admin.packages.edit', $newPackage)
            ->with('success', 'Package duplicated successfully! You can now edit the copy.');
    }

    /**
     * Get courses in package
     */
    public function getCourses(Package $package)
    {
        $courses = $package->courses()
            ->withPivot('sort_order')
            ->orderBy('sort_order')
            ->get();

        return response()->json($courses);
    }

    /**
     * Reorder courses in package
     */
    public function reorderCourses(Request $request, Package $package)
    {
        $validated = $request->validate([
            'course_ids' => 'required|array',
            'course_ids.*' => 'exists:courses,id',
        ]);

        $courseData = [];
        foreach ($validated['course_ids'] as $index => $courseId) {
            $courseData[$courseId] = ['sort_order' => $index + 1];
        }

        $package->courses()->sync($courseData);

        return response()->json(['success' => true, 'message' => 'Course order updated']);
    }
}
