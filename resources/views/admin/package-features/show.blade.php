@extends('layouts.admin')

@section('title', 'Feature Details')
@section('page-title', 'Feature Details')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">{{ $feature->feature_name }}</h2>
            <p class="mt-1 text-sm text-gray-600">Feature details and user access</p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('admin.package-features.edit', $feature) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                Edit Feature
            </a>
            <a href="{{ route('admin.package-features.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                Back to Features
            </a>
        </div>
    </div>

    <div class="space-y-6">
            <!-- Feature Details -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Feature Details</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Feature Name</label>
                            <p class="text-base text-gray-900">{{ $feature->feature_name }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Feature Key</label>
                            <code class="text-sm bg-gray-100 px-2 py-1 rounded">{{ $feature->feature_key }}</code>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Type</label>
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                {{ $feature->type === 'functional' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                {{ ucfirst($feature->type) }}
                            </span>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Status</label>
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                {{ $feature->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $feature->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-500 mb-1">Description</label>
                            <p class="text-base text-gray-900">{{ $feature->description ?: 'No description provided' }}</p>
                        </div>

                        @if($feature->implementation_details)
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-500 mb-1">Implementation Details</label>
                            <pre class="text-sm bg-gray-50 p-4 rounded border border-gray-200 overflow-x-auto">{{ json_encode($feature->implementation_details, JSON_PRETTY_PRINT) }}</pre>
                        </div>
                        @endif

                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Created At</label>
                            <p class="text-base text-gray-900">{{ $feature->created_at->format('M d, Y h:i A') }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Last Updated</label>
                            <p class="text-base text-gray-900">{{ $feature->updated_at->format('M d, Y h:i A') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Users with Access -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Users with Access</h3>
                        <span class="text-sm text-gray-500">{{ $users->total() }} total</span>
                    </div>

                    @if($users->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Granted At</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Expires At</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($users as $userAccess)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $userAccess->user->name }}</div>
                                                <div class="text-sm text-gray-500">{{ $userAccess->user->email }}</div>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-500">
                                                {{ $userAccess->access_granted_at->format('M d, Y') }}
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-500">
                                                {{ $userAccess->access_expires_at ? $userAccess->access_expires_at->format('M d, Y') : 'Never' }}
                                            </td>
                                            <td class="px-6 py-4">
                                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                                    {{ $userAccess->isValid() ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                    {{ $userAccess->isValid() ? 'Valid' : 'Expired' }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-right text-sm font-medium">
                                                <form action="{{ route('admin.package-features.revoke-access', [$feature, $userAccess->id]) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure?')">
                                                        Revoke
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        @if($users->hasPages())
                            <div class="mt-4">
                                {{ $users->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <p class="mt-2 text-sm text-gray-500">No users have access to this feature yet</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
