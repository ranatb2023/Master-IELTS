<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Admin Dashboard') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Font Awesome -->
    <script src="https://kit.fontawesome.com/92041d487f.js" crossorigin="anonymous"></script>
    
    <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.7.2/css/all.css">
</head>
<body class="bg-gray-100 text-gray-900">
    <div class="flex min-h-screen">

        <!-- Sidebar -->
        <aside class="w-64 bg-white border-r border-gray-200 flex flex-col shadow-lg">
            <!-- Logo -->
            <div class="h-16 flex items-center justify-center border-b border-gray-100">
                <span class="text-2xl font-bold text-indigo-500">Master<span class="text-gray-900"> IELTS</span></span>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 p-4 space-y-1">
                <a href="{{ route('dashboard') }}"
                   class="relative group flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-indigo-50 hover:text-indigo-600 transition-all duration-150 {{ request()->routeIs('dashboard') ? 'bg-indigo-50 text-indigo-700 font-semibold' : '' }}">
                    @if(request()->routeIs('dashboard'))
                        <span class="absolute left-0 inset-y-0 w-1 bg-indigo-600 rounded-r"></span>
                    @endif
                    <i class="fa-light fa-house-blank w-5 text-lg mr-3 text-gray-500 group-hover:text-indigo-600"></i>
                    <span>Dashboard</span>
                </a>

                <a href="#" class="relative group flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-indigo-50 hover:text-indigo-600 transition-all duration-150">
                    <i class="fa-light fa-book w-5 text-lg mr-3 text-gray-500 group-hover:text-indigo-600"></i>
                    <span>Courses</span>
                </a>

                <a href="#" class="relative group flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-indigo-50 hover:text-indigo-600 transition-all duration-150">
                    <i class="fa-light fa-user w-5 text-lg mr-3 text-gray-500 group-hover:text-indigo-600"></i>
                    <span>Students</span>
                </a>

                <a href="#" class="relative group flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-indigo-50 hover:text-indigo-600 transition-all duration-150">
                    <i class="fa-light fa-cog w-5 text-lg mr-3 text-gray-500 group-hover:text-indigo-600"></i>
                    <span>Settings</span>
                </a>
            </nav>

            <!-- Bottom section -->
            <div class="p-4 border-t border-gray-100">
                <div class="flex items-center space-x-3">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}" class="w-10 h-10 rounded-full shadow-sm" />
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-gray-800">{{ Auth::user()->name }}</p>
                        <form method="POST" action="{{ route('logout') }}" class="mt-1">
                            @csrf
                            <button type="submit" class="inline-flex items-center gap-2 text-sm text-gray-600 hover:text-indigo-600 transition">
                                <i class="fa-light fa-sign-out-alt"></i>
                                <span>Logout</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main content -->
        <div class="flex-1 flex flex-col">
            <header class="h-16 bg-white shadow-sm flex items-center justify-between px-6">
                <h1 class="text-lg font-semibold">@yield('title', 'Dashboard')</h1>
                <div class="hidden md:flex items-center gap-4">
                    <input type="text" placeholder="Search..." class="px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
            </header>

            <main class="flex-1 p-6 bg-gray-50">
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
