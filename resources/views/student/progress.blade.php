<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Learning Progress</h1>
                        <p class="mt-2 text-gray-600">Track your learning journey and achievements</p>
                    </div>
                    <a href="{{ route('student.dashboard') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Back to Dashboard
                    </a>
                </div>
            </div>

            <!-- Stats Overview -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Total Time Spent -->
                <div class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
                    <div class="flex items-center justify-between mb-2">
                        <div class="p-3 bg-white/20 rounded-lg">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <h3 class="text-2xl font-bold">{{ $stats['total_time_spent'] }}</h3>
                    <p class="text-indigo-100 text-sm mt-1">Total Learning Time</p>
                </div>

                <!-- Learning Streak -->
                <div class="bg-gradient-to-br from-orange-500 to-red-600 rounded-xl shadow-lg p-6 text-white">
                    <div class="flex items-center justify-between mb-2">
                        <div class="p-3 bg-white/20 rounded-lg">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"/>
                            </svg>
                        </div>
                    </div>
                    <h3 class="text-2xl font-bold">{{ $stats['current_streak'] }} days</h3>
                    <p class="text-orange-100 text-sm mt-1">Learning Streak</p>
                </div>

                <!-- Completed Lessons -->
                <div class="bg-gradient-to-br from-green-500 to-teal-600 rounded-xl shadow-lg p-6 text-white">
                    <div class="flex items-center justify-between mb-2">
                        <div class="p-3 bg-white/20 rounded-lg">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <h3 class="text-2xl font-bold">{{ $stats['total_lessons_completed'] }}</h3>
                    <p class="text-green-100 text-sm mt-1">Lessons Completed</p>
                </div>

                <!-- Average Quiz Score -->
                <div class="bg-gradient-to-br from-blue-500 to-cyan-600 rounded-xl shadow-lg p-6 text-white">
                    <div class="flex items-center justify-between mb-2">
                        <div class="p-3 bg-white/20 rounded-lg">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                            </svg>
                        </div>
                    </div>
                    <h3 class="text-2xl font-bold">{{ $stats['avg_quiz_score'] ? $stats['avg_quiz_score'] . '%' : 'N/A' }}</h3>
                    <p class="text-blue-100 text-sm mt-1">Avg Quiz Score</p>
                </div>
            </div>

            <!-- Course Progress -->
            <div class="bg-white rounded-xl shadow-lg p-8 mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Course Progress</h2>

                @if($courseProgress->isEmpty())
                    <div class="text-center py-12">
                        <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                        <p class="text-gray-500 text-lg">No course progress yet</p>
                        <a href="{{ route('student.courses.index') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                            Browse Courses
                        </a>
                    </div>
                @else
                    <div class="space-y-6">
                        @foreach($courseProgress as $progress)
                            <div class="border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow">
                                <div class="flex items-start justify-between mb-4">
                                    <div class="flex-1">
                                        <h3 class="text-lg font-semibold text-gray-900 mb-1">
                                            {{ $progress->course->title }}
                                        </h3>
                                        <p class="text-sm text-gray-500">Last accessed: {{ $progress->last_accessed_at->diffForHumans() }}</p>
                                    </div>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $progress->progress_percentage >= 100 ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                        {{ $progress->progress_percentage }}% Complete
                                    </span>
                                </div>

                                <!-- Progress Bar -->
                                <div class="mb-4">
                                    <div class="w-full bg-gray-200 rounded-full h-3">
                                        <div class="bg-gradient-to-r from-indigo-500 to-purple-600 h-3 rounded-full transition-all duration-300" style="width: {{ $progress->progress_percentage }}%"></div>
                                    </div>
                                </div>

                                <!-- Stats Grid -->
                                <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                                    <div class="text-center p-3 bg-gray-50 rounded-lg">
                                        <p class="text-2xl font-bold text-indigo-600">{{ $progress->completed_lessons }}/{{ $progress->total_lessons }}</p>
                                        <p class="text-xs text-gray-600 mt-1">Lessons</p>
                                    </div>
                                    <div class="text-center p-3 bg-gray-50 rounded-lg">
                                        <p class="text-2xl font-bold text-purple-600">{{ $progress->completed_quizzes }}/{{ $progress->total_quizzes }}</p>
                                        <p class="text-xs text-gray-600 mt-1">Quizzes</p>
                                    </div>
                                    <div class="text-center p-3 bg-gray-50 rounded-lg">
                                        <p class="text-2xl font-bold text-pink-600">{{ $progress->completed_assignments }}/{{ $progress->total_assignments }}</p>
                                        <p class="text-xs text-gray-600 mt-1">Assignments</p>
                                    </div>
                                    <div class="text-center p-3 bg-gray-50 rounded-lg">
                                        <p class="text-2xl font-bold text-green-600">{{ $progress->average_quiz_score ? round($progress->average_quiz_score, 1) . '%' : 'N/A' }}</p>
                                        <p class="text-xs text-gray-600 mt-1">Quiz Avg</p>
                                    </div>
                                    <div class="text-center p-3 bg-gray-50 rounded-lg">
                                        <p class="text-2xl font-bold text-orange-600">
                                            @php
                                                $hours = floor($progress->total_time_spent / 3600);
                                                $minutes = floor(($progress->total_time_spent % 3600) / 60);
                                            @endphp
                                            {{ $hours > 0 ? $hours . 'h' : '' }} {{ $minutes }}m
                                        </p>
                                        <p class="text-xs text-gray-600 mt-1">Time Spent</p>
                                    </div>
                                </div>

                                <!-- Continue Button -->
                                <div class="mt-4">
                                    <a href="{{ route('student.courses.learn', $progress->course) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                                        Continue Learning
                                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Recent Activity -->
            @if($recentLessons->isNotEmpty())
                <div class="bg-white rounded-xl shadow-lg p-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Recent Completed Lessons</h2>
                    <div class="space-y-3">
                        @foreach($recentLessons as $lessonProgress)
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                <div class="flex items-center space-x-4">
                                    <div class="p-2 bg-green-100 rounded-lg">
                                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $lessonProgress->progressable->title }}</p>
                                        <p class="text-sm text-gray-500">
                                            Completed {{ $lessonProgress->completed_at->diffForHumans() }}
                                            @if($lessonProgress->time_spent > 0)
                                                • Time spent: {{ floor($lessonProgress->time_spent / 60) }}m
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
