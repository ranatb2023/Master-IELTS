@extends('layouts.admin')

@section('title', 'Create Enrollment')
@section('page-title', 'Create New Enrollment')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Create New Enrollment</h2>
            <p class="mt-1 text-sm text-gray-600">Manually enroll a student in a course</p>
        </div>
        <a href="{{ route('admin.enrollments.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back
        </a>
    </div>

    <!-- Form -->
    <form action="{{ route('admin.enrollments.store') }}" method="POST">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content - 2 columns -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Student & Course Selection -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Enrollment Details</h3>
                        <p class="mt-1 text-sm text-gray-500">Select the student and course for this enrollment</p>
                    </div>
                    <div class="p-6 space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Student Selection -->
                            <div>
                                <label for="user_id" class="block text-sm font-medium text-gray-700">
                                    Student *
                                </label>
                                <select id="user_id"
                                        name="user_id"
                                        required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Select a student...</option>
                                    @foreach($students as $student)
                                        <option value="{{ $student->id }}" {{ old('user_id') == $student->id ? 'selected' : '' }}>
                                            {{ $student->name }} ({{ $student->email }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('user_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">Total: {{ $students->count() }} students</p>
                            </div>

                            <!-- Course Selection -->
                            <div>
                                <label for="course_id" class="block text-sm font-medium text-gray-700">
                                    Course *
                                </label>
                                <select id="course_id"
                                        name="course_id"
                                        required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Select a course...</option>
                                    @foreach($courses as $course)
                                        <option value="{{ $course->id }}"
                                                data-price="{{ $course->is_free ? 0 : $course->price }}"
                                                data-is-free="{{ $course->is_free ? '1' : '0' }}"
                                                {{ old('course_id') == $course->id ? 'selected' : '' }}>
                                            {{ $course->title }}
                                            @if($course->is_free)
                                                (Free)
                                            @else
                                                (${{ number_format($course->price, 2) }})
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('course_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">Total: {{ $courses->count() }} courses</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Information -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Payment Information</h3>
                        <p class="mt-1 text-sm text-gray-500">Configure payment details for this enrollment</p>
                    </div>
                    <div class="p-6 space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Payment Status -->
                            <div>
                                <label for="payment_status" class="block text-sm font-medium text-gray-700">
                                    Payment Status *
                                </label>
                                <select id="payment_status"
                                        name="payment_status"
                                        required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="free" {{ old('payment_status') == 'free' ? 'selected' : '' }}>Free</option>
                                    <option value="completed" {{ old('payment_status', 'completed') == 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="pending" {{ old('payment_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="failed" {{ old('payment_status') == 'failed' ? 'selected' : '' }}>Failed</option>
                                </select>
                                @error('payment_status')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Amount Paid -->
                            <div id="amount_paid_container">
                                <label for="amount_paid" class="block text-sm font-medium text-gray-700">
                                    Amount Paid
                                </label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">$</span>
                                    </div>
                                    <input type="number"
                                           step="0.01"
                                           min="0"
                                           id="amount_paid"
                                           name="amount_paid"
                                           value="{{ old('amount_paid', '0.00') }}"
                                           class="pl-7 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>
                                @error('amount_paid')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Additional Information -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Additional Information</h3>
                        <p class="mt-1 text-sm text-gray-500">Optional settings and notes</p>
                    </div>
                    <div class="p-6 space-y-6">
                        <!-- Notes -->
                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700">
                                Notes (Optional)
                            </label>
                            <textarea id="notes"
                                      name="notes"
                                      rows="4"
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                      placeholder="Add any notes about this enrollment...">{{ old('notes') }}</textarea>
                            @error('notes')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">Internal notes visible only to administrators</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar - 1 column -->
            <div class="space-y-6">
                <!-- Enrollment Settings -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Settings</h3>
                    </div>
                    <div class="p-6 space-y-6">
                        <!-- Enrollment Source -->
                        <div>
                            <label for="enrollment_source" class="block text-sm font-medium text-gray-700">
                                Enrollment Source
                            </label>
                            <select id="enrollment_source"
                                    name="enrollment_source"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="admin" {{ old('enrollment_source', 'admin') == 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="manual" {{ old('enrollment_source') == 'manual' ? 'selected' : '' }}>Manual</option>
                                <option value="package" {{ old('enrollment_source') == 'package' ? 'selected' : '' }}>Package</option>
                                <option value="import" {{ old('enrollment_source') == 'import' ? 'selected' : '' }}>Import</option>
                            </select>
                            @error('enrollment_source')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Expiry Date -->
                        <div>
                            <label for="expires_at" class="block text-sm font-medium text-gray-700">
                                Expiry Date (Optional)
                            </label>
                            <input type="datetime-local"
                                   id="expires_at"
                                   name="expires_at"
                                   value="{{ old('expires_at') }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <p class="mt-1 text-xs text-gray-500">Leave empty for lifetime access</p>
                            @error('expires_at')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Quick Info Card -->
                <div class="bg-gradient-to-br from-indigo-50 to-indigo-100 rounded-lg p-6 border border-indigo-200">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-indigo-900">Quick Tips</h3>
                            <div class="mt-2 text-sm text-indigo-800">
                                <ul class="list-disc list-inside space-y-1">
                                    <li>Select course to auto-fill amount</li>
                                    <li>Free courses set payment to free</li>
                                    <li>Expiry date is optional</li>
                                    <li>Student will receive notification</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="bg-white shadow rounded-lg p-6">
                    <div class="space-y-3">
                        <button type="submit"
                                class="w-full inline-flex justify-center items-center px-4 py-3 bg-indigo-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Create Enrollment
                        </button>
                        <a href="{{ route('admin.enrollments.index') }}"
                           class="w-full inline-flex justify-center items-center px-4 py-3 bg-white border border-gray-300 rounded-md font-semibold text-sm text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition">
                            Cancel
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    // Auto-fill amount based on course selection
    document.getElementById('course_id').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const price = selectedOption.dataset.price || '0.00';
        const isFree = selectedOption.dataset.isFree === '1';

        const amountInput = document.getElementById('amount_paid');
        const paymentStatus = document.getElementById('payment_status');

        if (isFree) {
            amountInput.value = '0.00';
            paymentStatus.value = 'free';
        } else {
            amountInput.value = price;
            paymentStatus.value = 'completed';
        }
    });

    // Toggle amount paid field based on payment status
    document.getElementById('payment_status').addEventListener('change', function() {
        const amountContainer = document.getElementById('amount_paid_container');
        const amountInput = document.getElementById('amount_paid');

        if (this.value === 'free') {
            amountInput.value = '0.00';
            amountInput.readOnly = true;
            amountContainer.classList.add('opacity-50');
        } else {
            amountInput.readOnly = false;
            amountContainer.classList.remove('opacity-50');
        }
    });

    // Trigger on page load
    document.addEventListener('DOMContentLoaded', function() {
        const paymentStatus = document.getElementById('payment_status');
        if (paymentStatus.value === 'free') {
            document.getElementById('amount_paid').readOnly = true;
            document.getElementById('amount_paid_container').classList.add('opacity-50');
        }
    });
</script>
@endpush
@endsection
