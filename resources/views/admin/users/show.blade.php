@extends('layouts.admin')

@section('title', 'User Details')
@section('page-title', 'User Profile')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">User Profile</h2>
            <p class="mt-1 text-sm text-gray-600">Detailed information about {{ $user->name }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.users.edit', $user) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit User
            </a>
            <a href="{{ route('admin.users.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">
                Back to List
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Profile Card -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Basic Information -->
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Basic Information</h3>
                </div>
                <div class="p-6">
                    <div class="flex items-start mb-6">
                        <div class="h-24 w-24 rounded-full bg-indigo-600 flex items-center justify-center text-white text-3xl font-bold">
                            {{ substr($user->name, 0, 2) }}
                        </div>
                        <div class="ml-6 flex-1">
                            <h2 class="text-2xl font-bold text-gray-900">{{ $user->name }}</h2>
                            <p class="text-gray-600">{{ $user->email }}</p>
                            <div class="mt-2 flex flex-wrap gap-2">
                                @foreach($user->roles as $role)
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full
                                        @if($role->name === 'super_admin') bg-purple-100 text-purple-800
                                        @elseif($role->name === 'tutor') bg-blue-100 text-blue-800
                                        @else bg-green-100 text-green-800
                                        @endif">
                                        {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Status</dt>
                            <dd class="mt-1">
                                @if($user->is_active)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Inactive</span>
                                @endif
                                @if($user->banned_until && $user->banned_until->isFuture())
                                    <span class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        Banned until {{ $user->banned_until->format('M d, Y') }}
                                    </span>
                                @endif
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Email Verified</dt>
                            <dd class="mt-1">
                                @if($user->email_verified_at)
                                    <span class="flex items-center text-green-600">
                                        <svg class="w-5 h-5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                        Verified on {{ $user->email_verified_at->format('M d, Y') }}
                                    </span>
                                @else
                                    <span class="text-gray-500">Not verified</span>
                                @endif
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Phone</dt>
                            <dd class="mt-1 text-gray-900">{{ $user->phone ?? 'N/A' }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Country</dt>
                            <dd class="mt-1 text-gray-900">{{ $user->country ?? 'N/A' }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Joined Date</dt>
                            <dd class="mt-1 text-gray-900">{{ $user->created_at->format('M d, Y') }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
                            <dd class="mt-1 text-gray-900">{{ $user->updated_at->diffForHumans() }}</dd>
                        </div>

                        @if($user->last_active_at)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Last Active</dt>
                            <dd class="mt-1 text-gray-900">{{ $user->last_active_at->diffForHumans() }}</dd>
                        </div>
                        @endif

                        @if($user->profile && $user->profile->bio)
                        <div class="md:col-span-2">
                            <dt class="text-sm font-medium text-gray-500">Bio</dt>
                            <dd class="mt-1 text-gray-900">{{ $user->profile->bio }}</dd>
                        </div>
                        @endif
                    </dl>
                </div>
            </div>

            <!-- Role-Specific Information -->
            @if($user->hasRole('tutor'))
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Tutor Information</h3>
                    </div>
                    <div class="p-6">
                        <dl class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Total Courses</dt>
                                <dd class="mt-1 text-2xl font-semibold text-gray-900">{{ $stats['total_courses'] ?? 0 }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Published Courses</dt>
                                <dd class="mt-1 text-2xl font-semibold text-gray-900">{{ $stats['published_courses'] ?? 0 }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Total Students</dt>
                                <dd class="mt-1 text-2xl font-semibold text-gray-900">
                                    {{ $stats['total_students'] ?? 0 }}
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>
            @endif

            @if($user->hasRole('student'))
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Student Information</h3>
                    </div>
                    <div class="p-6">
                        <dl class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Enrolled Courses</dt>
                                <dd class="mt-1 text-2xl font-semibold text-gray-900">{{ $user->enrollments()->count() }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Completed Courses</dt>
                                <dd class="mt-1 text-2xl font-semibold text-gray-900">{{ $user->enrollments()->where('status', 'completed')->count() }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Certificates</dt>
                                <dd class="mt-1 text-2xl font-semibold text-gray-900">{{ $user->certificates()->count() }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Quick Actions -->
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Quick Actions</h3>
                </div>
                <div class="p-6 space-y-3">
                    <a href="{{ route('admin.users.edit', $user) }}" class="block w-full text-center px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                        Edit Profile
                    </a>
                    @if(!$user->email_verified_at)
                        <form method="POST" action="{{ route('admin.users.verify-email', $user) }}">
                            @csrf
                            <button type="submit" class="block w-full text-center px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                                Verify Email
                            </button>
                        </form>
                    @endif
                    <form method="POST" action="{{ route('admin.users.toggle-status', $user) }}" onsubmit="return confirm('Are you sure you want to {{ $user->is_active ? 'deactivate' : 'activate' }} this user?');">
                        @csrf
                        @if($user->is_active)
                            <button type="submit" class="block w-full text-center px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700">
                                Deactivate User
                            </button>
                        @else
                            <button type="submit" class="block w-full text-center px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                                Activate User
                            </button>
                        @endif
                    </form>
                    @if(auth()->id() !== $user->id)
                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Are you sure you want to delete this user? This action cannot be undone.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="block w-full text-center px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                                Delete User
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <!-- Account Statistics -->
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Account Statistics</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Total Logins</span>
                        <span class="text-sm font-semibold text-gray-900">{{ $user->login_count ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Activity Logs</span>
                        <span class="text-sm font-semibold text-gray-900">{{ $user->activities()->count() }}</span>
                    </div>
                    @if($user->hasRole('student'))
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Quiz Attempts</span>
                            <span class="text-sm font-semibold text-gray-900">{{ $user->quizAttempts()->count() }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
