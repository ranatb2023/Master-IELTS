@extends('layouts.app')

@section('title', $package->name . ' - ' . config('app.name'))

@section('content')
    <!-- Package Hero -->
    <div class="bg-gradient-to-r from-indigo-600 to-purple-700 py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="lg:grid lg:grid-cols-2 lg:gap-8 lg:items-center">
                <div>
                    @if($package->category)
                        <span
                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-200 text-indigo-900 mb-4">
                            {{ ucfirst($package->category) }}
                        </span>
                    @endif
                    @if($package->is_featured)
                        <span
                            class="ml-2 inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-400 text-yellow-900 mb-4">
                            ⭐ FEATURED
                        </span>
                    @endif

                    <h1 class="text-4xl font-extrabold text-white sm:text-5xl">
                        {{ $package->name }}
                    </h1>
                    <p class="mt-4 text-xl text-indigo-100">
                        {{ $package->description }}
                    </p>

                    <div class="mt-6 flex items-center space-x-6">
                        <div>
                            @if($package->sale_price)
                                <span
                                    class="text-5xl font-bold text-green-300">${{ number_format($package->sale_price, 2) }}</span>
                                <span
                                    class="ml-2 text-3xl text-indigo-200 line-through">${{ number_format($package->price, 2) }}</span>
                            @else
                                <span class="text-5xl font-bold text-white">${{ number_format($package->price, 2) }}</span>
                            @endif
                            @if($package->is_lifetime)
                                <span class="ml-2 text-sm text-green-200 font-medium">Lifetime Access</span>
                            @elseif($package->duration_days)
                                <span class="ml-2 text-sm text-indigo-200">for {{ $package->duration_days }} days</span>
                            @endif
                        </div>
                    </div>

                    <div class="mt-8">
                        @auth
                            @if(auth()->user()->isStudent())
                                <a href="{{ route('student.packages.show', $package) }}"
                                    class="inline-flex items-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-indigo-600 bg-white hover:bg-indigo-50">
                                    View Package & Purchase
                                </a>
                            @else
                                <a href="{{ route('login') }}"
                                    class="inline-flex items-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-indigo-600 bg-white hover:bg-indigo-50">
                                    Log In to Purchase
                                </a>
                            @endif
                        @else
                            <a href="{{ route('register') }}"
                                class="inline-flex items-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-indigo-600 bg-white hover:bg-indigo-50 mr-3">
                                Get Started
                            </a>
                            <a href="{{ route('login') }}"
                                class="inline-flex items-center px-8 py-3 border-2 border-white text-base font-medium rounded-md text-white hover:bg-indigo-600">
                                Sign In
                            </a>
                        @endauth
                    </div>
                </div>

                <div class="mt-10 lg:mt-0">
                    <div class="bg-white bg-opacity-10 backdrop-blur-lg rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-white mb-4">Package Includes</h3>
                        <ul class="space-y-3">
                            <li class="flex items-center text-white">
                                <svg class="w-5 h-5 mr-3 text-green-300" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                {{ $package->courses->count() }} comprehensive courses
                            </li>
                            @if($package->is_lifetime)
                                <li class="flex items-center text-white">
                                    <svg class="w-5 h-5 mr-3 text-green-300" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                    Lifetime access to all content
                                </li>
                            @endif
                            @if($package->auto_enroll_courses)
                                <li class="flex items-center text-white">
                                    <svg class="w-5 h-5 mr-3 text-green-300" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                    Automatic enrollment in all courses
                                </li>
                            @endif
                            <li class="flex items-center text-white">
                                <svg class="w-5 h-5 mr-3 text-green-300" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                Instant access after purchase
                            </li>
                            <li class="flex items-center text-white">
                                <svg class="w-5 h-5 mr-3 text-green-300" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                Secure payment processing
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Included Courses -->
    <div class="bg-gray-50 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-extrabold text-gray-900 mb-8">Courses Included in This Package</h2>

            @if($package->courses->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($package->courses as $course)
                        <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
                            @if($course->thumbnail)
                                <img src="{{ asset('storage/' . $course->thumbnail) }}" alt="{{ $course->title }}"
                                    class="w-full h-48 object-cover">
                            @else
                                <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                                    <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                    </svg>
                                </div>
                            @endif
                            <div class="p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $course->title }}</h3>
                                <p class="text-sm text-gray-600 mb-4 line-clamp-2">{{ $course->short_description }}</p>
                                <div class="flex items-center justify-between text-sm text-gray-500">
                                    @if($course->level)
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                            {{ ucfirst($course->level) }}
                                        </span>
                                    @endif
                                    @if($course->duration_hours)
                                        <span>{{ $course->duration_hours }}h</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-center">No courses included in this package yet.</p>
            @endif
        </div>
    </div>

    <!-- Package Features -->
    @if($package->features && count($package->features) > 0)
        <div class="bg-white py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-3xl font-extrabold text-gray-900 mb-8">What You'll Get</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach($package->features as $feature)
                        <div class="flex items-start">
                            <svg class="w-6 h-6 text-green-500 mr-3 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span class="text-gray-700">{{ $feature }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- Related Packages -->
    @if($relatedPackages->count() > 0)
        <div class="bg-gray-50 py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-3xl font-extrabold text-gray-900 mb-8">You May Also Like</h2>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach($relatedPackages as $related)
                        <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
                            <div class="p-6">
                                <h3 class="text-lg font-bold text-gray-900 mb-2">{{ $related->name }}</h3>
                                <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ $related->description }}</p>
                                <div class="flex items-baseline mb-4">
                                    @if($related->sale_price)
                                        <span
                                            class="text-2xl font-bold text-green-600">${{ number_format($related->sale_price, 2) }}</span>
                                        <span
                                            class="ml-2 text-lg text-gray-400 line-through">${{ number_format($related->price, 2) }}</span>
                                    @else
                                        <span class="text-2xl font-bold text-gray-900">${{ number_format($related->price, 2) }}</span>
                                    @endif
                                    @if($related->is_lifetime)
                                        <span class="ml-2 text-sm text-green-600">Lifetime</span>
                                    @endif
                                </div>
                                <a href="{{ route('packages.show', $related) }}"
                                    class="block w-full text-center px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition">
                                    View Package
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- CTA Section -->
    <div class="bg-indigo-700">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:py-16 lg:px-8">
            <div class="text-center">
                <h2 class="text-3xl font-extrabold tracking-tight text-white sm:text-4xl">
                    <span class="block">Ready to start learning?</span>
                    <span class="block text-indigo-200">Get this package today and accelerate your IELTS journey.</span>
                </h2>
                <div class="mt-8 flex justify-center">
                    @auth
                        @if(auth()->user()->isStudent())
                            <a href="{{ route('student.packages.show', $package) }}"
                                class="inline-flex items-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-indigo-600 bg-white hover:bg-indigo-50">
                                Purchase Now
                            </a>
                        @else
                            <a href="{{ route('login') }}"
                                class="inline-flex items-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-indigo-600 bg-white hover:bg-indigo-50">
                                Log In to Purchase
                            </a>
                        @endif
                    @else
                        <a href="{{ route('register') }}"
                            class="inline-flex items-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-indigo-600 bg-white hover:bg-indigo-50 mr-3">
                            Get Started
                        </a>
                        <a href="{{ route('login') }}"
                            class="inline-flex items-center px-8 py-3 border-2 border-white text-base font-medium rounded-md text-white hover:bg-indigo-600">
                            Sign In
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </div>
@endsection