<!-- Sidebar for desktop -->
<aside class="hidden lg:flex lg:flex-shrink-0">
    <div class="flex flex-col w-64 bg-white border-r border-gray-200">
        <!-- Logo -->
        <div class="flex items-center justify-center h-16 px-4 border-b border-gray-200">
            <a href="{{ route('tutor.dashboard') }}">
                <img src="{{ asset('images/logo-full.png') }}" alt="Master IELTS" class="h-8">
            </a>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1">
            <!-- Dashboard -->
            <a href="{{ route('tutor.dashboard') }}"
                class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('tutor.dashboard') ? 'bg-indigo-50 text-indigo-700 border-l-4 border-indigo-700' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }} transition-colors duration-150">
                <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                Dashboard
            </a>

            <!-- Course Management with Submenu -->
            @canany(['course.view', 'course.create', 'course.update'])
                <div
                    x-data="{ open: {{ request()->routeIs('tutor.courses.*') || request()->routeIs('tutor.topics.*') || request()->routeIs('tutor.lessons.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open"
                        class="w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('tutor.courses.*') || request()->routeIs('tutor.topics.*') || request()->routeIs('tutor.lessons.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }} transition-colors duration-150">
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
                            <a href="{{ route('tutor.courses.index') }}"
                                class="block px-3 py-2 text-sm {{ request()->routeIs('tutor.courses.index') ? 'text-indigo-700 font-medium' : 'text-gray-600 hover:text-gray-900' }} hover:bg-gray-50 rounded-md">My
                                Courses</a>
                        @endcan
                        @can('course.create')
                            <a href="{{ route('tutor.courses.create') }}"
                                class="block px-3 py-2 text-sm {{ request()->routeIs('tutor.courses.create') ? 'text-indigo-700 font-medium' : 'text-gray-600 hover:text-gray-900' }} hover:bg-gray-50 rounded-md">Create
                                Course</a>
                        @endcan
                        @can('course.view')
                            <a href="{{ route('tutor.topics.all') }}"
                                class="block px-3 py-2 text-sm {{ request()->routeIs('tutor.topics.*') ? 'text-indigo-700 font-medium' : 'text-gray-600 hover:text-gray-900' }} hover:bg-gray-50 rounded-md">Topics</a>
                        @endcan
                        @can('course.view')
                            <a href="{{ route('tutor.lessons.all') }}"
                                class="block px-3 py-2 text-sm {{ request()->routeIs('tutor.lessons.*') ? 'text-indigo-700 font-medium' : 'text-gray-600 hover:text-gray-900' }} hover:bg-gray-50 rounded-md">Lessons</a>
                        @endcan
                        @can('course.delete')
                            <a href="{{ route('tutor.courses.trash') }}"
                                class="block px-3 py-2 text-sm {{ request()->routeIs('tutor.courses.trash') ? 'text-indigo-700 font-medium' : 'text-gray-600 hover:text-gray-900' }} hover:bg-gray-50 rounded-md">Trash</a>
                        @endcan
                    </div>
                </div>
            @endcanany

            <!-- Students Management with Submenu -->
            @can('user.view')
                <div x-data="{ open: {{ request()->routeIs('tutor.enrollments.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open"
                        class="w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('tutor.enrollments.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }} transition-colors duration-150">
                        <div class="flex items-center">
                            <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            Students
                        </div>
                        <svg class="h-5 w-5 transform transition-transform duration-150" :class="{ 'rotate-180': open }"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open" x-collapse class="mt-1 space-y-1 pl-11">
                        <a href="{{ route('tutor.enrollments.index') }}"
                            class="block px-3 py-2 text-sm {{ request()->routeIs('tutor.enrollments.*') ? 'text-indigo-700 font-medium' : 'text-gray-600 hover:text-gray-900' }} hover:bg-gray-50 rounded-md">All
                            Students</a>
                    </div>
                </div>
            @endcan

            <!-- Assessments with Submenu -->
            @canany(['quiz.view', 'quiz.manage', 'assignment.view', 'assignment.grade'])
                <div
                    x-data="{ open: {{ request()->routeIs('tutor.quizzes.*') || request()->routeIs('tutor.questions.*') || request()->routeIs('tutor.quiz-attempts.*') || request()->routeIs('tutor.assignments.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open"
                        class="w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('tutor.quizzes.*') || request()->routeIs('tutor.questions.*') || request()->routeIs('tutor.quiz-attempts.*') || request()->routeIs('tutor.assignments.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }} transition-colors duration-150">
                        <div class="flex items-center">
                            <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            Assessments
                        </div>
                        <svg class="h-5 w-5 transform transition-transform duration-150" :class="{ 'rotate-180': open }"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open" x-collapse class="mt-1 space-y-1 pl-11">
                        @canany(['quiz.view', 'quiz.manage'])
                            <a href="{{ route('tutor.quizzes.index') }}"
                                class="block px-3 py-2 text-sm {{ request()->routeIs('tutor.quizzes.*') && !request()->routeIs('tutor.quiz-attempts.*') ? 'text-indigo-700 font-medium' : 'text-gray-600 hover:text-gray-900' }} hover:bg-gray-50 rounded-md">Quizzes</a>
                        @endcanany
                        @can('quiz.manage')
                            <a href="{{ route('tutor.questions.index') }}"
                                class="block px-3 py-2 text-sm {{ request()->routeIs('tutor.questions.*') ? 'text-indigo-700 font-medium' : 'text-gray-600 hover:text-gray-900' }} hover:bg-gray-50 rounded-md">Questions</a>
                            <a href="{{ route('tutor.quiz-attempts.index') }}"
                                class="block px-3 py-2 text-sm {{ request()->routeIs('tutor.quiz-attempts.*') ? 'text-indigo-700 font-medium' : 'text-gray-600 hover:text-gray-900' }} hover:bg-gray-50 rounded-md">Quiz
                                Attempts</a>
                        @endcan
                        @canany(['assignment.view', 'assignment.grade'])
                            <a href="{{ route('tutor.assignments.index') }}"
                                class="block px-3 py-2 text-sm {{ request()->routeIs('tutor.assignments.*') ? 'text-indigo-700 font-medium' : 'text-gray-600 hover:text-gray-900' }} hover:bg-gray-50 rounded-md">Assignments</a>
                        @endcanany
                    </div>
                </div>
            @endcanany

            <!-- Analytics & Reports -->
            @can('reports.view')
                <div x-data="{ open: {{ request()->routeIs('tutor.analytics.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open"
                        class="w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('tutor.analytics.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }} transition-colors duration-150">
                        <div class="flex items-center">
                            <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                            Analytics
                        </div>
                        <svg class="h-5 w-5 transform transition-transform duration-150" :class="{ 'rotate-180': open }"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open" x-collapse class="mt-1 space-y-1 pl-11">
                        <a href="{{ route('tutor.analytics.dashboard') }}"
                            class="block px-3 py-2 text-sm {{ request()->routeIs('tutor.analytics.dashboard') ? 'text-indigo-700 font-medium' : 'text-gray-600 hover:text-gray-900' }} hover:bg-gray-50 rounded-md">Overview</a>
                        <a href="{{ route('tutor.analytics.course-performance') }}"
                            class="block px-3 py-2 text-sm {{ request()->routeIs('tutor.analytics.course-performance') ? 'text-indigo-700 font-medium' : 'text-gray-600 hover:text-gray-900' }} hover:bg-gray-50 rounded-md">Course
                            Performance</a>
                        <a href="{{ route('tutor.analytics.student-engagement') }}"
                            class="block px-3 py-2 text-sm {{ request()->routeIs('tutor.analytics.student-engagement') ? 'text-indigo-700 font-medium' : 'text-gray-600 hover:text-gray-900' }} hover:bg-gray-50 rounded-md">Student
                            Engagement</a>
                        <a href="{{ route('tutor.analytics.revenue') }}"
                            class="block px-3 py-2 text-sm {{ request()->routeIs('tutor.analytics.revenue') ? 'text-indigo-700 font-medium' : 'text-gray-600 hover:text-gray-900' }} hover:bg-gray-50 rounded-md">Revenue</a>
                    </div>
                </div>
            @endcan

            <!-- Certificates -->
            @can('certificate.view')
                <a href="{{ route('tutor.certificates.index') }}"
                    class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('tutor.certificates.*') ? 'bg-indigo-50 text-indigo-700 border-l-4 border-indigo-700' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }} transition-colors duration-150">
                    <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z">
                        </path>
                    </svg>
                    Certificates
                </a>
            @endcan

            <!-- Lesson Comments -->
            <a href="{{ route('tutor.lesson-comments.index') }}"
                class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('tutor.lesson-comments.*') ? 'bg-indigo-50 text-indigo-700 border-l-4 border-indigo-700' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }} transition-colors duration-150">
                <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                </svg>
                Lesson Comments
                @php
                    $tutorCourseIds = auth()->user()->createdCourses()->pluck('id')->toArray();
                    $tutorCommentsCount = \App\Models\LessonComment::query()
                        ->topLevel()
                        ->whereHas('lesson.topic.course', function ($q) use ($tutorCourseIds) {
                            $q->whereIn('id', $tutorCourseIds);
                        })
                        ->where('created_at', '>', now()->subDays(7))
                        ->count();
                @endphp
                @if($tutorCommentsCount > 0)
                    <span class="ml-auto bg-indigo-100 text-indigo-600 py-0.5 px-2 rounded-full text-xs font-medium">
                        {{ $tutorCommentsCount > 99 ? '99+' : $tutorCommentsCount }}
                    </span>
                @endif
            </a>
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
                        <p class="text-xs text-gray-500">Tutor</p>
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
                <a href="{{ route('tutor.dashboard') }}">
                    <img src="{{ asset('images/logo-full.png') }}" alt="Master IELTS" class="h-8">
                </a>
            </div>
            <nav class="px-3 space-y-1">
                <!-- Dashboard -->
                <a href="{{ route('tutor.dashboard') }}"
                    class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('tutor.dashboard') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    Dashboard
                </a>

                <!-- Course Management with Submenu -->
                @canany(['course.view', 'course.create', 'course.update'])
                    <div
                        x-data="{ open: {{ request()->routeIs('tutor.courses.*') || request()->routeIs('tutor.topics.*') || request()->routeIs('tutor.lessons.*') ? 'true' : 'false' }} }">
                        <button @click="open = !open"
                            class="w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('tutor.courses.*') || request()->routeIs('tutor.topics.*') || request()->routeIs('tutor.lessons.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:bg-gray-50' }}">
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
                                <a href="{{ route('tutor.courses.index') }}"
                                    class="block px-3 py-2 text-sm {{ request()->routeIs('tutor.courses.index') ? 'text-indigo-700 font-medium' : 'text-gray-600 hover:text-gray-900' }} hover:bg-gray-50 rounded-md">My
                                    Courses</a>
                            @endcan
                            @can('course.create')
                                <a href="{{ route('tutor.courses.create') }}"
                                    class="block px-3 py-2 text-sm {{ request()->routeIs('tutor.courses.create') ? 'text-indigo-700 font-medium' : 'text-gray-600 hover:text-gray-900' }} hover:bg-gray-50 rounded-md">Create
                                    Course</a>
                            @endcan
                            @can('course.view')
                                <a href="{{ route('tutor.topics.all') }}"
                                    class="block px-3 py-2 text-sm {{ request()->routeIs('tutor.topics.*') ? 'text-indigo-700 font-medium' : 'text-gray-600 hover:text-gray-900' }} hover:bg-gray-50 rounded-md">Topics</a>
                            @endcan
                            @can('course.view')
                                <a href="{{ route('tutor.lessons.all') }}"
                                    class="block px-3 py-2 text-sm {{ request()->routeIs('tutor.lessons.*') ? 'text-indigo-700 font-medium' : 'text-gray-600 hover:text-gray-900' }} hover:bg-gray-50 rounded-md">Lessons</a>
                            @endcan
                            @can('course.delete')
                                <a href="{{ route('tutor.courses.trash') }}"
                                    class="block px-3 py-2 text-sm {{ request()->routeIs('tutor.courses.trash') ? 'text-indigo-700 font-medium' : 'text-gray-600 hover:text-gray-900' }} hover:bg-gray-50 rounded-md">Trash</a>
                            @endcan
                        </div>
                    </div>
                @endcanany

                <!-- Topics Management -->
                @can('course.view')
                    <div x-data="{ open: {{ request()->routeIs('tutor.topics.*') ? 'true' : 'false' }} }">
                        <button @click="open = !open"
                            class="w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('tutor.topics.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:bg-gray-50' }}">
                            <div class="flex items-center">
                                <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                </svg>
                                Topics
                            </div>
                            <svg class="h-5 w-5 transform transition-transform duration-150" :class="{ 'rotate-180': open }"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="open" x-collapse class="mt-1 space-y-1 pl-11">
                            <a href="{{ route('tutor.topics.all') }}"
                                class="block px-3 py-2 text-sm {{ request()->routeIs('tutor.topics.all') ? 'text-indigo-700 font-medium' : 'text-gray-600 hover:text-gray-900' }} hover:bg-gray-50 rounded-md">All
                                Topics</a>
                            @can('course.delete')
                                <a href="{{ route('tutor.topics.trash') }}"
                                    class="block px-3 py-2 text-sm {{ request()->routeIs('tutor.topics.trash') ? 'text-indigo-700 font-medium' : 'text-gray-600 hover:text-gray-900' }} hover:bg-gray-50 rounded-md">Trash</a>
                            @endcan
                        </div>
                    </div>
                @endcan

                <!-- Lessons Management -->
                @can('course.view')
                    <div x-data="{ open: {{ request()->routeIs('tutor.lessons.*') ? 'true' : 'false' }} }">
                        <button @click="open = !open"
                            class="w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('tutor.lessons.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:bg-gray-50' }}">
                            <div class="flex items-center">
                                <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                </svg>
                                Lessons
                            </div>
                            <svg class="h-5 w-5 transform transition-transform duration-150" :class="{ 'rotate-180': open }"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="open" x-collapse class="mt-1 space-y-1 pl-11">
                            <a href="{{ route('tutor.lessons.all') }}"
                                class="block px-3 py-2 text-sm {{ request()->routeIs('tutor.lessons.all') ? 'text-indigo-700 font-medium' : 'text-gray-600 hover:text-gray-900' }} hover:bg-gray-50 rounded-md">All
                                Lessons</a>
                            @can('course.delete')
                                <a href="{{ route('tutor.lessons.trash') }}"
                                    class="block px-3 py-2 text-sm {{ request()->routeIs('tutor.lessons.trash') ? 'text-indigo-700 font-medium' : 'text-gray-600 hover:text-gray-900' }} hover:bg-gray-50 rounded-md">Trash</a>
                            @endcan
                        </div>
                    </div>
                @endcan

                <!-- Students Management -->
                @can('user.view')
                    <div x-data="{ open: {{ request()->routeIs('tutor.enrollments.*') ? 'true' : 'false' }} }">
                        <button @click="open = !open"
                            class="w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('tutor.enrollments.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:bg-gray-50' }}">
                            <div class="flex items-center">
                                <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                                Students
                            </div>
                            <svg class="h-5 w-5 transform transition-transform duration-150" :class="{ 'rotate-180': open }"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="open" x-collapse class="mt-1 space-y-1 pl-11">
                            <a href="{{ route('tutor.enrollments.index') }}"
                                class="block px-3 py-2 text-sm {{ request()->routeIs('tutor.enrollments.*') ? 'text-indigo-700 font-medium' : 'text-gray-600 hover:text-gray-900' }} hover:bg-gray-50 rounded-md">All
                                Students</a>
                        </div>
                    </div>
                @endcan

                <!-- Assessments -->
                @canany(['quiz.view', 'quiz.manage', 'assignment.view', 'assignment.grade'])
                    <div
                        x-data="{ open: {{ request()->routeIs('tutor.quizzes.*') || request()->routeIs('tutor.questions.*') || request()->routeIs('tutor.quiz-attempts.*') || request()->routeIs('tutor.assignments.*') ? 'true' : 'false' }} }">
                        <button @click="open = !open"
                            class="w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('tutor.quizzes.*') || request()->routeIs('tutor.questions.*') || request()->routeIs('tutor.quiz-attempts.*') || request()->routeIs('tutor.assignments.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:bg-gray-50' }}">
                            <div class="flex items-center">
                                <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                                Assessments
                            </div>
                            <svg class="h-5 w-5 transform transition-transform duration-150" :class="{ 'rotate-180': open }"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="open" x-collapse class="mt-1 space-y-1 pl-11">
                            @canany(['quiz.view', 'quiz.manage'])
                                <a href="{{ route('tutor.quizzes.index') }}"
                                    class="block px-3 py-2 text-sm {{ request()->routeIs('tutor.quizzes.*') && !request()->routeIs('tutor.quiz-attempts.*') ? 'text-indigo-700 font-medium' : 'text-gray-600 hover:text-gray-900' }} hover:bg-gray-50 rounded-md">Quizzes</a>
                            @endcanany
                            @can('quiz.manage')
                                <a href="{{ route('tutor.questions.index') }}"
                                    class="block px-3 py-2 text-sm {{ request()->routeIs('tutor.questions.*') ? 'text-indigo-700 font-medium' : 'text-gray-600 hover:text-gray-900' }} hover:bg-gray-50 rounded-md">Questions</a>
                                <a href="{{ route('tutor.quiz-attempts.index') }}"
                                    class="block px-3 py-2 text-sm {{ request()->routeIs('tutor.quiz-attempts.*') ? 'text-indigo-700 font-medium' : 'text-gray-600 hover:text-gray-900' }} hover:bg-gray-50 rounded-md">Quiz
                                    Attempts</a>
                            @endcan
                            @canany(['assignment.view', 'assignment.grade'])
                                <a href="{{ route('tutor.assignments.index') }}"
                                    class="block px-3 py-2 text-sm {{ request()->routeIs('tutor.assignments.*') ? 'text-indigo-700 font-medium' : 'text-gray-600 hover:text-gray-900' }} hover:bg-gray-50 rounded-md">Assignments</a>
                            @endcanany
                        </div>
                    </div>
                @endcanany

                <!-- Analytics -->
                @can('reports.view')
                    <div x-data="{ open: {{ request()->routeIs('tutor.analytics.*') ? 'true' : 'false' }} }">
                        <button @click="open = !open"
                            class="w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('tutor.analytics.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:bg-gray-50' }}">
                            <div class="flex items-center">
                                <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                                Analytics
                            </div>
                            <svg class="h-5 w-5 transform transition-transform duration-150" :class="{ 'rotate-180': open }"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="open" x-collapse class="mt-1 space-y-1 pl-11">
                            <a href="{{ route('tutor.analytics.dashboard') }}"
                                class="block px-3 py-2 text-sm {{ request()->routeIs('tutor.analytics.dashboard') ? 'text-indigo-700 font-medium' : 'text-gray-600 hover:text-gray-900' }} hover:bg-gray-50 rounded-md">Overview</a>
                            <a href="{{ route('tutor.analytics.course-performance') }}"
                                class="block px-3 py-2 text-sm {{ request()->routeIs('tutor.analytics.course-performance') ? 'text-indigo-700 font-medium' : 'text-gray-600 hover:text-gray-900' }} hover:bg-gray-50 rounded-md">Course
                                Performance</a>
                            <a href="{{ route('tutor.analytics.student-engagement') }}"
                                class="block px-3 py-2 text-sm {{ request()->routeIs('tutor.analytics.student-engagement') ? 'text-indigo-700 font-medium' : 'text-gray-600 hover:text-gray-900' }} hover:bg-gray-50 rounded-md">Student
                                Engagement</a>
                            <a href="{{ route('tutor.analytics.revenue') }}"
                                class="block px-3 py-2 text-sm {{ request()->routeIs('tutor.analytics.revenue') ? 'text-indigo-700 font-medium' : 'text-gray-600 hover:text-gray-900' }} hover:bg-gray-50 rounded-md">Revenue</a>
                        </div>
                    </div>
                @endcan

                <!-- Certificates -->
                @can('certificate.view')
                    <a href="{{ route('tutor.certificates.index') }}"
                        class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('tutor.certificates.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:bg-gray-50' }}">
                        <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z">
                            </path>
                        </svg>
                        Certificates
                    </a>
                @endcan

                <!-- Lesson Comments -->
                <a href="{{ route('tutor.lesson-comments.index') }}"
                    class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('tutor.lesson-comments.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                    </svg>
                    Lesson Comments
                    @php
                        $tutorCourseIds = auth()->user()->createdCourses()->pluck('id')->toArray();
                        $tutorCommentsCount = \App\Models\LessonComment::query()
                            ->topLevel()
                            ->whereHas('lesson.topic.course', function ($q) use ($tutorCourseIds) {
                                $q->whereIn('id', $tutorCourseIds);
                            })
                            ->where('created_at', '>', now()->subDays(7))
                            ->count();
                    @endphp
                    @if($tutorCommentsCount > 0)
                        <span class="ml-auto bg-indigo-100 text-indigo-600 py-0.5 px-2 rounded-full text-xs font-medium">
                            {{ $tutorCommentsCount > 99 ? '99+' : $tutorCommentsCount }}
                        </span>
                    @endif
                </a>
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
                    <p class="text-xs text-gray-500">Tutor</p>
                </div>
            </div>
        </div>
    </div>
</div>