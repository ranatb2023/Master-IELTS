<!-- Sidebar for desktop -->
<aside class="hidden lg:flex lg:flex-shrink-0">
    <div class="flex flex-col w-64 bg-white border-r border-gray-200">
        <!-- Logo -->
        <div class="flex items-center justify-center h-16 px-4 border-b border-gray-200">
            <a href="{{ route('student.dashboard') }}">
                <img src="{{ asset('images/logo-full.png') }}" alt="Master IELTS" class="h-8">
            </a>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1">
            <!-- Dashboard -->
            <a href="{{ route('student.dashboard') }}"
                class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('student.dashboard') ? 'bg-indigo-50 text-indigo-700 border-l-4 border-indigo-700' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }} transition-colors duration-150">
                <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                Dashboard
            </a>

            <!-- My Learning Section -->
            @can('course.view')
                <div class="pt-4 pb-2">
                    <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">My Learning</p>
                </div>

                <!-- My Courses / Enrollments -->
                <a href="{{ route('student.enrollments.index') }}"
                    class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('student.enrollments.*') ? 'bg-indigo-50 text-indigo-700 border-l-4 border-indigo-700' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }} transition-colors duration-150">
                    <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    My Courses
                </a>

                <!-- Browse Courses -->
                @can('course.enroll')
                    <a href="{{ route('student.courses.index') }}"
                        class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('student.courses.*') && !request()->routeIs('student.courses.learn') ? 'bg-indigo-50 text-indigo-700 border-l-4 border-indigo-700' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }} transition-colors duration-150">
                        <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        Browse Courses
                    </a>
                @endcan

                <!-- Packages -->
                <a href="{{ route('student.packages.index') }}"
                    class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('student.packages.*') && !request()->routeIs('student.packages.my-packages') ? 'bg-indigo-50 text-indigo-700 border-l-4 border-indigo-700' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }} transition-colors duration-150">
                    <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                    Browse Packages
                </a>

                <!-- My Packages -->
                <a href="{{ route('student.packages.my-packages') }}"
                    class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('student.packages.my-packages') ? 'bg-indigo-50 text-indigo-700 border-l-4 border-indigo-700' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }} transition-colors duration-150">
                    <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    My Packages
                </a>

                <!-- My Notes -->
                <a href="{{ route('student.notes.index') }}"
                    class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('student.notes.*') ? 'bg-indigo-50 text-indigo-700 border-l-4 border-indigo-700' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }} transition-colors duration-150">
                    <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    My Notes
                </a>

                <!-- Subscriptions Dropdown -->
                <div x-data="{ open: {{ request()->routeIs('student.subscriptions.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open"
                        class="w-full group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('student.subscriptions.*') ? 'bg-indigo-100 text-indigo-900' : 'text-gray-700 hover:bg-gray-100' }}">
                        <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Subscriptions
                        <svg class="ml-auto h-5 w-5 transform transition-transform" :class="{'rotate-90': open}" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                    <div x-show="open" x-transition class="ml-8 mt-1 space-y-1">
                        <a href="{{ route('student.subscriptions.index') }}"
                            class="group flex items-center px-2 py-2 text-sm rounded-md {{ request()->routeIs('student.subscriptions.index') ? 'text-indigo-900 bg-indigo-50' : 'text-gray-600 hover:bg-gray-50' }}">
                            Browse Plans
                        </a>
                        <a href="{{ route('student.subscriptions.manage') }}"
                            class="group flex items-center px-2 py-2 text-sm rounded-md {{ request()->routeIs('student.subscriptions.manage') ? 'text-indigo-900 bg-indigo-50' : 'text-gray-600 hover:bg-gray-50' }}">
                            Manage Subscription
                        </a>
                        <a href="{{ route('student.subscriptions.invoices') }}"
                            class="group flex items-center px-2 py-2 text-sm rounded-md {{ request()->routeIs('student.subscriptions.invoices') ? 'text-indigo-900 bg-indigo-50' : 'text-gray-600 hover:bg-gray-50' }}">
                            Invoices
                        </a>
                        <a href="{{ route('student.subscriptions.payment-method') }}"
                            class="group flex items-center px-2 py-2 text-sm rounded-md {{ request()->routeIs('student.subscriptions.payment-method') ? 'text-indigo-900 bg-indigo-50' : 'text-gray-600 hover:bg-gray-50' }}">
                            Payment Method
                        </a>
                    </div>
                </div>
            @endcan


            {{-- Assessments Section - Commented until routes are created
            @canany(['quiz.view', 'assignment.view'])
            <div class="pt-4 pb-2">
                <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Assessments</p>
            </div>

            <!-- Quizzes -->
            @can('quiz.view')
            <a href="{{ route('student.quizzes.index') }}"
                class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('student.quizzes.*') ? 'bg-indigo-50 text-indigo-700 border-l-4 border-indigo-700' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }} transition-colors duration-150">
                <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                My Quizzes
            </a>
            @endcan

            <!-- Assignments -->
            @can('assignment.view')
            <a href="{{ route('student.assignments.index') }}"
                class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('student.assignments.*') ? 'bg-indigo-50 text-indigo-700 border-l-4 border-indigo-700' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }} transition-colors duration-150">
                <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                My Assignments
            </a>
            @endcan
            @endcanany
            --}}

            <!-- Achievements Section -->
            @can('certificate.view')
                <div class="pt-4 pb-2">
                    <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Achievements</p>
                </div>

                <!-- Certificates -->
                <a href="{{ route('student.certificates.index') }}"
                    class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('student.certificates.*') ? 'bg-indigo-50 text-indigo-700 border-l-4 border-indigo-700' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }} transition-colors duration-150">
                    <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                    </svg>
                    My Certificates
                </a>
            @endcan

            {{-- Community Section - Commented until routes are created
            @canany(['forum.view', 'blog.view'])
            <div class="pt-4 pb-2">
                <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Community</p>
            </div>

            <!-- Forum -->
            @can('forum.view')
            <a href="{{ route('student.forum.index') }}"
                class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('student.forum.*') ? 'bg-indigo-50 text-indigo-700 border-l-4 border-indigo-700' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }} transition-colors duration-150">
                <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z" />
                </svg>
                Forum
            </a>
            @endcan

            <!-- Blog -->
            @can('blog.view')
            <a href="{{ route('student.blog.index') }}"
                class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('student.blog.*') ? 'bg-indigo-50 text-indigo-700 border-l-4 border-indigo-700' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }} transition-colors duration-150">
                <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                </svg>
                Blog
            </a>
            @endcan
            @endcanany
            --}}

            <!-- Divider -->
            <div class="my-4 border-t border-gray-200"></div>

            <!-- Account Section -->
            <a href="{{ route('profile.edit') }}"
                class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('profile.*') ? 'bg-indigo-50 text-indigo-700 border-l-4 border-indigo-700' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }} transition-colors duration-150">
                <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                Settings
            </a>
        </nav>

        <!-- User Profile -->
        @auth
            <div class="flex-shrink-0 flex border-t border-gray-200 p-4">
                <div class="flex-shrink-0 w-full">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            @if(auth()->user()->avatar)
                                <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="{{ auth()->user()->name }}"
                                    class="h-10 w-10 rounded-full object-cover">
                            @else
                                <div
                                    class="h-10 w-10 rounded-full bg-indigo-600 flex items-center justify-center text-white font-semibold">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                                </div>
                            @endif
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-gray-500">Student</p>
                        </div>
                    </div>
                </div>
            </div>
        @endauth
    </div>
</aside>

<!-- Mobile sidebar -->
<div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 z-40 lg:hidden" style="display: none;">
    <div class="fixed inset-0 bg-gray-600 bg-opacity-75 transition-opacity"></div>
</div>

<div x-show="sidebarOpen" x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
    x-transition:leave="transition ease-in duration-300" x-transition:leave-start="translate-x-0"
    x-transition:leave-end="-translate-x-full" class="fixed inset-0 flex z-40 lg:hidden" style="display: none;">
    <div class="relative flex-1 flex flex-col max-w-xs w-full bg-white">
        <div class="absolute top-0 right-0 -mr-12 pt-2">
            <button @click="sidebarOpen = false"
                class="ml-1 flex items-center justify-center h-10 w-10 rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white">
                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Same navigation as desktop -->
        <div class="flex-1 h-0 pt-5 pb-4 overflow-y-auto">
            <div class="flex items-center flex-shrink-0 px-4 mb-5">
                <a href="{{ route('student.dashboard') }}">
                    <img src="{{ asset('images/logo-full.png') }}" alt="Master IELTS" class="h-8">
                </a>
            </div>
            <nav class="px-3 space-y-1">
                <!-- Dashboard -->
                <a href="{{ route('student.dashboard') }}"
                    class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('student.dashboard') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    Dashboard
                </a>

                <!-- My Learning Section -->
                @can('course.view')
                    <div class="pt-4 pb-2">
                        <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">My Learning</p>
                    </div>

                    <a href="{{ route('student.enrollments.index') }}"
                        class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('student.enrollments.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:bg-gray-50' }}">
                        <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                        My Courses
                    </a>

                    @can('course.enroll')
                        <a href="{{ route('student.courses.index') }}"
                            class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('student.courses.*') && !request()->routeIs('student.courses.learn') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:bg-gray-50' }}">
                            <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            Browse Courses
                        </a>
                    @endcan

                    <!-- Packages -->
                    <a href="{{ route('student.packages.index') }}"
                        class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('student.packages.*') && !request()->routeIs('student.packages.my-packages') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:bg-gray-50' }}">
                        <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                        </svg>
                        Browse Packages
                    </a>

                    <!-- My Packages -->
                    <a href="{{ route('student.packages.my-packages') }}"
                        class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('student.packages.my-packages') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:bg-gray-50' }}">
                        <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        My Packages
                    </a>

                    <!-- My Notes -->
                    <a href="{{ route('student.notes.index') }}"
                        class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('student.notes.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:bg-gray-50' }}">
                        <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        My Notes
                    </a>

                    <!-- Subscriptions -->
                    <!-- Subscriptions Dropdown -->
                    <div x-data="{ open: {{ request()->routeIs('student.subscriptions.*') ? 'true' : 'false' }} }">
                        <button @click="open = !open"
                            class="w-full group flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('student.subscriptions.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:bg-gray-50' }}">
                            <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            Subscriptions
                            <svg class="ml-auto h-5 w-5 transform" :class="{'rotate-90': open}" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                        <div x-show="open" class="ml-8 mt-1 space-y-1">
                            <a href="{{ route('student.subscriptions.index') }}"
                                class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('student.subscriptions.index') ? 'text-indigo-700 bg-indigo-50' : 'text-gray-700 hover:bg-gray-50' }}">
                                Browse Plans
                            </a>
                            <a href="{{ route('student.subscriptions.manage') }}"
                                class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('student.subscriptions.manage') ? 'text-indigo-700 bg-indigo-50' : 'text-gray-700 hover:bg-gray-50' }}">
                                Manage Subscription
                            </a>
                            <a href="{{ route('student.subscriptions.invoices') }}"
                                class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('student.subscriptions.invoices') ? 'text-indigo-700 bg-indigo-50' : 'text-gray-700 hover:bg-gray-50' }}">
                                Invoices
                            </a>
                            <a href="{{ route('student.subscriptions.payment-method') }}"
                                class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('student.subscriptions.payment-method') ? 'text-indigo-700 bg-indigo-50' : 'text-gray-700 hover:bg-gray-50' }}">
                                Payment Method
                            </a>
                        </div>
                    </div>
                @endcan

                {{-- Assessments Section - Commented until routes are created
                @canany(['quiz.view', 'assignment.view'])
                <div class="pt-4 pb-2">
                    <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Assessments</p>
                </div>

                @can('quiz.view')
                <a href="{{ route('student.quizzes.index') }}"
                    class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('student.quizzes.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    My Quizzes
                </a>
                @endcan

                @can('assignment.view')
                <a href="{{ route('student.assignments.index') }}"
                    class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('student.assignments.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    My Assignments
                </a>
                @endcan
                @endcanany
                --}}

                <!-- Achievements Section -->
                @can('certificate.view')
                    <div class="pt-4 pb-2">
                        <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Achievements</p>
                    </div>

                    <a href="{{ route('student.certificates.index') }}"
                        class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('student.certificates.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:bg-gray-50' }}">
                        <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                        </svg>
                        My Certificates
                    </a>
                @endcan

                {{-- Community Section - Commented until routes are created
                @canany(['forum.view', 'blog.view'])
                <div class="pt-4 pb-2">
                    <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Community</p>
                </div>

                @can('forum.view')
                <a href="{{ route('student.forum.index') }}"
                    class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('student.forum.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z" />
                    </svg>
                    Forum
                </a>
                @endcan

                @can('blog.view')
                <a href="{{ route('student.blog.index') }}"
                    class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('student.blog.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                    </svg>
                    Blog
                </a>
                @endcan
                @endcanany
                --}}

                <!-- Divider -->
                <div class="my-4 border-t border-gray-200"></div>

                <!-- Account Section -->
                <a href="{{ route('profile.edit') }}"
                    class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('profile.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Settings
                </a>
            </nav>
        </div>

        <!-- Mobile User Profile -->
        @auth
            <div class="flex-shrink-0 flex border-t border-gray-200 p-4">
                <div class="flex items-center">
                    @if(auth()->user()->avatar)
                        <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="{{ auth()->user()->name }}"
                            class="h-10 w-10 rounded-full object-cover">
                    @else
                        <div
                            class="h-10 w-10 rounded-full bg-indigo-600 flex items-center justify-center text-white font-semibold">
                            {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                        </div>
                    @endif
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-500">Student</p>
                    </div>
                </div>
            </div>
        @endauth
    </div>
</div>