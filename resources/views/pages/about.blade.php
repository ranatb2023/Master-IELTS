<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('About Us') }}
        </h2>
    </x-slot>
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Hero Section -->
        <div class="bg-gradient-to-r from-indigo-600 to-purple-700 rounded-lg shadow-xl overflow-hidden mb-12">
            <div class="px-6 py-16 sm:px-12 text-center">
                <h1 class="text-4xl font-bold text-white mb-4">About Master IELTS</h1>
                <p class="text-xl text-indigo-100 max-w-3xl mx-auto">
                    Your trusted partner in achieving IELTS success through comprehensive online learning
                </p>
            </div>
        </div>

        <!-- Mission Section -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12 mb-16">
            <div class="bg-white rounded-lg shadow-sm p-8">
                <div class="flex items-center mb-6">
                    <div class="p-3 bg-indigo-100 rounded-lg">
                        <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900 ml-4">Our Mission</h2>
                </div>
                <p class="text-gray-600 leading-relaxed">
                    At Master IELTS, we are dedicated to empowering students worldwide to achieve their desired IELTS scores.
                    We provide high-quality, accessible, and comprehensive IELTS preparation courses that adapt to each student's
                    unique learning style and pace. Our mission is to make IELTS success attainable for everyone, regardless of
                    their background or location.
                </p>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-8">
                <div class="flex items-center mb-6">
                    <div class="p-3 bg-purple-100 rounded-lg">
                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900 ml-4">Our Vision</h2>
                </div>
                <p class="text-gray-600 leading-relaxed">
                    We envision a world where language barriers don't limit opportunities. By providing exceptional IELTS
                    preparation, we help students unlock doors to international education, career advancement, and global
                    mobility. Our goal is to be the world's most trusted and effective IELTS preparation platform.
                </p>
            </div>
        </div>

        <!-- Stats Section -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-16">
            <div class="p-8">
                <h2 class="text-3xl font-bold text-gray-900 text-center mb-12">Our Impact</h2>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                    <div class="text-center">
                        <div class="text-4xl font-bold text-indigo-600 mb-2">10,000+</div>
                        <div class="text-gray-600">Students Enrolled</div>
                    </div>
                    <div class="text-center">
                        <div class="text-4xl font-bold text-indigo-600 mb-2">500+</div>
                        <div class="text-gray-600">Expert Tutors</div>
                    </div>
                    <div class="text-center">
                        <div class="text-4xl font-bold text-indigo-600 mb-2">95%</div>
                        <div class="text-gray-600">Success Rate</div>
                    </div>
                    <div class="text-center">
                        <div class="text-4xl font-bold text-indigo-600 mb-2">150+</div>
                        <div class="text-gray-600">Countries</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Why Choose Us Section -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-16">
            <div class="p-8">
                <h2 class="text-3xl font-bold text-gray-900 text-center mb-12">Why Choose Master IELTS?</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="text-center">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-indigo-100 rounded-full mb-4">
                            <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Expert-Led Courses</h3>
                        <p class="text-gray-600">Learn from experienced IELTS instructors with proven track records of student success.</p>
                    </div>

                    <div class="text-center">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-indigo-100 rounded-full mb-4">
                            <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Flexible Learning</h3>
                        <p class="text-gray-600">Study at your own pace with 24/7 access to course materials and resources.</p>
                    </div>

                    <div class="text-center">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-indigo-100 rounded-full mb-4">
                            <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Personalized Feedback</h3>
                        <p class="text-gray-600">Receive detailed feedback on your progress and personalized improvement strategies.</p>
                    </div>

                    <div class="text-center">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-indigo-100 rounded-full mb-4">
                            <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Comprehensive Materials</h3>
                        <p class="text-gray-600">Access a complete library of practice tests, study guides, and learning resources.</p>
                    </div>

                    <div class="text-center">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-indigo-100 rounded-full mb-4">
                            <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Community Support</h3>
                        <p class="text-gray-600">Join a vibrant community of learners and get support whenever you need it.</p>
                    </div>

                    <div class="text-center">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-indigo-100 rounded-full mb-4">
                            <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Proven Results</h3>
                        <p class="text-gray-600">Our students consistently achieve their target scores and reach their goals.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Core Values -->
        <div class="bg-gradient-to-br from-indigo-50 to-purple-50 rounded-lg shadow-sm overflow-hidden mb-16">
            <div class="p-8">
                <h2 class="text-3xl font-bold text-gray-900 text-center mb-12">Our Core Values</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div class="flex items-center justify-center w-12 h-12 rounded-md bg-indigo-600 text-white">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900">Excellence</h3>
                            <p class="mt-2 text-gray-600">We strive for excellence in everything we do, from course content to student support.</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div class="flex items-center justify-center w-12 h-12 rounded-md bg-indigo-600 text-white">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900">Accessibility</h3>
                            <p class="mt-2 text-gray-600">Quality IELTS preparation should be accessible to everyone, everywhere.</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div class="flex items-center justify-center w-12 h-12 rounded-md bg-indigo-600 text-white">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900">Innovation</h3>
                            <p class="mt-2 text-gray-600">We continuously innovate to provide the best learning experience possible.</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div class="flex items-center justify-center w-12 h-12 rounded-md bg-indigo-600 text-white">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900">Student Success</h3>
                            <p class="mt-2 text-gray-600">Your success is our success. We're committed to helping you achieve your goals.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- CTA Section -->
        <div class="bg-gradient-to-r from-indigo-600 to-purple-700 rounded-lg shadow-xl overflow-hidden">
            <div class="px-6 py-12 sm:px-12 text-center">
                <h2 class="text-3xl font-bold text-white mb-4">Ready to Start Your IELTS Journey?</h2>
                <p class="text-xl text-indigo-100 mb-8 max-w-2xl mx-auto">
                    Join thousands of successful students who have achieved their IELTS goals with Master IELTS.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('courses.index') }}" class="inline-flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-indigo-700 bg-white hover:bg-indigo-50">
                        Browse Courses
                    </a>
                    <a href="{{ route('contact') }}" class="inline-flex items-center justify-center px-8 py-3 border border-white text-base font-medium rounded-md text-white hover:bg-indigo-700">
                        Contact Us
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
</x-app-layout>
