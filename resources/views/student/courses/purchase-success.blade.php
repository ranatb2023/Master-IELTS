<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Purchase Successful
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Success Message -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
                <div class="p-8 text-center">
                    <!-- Success Icon -->
                    <div class="flex justify-center mb-6">
                        <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center">
                            <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                    </div>

                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Payment Successful!</h1>
                    <p class="text-lg text-gray-600 mb-6">Thank you for purchasing {{ $course->title }}. You now have
                        access to the course.</p>

                    <!-- Purchase Details -->
                    <div class="max-w-md mx-auto bg-gray-50 rounded-lg p-6 mb-6">
                        <div class="space-y-3 text-left">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Course:</span>
                                <span class="font-semibold text-gray-900">{{ $course->title }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Amount Paid:</span>
                                <span
                                    class="font-semibold text-gray-900">{{ $course->currency }}{{ number_format($order->total, 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Purchase Date:</span>
                                <span
                                    class="font-semibold text-gray-900">{{ $order->created_at->format('M d, Y') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Order Number:</span>
                                <span class="font-semibold text-gray-900">{{ $order->order_number }}</span>
                            </div>
                            @if($enrollment)
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Access Type:</span>
                                    <span class="font-semibold text-green-600">Lifetime Access</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="{{ route('student.courses.learn', $course->slug) }}"
                            class="px-8 py-3 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 transition">
                            Start Learning
                        </a>
                        <a href="{{ route('student.enrollments.index') }}"
                            class="px-8 py-3 bg-gray-200 text-gray-700 font-semibold rounded-lg hover:bg-gray-300 transition">
                            View My Courses
                        </a>
                    </div>
                </div>
            </div>

            <!-- Course Details -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Course Details</h3>
                </div>
                <div class="p-6">
                    <div class="flex items-start">
                        @if($course->thumbnail)
                            <img src="{{ asset('storage/' . $course->thumbnail) }}" alt="{{ $course->title }}"
                                class="w-32 h-32 object-cover rounded-lg mr-6">
                        @else
                            <div class="w-32 h-32 bg-indigo-100 rounded-lg flex items-center justify-center mr-6">
                                <svg class="w-16 h-16 text-indigo-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                            </div>
                        @endif
                        <div class="flex-1">
                            <h4 class="text-xl font-semibold text-gray-900 mb-2">{{ $course->title }}</h4>
                            @if($course->short_description)
                                <p class="text-gray-600 mb-3">{{ $course->short_description }}</p>
                            @endif
                            <div class="flex flex-wrap gap-3 mb-3">
                                @if($course->level)
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                        {{ ucfirst($course->level) }}
                                    </span>
                                @endif
                                @if($course->duration_hours)
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                        {{ $course->duration_hours }} hours
                                    </span>
                                @endif
                                @if($course->language)
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                        {{ $course->language }}
                                    </span>
                                @endif
                            </div>
                            @if($course->instructor)
                                <div class="flex items-center text-sm text-gray-600">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    Instructor: {{ $course->instructor->name }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- What's Included -->
            @if($course->learning_outcomes && count(json_decode($course->learning_outcomes)) > 0)
                <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
                    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">What You'll Learn</h3>
                    </div>
                    <div class="p-6">
                        <ul class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach(json_decode($course->learning_outcomes) as $outcome)
                                <li class="flex items-start">
                                    <svg class="w-6 h-6 text-green-500 mr-3 flex-shrink-0" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span class="text-gray-700">{{ $outcome }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <!-- Next Steps -->
            <div class="bg-blue-50 rounded-lg shadow-md overflow-hidden">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-blue-900 mb-4">What's Next?</h3>
                    <div class="space-y-4">
                        <div class="flex items-start">
                            <div
                                class="flex-shrink-0 w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center font-bold mr-4">
                                1
                            </div>
                            <div class="flex-1">
                                <h4 class="font-semibold text-blue-900 mb-1">Check Your Email</h4>
                                <p class="text-sm text-blue-800">You'll receive a confirmation email with your purchase
                                    details and receipt.</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div
                                class="flex-shrink-0 w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center font-bold mr-4">
                                2
                            </div>
                            <div class="flex-1">
                                <h4 class="font-semibold text-blue-900 mb-1">Start Learning</h4>
                                <p class="text-sm text-blue-800">Access your course content and begin your learning
                                    journey at your own pace.</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div
                                class="flex-shrink-0 w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center font-bold mr-4">
                                3
                            </div>
                            <div class="flex-1">
                                <h4 class="font-semibold text-blue-900 mb-1">Track Your Progress</h4>
                                <p class="text-sm text-blue-800">Monitor your learning progress and earn your completion
                                    certificate.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Support Section -->
            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600">
                    Need help? <a href="#" class="text-indigo-600 hover:text-indigo-800 font-medium">Contact Support</a>
                </p>
            </div>
        </div>
    </div>
</x-app-layout>