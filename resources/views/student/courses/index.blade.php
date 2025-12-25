@extends('layouts.student')

@section('title', 'Browse Courses')
@section('page-title', 'Browse Courses')

@section('content')
<div>
    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-indigo-600 to-indigo-800">
        <div class="max-w-7xl mx-auto py-16 px-4 sm:py-24 sm:px-6 lg:px-8">
            <div class="text-center">
                <h1 class="text-4xl font-extrabold text-white sm:text-5xl sm:tracking-tight lg:text-6xl">
                    Master IELTS Courses
                </h1>
                <p class="mt-5 max-w-xl mx-auto text-xl text-indigo-100">
                    Explore our comprehensive IELTS preparation courses designed to help you achieve your target score
                </p>

                <!-- Search Bar -->
                <div class="mt-10 max-w-2xl mx-auto">
                    <form action="{{ route('student.courses.index') }}" method="GET" class="flex gap-2">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search for courses..." class="flex-1 rounded-md border-transparent text-gray-900 placeholder-gray-500 focus:border-white focus:ring-white">
                        <button type="submit" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-indigo-700 bg-white hover:bg-indigo-50">
                            Search
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="lg:grid lg:grid-cols-12 lg:gap-8">
            <!-- Filters Sidebar -->
            <aside class="lg:col-span-3">
                <div class="sticky top-4 space-y-6">
                    <form action="{{ route('student.courses.index') }}" method="GET">
                        <!-- Keep search parameter -->
                        @if(request('search'))
                        <input type="hidden" name="search" value="{{ request('search') }}">
                        @endif

                        <!-- Category Filter -->
                        <div class="bg-white rounded-lg shadow p-6 mb-4">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Categories</h3>
                            <div class="space-y-3">
                                <label class="inline-flex items-center">
                                    <input type="radio" name="category" value="" {{ !request('category') ? 'checked' : '' }} class="rounded-full border-gray-300 text-indigo-600">
                                    <span class="ml-2 text-sm text-gray-700">All Categories</span>
                                </label>
                                @foreach($categories as $category)
                                <label class="inline-flex items-center block">
                                    <input type="radio" name="category" value="{{ $category->id }}" {{ request('category') == $category->id ? 'checked' : '' }} class="rounded-full border-gray-300 text-indigo-600">
                                    <span class="ml-2 text-sm text-gray-700">{{ $category->name }}</span>
                                </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- Level Filter -->
                        <div class="bg-white rounded-lg shadow p-6 mb-4">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Level</h3>
                            <div class="space-y-3">
                                <label class="inline-flex items-center block">
                                    <input type="radio" name="level" value="" {{ !request('level') ? 'checked' : '' }} class="rounded-full border-gray-300 text-indigo-600">
                                    <span class="ml-2 text-sm text-gray-700">All Levels</span>
                                </label>
                                <label class="inline-flex items-center block">
                                    <input type="radio" name="level" value="beginner" {{ request('level') == 'beginner' ? 'checked' : '' }} class="rounded-full border-gray-300 text-indigo-600">
                                    <span class="ml-2 text-sm text-gray-700">Beginner</span>
                                </label>
                                <label class="inline-flex items-center block">
                                    <input type="radio" name="level" value="intermediate" {{ request('level') == 'intermediate' ? 'checked' : '' }} class="rounded-full border-gray-300 text-indigo-600">
                                    <span class="ml-2 text-sm text-gray-700">Intermediate</span>
                                </label>
                                <label class="inline-flex items-center block">
                                    <input type="radio" name="level" value="advanced" {{ request('level') == 'advanced' ? 'checked' : '' }} class="rounded-full border-gray-300 text-indigo-600">
                                    <span class="ml-2 text-sm text-gray-700">Advanced</span>
                                </label>
                                <label class="inline-flex items-center block">
                                    <input type="radio" name="level" value="all_levels" {{ request('level') == 'all_levels' ? 'checked' : '' }} class="rounded-full border-gray-300 text-indigo-600">
                                    <span class="ml-2 text-sm text-gray-700">All Levels</span>
                                </label>
                            </div>
                        </div>

                        <!-- Price Filter -->
                        <div class="bg-white rounded-lg shadow p-6 mb-4">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Price</h3>
                            <div class="space-y-3">
                                <label class="inline-flex items-center block">
                                    <input type="radio" name="price_type" value="" {{ !request('price_type') ? 'checked' : '' }} class="rounded-full border-gray-300 text-indigo-600">
                                    <span class="ml-2 text-sm text-gray-700">All Prices</span>
                                </label>
                                <label class="inline-flex items-center block">
                                    <input type="radio" name="price_type" value="free" {{ request('price_type') == 'free' ? 'checked' : '' }} class="rounded-full border-gray-300 text-indigo-600">
                                    <span class="ml-2 text-sm text-gray-700">Free</span>
                                </label>
                                <label class="inline-flex items-center block">
                                    <input type="radio" name="price_type" value="paid" {{ request('price_type') == 'paid' ? 'checked' : '' }} class="rounded-full border-gray-300 text-indigo-600">
                                    <span class="ml-2 text-sm text-gray-700">Paid</span>
                                </label>
                            </div>
                        </div>

                        <!-- Rating Filter -->
                        <div class="bg-white rounded-lg shadow p-6 mb-4">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Rating</h3>
                            <div class="space-y-3">
                                @for($i = 4; $i >= 1; $i--)
                                <label class="inline-flex items-center block">
                                    <input type="radio" name="rating" value="{{ $i }}" {{ request('rating') == $i ? 'checked' : '' }} class="rounded-full border-gray-300 text-indigo-600">
                                    <span class="ml-2 text-sm text-gray-700 flex items-center">
                                        {{ $i }}
                                        <svg class="w-4 h-4 ml-1 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                        & up
                                    </span>
                                </label>
                                @endfor
                            </div>
                        </div>

                        <div class="flex space-x-2">
                            <button type="submit" class="flex-1 bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                                Apply Filters
                            </button>
                            @if(request()->hasAny(['category', 'level', 'price_type', 'rating']))
                            <a href="{{ route('student.courses.index') }}" class="flex-1 bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300 text-center">
                                Clear
                            </a>
                            @endif
                        </div>
                    </form>
                </div>
            </aside>

            <!-- Course Grid -->
            <div class="mt-6 lg:mt-0 lg:col-span-9">
                <!-- Sort & Results Info -->
                <div class="flex items-center justify-between mb-6">
                    <p class="text-sm text-gray-700">
                        Showing <span class="font-medium">{{ $courses->firstItem() ?? 0 }}</span> to <span class="font-medium">{{ $courses->lastItem() ?? 0 }}</span> of <span class="font-medium">{{ $courses->total() }}</span> courses
                    </p>
                    <form action="{{ route('student.courses.index') }}" method="GET">
                        <!-- Keep all current filters -->
                        @foreach(request()->except('sort') as $key => $value)
                            @if(is_array($value))
                                @foreach($value as $item)
                                <input type="hidden" name="{{ $key }}[]" value="{{ $item }}">
                                @endforeach
                            @else
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endif
                        @endforeach
                        <select name="sort" onchange="this.form.submit()" class="rounded-md border-gray-300 text-sm">
                            <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>Most Popular</option>
                            <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest</option>
                            <option value="rating" {{ request('sort') == 'rating' ? 'selected' : '' }}>Highest Rated</option>
                            <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                            <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                        </select>
                    </form>
                </div>

                <!-- Course Cards -->
                @if($courses->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                    @foreach($courses as $course)
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
                        <a href="{{ route('student.courses.show', $course) }}">
                            @if($course->thumbnail)
                            <img src="{{ asset('storage/' . $course->thumbnail) }}" alt="{{ $course->title }}" class="w-full h-48 object-cover">
                            @else
                            <div class="w-full h-48 bg-gradient-to-r from-indigo-500 to-purple-600 flex items-center justify-center">
                                <span class="text-white text-2xl font-bold">{{ substr($course->title, 0, 1) }}</span>
                            </div>
                            @endif
                        </a>
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-2">
                                @if($course->category)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                    {{ $course->category->name }}
                                </span>
                                @endif
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    {{ ucfirst($course->level) }}
                                </span>
                            </div>
                            <a href="{{ route('student.courses.show', $course) }}" class="block">
                                <h3 class="text-lg font-semibold text-gray-900 mb-2 hover:text-indigo-600">{{ $course->title }}</h3>
                            </a>
                            <p class="text-sm text-gray-600 mb-4 line-clamp-2">{{ $course->short_description }}</p>

                            <div class="flex items-center mb-4">
                                <img src="{{ $course->instructor->avatar ? asset('storage/' . $course->instructor->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($course->instructor->name) }}" alt="{{ $course->instructor->name }}" class="w-8 h-8 rounded-full mr-2">
                                <span class="text-sm text-gray-700">{{ $course->instructor->name }}</span>
                            </div>

                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center">
                                    <span class="text-yellow-400 text-sm font-medium">{{ number_format($course->average_rating, 1) }}</span>
                                    <svg class="w-4 h-4 ml-1 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                    <span class="text-sm text-gray-500 ml-1">({{ $course->reviews_count }})</span>
                                </div>
                                <span class="text-sm text-gray-500">{{ $course->enrollments_count }} students</span>
                            </div>

                            <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                                @if($course->is_free)
                                <span class="text-2xl font-bold text-green-600">Free</span>
                                @else
                                <div>
                                    @if($course->sale_price)
                                    <span class="text-2xl font-bold text-gray-900">{{ $course->currency }}{{ number_format($course->sale_price, 2) }}</span>
                                    <span class="text-sm text-gray-500 line-through ml-2">{{ $course->currency }}{{ number_format($course->price, 2) }}</span>
                                    @else
                                    <span class="text-2xl font-bold text-gray-900">{{ $course->currency }}{{ number_format($course->price, 2) }}</span>
                                    @endif
                                </div>
                                @endif
                                @if($course->duration_hours)
                                <span class="text-sm text-gray-500">{{ $course->duration_hours }}h</span>
                                @endif
                            </div>

                            <a href="{{ route('student.courses.show', $course) }}" class="mt-4 block w-full text-center px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                                View Course
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if($courses->hasPages())
                <div class="mt-8">
                    {{ $courses->links() }}
                </div>
                @endif
                @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <h3 class="mt-2 text-lg font-medium text-gray-900">No courses found</h3>
                    <p class="mt-1 text-sm text-gray-500">Try adjusting your search or filters.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
