@extends('layouts.admin')

@section('title', 'Create Subscription Plan')
@section('page-title', 'Create Subscription Plan')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Create New Subscription Plan</h1>
            <p class="mt-1 text-sm text-gray-600">Create a recurring subscription plan with Stripe integration</p>
        </div>
        <a href="{{ route('admin.subscription-plans.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Plans
        </a>
    </div>

    <!-- Form -->
    <form action="{{ route('admin.subscription-plans.store') }}" method="POST">
        @csrf

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
                        <!-- Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Plan Name *</label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('name') border-red-300 @enderror">
                            @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Slug -->
                        <div>
                            <label for="slug" class="block text-sm font-medium text-gray-700">Slug (URL-friendly name)</label>
                            <input type="text" name="slug" id="slug" value="{{ old('slug') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('slug') border-red-300 @enderror">
                            <p class="mt-1 text-sm text-gray-500">Leave blank to auto-generate from name</p>
                            @error('slug')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                            <textarea name="description" id="description" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('description') border-red-300 @enderror">{{ old('description') }}</textarea>
                            @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Pricing Configuration -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Pricing Configuration</h2>

                    <div class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Currency -->
                            <div>
                                <label for="currency" class="block text-sm font-medium text-gray-700">Currency *</label>
                                <select name="currency" id="currency" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('currency') border-red-300 @enderror">
                                    <option value="USD" {{ old('currency', 'USD') == 'USD' ? 'selected' : '' }}>USD ($)</option>
                                    <option value="EUR" {{ old('currency') == 'EUR' ? 'selected' : '' }}>EUR (€)</option>
                                    <option value="GBP" {{ old('currency') == 'GBP' ? 'selected' : '' }}>GBP (£)</option>
                                </select>
                                @error('currency')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Interval -->
                            <div>
                                <label for="interval" class="block text-sm font-medium text-gray-700">Billing Interval *</label>
                                <select name="interval" id="interval" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('interval') border-red-300 @enderror">
                                    <option value="month" {{ old('interval', 'month') == 'month' ? 'selected' : '' }}>Monthly</option>
                                    <option value="year" {{ old('interval') == 'year' ? 'selected' : '' }}>Yearly</option>
                                </select>
                                @error('interval')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Price -->
                            <div>
                                <label for="price" class="block text-sm font-medium text-gray-700">Regular Price *</label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">$</span>
                                    </div>
                                    <input type="number" name="price" id="price" step="0.01" min="0" value="{{ old('price') }}" required class="pl-7 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 @error('price') border-red-300 @enderror">
                                </div>
                                @error('price')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- First Month Price -->
                            <div>
                                <label for="first_month_price" class="block text-sm font-medium text-gray-700">First Month Price (Promotional)</label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">$</span>
                                    </div>
                                    <input type="number" name="first_month_price" id="first_month_price" step="0.01" min="0" value="{{ old('first_month_price') }}" class="pl-7 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 @error('first_month_price') border-red-300 @enderror">
                                </div>
                                <p class="mt-1 text-sm text-gray-500">Optional discounted price for first month</p>
                                @error('first_month_price')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Regular Price (After Promo) -->
                            <div>
                                <label for="regular_price" class="block text-sm font-medium text-gray-700">Regular Price (After Promotion)</label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">$</span>
                                    </div>
                                    <input type="number" name="regular_price" id="regular_price" step="0.01" min="0" value="{{ old('regular_price') }}" class="pl-7 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 @error('regular_price') border-red-300 @enderror">
                                </div>
                                <p class="mt-1 text-sm text-gray-500">Leave blank if same as regular price</p>
                                @error('regular_price')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Promotional Months -->
                            <div>
                                <label for="promotional_months" class="block text-sm font-medium text-gray-700">Promotional Period (Months)</label>
                                <input type="number" name="promotional_months" id="promotional_months" min="1" value="{{ old('promotional_months') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('promotional_months') border-red-300 @enderror">
                                <p class="mt-1 text-sm text-gray-500">How many months the promotional price applies</p>
                                @error('promotional_months')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Trial Days -->
                        <div>
                            <label for="trial_days" class="block text-sm font-medium text-gray-700">Free Trial Days</label>
                            <input type="number" name="trial_days" id="trial_days" min="0" value="{{ old('trial_days') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('trial_days') border-red-300 @enderror">
                            <p class="mt-1 text-sm text-gray-500">Number of free trial days (0 for no trial)</p>
                            @error('trial_days')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Stripe Integration -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Stripe Integration</h2>

                    <div class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Stripe Price ID -->
                            <div>
                                <label for="stripe_price_id" class="block text-sm font-medium text-gray-700">Stripe Price ID</label>
                                <input type="text" name="stripe_price_id" id="stripe_price_id" value="{{ old('stripe_price_id') }}" placeholder="price_xxxxxxxxxxxxx" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('stripe_price_id') border-red-300 @enderror">
                                <p class="mt-1 text-sm text-gray-500">From Stripe Dashboard → Products → Pricing</p>
                                @error('stripe_price_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Stripe Product ID -->
                            <div>
                                <label for="stripe_product_id" class="block text-sm font-medium text-gray-700">Stripe Product ID</label>
                                <input type="text" name="stripe_product_id" id="stripe_product_id" value="{{ old('stripe_product_id') }}" placeholder="prod_xxxxxxxxxxxxx" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('stripe_product_id') border-red-300 @enderror">
                                <p class="mt-1 text-sm text-gray-500">From Stripe Dashboard → Products</p>
                                @error('stripe_product_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- PayPal Plan ID -->
                        <div>
                            <label for="paypal_plan_id" class="block text-sm font-medium text-gray-700">PayPal Plan ID (Optional)</label>
                            <input type="text" name="paypal_plan_id" id="paypal_plan_id" value="{{ old('paypal_plan_id') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('paypal_plan_id') border-red-300 @enderror">
                            <p class="mt-1 text-sm text-gray-500">For future PayPal integration</p>
                            @error('paypal_plan_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Features -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Plan Features</h2>

                    <div class="space-y-4">
                        <div id="features-list">
                            <div class="flex items-center space-x-2 mb-2">
                                <input type="text" name="features[]" placeholder="Feature description" class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <button type="button" onclick="addFeature()" class="px-3 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <p class="text-sm text-gray-500">Add features that will be displayed to customers (e.g., "Unlimited course access", "Priority support")</p>
                    </div>
                </div>

                <!-- Included Packages & Courses -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Included Content</h2>

                    <div class="space-y-6">
                        <!-- Included Packages -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Included Packages</label>
                            <div class="max-h-60 overflow-y-auto border border-gray-300 rounded-md p-4 space-y-2">
                                @forelse($packages as $package)
                                    <div class="flex items-start">
                                        <input type="checkbox" name="included_package_ids[]" id="package_{{ $package->id }}" value="{{ $package->id }}" {{ in_array($package->id, old('included_package_ids', [])) ? 'checked' : '' }} class="mt-1 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                        <label for="package_{{ $package->id }}" class="ml-2 text-sm text-gray-700">
                                            {{ $package->name }}
                                            <span class="text-gray-500">({{ $package->courses()->count() }} courses)</span>
                                        </label>
                                    </div>
                                @empty
                                    <p class="text-sm text-gray-500">No packages available</p>
                                @endforelse
                            </div>
                            <p class="mt-2 text-sm text-gray-500">Subscribers will get automatic access to all courses in selected packages</p>
                        </div>

                        <!-- Included Individual Courses -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Included Individual Courses</label>
                            <div class="max-h-60 overflow-y-auto border border-gray-300 rounded-md p-4 space-y-2">
                                @forelse($courses as $course)
                                    <div class="flex items-start">
                                        <input type="checkbox" name="included_course_ids[]" id="course_{{ $course->id }}" value="{{ $course->id }}" {{ in_array($course->id, old('included_course_ids', [])) ? 'checked' : '' }} class="mt-1 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                        <label for="course_{{ $course->id }}" class="ml-2 text-sm text-gray-700">
                                            {{ $course->title }}
                                        </label>
                                    </div>
                                @empty
                                    <p class="text-sm text-gray-500">No courses available</p>
                                @endforelse
                            </div>
                            <p class="mt-2 text-sm text-gray-500">Add individual courses not included in packages</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column (1/3 width) -->
            <div class="space-y-6">
                <!-- Status & Settings -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Status & Settings</h2>

                    <div class="space-y-4">
                        <!-- Active Status -->
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            </div>
                            <div class="ml-3">
                                <label for="is_active" class="font-medium text-gray-700">Active</label>
                                <p class="text-sm text-gray-500">Plan is visible and available for purchase</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Actions</h2>

                    <div class="space-y-3">
                        <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Create Plan
                        </button>

                        <a href="{{ route('admin.subscription-plans.index') }}" class="w-full inline-flex justify-center items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Cancel
                        </a>
                    </div>
                </div>

                <!-- Help Text -->
                <div class="bg-blue-50 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-blue-800">Stripe Setup Required</h3>
                            <div class="mt-2 text-sm text-blue-700">
                                <p>Create product and price in Stripe Dashboard first, then copy the IDs here.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
function addFeature() {
    const featuresList = document.getElementById('features-list');
    const newFeature = document.createElement('div');
    newFeature.className = 'flex items-center space-x-2 mb-2';
    newFeature.innerHTML = `
        <input type="text" name="features[]" placeholder="Feature description" class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        <button type="button" onclick="removeFeature(this)" class="px-3 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    `;
    featuresList.appendChild(newFeature);
}

function removeFeature(button) {
    button.parentElement.remove();
}
</script>
@endsection
