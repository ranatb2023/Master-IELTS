@extends('layouts.admin')

@section('title', 'Edit Permission')

@section('content')
<div class="container mx-auto px-4 max-w-2xl">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Edit Permission</h1>
        <p class="mt-1 text-sm text-gray-600">Update permission name</p>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('admin.permissions.update', $permission) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Permission Name -->
            <div class="mb-6">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Permission Name</label>
                <input type="text" name="name" id="name" value="{{ old('name', $permission->name) }}" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder="e.g., blog.publish, course.create">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500">Use format: resource.action (e.g., blog.publish, user.delete)</p>
            </div>

            <!-- Current Assignments Info -->
            @if($permission->roles->count() > 0)
            <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-md">
                <h3 class="text-sm font-semibold text-blue-900 mb-2">Currently Assigned To:</h3>
                <ul class="list-disc list-inside text-sm text-blue-800">
                    @foreach($permission->roles as $role)
                    <li>{{ ucfirst(str_replace('_', ' ', $role->name)) }} ({{ $role->users()->count() }} users)</li>
                    @endforeach
                </ul>
                <p class="mt-2 text-xs text-blue-700">
                    <strong>Note:</strong> Renaming this permission will affect all users with these roles.
                </p>
            </div>
            @endif

            <!-- Action Buttons -->
            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('admin.permissions.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                    Cancel
                </a>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                    Update Permission
                </button>
            </div>
        </form>
    </div>

    <!-- Danger Zone -->
    @if($permission->roles->count() === 0)
    <div class="mt-6 bg-white rounded-lg shadow p-6 border-l-4 border-red-500">
        <h3 class="text-lg font-semibold text-red-900 mb-2">Danger Zone</h3>
        <p class="text-sm text-gray-600 mb-4">
            This permission is not assigned to any roles. You can safely delete it if it's no longer needed.
        </p>
        <form action="{{ route('admin.permissions.destroy', $permission) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this permission? This action cannot be undone.');">
            @csrf
            @method('DELETE')
            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                Delete Permission
            </button>
        </form>
    </div>
    @endif
</div>
@endsection
