<!-- Sidebar for desktop -->
<aside class="hidden lg:flex lg:flex-shrink-0">
    <div class="flex flex-col w-64 bg-white border-r border-gray-200">
        <!-- Logo -->
        <div class="flex items-center justify-center h-16 px-4 border-b border-gray-200">
            <a href="{{ route('admin.dashboard') }}">
                <img src="{{ asset('images/logo-full.png') }}" alt="Master IELTS" class="h-8">
            </a>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1">
            <!-- Dashboard -->
            <a href="{{ route('admin.dashboard') }}"
                class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-50 text-indigo-700 border-l-4 border-indigo-700' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }} transition-colors duration-150">
                <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                Dashboard
            </a>

            <!-- Course Management with Submenu -->
            @canany(['course.view', 'course.create', 'course.update'])
                <div
                    x-data="{ open: {{ request()->routeIs('admin.courses.*') || request()->routeIs('admin.topics.*') || request()->routeIs('admin.lessons.*') || request()->routeIs('admin.course-categories.*') || request()->routeIs('admin.course-tags.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open"
                        class="w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.courses.*') || request()->routeIs('admin.topics.*') || request()->routeIs('admin.lessons.*') || request()->routeIs('admin.course-categories.*') || request()->routeIs('admin.course-tags.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }} transition-colors duration-150">
                        <div class="flex items-center">
                            <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                            Course Management
                        </div>
                        <svg class="h-5 w-5 transform transition-transform duration-150" :class="{ 'rotate-180': open }"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open" x-collapse class="mt-1 space-y-1 pl-11">
                        @can('course.view')
                            <a href="{{ route('admin.courses.index') }}"
                                class="block px-3 py-2 text-sm {{ request()->routeIs('admin.courses.*') && !request()->routeIs('admin.course-categories.*') && !request()->routeIs('admin.course-tags.*') ? 'text-indigo-700 font-medium' : 'text-gray-600 hover:text-gray-900' }} hover:bg-gray-50 rounded-md">All
                                Courses</a>
                        @endcan
                        @can('course.view')
                            <a href="{{ route('admin.course-categories.index') }}"
                                class="block px-3 py-2 text-sm {{ request()->routeIs('admin.course-categories.*') ? 'text-indigo-700 font-medium' : 'text-gray-600 hover:text-gray-900' }} hover:bg-gray-50 rounded-md">Course
                                Categories</a>
                        @endcan
                        @can('course.view')
                            <a href="{{ route('admin.course-tags.index') }}"
                                class="block px-3 py-2 text-sm {{ request()->routeIs('admin.course-tags.*') ? 'text-indigo-700 font-medium' : 'text-gray-600 hover:text-gray-900' }} hover:bg-gray-50 rounded-md">Course
                                Tags</a>
                        @endcan
                        @can('topic.view')
                            <a href="{{ route('admin.topics.index') }}"
                                class="block px-3 py-2 text-sm {{ request()->routeIs('admin.topics.*') ? 'text-indigo-700 font-medium' : 'text-gray-600 hover:text-gray-900' }} hover:bg-gray-50 rounded-md">Topics</a>
                        @endcan
                        @can('lesson.view')
                            <a href="{{ route('admin.lessons.index') }}"
                                class="block px-3 py-2 text-sm {{ request()->routeIs('admin.lessons.*') ? 'text-indigo-700 font-medium' : 'text-gray-600 hover:text-gray-900' }} hover:bg-gray-50 rounded-md">Lessons</a>
                        @endcan
                    </div>
                </div>
            @endcanany

            <!-- Categories -->
            @canany(['course.view', 'course.create', 'course.update'])
                <a href="{{ route('admin.categories.index') }}"
                    class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.categories.*') ? 'bg-indigo-50 text-indigo-700 border-l-4 border-indigo-700' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }} transition-colors duration-150">
                    <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                    </svg>
                    Categories
                </a>
            @endcanany

            <!-- Users with Submenu -->
            @canany(['user.view', 'user.create', 'user.update'])
                <div
                    x-data="{ open: {{ request()->routeIs('admin.users.*') || request()->routeIs('admin.roles.*') || request()->routeIs('admin.permissions.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open"
                        class="w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.users.*') || request()->routeIs('admin.roles.*') || request()->routeIs('admin.permissions.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }} transition-colors duration-150">
                        <div class="flex items-center">
                            <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            Users
                        </div>
                        <svg class="h-5 w-5 transform transition-transform duration-150" :class="{ 'rotate-180': open }"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open" x-collapse class="mt-1 space-y-1 pl-11">
                        @can('user.view')
                            <a href="{{ route('admin.users.index') }}"
                                class="block px-3 py-2 text-sm {{ request()->routeIs('admin.users.*') ? 'text-indigo-700 font-medium' : 'text-gray-600 hover:text-gray-900' }} hover:bg-gray-50 rounded-md">All
                                Users</a>
                        @endcan
                        @if(auth()->user()->hasRole('super_admin'))
                            <a href="{{ route('admin.roles.index') }}"
                                class="block px-3 py-2 text-sm {{ request()->routeIs('admin.roles.*') ? 'text-indigo-700 font-medium' : 'text-gray-600 hover:text-gray-900' }} hover:bg-gray-50 rounded-md">Roles</a>
                            <a href="{{ route('admin.permissions.index') }}"
                                class="block px-3 py-2 text-sm {{ request()->routeIs('admin.permissions.*') ? 'text-indigo-700 font-medium' : 'text-gray-600 hover:text-gray-900' }} hover:bg-gray-50 rounded-md">Permissions</a>
                        @endif
                    </div>
                </div>
            @endcanany

            <!-- Enrollments -->
            @can('order.view')
                <a href="{{ route('admin.enrollments.index') }}"
                    class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.enrollments.*') ? 'bg-indigo-50 text-indigo-700 border-l-4 border-indigo-700' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }} transition-colors duration-150">
                    <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    Enrollments
                </a>
            @endcan

            <!-- Quizzes & Questions -->
            @canany(['quiz.view', 'quiz.manage'])
                <div
                    x-data="{ open: {{ request()->routeIs('admin.quizzes.*') || request()->routeIs('admin.questions.*') || request()->routeIs('admin.quiz-attempts.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open"
                        class="w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.quizzes.*') || request()->routeIs('admin.questions.*') || request()->routeIs('admin.quiz-attempts.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }} transition-colors duration-150">
                        <div class="flex items-center">
                            <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Quiz
                        </div>
                        <svg class="h-5 w-5 transform transition-transform duration-150" :class="{ 'rotate-180': open }"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open" x-collapse class="mt-1 space-y-1 pl-11">
                        <a href="{{ route('admin.quizzes.index') }}"
                            class="block px-3 py-2 text-sm {{ request()->routeIs('admin.quizzes.*') && !request()->routeIs('admin.quiz-attempts.*') && !request()->routeIs('admin.questions.*') ? 'text-indigo-700 font-medium' : 'text-gray-600 hover:text-gray-900' }} hover:bg-gray-50 rounded-md">All
                            Quizzes</a>
                        <a href="{{ route('admin.questions.index') }}"
                            class="block px-3 py-2 text-sm {{ request()->routeIs('admin.questions.*') ? 'text-indigo-700 font-medium' : 'text-gray-600 hover:text-gray-900' }} hover:bg-gray-50 rounded-md">All
                            Questions</a>
                        <a href="{{ route('admin.quiz-attempts.index') }}"
                            class="block px-3 py-2 text-sm {{ request()->routeIs('admin.quiz-attempts.*') ? 'text-indigo-700 font-medium' : 'text-gray-600 hover:text-gray-900' }} hover:bg-gray-50 rounded-md">All
                            Quiz Attempts</a>
                    </div>
                </div>
            @endcanany

            <!-- Assignment Management with Submenu -->
            @canany(['assignment.view', 'assignment.manage'])
                <div
                    x-data="{ open: {{ request()->routeIs('admin.assignments.*') || request()->routeIs('admin.assignment-submissions.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open"
                        class="w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.assignments.*') || request()->routeIs('admin.assignment-submissions.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }} transition-colors duration-150">
                        <div class="flex items-center">
                            <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Assignment
                        </div>
                        <svg class="h-5 w-5 transform transition-transform duration-150" :class="{ 'rotate-180': open }"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open" x-collapse class="mt-1 space-y-1 pl-11">
                        <a href="{{ route('admin.assignments.index') }}"
                            class="block px-3 py-2 text-sm {{ request()->routeIs('admin.assignments.*') && !request()->routeIs('admin.assignment-submissions.*') ? 'text-indigo-700 font-medium' : 'text-gray-600 hover:text-gray-900' }} hover:bg-gray-50 rounded-md">All
                            Assignments</a>
                        <a href="{{ route('admin.assignment-submissions.index') }}"
                            class="block px-3 py-2 text-sm {{ request()->routeIs('admin.assignment-submissions.*') ? 'text-indigo-700 font-medium' : 'text-gray-600 hover:text-gray-900' }} hover:bg-gray-50 rounded-md">All
                            Submissions</a>
                    </div>
                </div>
            @endcanany

            <!-- Monetization & Billing -->
            @can('order.view')
                <div
                    x-data="{ open: {{ request()->routeIs('admin.packages.*') || request()->routeIs('admin.package-features.*') || request()->routeIs('admin.subscription-plans.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open"
                        class="w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.packages.*') || request()->routeIs('admin.package-features.*') || request()->routeIs('admin.subscription-plans.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }} transition-colors duration-150">
                        <div class="flex items-center">
                            <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                            Monetization
                        </div>
                        <svg class="h-5 w-5 transform transition-transform duration-150" :class="{ 'rotate-180': open }"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open" x-collapse class="mt-1 space-y-1 pl-11">
                        <a href="{{ route('admin.packages.index') }}"
                            class="block px-3 py-2 text-sm {{ request()->routeIs('admin.packages.*') && !request()->routeIs('admin.package-features.*') ? 'text-indigo-700 font-medium' : 'text-gray-600 hover:text-gray-900' }} hover:bg-gray-50 rounded-md">Packages</a>
                        <a href="{{ route('admin.package-features.index') }}"
                            class="block px-3 py-2 text-sm {{ request()->routeIs('admin.package-features.*') ? 'text-indigo-700 font-medium' : 'text-gray-600 hover:text-gray-900' }} hover:bg-gray-50 rounded-md">Package
                            Features</a>
                        <a href="{{ route('admin.subscription-plans.index') }}"
                            class="block px-3 py-2 text-sm {{ request()->routeIs('admin.subscription-plans.*') ? 'text-indigo-700 font-medium' : 'text-gray-600 hover:text-gray-900' }} hover:bg-gray-50 rounded-md">Subscription
                            Plans</a>
                    </div>
                </div>
            @endcan

            <!-- Certificate Management -->
            <div
                x-data="{ open: {{ request()->routeIs('admin.certificates.*') || request()->routeIs('admin.certificate-templates.*') ? 'true' : 'false' }} }">
                <button @click="open = !open"
                    class="w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.certificates.*') || request()->routeIs('admin.certificate-templates.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }} transition-colors duration-150">
                    <div class="flex items-center">
                        <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z">
                            </path>
                        </svg>
                        Certificates
                    </div>
                    <svg class="h-5 w-5 transform transition-transform duration-150" :class="{ 'rotate-180': open }"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="open" x-collapse class="mt-1 space-y-1 pl-11">
                    <a href="{{ route('admin.certificates.index') }}"
                        class="block px-3 py-2 text-sm {{ request()->routeIs('admin.certificates.*') && !request()->routeIs('admin.certificate-templates.*') ? 'text-indigo-700 font-medium' : 'text-gray-600 hover:text-gray-900' }} hover:bg-gray-50 rounded-md">All
                        Certificates</a>
                    <a href="{{ route('admin.certificates.analytics') }}"
                        class="block px-3 py-2 text-sm {{ request()->routeIs('admin.certificates.analytics') ? 'text-indigo-700 font-medium' : 'text-gray-600 hover:text-gray-900' }} hover:bg-gray-50 rounded-md">Analytics</a>
                    <a href="{{ route('admin.certificate-templates.index') }}"
                        class="block px-3 py-2 text-sm {{ request()->routeIs('admin.certificate-templates.*') ? 'text-indigo-700 font-medium' : 'text-gray-600 hover:text-gray-900' }} hover:bg-gray-50 rounded-md">Templates</a>
                </div>
            </div>

            <!-- Lesson Comments -->
            <a href="{{ route('admin.lesson-comments.index') }}"
                class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.lesson-comments.*') ? 'bg-indigo-50 text-indigo-700 border-l-4 border-indigo-700' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }} transition-colors duration-150">
                <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                </svg>
                Lesson Comments
                @php
                    $unreadCommentsCount = \App\Models\LessonComment::topLevel()->where('created_at', '>', now()->subDays(7))->count();
                @endphp
                @if($unreadCommentsCount > 0)
                    <span class="ml-auto bg-indigo-100 text-indigo-600 py-0.5 px-2 rounded-full text-xs font-medium">
                        {{ $unreadCommentsCount > 99 ? '99+' : $unreadCommentsCount }}
                    </span>
                @endif
            </a>

            <!-- Divider -->
            @can('reports.view')
                <div class="my-4 border-t border-gray-200"></div>

                <!-- Reports -->
                <div x-data="{ open: {{ request()->routeIs('admin.reports.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open"
                        class="w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-md text-gray-700 hover:bg-gray-50 hover:text-gray-900 transition-colors duration-150">
                        <div class="flex items-center">
                            <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                            Reports
                        </div>
                        <svg class="h-5 w-5 transform transition-transform duration-150" :class="{ 'rotate-180': open }"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open" x-collapse class="mt-1 space-y-1 pl-11">
                        <a href="{{ route('admin.reports.dashboard') }}"
                            class="block px-3 py-2 text-sm text-gray-600 hover:text-gray-900 hover:bg-gray-50 rounded-md transition-colors duration-150">Overview</a>
                        <a href="{{ route('admin.reports.revenue') }}"
                            class="block px-3 py-2 text-sm text-gray-600 hover:text-gray-900 hover:bg-gray-50 rounded-md transition-colors duration-150">Revenue</a>
                        <a href="{{ route('admin.reports.enrollments') }}"
                            class="block px-3 py-2 text-sm text-gray-600 hover:text-gray-900 hover:bg-gray-50 rounded-md transition-colors duration-150">Enrollments</a>
                        <a href="{{ route('admin.reports.course-performance') }}"
                            class="block px-3 py-2 text-sm text-gray-600 hover:text-gray-900 hover:bg-gray-50 rounded-md transition-colors duration-150">Course
                            Performance</a>
                        <a href="{{ route('admin.reports.student-progress') }}"
                            class="block px-3 py-2 text-sm text-gray-600 hover:text-gray-900 hover:bg-gray-50 rounded-md transition-colors duration-150">Student
                            Progress</a>
                        <a href="{{ route('admin.reports.tutor-performance') }}"
                            class="block px-3 py-2 text-sm text-gray-600 hover:text-gray-900 hover:bg-gray-50 rounded-md transition-colors duration-150">Tutor
                            Performance</a>
                    </div>
                </div>
            @endcan
        </nav>

        <!-- User Profile -->
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
                                {{ substr(auth()->user()->name, 0, 2) }}
                            </div>
                        @endif
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-500">Administrator</p>
                    </div>
                </div>
            </div>
        </div>
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
                <a href="{{ route('admin.dashboard') }}">
                    <img src="{{ asset('images/logo-full.png') }}" alt="Master IELTS" class="h-8">
                </a>
            </div>
            <nav class="px-3 space-y-1">
                <!-- Dashboard -->
                <a href="{{ route('admin.dashboard') }}"
                    class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    Dashboard
                </a>

                <!-- Course Management with Submenu -->
                @canany(['course.view', 'course.create', 'course.update'])
                    <div
                        x-data="{ open: {{ request()->routeIs('admin.courses.*') || request()->routeIs('admin.topics.*') || request()->routeIs('admin.lessons.*') || request()->routeIs('admin.course-categories.*') || request()->routeIs('admin.course-tags.*') ? 'true' : 'false' }} }">
                        <button @click="open = !open"
                            class="w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.courses.*') || request()->routeIs('admin.topics.*') || request()->routeIs('admin.lessons.*') || request()->routeIs('admin.course-categories.*') || request()->routeIs('admin.course-tags.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:bg-gray-50' }}">
                            <div class="flex items-center">
                                <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                                Course Management
                            </div>
                            <svg class="h-5 w-5 transform transition-transform duration-150" :class="{ 'rotate-180': open }"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="open" x-collapse class="mt-1 space-y-1 pl-11">
                            @can('course.view')
                                <a href="{{ route('admin.courses.index') }}"
                                    class="block px-3 py-2 text-sm {{ request()->routeIs('admin.courses.*') && !request()->routeIs('admin.course-categories.*') && !request()->routeIs('admin.course-tags.*') ? 'text-indigo-700 font-medium' : 'text-gray-600 hover:text-gray-900' }} hover:bg-gray-50 rounded-md">All
                                    Courses</a>
                            @endcan
                            @can('course.view')
                                <a href="{{ route('admin.course-categories.index') }}"
                                    class="block px-3 py-2 text-sm {{ request()->routeIs('admin.course-categories.*') ? 'text-indigo-700 font-medium' : 'text-gray-600 hover:text-gray-900' }} hover:bg-gray-50 rounded-md">Course
                                    Categories</a>
                            @endcan
                            @can('course.view')
                                <a href="{{ route('admin.course-tags.index') }}"
                                    class="block px-3 py-2 text-sm {{ request()->routeIs('admin.course-tags.*') ? 'text-indigo-700 font-medium' : 'text-gray-600 hover:text-gray-900' }} hover:bg-gray-50 rounded-md">Course
                                    Tags</a>
                            @endcan
                            @can('topic.view')
                                <a href="{{ route('admin.topics.index') }}"
                                    class="block px-3 py-2 text-sm {{ request()->routeIs('admin.topics.*') ? 'text-indigo-700 font-medium' : 'text-gray-600 hover:text-gray-900' }} hover:bg-gray-50 rounded-md">Topics</a>
                            @endcan
                            @can('lesson.view')
                                <a href="{{ route('admin.lessons.index') }}"
                                    class="block px-3 py-2 text-sm {{ request()->routeIs('admin.lessons.*') ? 'text-indigo-700 font-medium' : 'text-gray-600 hover:text-gray-900' }} hover:bg-gray-50 rounded-md">Lessons</a>
                            @endcan
                        </div>
                    </div>
                @endcanany

                <!-- Categories -->
                @canany(['course.view', 'course.create', 'course.update'])
                    <a href="{{ route('admin.categories.index') }}"
                        class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.categories.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:bg-gray-50' }}">
                        <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                        Categories
                    </a>
                @endcanany

                <!-- Users with Submenu -->
                @canany(['user.view', 'user.create', 'user.update'])
                    <div
                        x-data="{ open: {{ request()->routeIs('admin.users.*') || request()->routeIs('admin.roles.*') || request()->routeIs('admin.permissions.*') ? 'true' : 'false' }} }">
                        <button @click="open = !open"
                            class="w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.users.*') || request()->routeIs('admin.roles.*') || request()->routeIs('admin.permissions.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:bg-gray-50' }}">
                            <div class="flex items-center">
                                <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                                Users
                            </div>
                            <svg class="h-5 w-5 transform transition-transform duration-150" :class="{ 'rotate-180': open }"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="open" x-collapse class="mt-1 space-y-1 pl-11">
                            @can('user.view')
                                <a href="{{ route('admin.users.index') }}"
                                    class="block px-3 py-2 text-sm {{ request()->routeIs('admin.users.*') ? 'text-indigo-700 font-medium' : 'text-gray-600 hover:text-gray-900' }} hover:bg-gray-50 rounded-md">All
                                    Users</a>
                            @endcan
                            @if(auth()->user()->hasRole('super_admin'))
                                <a href="{{ route('admin.roles.index') }}"
                                    class="block px-3 py-2 text-sm {{ request()->routeIs('admin.roles.*') ? 'text-indigo-700 font-medium' : 'text-gray-600 hover:text-gray-900' }} hover:bg-gray-50 rounded-md">Roles</a>
                                <a href="{{ route('admin.permissions.index') }}"
                                    class="block px-3 py-2 text-sm {{ request()->routeIs('admin.permissions.*') ? 'text-indigo-700 font-medium' : 'text-gray-600 hover:text-gray-900' }} hover:bg-gray-50 rounded-md">Permissions</a>
                            @endif
                        </div>
                    </div>
                @endcanany

                <!-- Enrollments -->
                @can('order.view')
                    <a href="{{ route('admin.enrollments.index') }}"
                        class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.enrollments.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:bg-gray-50' }}">
                        <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        Enrollments
                    </a>
                @endcan

                <!-- Quizzes & Questions -->
                @canany(['quiz.view', 'quiz.manage'])
                    <div
                        x-data="{ open: {{ request()->routeIs('admin.quizzes.*') || request()->routeIs('admin.questions.*') || request()->routeIs('admin.quiz-attempts.*') ? 'true' : 'false' }} }">
                        <button @click="open = !open"
                            class="w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.quizzes.*') || request()->routeIs('admin.questions.*') || request()->routeIs('admin.quiz-attempts.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:bg-gray-50' }}">
                            <div class="flex items-center">
                                <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Quiz Management
                            </div>
                            <svg class="h-5 w-5 transform transition-transform duration-150" :class="{ 'rotate-180': open }"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="open" x-collapse class="mt-1 space-y-1 pl-11">
                            <a href="{{ route('admin.quizzes.index') }}"
                                class="block px-3 py-2 text-sm {{ request()->routeIs('admin.quizzes.*') && !request()->routeIs('admin.quiz-attempts.*') && !request()->routeIs('admin.questions.*') ? 'text-indigo-700 font-medium' : 'text-gray-600 hover:text-gray-900' }} hover:bg-gray-50 rounded-md">All
                                Quizzes</a>
                            <a href="{{ route('admin.questions.index') }}"
                                class="block px-3 py-2 text-sm {{ request()->routeIs('admin.questions.*') ? 'text-indigo-700 font-medium' : 'text-gray-600 hover:text-gray-900' }} hover:bg-gray-50 rounded-md">All
                                Questions</a>
                            <a href="{{ route('admin.quiz-attempts.index') }}"
                                class="block px-3 py-2 text-sm {{ request()->routeIs('admin.quiz-attempts.*') ? 'text-indigo-700 font-medium' : 'text-gray-600 hover:text-gray-900' }} hover:bg-gray-50 rounded-md">All
                                Quiz Attempts</a>
                        </div>
                    </div>
                @endcanany

                <!-- Assignment Management with Submenu -->
                @canany(['assignment.view', 'assignment.manage'])
                    <div
                        x-data="{ open: {{ request()->routeIs('admin.assignments.*') || request()->routeIs('admin.assignment-submissions.*') ? 'true' : 'false' }} }">
                        <button @click="open = !open"
                            class="w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.assignments.*') || request()->routeIs('admin.assignment-submissions.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:bg-gray-50' }}">
                            <div class="flex items-center">
                                <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Assignment Management
                            </div>
                            <svg class="h-5 w-5 transform transition-transform duration-150" :class="{ 'rotate-180': open }"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="open" x-collapse class="mt-1 space-y-1 pl-11">
                            <a href="{{ route('admin.assignments.index') }}"
                                class="block px-3 py-2 text-sm {{ request()->routeIs('admin.assignments.*') && !request()->routeIs('admin.assignment-submissions.*') ? 'text-indigo-700 font-medium' : 'text-gray-600 hover:text-gray-900' }} hover:bg-gray-50 rounded-md">All
                                Assignments</a>
                            <a href="{{ route('admin.assignment-submissions.index') }}"
                                class="block px-3 py-2 text-sm {{ request()->routeIs('admin.assignment-submissions.*') ? 'text-indigo-700 font-medium' : 'text-gray-600 hover:text-gray-900' }} hover:bg-gray-50 rounded-md">All
                                Submissions</a>
                        </div>
                    </div>
                @endcanany

                <!-- Monetization & Billing -->
                @can('order.view')
                    <div
                        x-data="{ open: {{ request()->routeIs('admin.packages.*') || request()->routeIs('admin.package-features.*') || request()->routeIs('admin.subscription-plans.*') ? 'true' : 'false' }} }">
                        <button @click="open = !open"
                            class="w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.packages.*') || request()->routeIs('admin.package-features.*') || request()->routeIs('admin.subscription-plans.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:bg-gray-50' }}">
                            <div class="flex items-center">
                                <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                </svg>
                                Monetization
                            </div>
                            <svg class="h-5 w-5 transform transition-transform duration-150" :class="{ 'rotate-180': open }"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="open" x-collapse class="mt-1 space-y-1 pl-11">
                            <a href="{{ route('admin.packages.index') }}"
                                class="block px-3 py-2 text-sm {{ request()->routeIs('admin.packages.*') && !request()->routeIs('admin.package-features.*') ? 'text-indigo-700 font-medium' : 'text-gray-600 hover:text-gray-900' }} hover:bg-gray-50 rounded-md">Packages</a>
                            <a href="{{ route('admin.package-features.index') }}"
                                class="block px-3 py-2 text-sm {{ request()->routeIs('admin.package-features.*') ? 'text-indigo-700 font-medium' : 'text-gray-600 hover:text-gray-900' }} hover:bg-gray-50 rounded-md">Package
                                Features</a>
                            <a href="{{ route('admin.subscription-plans.index') }}"
                                class="block px-3 py-2 text-sm {{ request()->routeIs('admin.subscription-plans.*') ? 'text-indigo-700 font-medium' : 'text-gray-600 hover:text-gray-900' }} hover:bg-gray-50 rounded-md">Subscription
                                Plans</a>
                        </div>
                    </div>
                @endcan

                <!-- Certificate Management -->
                <div
                    x-data="{ open: {{ request()->routeIs('admin.certificates.*') || request()->routeIs('admin.certificate-templates.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open"
                        class="w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.certificates.*') || request()->routeIs('admin.certificate-templates.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:bg-gray-50' }}">
                        <div class="flex items-center">
                            <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z">
                                </path>
                            </svg>
                            Certificates
                        </div>
                        <svg class="h-5 w-5 transform transition-transform duration-150" :class="{ 'rotate-180': open }"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open" x-collapse class="mt-1 space-y-1 pl-11">
                        <a href="{{ route('admin.certificates.index') }}"
                            class="block px-3 py-2 text-sm {{ request()->routeIs('admin.certificates.*') && !request()->routeIs('admin.certificate-templates.*') ? 'text-indigo-700 font-medium' : 'text-gray-600 hover:text-gray-900' }} hover:bg-gray-50 rounded-md">All
                            Certificates</a>
                        <a href="{{ route('admin.certificates.analytics') }}"
                            class="block px-3 py-2 text-sm {{ request()->routeIs('admin.certificates.analytics') ? 'text-indigo-700 font-medium' : 'text-gray-600 hover:text-gray-900' }} hover:bg-gray-50 rounded-md">Analytics</a>
                        <a href="{{ route('admin.certificate-templates.index') }}"
                            class="block px-3 py-2 text-sm {{ request()->routeIs('admin.certificate-templates.*') ? 'text-indigo-700 font-medium' : 'text-gray-600 hover:text-gray-900' }} hover:bg-gray-50 rounded-md">Templates</a>
                    </div>
                </div>

                <!-- Lesson Comments -->
                <a href="{{ route('admin.lesson-comments.index') }}"
                    class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.lesson-comments.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                    </svg>
                    Lesson Comments
                    @php
                        $unreadCommentsCount = \App\Models\LessonComment::topLevel()->where('created_at', '>', now()->subDays(7))->count();
                    @endphp
                    @if($unreadCommentsCount > 0)
                        <span class="ml-auto bg-indigo-100 text-indigo-600 py-0.5 px-2 rounded-full text-xs font-medium">
                            {{ $unreadCommentsCount > 99 ? '99+' : $unreadCommentsCount }}
                        </span>
                    @endif
                </a>

                <!-- Divider -->
                @can('reports.view')
                    <div class="my-4 border-t border-gray-200"></div>

                    <!-- Reports -->
                    <div x-data="{ open: {{ request()->routeIs('admin.reports.*') ? 'true' : 'false' }} }">
                        <button @click="open = !open"
                            class="w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-md text-gray-700 hover:bg-gray-50">
                            <div class="flex items-center">
                                <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                                Reports
                            </div>
                            <svg class="h-5 w-5 transform transition-transform duration-150" :class="{ 'rotate-180': open }"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="open" x-collapse class="mt-1 space-y-1 pl-11">
                            <a href="{{ route('admin.reports.dashboard') }}"
                                class="block px-3 py-2 text-sm text-gray-600 hover:text-gray-900 hover:bg-gray-50 rounded-md">Overview</a>
                            <a href="{{ route('admin.reports.revenue') }}"
                                class="block px-3 py-2 text-sm text-gray-600 hover:text-gray-900 hover:bg-gray-50 rounded-md">Revenue</a>
                            <a href="{{ route('admin.reports.enrollments') }}"
                                class="block px-3 py-2 text-sm text-gray-600 hover:text-gray-900 hover:bg-gray-50 rounded-md">Enrollments</a>
                            <a href="{{ route('admin.reports.course-performance') }}"
                                class="block px-3 py-2 text-sm text-gray-600 hover:text-gray-900 hover:bg-gray-50 rounded-md">Course
                                Performance</a>
                            <a href="{{ route('admin.reports.student-progress') }}"
                                class="block px-3 py-2 text-sm text-gray-600 hover:text-gray-900 hover:bg-gray-50 rounded-md">Student
                                Progress</a>
                            <a href="{{ route('admin.reports.tutor-performance') }}"
                                class="block px-3 py-2 text-sm text-gray-600 hover:text-gray-900 hover:bg-gray-50 rounded-md">Tutor
                                Performance</a>
                        </div>
                    </div>
                @endcan
            </nav>
        </div>

        <!-- Mobile User Profile -->
        <div class="flex-shrink-0 flex border-t border-gray-200 p-4">
            <div class="flex items-center">
                @if(auth()->user()->avatar)
                    <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="{{ auth()->user()->name }}"
                        class="h-10 w-10 rounded-full object-cover">
                @else
                    <div
                        class="h-10 w-10 rounded-full bg-indigo-600 flex items-center justify-center text-white font-semibold">
                        {{ substr(auth()->user()->name, 0, 2) }}
                    </div>
                @endif
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-900">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-gray-500">Administrator</p>
                </div>
            </div>
        </div>
    </div>
</div>