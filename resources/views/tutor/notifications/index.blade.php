@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Notifications</h1>

        <!-- Tabs -->
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
                    <a href="?status=all"
                        class="@if(!request('status') || request('status') === 'all') border-indigo-500 text-indigo-600 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        All
                    </a>
                    <a href="?status=unread"
                        class="@if(request('status') === 'unread') border-indigo-500 text-indigo-600 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Unread
                        @if(auth()->user()->unreadNotifications->count() > 0)
                            <span
                                class="ml-2 bg-red-600 text-white text-xs px-2 py-1 rounded-full">{{ auth()->user()->unreadNotifications->count() }}</span>
                        @endif
                    </a>
                </nav>
            </div>

            <!-- Category Pills -->
            <div class="px-6 py-3 bg-gray-50 flex flex-wrap gap-2">
                <a href="?category=all&status={{ request('status', 'all') }}"
                    class="@if(!request('category') || request('category') === 'all') bg-indigo-600 text-white @else bg-white text-gray-700 @endif px-3 py-1 rounded-full text-sm border border-gray-300 hover:bg-indigo-50">
                    All
                </a>
                <a href="?category=students&status={{ request('status', 'all') }}"
                    class="@if(request('category') === 'students') bg-indigo-600 text-white @else bg-white text-gray-700 @endif px-3 py-1 rounded-full text-sm border border-gray-300 hover:bg-indigo-50">
                    Students
                </a>
                <a href="?category=assignments&status={{ request('status', 'all') }}"
                    class="@if(request('category') === 'assignments') bg-indigo-600 text-white @else bg-white text-gray-700 @endif px-3 py-1 rounded-full text-sm border border-gray-300 hover:bg-indigo-50">
                    Assignments
                </a>
                <a href="?category=courses&status={{ request('status', 'all') }}"
                    class="@if(request('category') === 'courses') bg-indigo-600 text-white @else bg-white text-gray-700 @endif px-3 py-1 rounded-full text-sm border border-gray-300 hover:bg-indigo-50">
                    Courses
                </a>
                <a href="?category=reviews&status={{ request('status', 'all') }}"
                    class="@if(request('category') === 'reviews') bg-indigo-600 text-white @else bg-white text-gray-700 @endif px-3 py-1 rounded-full text-sm border border-gray-300 hover:bg-indigo-50">
                    Reviews
                </a>
            </div>
        </div>

        <!-- Actions Bar -->
        @if($notifications->count() > 0)
            <div class="bg-white rounded-lg shadow p-4 mb-6 flex items-center justify-between">
                <p class="text-sm text-gray-600">
                    Showing {{ $notifications->count() }} of {{ $notifications->total() }} notifications
                </p>
                @if(auth()->user()->unreadNotifications->count() > 0)
                    <form action="{{ route('tutor.notifications.readAll') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm">
                            Mark All as Read
                        </button>
                    </form>
                @endif
            </div>
        @endif

        <!-- Notifications List -->
        <div class="bg-white rounded-lg shadow">
            @if($notifications->count() > 0)
                <ul class="divide-y divide-gray-200">
                    @foreach($notifications as $notification)
                        <li class="@if(is_null($notification->read_at)) bg-blue-50 @endif hover:bg-gray-50 transition">
                            <div class="px-6 py-4">
                                <div class="flex items-start">
                                    <!-- Icon -->
                                    <div class="flex-shrink-0">
                                        <div
                                            class="h-10 w-10 rounded-full bg-{{ $notification->data['color'] ?? 'blue' }}-100 flex items-center justify-center">
                                            <svg class="h-5 w-5 text-{{ $notification->data['color'] ?? 'blue' }}-600" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                            </svg>
                                        </div>
                                    </div>

                                    <!-- Content -->
                                    <div class="ml-4 flex-1">
                                        <div class="flex items-center justify-between">
                                            <p class="text-sm font-medium text-gray-900">
                                                @if(is_null($notification->read_at))
                                                    <span class="inline-block w-2 h-2 bg-blue-600 rounded-full mr-2"></span>
                                                @endif
                                                {{ $notification->data['title'] ?? 'Notification' }}
                                            </p>
                                            <p class="text-xs text-gray-500">
                                                {{ $notification->created_at->diffForHumans() }}
                                            </p>
                                        </div>
                                        <p class="mt-1 text-sm text-gray-600">
                                            {{ $notification->data['message'] ?? '' }}
                                        </p>
                                        <div class="mt-3 flex items-center space-x-4">
                                            @if($notification->data['action_url'] ?? null)
                                                <a href="{{ $notification->data['action_url'] }}"
                                                    onclick="event.preventDefault(); markAsRead('{{ $notification->id }}', '{{ $notification->data['action_url'] }}')"
                                                    class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                                                    View Details →
                                                </a>
                                            @endif
                                            @if(is_null($notification->read_at))
                                                <form action="{{ route('tutor.notifications.read', $notification->id) }}" method="POST"
                                                    class="inline">
                                                    @csrf
                                                    <button type="submit" class="text-sm text-gray-600 hover:text-gray-800">
                                                        Mark as Read
                                                    </button>
                                                </form>
                                            @endif
                                            <form action="{{ route('tutor.notifications.destroy', $notification->id) }}"
                                                method="POST" class="inline" onsubmit="return confirm('Delete this notification?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-sm text-red-600 hover:text-red-800">
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $notifications->links() }}
                </div>
            @else
                <!-- Empty State -->
                <div class="px-6 py-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No notifications</h3>
                    <p class="mt-1 text-sm text-gray-500">You're all caught up!</p>
                </div>
            @endif
        </div>
    </div>
@endsection