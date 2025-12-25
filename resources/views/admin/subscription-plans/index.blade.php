@extends('layouts.admin')

@section('title', 'Subscription Plans')
@section('page-title', 'Manage Subscription Plans')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Subscription Plans</h2>
            <p class="mt-1 text-sm text-gray-600">Manage subscription plans and pricing</p>
        </div>
        <a href="{{ route('admin.subscription-plans.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Create Plan
        </a>
    </div>

    <!-- Plans Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($plans as $plan)
        <div class="bg-white rounded-lg shadow-md overflow-hidden {{ $plan->is_active ? '' : 'opacity-60' }}">
            <div class="p-6">
                <!-- Plan Header -->
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">{{ $plan->name }}</h3>
                        @if($plan->is_active)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 mt-1">
                                Active
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 mt-1">
                                Inactive
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Price -->
                <div class="mb-4">
                    <div class="flex items-baseline">
                        <span class="text-4xl font-extrabold text-gray-900">{{ $plan->formatted_price }}</span>
                        <span class="ml-2 text-gray-500">/{{ $plan->interval }}</span>
                    </div>
                    @if($plan->trial_days)
                        <p class="mt-1 text-sm text-gray-500">{{ $plan->trial_days }}-day free trial</p>
                    @endif
                </div>

                <!-- Description -->
                @if($plan->description)
                <p class="text-sm text-gray-600 mb-4">{{ Str::limit($plan->description, 100) }}</p>
                @endif

                <!-- Features -->
                @if($plan->features && is_array($plan->features) && count($plan->features) > 0)
                <div class="mb-4">
                    <h4 class="text-sm font-semibold text-gray-900 mb-2">Features:</h4>
                    <ul class="space-y-2">
                        @foreach(array_slice($plan->features, 0, 3) as $feature)
                        <li class="flex items-start text-sm text-gray-600">
                            <svg class="h-5 w-5 text-green-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            {{ $feature }}
                        </li>
                        @endforeach
                        @if(count($plan->features) > 3)
                        <li class="text-sm text-gray-500 ml-7">+ {{ count($plan->features) - 3 }} more features</li>
                        @endif
                    </ul>
                </div>
                @endif

                <!-- Stats -->
                <div class="mb-4 pt-4 border-t border-gray-200">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Active Subscriptions:</span>
                        <span class="font-semibold text-gray-900">{{ $plan->subscriptions_count ?? 0 }}</span>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex space-x-2">
                    <a href="{{ route('admin.subscription-plans.edit', $plan) }}" class="flex-1 bg-indigo-600 text-white text-center px-4 py-2 rounded-md hover:bg-indigo-700 text-sm font-medium">
                        Edit
                    </a>
                    <a href="{{ route('admin.subscription-plans.show', $plan) }}" class="flex-1 bg-gray-200 text-gray-700 text-center px-4 py-2 rounded-md hover:bg-gray-300 text-sm font-medium">
                        View
                    </a>
                </div>

                <!-- Delete Button -->
                <form action="{{ route('admin.subscription-plans.destroy', $plan) }}" method="POST" class="mt-2" onsubmit="return confirm('Are you sure you want to delete this plan?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full bg-red-50 text-red-600 text-center px-4 py-2 rounded-md hover:bg-red-100 text-sm font-medium">
                        Delete Plan
                    </button>
                </form>
            </div>
        </div>
        @empty
        <div class="col-span-full">
            <div class="text-center py-12 bg-white rounded-lg shadow">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No subscription plans</h3>
                <p class="mt-1 text-sm text-gray-500">Get started by creating a new subscription plan.</p>
                <div class="mt-6">
                    <a href="{{ route('admin.subscription-plans.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Create Plan
                    </a>
                </div>
            </div>
        </div>
        @endforelse
    </div>
</div>
@endsection
