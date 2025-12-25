@extends('layouts.tutor')

@section('title', 'Student Engagement')
@section('page-title', 'Student Engagement Analytics')

@section('content')
    <div class="space-y-6">
        <div>
            <h2 class="text-2xl font-semibold text-gray-900">Student Engagement</h2>
        </div>

        <!-- Engagement Stats -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Active vs Inactive Students</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Active (Last 7 days)</span>
                        <span class="text-sm font-semibold text-green-600">{{ $activeStudents }}</span>
                    </div>
                    <div class="w-full h-4 bg-gray-200 rounded-full">
                        <div class="h-4 bg-green-500 rounded-full"
                            style="width: {{ ($activeStudents + $inactiveStudents) > 0 ? ($activeStudents / ($activeStudents + $inactiveStudents) * 100) : 0 }}%">
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Inactive</span>
                        <span class="text-sm font-semibold text-red-600">{{ $inactiveStudents }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900  mb-4">Total Students</h3>
                <div class="text-4xl font-bold text-indigo-600">{{ $activeStudents + $inactiveStudents }}</div>
                <p class="text-sm text-gray-500 mt-2">Across all your courses</p>
            </div>
        </div>

        <!-- Top Engaged Students -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Most Engaged Students</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Course</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Progress</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quizzes</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Assignments</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($topStudents as $enrollment)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $enrollment->user->name }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ $enrollment->course->title }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <span
                                            class="text-sm font-medium text-gray-900">{{ $enrollment->progress_percentage }}%</span>
                                        <div class="ml-2 w-16 h-2 bg-gray-200 rounded-full">
                                            <div class="h-2 bg-indigo-500 rounded-full"
                                                style="width: {{ $enrollment->progress_percentage }}%"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $enrollment->quiz_attempts_count }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $enrollment->assignment_submissions_count }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection