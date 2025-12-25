@extends('layouts.admin')

@section('title', 'Enrollments')
@section('page-title', 'Manage Enrollments')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">All Enrollments</h2>
                <p class="mt-1 text-sm text-gray-600">Manage course enrollments and student progress</p>
            </div>
            <a href="{{ route('admin.enrollments.create') }}"
                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Create Enrollment
            </a>
        </div>

        <!-- Filters -->
        <div class="bg-white p-4 rounded-lg shadow">
            <form method="GET" action="{{ route('admin.enrollments.index') }}"
                class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Search student or course..."
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div>
                    <select name="course"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">All Courses</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}" {{ request('course') == $course->id ? 'selected' : '' }}>
                                {{ $course->title }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <select name="status"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                        <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                    </select>
                </div>

                <div>
                    <select name="payment_status"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">All Payment Status</option>
                        <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>Pending
                        </option>
                        <option value="refunded" {{ request('payment_status') == 'refunded' ? 'selected' : '' }}>Refunded
                        </option>
                        <option value="failed" {{ request('payment_status') == 'failed' ? 'selected' : '' }}>Failed</option>
                    </select>
                </div>

                <div class="flex space-x-2">
                    <button type="submit"
                        class="flex-1 bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">Filter</button>
                    <a href="{{ route('admin.enrollments.index') }}"
                        class="flex-1 bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400 text-center">Reset</a>
                </div>
            </form>
        </div>

        <!-- Bulk Actions Bar -->
        <div class="bg-white p-4 rounded-lg shadow">
            <form id="bulkActionForm" action="{{ route('admin.enrollments.bulk-action') }}" method="POST">
                @csrf
                <div class="flex items-center space-x-4">
                    <div class="flex items-center">
                        <input type="checkbox" id="selectAll"
                            class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <label for="selectAll" class="ml-2 text-sm text-gray-700 font-medium">Select All</label>
                    </div>

                    <select name="action"
                        class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        <option value="">Choose Action</option>
                        <option value="activate">Activate Selected</option>
                        <option value="suspend">Suspend Selected</option>
                        <option value="cancel">Cancel Selected</option>
                        <option value="delete">Delete Selected</option>
                    </select>

                    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700"
                        onclick="return confirm('Are you sure you want to perform this bulk action?');">
                        Apply to Selected
                    </button>

                    <span id="selectedCount" class="text-sm text-gray-600"></span>
                </div>
            </form>
        </div>

        <!-- Enrollments Table -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-12">
                            <span class="sr-only">Select</span>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Enrolled
                            Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Progress
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($enrollments as $enrollment)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input type="checkbox" name="enrollment_ids[]" value="{{ $enrollment->id }}"
                                    class="enrollment-checkbox rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                    form="bulkActionForm">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $enrollment->user->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $enrollment->user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ $enrollment->course->title }}</div>
                                <div class="text-sm text-gray-500">{{ $enrollment->course->instructor->name ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $enrollment->enrolled_at ? $enrollment->enrolled_at->format('M d, Y') : 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-full bg-gray-200 rounded-full h-2 mr-2">
                                        <div class="bg-indigo-600 h-2 rounded-full"
                                            style="width: {{ $enrollment->progress_percentage ?? 0 }}%"></div>
                                    </div>
                                    <span
                                        class="text-sm text-gray-700">{{ number_format($enrollment->progress_percentage ?? 0, 0) }}%</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusColors = [
                                        'active' => 'bg-green-100 text-green-800',
                                        'completed' => 'bg-blue-100 text-blue-800',
                                        'suspended' => 'bg-red-100 text-red-800',
                                        'expired' => 'bg-gray-100 text-gray-800',
                                    ];
                                    $color = $statusColors[$enrollment->status] ?? 'bg-gray-100 text-gray-800';
                                @endphp
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $color }}">
                                    {{ ucfirst($enrollment->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $paymentColors = [
                                        'paid' => 'bg-green-100 text-green-800',
                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                        'refunded' => 'bg-red-100 text-red-800',
                                        'failed' => 'bg-red-100 text-red-800',
                                    ];
                                    $color = $paymentColors[$enrollment->payment_status] ?? 'bg-gray-100 text-gray-800';
                                @endphp
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $color }}">
                                    {{ ucfirst($enrollment->payment_status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('admin.enrollments.show', $enrollment) }}"
                                    class="text-indigo-600 hover:text-indigo-900 mr-3">View</a>
                                <a href="{{ route('admin.enrollments.edit', $enrollment) }}"
                                    class="text-yellow-600 hover:text-yellow-900 mr-3">Edit</a>
                                <form action="{{ route('admin.enrollments.destroy', $enrollment) }}" method="POST"
                                    class="inline"
                                    onsubmit="return confirm('Are you sure you want to delete this enrollment?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                No enrollments found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $enrollments->links() }}
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const selectAllCheckbox = document.getElementById('selectAll');
                const enrollmentCheckboxes = document.querySelectorAll('.enrollment-checkbox');
                const selectedCountSpan = document.getElementById('selectedCount');
                const bulkActionForm = document.getElementById('bulkActionForm');

                // Update the count of selected items
                function updateSelectedCount() {
                    const checkedCount = document.querySelectorAll('.enrollment-checkbox:checked').length;
                    if (checkedCount > 0) {
                        selectedCountSpan.textContent = `${checkedCount} enrollment(s) selected`;
                    } else {
                        selectedCountSpan.textContent = '';
                    }
                }

                // Select/Deselect all checkboxes
                selectAllCheckbox.addEventListener('change', function () {
                    enrollmentCheckboxes.forEach(checkbox => {
                        checkbox.checked = this.checked;
                    });
                    updateSelectedCount();
                });

                // Update select all checkbox state when individual checkboxes change
                enrollmentCheckboxes.forEach(checkbox => {
                    checkbox.addEventListener('change', function () {
                        const allChecked = Array.from(enrollmentCheckboxes).every(cb => cb.checked);
                        const someChecked = Array.from(enrollmentCheckboxes).some(cb => cb.checked);

                        selectAllCheckbox.checked = allChecked;
                        selectAllCheckbox.indeterminate = someChecked && !allChecked;

                        updateSelectedCount();
                    });
                });

                // Validate form submission
                bulkActionForm.addEventListener('submit', function (e) {
                    const checkedCount = document.querySelectorAll('.enrollment-checkbox:checked').length;
                    if (checkedCount === 0) {
                        e.preventDefault();
                        alert('Please select at least one enrollment.');
                        return false;
                    }
                });

                // Initial count update
                updateSelectedCount();
            });
        </script>
    @endpush
@endsection