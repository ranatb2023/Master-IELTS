@extends('layouts.tutor')

@section('title', 'Course Performance')
@section('page-title', 'Course Performance Analytics')

@section('content')
    <div class="space-y-6">
        <div>
            <h2 class="text-2xl font-semibold text-gray-900">Course Performance</h2>
        </div>

        <!-- Performance Table -->
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Course</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Students</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Completion Rate</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Avg Quiz Score</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Avg Assignment</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rating</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($courses as $course)
                        <tr>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $course->title }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ $course->enrollments_count }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <span class="text-sm font-medium text-gray-900">{{ $course->completion_rate }}%</span>
                                    <div class="ml-2 w-16 h-2 bg-gray-200 rounded-full">
                                        <div class="h-2 bg-green-500 rounded-full"
                                            style="width: {{ $course->completion_rate }}%"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ number_format($course->avg_quiz_score, 1) }}%
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ number_format($course->avg_assignment_grade, 1) }}%
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ number_format($course->average_rating, 1) }} ⭐
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection