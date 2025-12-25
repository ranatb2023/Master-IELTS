@extends('layouts.tutor')

@section('title', 'Revenue Analytics')
@section('page-title', 'Revenue & Earnings')

@section('content')
    <div class="space-y-6">
        <div>
            <h2 class="text-2xl font-semibold text-gray-900">Revenue & Earnings</h2>
        </div>

        <!-- Revenue Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white shadow rounded-lg p-6">
                <p class="text-sm text-gray-500">Total Revenue</p>
                <p class="text-3xl font-bold text-green-600">${{ number_format($totalRevenue, 2) }}</p>
                <p class="text-xs text-gray-400 mt-1">All time</p>
            </div>
            <div class="bg-white shadow rounded-lg p-6">
                <p class="text-sm text-gray-500">This Month</p>
                <p class="text-3xl font-bold text-blue-600">${{ number_format($monthlyRevenue, 2) }}</p>
                <p class="text-xs text-gray-400 mt-1">{{ now()->format('F Y') }}</p>
            </div>
            <div class="bg-white shadow rounded-lg p-6">
                <p class="text-sm text-gray-500">This Year</p>
                <p class="text-3xl font-bold text-indigo-600">${{ number_format($yearlyRevenue, 2) }}</p>
                <p class="text-xs text-gray-400 mt-1">{{ now()->format('Y') }}</p>
            </div>
        </div>

        <!-- Revenue by Course -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Revenue by Course</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Course</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Enrollments</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Revenue</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Avg per Student</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($revenueByCourse as $course)
                            <tr>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $course->title }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ $course->total_enrollments }}
                                </td>
                                <td class="px-6 py-4 text-sm font-semibold text-green-600">
                                    ${{ number_format($course->total_revenue, 2) }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    ${{ number_format($course->avg_revenue_per_student, 2) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Revenue Over Time -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Revenue Over Time (Last 12 Months)</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Month</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Enrollments</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Revenue</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($revenueOverTime as $data)
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $data->month }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $data->enrollments }}</td>
                                <td class="px-6 py-4 text-sm font-semibold text-green-600">
                                    ${{ number_format($data->revenue, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection