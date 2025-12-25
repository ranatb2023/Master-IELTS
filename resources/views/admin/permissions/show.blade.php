@extends('layouts.admin')

@section('title', 'Permission Details')

@section('content')
<div class="container mx-auto px-4 max-w-4xl">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $permission->name }}</h1>
            <p class="mt-1 text-sm text-gray-600">Permission details</p>
        </div>
        <a href="{{ route('admin.permissions.edit', $permission) }}" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
            Edit Permission
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <!-- Permission Info Card -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Permission Information</h2>
            <dl class="space-y-3">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Permission Name</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $permission->name }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Category</dt>
                    <dd class="mt-1 text-sm text-gray-900 capitalize">{{ ucfirst(explode('.', $permission->name)[0]) }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Assigned to Roles</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $permission->roles->count() }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Created At</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $permission->created_at->format('M d, Y H:i') }}</dd>
                </div>
            </dl>
        </div>

        <!-- Roles with this Permission -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Roles ({{ $permission->roles->count() }})</h2>
            @if($permission->roles->count() > 0)
                <ul class="space-y-2">
                    @foreach($permission->roles as $role)
                    <li class="flex items-center justify-between p-2 rounded-lg hover:bg-gray-50">
                        <div class="flex items-center gap-2">
                            <span class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 text-xs font-semibold">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </span>
                            <div>
                                <div class="text-sm font-medium text-gray-900">{{ ucfirst(str_replace('_', ' ', $role->name)) }}</div>
                                <div class="text-xs text-gray-500">{{ $role->users()->count() }} users</div>
                            </div>
                        </div>
                        <a href="{{ route('admin.roles.show', $role) }}" class="text-indigo-600 hover:text-indigo-900 text-sm">
                            View
                        </a>
                    </li>
                    @endforeach
                </ul>
            @else
                <p class="text-sm text-gray-500">This permission is not assigned to any roles</p>
            @endif
        </div>
    </div>

    <!-- Description Card -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">What this permission controls</h2>
        <div class="text-sm text-gray-600">
            @php
                $parts = explode('.', $permission->name);
                $resource = $parts[0] ?? '';
                $action = $parts[1] ?? '';
            @endphp

            <p class="mb-2">
                This permission allows users to <strong class="text-gray-900">{{ $action }}</strong>
                {{ $resource ? 'in the ' : '' }}<strong class="text-gray-900">{{ ucfirst($resource) }}</strong> module.
            </p>

            @if($permission->roles->count() > 0)
            <p class="mt-3 p-3 bg-blue-50 border border-blue-200 rounded-md">
                <strong>Currently assigned to:</strong>
                {{ $permission->roles->pluck('name')->map(fn($n) => ucfirst(str_replace('_', ' ', $n)))->join(', ') }}
            </p>
            @else
            <p class="mt-3 p-3 bg-yellow-50 border border-yellow-200 rounded-md">
                <strong>Warning:</strong> This permission is not assigned to any role and has no effect.
            </p>
            @endif
        </div>
    </div>

    <!-- Back Button -->
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.permissions.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Permissions
        </a>

        @if($permission->roles->count() === 0)
        <form action="{{ route('admin.permissions.destroy', $permission) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this permission?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                Delete Permission
            </button>
        </form>
        @endif
    </div>
</div>
@endsection
