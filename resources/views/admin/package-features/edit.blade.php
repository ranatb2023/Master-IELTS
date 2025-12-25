@extends('layouts.admin')

@section('title', 'Edit Package Feature')
@section('page-title', 'Edit Package Feature')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Edit Package Feature</h2>
            <p class="mt-1 text-sm text-gray-600">Update feature details</p>
        </div>
        <a href="{{ route('admin.package-features.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Features
        </a>
    </div>

    <form action="{{ route('admin.package-features.update', $feature) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Two Column Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column (2/3 width) -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Basic Information -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h2>

                    <div class="space-y-4">
                        <!-- Feature Name -->
                        <div>
                            <label for="feature_name" class="block text-sm font-medium text-gray-700">
                                Feature Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="feature_name" id="feature_name" value="{{ old('feature_name', $feature->feature_name) }}" required
                                class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('feature_name') border-red-500 @enderror">
                            @error('feature_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Feature Key -->
                        <div>
                            <label for="feature_key" class="block text-sm font-medium text-gray-700">
                                Feature Key <span class="text-red-500">*</span>
                                <span class="text-gray-500 font-normal">(Unique identifier)</span>
                            </label>
                            <input type="text" name="feature_key" id="feature_key" value="{{ old('feature_key', $feature->feature_key) }}" required
                                class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('feature_key') border-red-500 @enderror">
                            <p class="mt-1 text-sm text-yellow-600">Warning: Changing the key may break existing integrations</p>
                            @error('feature_key')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700">
                                Description
                            </label>
                            <textarea name="description" id="description" rows="3"
                                class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description', $feature->description) }}</textarea>
                            <p class="mt-1 text-sm text-gray-500">Brief description of what this feature provides</p>
                        </div>
                    </div>
                </div>

                <!-- Technical Details -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Technical Details</h2>

                    <div class="space-y-4">
                        <!-- Implementation Details (JSON) -->
                        <div>
                            <label for="implementation_details" class="block text-sm font-medium text-gray-700">
                                Implementation Details (JSON)
                                <span class="text-gray-500 font-normal">(Optional)</span>
                            </label>
                            <textarea name="implementation_details" id="implementation_details" rows="6"
                                class="mt-1 w-full font-mono text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('implementation_details') border-red-500 @enderror"
                            >{{ old('implementation_details', $feature->implementation_details ? json_encode($feature->implementation_details, JSON_PRETTY_PRINT) : '') }}</textarea>
                            @error('implementation_details')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-sm text-gray-500">Enter valid JSON for technical configuration (e.g., limits, settings). Leave empty if not needed.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column (1/3 width) -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Actions -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Actions</h2>

                    <div class="space-y-3">
                        <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-medium hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Update Feature
                        </button>
                        <a href="{{ route('admin.package-features.index') }}" class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            Cancel
                        </a>
                    </div>
                </div>

                <!-- Settings -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Settings</h2>

                    <div class="space-y-4">
                        <!-- Feature Type -->
                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                                Feature Type <span class="text-red-500">*</span>
                            </label>
                            <select name="type" id="type" required
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="display" {{ old('type', $feature->type) === 'display' ? 'selected' : '' }}>Display (Marketing/Visual)</option>
                                <option value="functional" {{ old('type', $feature->type) === 'functional' ? 'selected' : '' }}>Functional (Actual Feature)</option>
                            </select>
                            <p class="mt-1 text-sm text-gray-500">Display features are for marketing. Functional features provide actual capabilities.</p>
                        </div>

                        <!-- Is Active -->
                        <div>
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $feature->is_active) ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>
                                <div class="ml-3">
                                    <label for="is_active" class="text-sm font-medium text-gray-700">
                                        Active
                                    </label>
                                    <p class="text-sm text-gray-500">Feature is available for use</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Danger Zone -->
                <div class="bg-white rounded-lg shadow-sm p-6 border-2 border-red-200">
                    <h2 class="text-lg font-semibold text-red-800 mb-2">Danger Zone</h2>
                    <p class="text-sm text-red-600 mb-4">Once you delete this feature, there is no going back. Please be certain.</p>

                    <button type="button" onclick="document.getElementById('delete-form').submit();" class="w-full px-4 py-2 bg-red-600 text-white rounded-md text-sm font-medium hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        Delete Feature
                    </button>
                </div>
            </div>
        </div>
    </form>

    <!-- Delete Feature Form (hidden) -->
    <form id="delete-form" action="{{ route('admin.package-features.destroy', $feature) }}" method="POST" class="hidden" onsubmit="return confirm('Are you sure you want to delete this feature? This action cannot be undone.');">
        @csrf
        @method('DELETE')
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const featureNameInput = document.getElementById('feature_name');
    const featureKeyInput = document.getElementById('feature_key');
    let isManuallyEdited = true; // Default to true on edit page since key already exists

    // Check if feature key has been manually edited
    featureKeyInput.addEventListener('input', function() {
        if (this.value !== '') {
            isManuallyEdited = true;
        }
    });

    // Auto-generate feature key from feature name
    featureNameInput.addEventListener('input', function() {
        if (!isManuallyEdited) {
            const slug = this.value
                .toLowerCase()
                .trim()
                .replace(/[^a-z0-9\s-]/g, '') // Remove special characters
                .replace(/\s+/g, '_')          // Replace spaces with underscores
                .replace(/-+/g, '_')           // Replace hyphens with underscores
                .replace(/_+/g, '_')           // Replace multiple underscores with single
                .replace(/^_|_$/g, '');        // Remove leading/trailing underscores

            featureKeyInput.value = slug;
        }
    });

    // Reset manual edit flag when feature key is cleared
    featureKeyInput.addEventListener('blur', function() {
        if (this.value === '') {
            isManuallyEdited = false;
        }
    });
});
</script>
@endsection
