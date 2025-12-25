@extends('layouts.student')

@section('title', 'Browse Packages')

@section('content')
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Browse Packages
            </h2>
            <a href="{{ route('student.packages.my-packages') }}" class="text-sm text-indigo-600 hover:text-indigo-900">
                My Packages →
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">


            <!-- Search and Filters -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <form method="GET" action="{{ route('student.packages.index') }}" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Search -->
                        <div>
                            <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                            <input type="text" name="search" id="search" value="{{ request('search') }}"
                                placeholder="Search packages..."
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <!-- Category Filter -->
                        <div>
                            <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                            <select name="category" id="category"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="all">All Categories</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category }}" {{ request('category') === $category ? 'selected' : '' }}>
                                        {{ ucfirst($category) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Sort -->
                        <div>
                            <label for="sort" class="block text-sm font-medium text-gray-700 mb-1">Sort By</label>
                            <select name="sort" id="sort"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="newest" {{ request('sort') === 'newest' || request('sort') === '' ? 'selected' : '' }}>Newest
                                    First</option>
                                <option value="price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>Price:
                                    Low to
                                    High</option>
                                <option value="popular" {{ request('sort') === 'popular' ? 'selected' : '' }}>Most Popular
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit"
                            class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition">
                            Apply Filters
                        </button>
                        <a href="{{ route('student.packages.index') }}"
                            class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition">
                            Clear Filters
                        </a>
                    </div>
                </form>
            </div>

            <!-- All Packages -->
            <div class="mb-6">
                <h3 class="text-2xl font-bold text-gray-900 mb-4">
                    All Packages
                    <span class="text-base font-normal text-gray-500">({{ $packages->total() }} packages)</span>
                </h3>
            </div>

            @if($packages->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
                    @foreach($packages as $package)
                        <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
                            <div class="p-6">
                                <!-- Package Header -->
                                <div class="flex items-start justify-between mb-3">
                                    <div>
                                        @if($package->category)
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                {{ ucfirst($package->category) }}
                                            </span>
                                        @endif
                                        @if(in_array($package->id, $purchasedPackageIds))
                                            <span
                                                class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                ✓ Purchased
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Package Name -->
                                <h4 class="text-lg font-bold text-gray-900 mb-2">{{ $package->name }}</h4>

                                <!-- Description -->
                                <p class="text-gray-600 text-sm mb-4 line-clamp-3">{{ $package->description }}</p>

                                {{-- Price --}}
                                <div class="flex items-baseline mb-4">
                                    @if($package->sale_price)
                                        {{-- Sale Price --}}
                                        <span
                                            class="text-2xl font-bold text-green-600">${{ number_format($package->sale_price, 2) }}</span>
                                        <span
                                            class="ml-2 text-lg text-gray-400 line-through">${{ number_format($package->price, 2) }}</span>
                                    @else
                                        {{-- Regular Price --}}
                                        <span class="text-2xl font-bold text-gray-900">${{ number_format($package->price, 2) }}</span>
                                    @endif
                                    @if(!$package->is_lifetime && $package->duration_days)
                                        <span class="ml-2 text-sm text-gray-500">/ {{ $package->duration_days }} days</span>
                                    @elseif($package->is_lifetime)
                                        <span class="ml-2 text-sm text-green-600 font-medium">Lifetime</span>
                                    @endif
                                </div>

                                <!-- Package Info -->
                                <div class="space-y-2 mb-4">
                                    <div class="flex items-center text-sm text-gray-600">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                        </svg>
                                        <strong>{{ $package->courses->count() }}</strong>&nbsp;courses
                                    </div>
                                    @if($package->is_lifetime)
                                        <div class="flex items-center text-sm text-green-600 font-medium">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7" />
                                            </svg>
                                            Lifetime Access
                                        </div>
                                    @endif
                                </div>

                                <!-- Action Button -->
                                @if(in_array($package->id, $purchasedPackageIds))
                                    <a href="{{ route('student.packages.show', $package) }}"
                                        class="block w-full text-center px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition">
                                        Access Package
                                    </a>
                                @else
                                    <a href="{{ route('student.packages.show', $package) }}"
                                        class="block w-full text-center px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition">
                                        View Details
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $packages->links() }}
                </div>
            @else
                <div class="bg-white rounded-lg shadow-md p-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No packages found</h3>
                    <p class="mt-1 text-sm text-gray-500">Try adjusting your search or filter criteria.</p>
                    <div class="mt-6">
                        <a href="{{ route('student.packages.index') }}"
                            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                            Clear Filters
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection