@extends('layouts.admin')

@section('title', 'Edit Enrollment')
@section('page-title', 'Edit Enrollment')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Edit Enrollment</h2>
            <p class="mt-1 text-sm text-gray-600">{{ $enrollment->user->name }} - {{ $enrollment->course->title }}</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('admin.enrollments.show', $enrollment) }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
                View
            </a>
            <a href="{{ route('admin.enrollments.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back
            </a>
        </div>
    </div>

    <!-- Form -->
    <form action="{{ route('admin.enrollments.update', $enrollment) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content - 2 columns -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Student & Course Info (Read-only) -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Enrollment Information</h3>
                        <p class="mt-1 text-sm text-gray-500">Student and course details (read-only)</p>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Student</label>
                                <div class="mt-1 px-3 py-2 bg-gray-50 border border-gray-300 rounded-md text-gray-700">
                                    {{ $enrollment->user->name }}
                                </div>
                                <p class="mt-1 text-xs text-gray-500">{{ $enrollment->user->email }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Course</label>
                                <div class="mt-1 px-3 py-2 bg-gray-50 border border-gray-300 rounded-md text-gray-700">
                                    {{ $enrollment->course->title }}
                                </div>
                                <p class="mt-1 text-xs text-gray-500">Enrolled: {{ $enrollment->enrolled_at->format('M d, Y') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Status Management -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Status Management</h3>
                        <p class="mt-1 text-sm text-gray-500">Update enrollment and payment status</p>
                    </div>
                    <div class="p-6 space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Enrollment Status -->
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700">
                                    Enrollment Status *
                                </label>
                                <select id="status"
                                        name="status"
                                        required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="active" {{ old('status', $enrollment->status) == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="completed" {{ old('status', $enrollment->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="expired" {{ old('status', $enrollment->status) == 'expired' ? 'selected' : '' }}>Expired</option>
                                    <option value="canceled" {{ old('status', $enrollment->status) == 'canceled' ? 'selected' : '' }}>Canceled</option>
                                    <option value="suspended" {{ old('status', $enrollment->status) == 'suspended' ? 'selected' : '' }}>Suspended</option>
                                </select>
                                @error('status')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Payment Status -->
                            <div>
                                <label for="payment_status" class="block text-sm font-medium text-gray-700">
                                    Payment Status *
                                </label>
                                <select id="payment_status"
                                        name="payment_status"
                                        required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="free" {{ old('payment_status', $enrollment->payment_status) == 'free' ? 'selected' : '' }}>Free</option>
                                    <option value="completed" {{ old('payment_status', $enrollment->payment_status) == 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="pending" {{ old('payment_status', $enrollment->payment_status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="failed" {{ old('payment_status', $enrollment->payment_status) == 'failed' ? 'selected' : '' }}>Failed</option>
                                    <option value="refunded" {{ old('payment_status', $enrollment->payment_status) == 'refunded' ? 'selected' : '' }}>Refunded</option>
                                </select>
                                @error('payment_status')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Amount Paid -->
                            <div>
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
                                           value="{{ old('amount_paid', $enrollment->amount_paid) }}"
                                           class="pl-7 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>
                                @error('amount_paid')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Progress Percentage -->
                            <div>
                                <label for="progress_percentage" class="block text-sm font-medium text-gray-700">
                                    Progress Percentage
                                </label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <input type="number"
                                           step="0.01"
                                           min="0"
                                           max="100"
                                           id="progress_percentage"
                                           name="progress_percentage"
                                           value="{{ old('progress_percentage', $enrollment->progress_percentage) }}"
                                           class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">%</span>
                                    </div>
                                </div>
                                @error('progress_percentage')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Date Management -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Date Management</h3>
                        <p class="mt-1 text-sm text-gray-500">Update expiry and completion dates</p>
                    </div>
                    <div class="p-6 space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Expiry Date -->
                            <div>
                                <label for="expires_at" class="block text-sm font-medium text-gray-700">
                                    Expiry Date (Optional)
                                </label>
                                <input type="datetime-local"
                                       id="expires_at"
                                       name="expires_at"
                                       value="{{ old('expires_at', $enrollment->expires_at?->format('Y-m-d\TH:i')) }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <p class="mt-1 text-xs text-gray-500">Leave empty for lifetime access</p>
                                @error('expires_at')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Completion Date -->
                            <div>
                                <label for="completed_at" class="block text-sm font-medium text-gray-700">
                                    Completion Date (Optional)
                                </label>
                                <input type="datetime-local"
                                       id="completed_at"
                                       name="completed_at"
                                       value="{{ old('completed_at', $enrollment->completed_at?->format('Y-m-d\TH:i')) }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <p class="mt-1 text-xs text-gray-500">Set when student completes the course</p>
                                @error('completed_at')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notes -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Notes</h3>
                        <p class="mt-1 text-sm text-gray-500">Internal notes about this enrollment</p>
                    </div>
                    <div class="p-6">
                        <textarea id="notes"
                                  name="notes"
                                  rows="4"
                                  class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                  placeholder="Add any notes about this enrollment...">{{ old('notes', $enrollment->notes) }}</textarea>
                        @error('notes')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">These notes are only visible to administrators</p>
                    </div>
                </div>
            </div>

            <!-- Sidebar - 1 column -->
            <div class="space-y-6">
                <!-- Quick Stats -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Quick Stats</h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Enrolled Date:</span>
                            <span class="text-sm font-medium text-gray-900">{{ $enrollment->enrolled_at->format('M d, Y') }}</span>
                        </div>
                        @if($enrollment->last_accessed_at)
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Last Accessed:</span>
                            <span class="text-sm font-medium text-gray-900">{{ $enrollment->last_accessed_at->diffForHumans() }}</span>
                        </div>
                        @endif
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Current Progress:</span>
                            <span class="text-sm font-medium text-indigo-600">{{ number_format($enrollment->progress_percentage, 0) }}%</span>
                        </div>
                        @if($enrollment->payment_status == 'refunded')
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Refund Amount:</span>
                            <span class="text-sm font-medium text-red-600">${{ number_format($enrollment->refund_amount, 2) }}</span>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="bg-white shadow rounded-lg p-6">
                    <div class="space-y-3">
                        <button type="submit"
                                class="w-full inline-flex justify-center items-center px-4 py-3 bg-indigo-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Update Enrollment
                        </button>
                        <a href="{{ route('admin.enrollments.show', $enrollment) }}"
                           class="w-full inline-flex justify-center items-center px-4 py-3 bg-white border border-gray-300 rounded-md font-semibold text-sm text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition">
                            Cancel
                        </a>
                    </div>
                </div>

                <!-- Danger Zone -->
                <div class="bg-white shadow rounded-lg border-2 border-red-200">
                    <div class="px-6 py-4 border-b border-red-200 bg-red-50">
                        <h3 class="text-lg font-medium text-red-900">Danger Zone</h3>
                    </div>
                    <div class="p-6">
                        <p class="text-sm text-gray-600 mb-4">Once you delete this enrollment, there is no going back. Please be certain.</p>
                        <form action="{{ route('admin.enrollments.destroy', $enrollment) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this enrollment? This action cannot be undone.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="w-full inline-flex justify-center items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                                Delete Enrollment
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
