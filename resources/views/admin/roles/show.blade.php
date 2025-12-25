@extends('layouts.admin')

@section('title', 'Role Details')

@section('content')
<div class="container mx-auto px-4 max-w-4xl">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ ucfirst(str_replace('_', ' ', $role->name)) }}</h1>
            <p class="mt-1 text-sm text-gray-600">Role details and permissions</p>
        </div>
        <a href="{{ route('admin.roles.edit', $role) }}" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
            Edit Role
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <!-- Role Info Card -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Role Information</h2>
            <dl class="space-y-3">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Role Name</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $role->name }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Total Permissions</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $role->permissions->count() }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Total Users</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $role->users->count() }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Created At</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $role->created_at->format('M d, Y H:i') }}</dd>
                </div>
            </dl>
        </div>

        <!-- Users with this Role -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Users ({{ $role->users->count() }})</h2>
            @if($role->users->count() > 0)
                <ul class="space-y-2 max-h-64 overflow-y-auto">
                    @foreach($role->users->take(10) as $user)
                    <li class="flex items-center gap-2">
                        <span class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 text-xs font-semibold">
                            {{ substr($user->name, 0, 2) }}
                        </span>
                        <div>
                            <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                            <div class="text-xs text-gray-500">{{ $user->email }}</div>
                        </div>
                    </li>
                    @endforeach
                    @if($role->users->count() > 10)
                    <li class="text-sm text-gray-500 pt-2">
                        And {{ $role->users->count() - 10 }} more...
                    </li>
                    @endif
                </ul>
            @else
                <p class="text-sm text-gray-500">No users assigned to this role</p>
            @endif
        </div>
    </div>

    <!-- Permissions -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Permissions ({{ $role->permissions->count() }})</h2>
        @if($role->permissions->count() > 0)
            @php
                $groupedPermissions = $role->permissions->groupBy(function($permission) {
                    return explode('.', $permission->name)[0];
                });
            @endphp
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($groupedPermissions as $group => $permissions)
                <div class="border border-gray-200 rounded-lg p-4">
                    <h3 class="font-semibold text-gray-900 mb-2 capitalize">{{ ucfirst($group) }}</h3>
                    <ul class="space-y-1">
                        @foreach($permissions as $permission)
                        <li class="text-sm text-gray-700 flex items-center">
                            <svg class="w-4 h-4 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            {{ str_replace('.', ' ', $permission->name) }}
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endforeach
            </div>
        @else
            <p class="text-sm text-gray-500">No permissions assigned to this role</p>
        @endif
    </div>

    <!-- Back Button -->
    <div class="mt-6">
        <a href="{{ route('admin.roles.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Roles
        </a>
    </div>
</div>
@endsection
