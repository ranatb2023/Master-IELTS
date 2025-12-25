@extends('layouts.admin')

@section('title', 'Edit Package')
@section('page-title', 'Edit Package')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Edit Package</h1>
            <p class="mt-1 text-sm text-gray-600">Update package details</p>
        </div>
        <a href="{{ route('admin.packages.show', $package) }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back
        </a>
    </div>

    <!-- Form -->
    <form action="{{ route('admin.packages.update', $package) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Validation Errors -->
        @if ($errors->any())
            <div class="bg-red-50 border-l-4 border-red-400 p-4 rounded">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">Please fix the following errors:</h3>
                        <ul class="mt-2 list-disc list-inside text-sm text-red-700">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <!-- Two Column Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column (2/3 width) -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Basic Information -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h2>

                    <div class="space-y-4">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Package Name *</label>
                            <input type="text" name="name" id="name" value="{{ old('name', $package->name) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('name') border-red-300 @enderror">
                            @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="slug" class="block text-sm font-medium text-gray-700">Slug</label>
                            <input type="text" name="slug" id="slug" value="{{ old('slug', $package->slug) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('slug') border-red-300 @enderror">
                            @error('slug')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                            <textarea name="description" id="description" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('description') border-red-300 @enderror">{{ old('description', $package->description) }}</textarea>
                            @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Pricing & Duration -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Pricing & Duration</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                    <label for="price" class="block text-sm font-medium text-gray-700">Regular Price *</label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">$</span>
                        </div>
                        <input type="number" name="price" id="price" step="0.01" min="0" value="{{ old('price', $package->price) }}" required class="pl-7 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 @error('price') border-red-300 @enderror">
                    </div>
                    @error('price')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="sale_price" class="block text-sm font-medium text-gray-700">Sale Price</label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">$</span>
                        </div>
                        <input type="number" name="sale_price" id="sale_price" step="0.01" min="0" value="{{ old('sale_price', $package->sale_price) }}" class="pl-7 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 @error('sale_price') border-red-300 @enderror">
                    </div>
                    @error('sale_price')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                    </div>

                    <!-- Duration -->
                    <div class="pt-4 border-t border-gray-200">
                        <div class="space-y-4">
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input type="checkbox" name="is_lifetime" id="is_lifetime" value="1" {{ old('is_lifetime', $package->is_lifetime) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" onchange="toggleDuration()">
                                </div>
                                <div class="ml-3">
                                    <label for="is_lifetime" class="text-sm font-medium text-gray-700">Lifetime Access</label>
                                    <p class="text-sm text-gray-500">Users get permanent access to this package</p>
                                </div>
                            </div>

                            <div id="duration-field">
                                <label for="duration_days" class="block text-sm font-medium text-gray-700">Duration (days) *</label>
                                <input type="number" name="duration_days" id="duration_days" min="1" value="{{ old('duration_days', $package->duration_days) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('duration_days') border-red-300 @enderror">
                                @error('duration_days')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Courses -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Included Courses</h2>

                    <div class="space-y-4">
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input type="checkbox" name="auto_enroll_courses" id="auto_enroll_courses" value="1" {{ old('auto_enroll_courses', $package->auto_enroll_courses) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            </div>
                            <div class="ml-3">
                                <label for="auto_enroll_courses" class="text-sm font-medium text-gray-700">Auto-Enroll in Courses</label>
                                <p class="text-sm text-gray-500">Automatically enroll users when they purchase this package</p>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Select Courses</label>
                            <div class="border border-gray-300 rounded-md max-h-64 overflow-y-auto p-2 space-y-2">
                                @forelse($courses as $course)
                                <label class="flex items-center p-2 hover:bg-gray-50 rounded cursor-pointer">
                                    <input type="checkbox" name="course_ids[]" value="{{ $course->id }}" {{ in_array($course->id, old('course_ids', $selectedCourses)) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <span class="ml-3 text-sm text-gray-900">{{ $course->title }}</span>
                                </label>
                                @empty
                                <p class="text-sm text-gray-500 p-2">No courses available</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column (1/3 width) -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Settings -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Settings</h2>

                    <div class="space-y-4">
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input type="checkbox" name="is_featured" id="is_featured" value="1" {{ old('is_featured', $package->is_featured) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            </div>
                            <div class="ml-3">
                                <label for="is_featured" class="text-sm font-medium text-gray-700">Featured Package</label>
                                <p class="text-sm text-gray-500">Display prominently</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input type="checkbox" name="is_subscription_package" id="is_subscription_package" value="1" {{ old('is_subscription_package', $package->is_subscription_package) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" onchange="toggleSubscriptionPlans()">
                            </div>
                            <div class="ml-3">
                                <label for="is_subscription_package" class="text-sm font-medium text-gray-700">Subscription Package</label>
                                <p class="text-sm text-gray-500">Requires recurring billing</p>
                            </div>
                        </div>

                        <div id="subscription-plans-section" style="display: none;">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Link to Subscription Plans</label>
                            <div class="border border-gray-300 rounded-md max-h-48 overflow-y-auto p-2 space-y-2">
                                @forelse($subscriptionPlans as $plan)
                                <label class="flex items-center p-2 hover:bg-gray-50 rounded cursor-pointer">
                                    <input type="checkbox" name="subscription_plan_ids[]" value="{{ $plan->id }}" {{ in_array($plan->id, old('subscription_plan_ids', $selectedPlanIds)) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <span class="ml-3 text-sm text-gray-900">{{ $plan->name }} - ${{ number_format($plan->price, 2) }}/{{ $plan->interval }}</span>
                                </label>
                                @empty
                                <p class="text-sm text-gray-500 p-2">No subscription plans available</p>
                                @endforelse
                            </div>
                            <p class="mt-1 text-sm text-gray-500">Select Stripe subscription plans for recurring billing</p>
                        </div>

                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                            <select name="status" id="status" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="draft" {{ old('status', $package->status) === 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="published" {{ old('status', $package->status) === 'published' ? 'selected' : '' }}>Published</option>
                                <option value="archived" {{ old('status', $package->status) === 'archived' ? 'selected' : '' }}>Archived</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Actions</h2>

                    <div class="space-y-3">
                        <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-medium hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Update Package
                        </button>
                        <a href="{{ route('admin.packages.show', $package) }}" class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            Cancel
                        </a>
                    </div>
                </div>

                <!-- Package Features -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Package Features</h2>
                    <p class="text-sm text-gray-600 mb-4">Select features to include</p>

                    <div class="space-y-6">
                <!-- Display Features -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Display Features
                        <span class="font-normal text-gray-500">(Marketing/Visual features shown on package card)</span>
                    </label>
                    <!-- Hidden input to ensure array is submitted even when empty -->
                    <input type="hidden" name="display_feature_keys[]" value="">
                    <div class="border border-gray-300 rounded-md max-h-48 overflow-y-auto p-2 space-y-2">
                        @php
                            $displayFeatures = $features->where('type', 'display');
                        @endphp
                        @forelse($displayFeatures as $feature)
                        <label class="flex items-start p-2 hover:bg-gray-50 rounded cursor-pointer">
                            <input type="checkbox" name="display_feature_keys[]" value="{{ $feature->feature_key }}"
                                {{ in_array($feature->feature_key, old('display_feature_keys', $selectedDisplayFeatures)) ? 'checked' : '' }}
                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 mt-1">
                            <div class="ml-3 flex-1">
                                <span class="text-sm font-medium text-gray-900">{{ $feature->feature_name }}</span>
                                @if($feature->description)
                                <p class="text-xs text-gray-500">{{ $feature->description }}</p>
                                @endif
                            </div>
                        </label>
                        @empty
                        <p class="text-sm text-gray-500 p-2">No display features available. <a href="{{ route('admin.package-features.create') }}" class="text-indigo-600 hover:text-indigo-700">Create one</a></p>
                        @endforelse
                    </div>
                </div>

                <!-- Functional Features -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Functional Features
                        <span class="font-normal text-gray-500">(Actual system features users get access to)</span>
                    </label>
                    <!-- Hidden input to ensure array is submitted even when empty -->
                    <input type="hidden" name="functional_feature_keys[]" value="">
                    <div class="border border-gray-300 rounded-md max-h-48 overflow-y-auto p-2 space-y-2">
                        @php
                            $functionalFeatures = $features->where('type', 'functional');
                        @endphp
                        @forelse($functionalFeatures as $feature)
                        <label class="flex items-start p-2 hover:bg-gray-50 rounded cursor-pointer">
                            <input type="checkbox" name="functional_feature_keys[]" value="{{ $feature->feature_key }}"
                                {{ in_array($feature->feature_key, old('functional_feature_keys', $selectedFunctionalFeatures)) ? 'checked' : '' }}
                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 mt-1">
                            <div class="ml-3 flex-1">
                                <span class="text-sm font-medium text-gray-900">{{ $feature->feature_name }}</span>
                                <code class="text-xs text-gray-600 bg-gray-100 px-1 py-0.5 rounded ml-2">{{ $feature->feature_key }}</code>
                                @if($feature->description)
                                <p class="text-xs text-gray-500">{{ $feature->description }}</p>
                                @endif
                            </div>
                        </label>
                        @empty
                        <p class="text-sm text-gray-500 p-2">No functional features available. <a href="{{ route('admin.package-features.create') }}" class="text-indigo-600 hover:text-indigo-700">Create one</a></p>
                        @endforelse
                    </div>
                </div>
                    </div>
                </div>

                <!-- Danger Zone -->
                <div class="bg-white rounded-lg shadow-sm p-6 border-2 border-red-200">
                    <h2 class="text-lg font-semibold text-red-800 mb-2">Danger Zone</h2>
                    <p class="text-sm text-red-600 mb-4">Once you delete this package, there is no going back. Please be certain.</p>

                    <button type="button" onclick="document.getElementById('delete-form').submit();" class="w-full px-4 py-2 bg-red-600 text-white rounded-md text-sm font-medium hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        Delete Package
                    </button>
                </div>
            </div>
        </div>
    </form>

    <!-- Delete Package Form (hidden) -->
    <form id="delete-form" action="{{ route('admin.packages.destroy', $package) }}" method="POST" class="hidden" onsubmit="return confirm('Are you sure you want to delete this package? This action cannot be undone.');">
        @csrf
        @method('DELETE')
    </form>
</div>

@push('scripts')
<script>
function toggleSubscriptionPlans() {
    const isSubscription = document.getElementById('is_subscription_package').checked;
    const plansSection = document.getElementById('subscription-plans-section');

    plansSection.style.display = isSubscription ? 'block' : 'none';
}

function toggleDuration() {
    const isLifetime = document.getElementById('is_lifetime').checked;
    const durationField = document.getElementById('duration-field');
    const durationInput = document.getElementById('duration_days');

    if (isLifetime) {
        durationField.style.display = 'none';
        durationInput.required = false;
    } else {
        durationField.style.display = 'block';
        durationInput.required = true;
    }
}

document.addEventListener('DOMContentLoaded', function() {
    toggleDuration();
    toggleSubscriptionPlans();
});
</script>
@endpush
@endsection
