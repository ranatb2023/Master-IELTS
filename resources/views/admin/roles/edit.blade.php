@extends('layouts.admin')

@section('title', 'Edit Role')

@section('content')
<div class="container mx-auto px-4 max-w-4xl">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Edit Role: {{ ucfirst(str_replace('_', ' ', $role->name)) }}</h1>
        <p class="mt-1 text-sm text-gray-600">Update role name and permissions</p>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('admin.roles.update', $role) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Role Name -->
            <div class="mb-6">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Role Name</label>
                <input type="text" name="name" id="name" value="{{ old('name', $role->name) }}" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                    @if(in_array($role->name, ['super_admin', 'tutor', 'student'])) readonly @endif>
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                @if(in_array($role->name, ['super_admin', 'tutor', 'student']))
                    <p class="mt-1 text-xs text-yellow-600">Core system roles cannot be renamed</p>
                @else
                    <p class="mt-1 text-xs text-gray-500">Use lowercase with underscores (e.g., content_manager)</p>
                @endif
            </div>

            <!-- Permissions -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-3">Permissions</label>
                <div class="border border-gray-300 rounded-md p-4 max-h-96 overflow-y-auto">
                    @foreach($permissions as $group => $groupPermissions)
                    <div class="mb-4 last:mb-0">
                        <h3 class="font-semibold text-gray-900 mb-2 capitalize">{{ ucfirst($group) }} Permissions</h3>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                            @foreach($groupPermissions as $permission)
                            <label class="flex items-center">
                                <input type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                                    {{ in_array($permission->name, old('permissions', $rolePermissions)) ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                <span class="ml-2 text-sm text-gray-700">{{ str_replace('.', ' ', $permission->name) }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
                @error('permissions')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('admin.roles.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                    Cancel
                </a>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                    Update Role
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
