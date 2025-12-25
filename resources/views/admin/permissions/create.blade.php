@extends('layouts.admin')

@section('title', 'Create Permission')

@section('content')
<div class="container mx-auto px-4 max-w-2xl">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Create New Permission</h1>
        <p class="mt-1 text-sm text-gray-600">Add a new permission to the system</p>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('admin.permissions.store') }}" method="POST">
            @csrf

            <!-- Permission Name -->
            <div class="mb-6">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Permission Name</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder="e.g., blog.publish, course.create, user.delete">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500">Use format: resource.action (e.g., blog.publish, user.delete)</p>
            </div>

            <!-- Common Permission Examples -->
            <div class="mb-6 p-4 bg-gray-50 border border-gray-200 rounded-md">
                <h3 class="text-sm font-semibold text-gray-900 mb-2">Common Permission Patterns:</h3>
                <ul class="text-xs text-gray-600 space-y-1">
                    <li><strong>CRUD Operations:</strong> resource.create, resource.view, resource.update, resource.delete</li>
                    <li><strong>Special Actions:</strong> resource.publish, resource.approve, resource.export</li>
                    <li><strong>Management:</strong> resource.manage, settings.update, reports.view</li>
                </ul>
            </div>

            <!-- Info Box -->
            <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-md">
                <p class="text-sm text-blue-800">
                    <strong>Note:</strong> After creating a permission, you need to assign it to roles in the Roles management section.
                </p>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('admin.permissions.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                    Cancel
                </a>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                    Create Permission
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
