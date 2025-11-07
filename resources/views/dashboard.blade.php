@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <!-- Stats Cards -->
    <div class="bg-white p-6 rounded-xl shadow">
        <h2 class="text-gray-600 text-sm">Total Users</h2>
        <p class="text-3xl font-bold mt-2">1,245</p>
        <p class="text-green-600 text-sm mt-1">+5% from last week</p>
    </div>
    <div class="bg-white p-6 rounded-xl shadow">
        <h2 class="text-gray-600 text-sm">Revenue</h2>
        <p class="text-3xl font-bold mt-2">$12,350</p>
        <p class="text-green-600 text-sm mt-1">+8% growth</p>
    </div>
    <div class="bg-white p-6 rounded-xl shadow">
        <h2 class="text-gray-600 text-sm">Active Courses</h2>
        <p class="text-3xl font-bold mt-2">32</p>
        <p class="text-blue-600 text-sm mt-1">Stable this week</p>
    </div>
</div>

<!-- Recent activity table -->
<div class="mt-8 bg-white rounded-xl shadow">
    <div class="p-4 border-b">
        <h3 class="text-lg font-semibold">Recent Activity</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">John Doe</td>
                    <td class="px-6 py-4 whitespace-nowrap">Enrolled in Course “Laravel Basics”</td>
                    <td class="px-6 py-4 whitespace-nowrap">Nov 7, 2025</td>
                </tr>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">Sarah Smith</td>
                    <td class="px-6 py-4 whitespace-nowrap">Completed “React Fundamentals”</td>
                    <td class="px-6 py-4 whitespace-nowrap">Nov 6, 2025</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection
