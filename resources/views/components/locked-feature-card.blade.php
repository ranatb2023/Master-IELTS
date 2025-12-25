<div class="bg-white overflow-hidden shadow rounded-lg p-6 relative">
    <!-- Lock Overlay -->
    <div
        class="absolute inset-0 bg-gray-900 bg-opacity-10 backdrop-blur-sm rounded-lg flex items-center justify-center z-10">
        <div class="text-center p-6">
            <div class="mx-auto flex items-center justify-center h-14 w-14 rounded-full bg-yellow-100 mb-3">
                <svg class="h-7 w-7 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
            </div>
            <h3 class="text-base font-semibold text-gray-900 mb-1">{{ $title }}</h3>
            <p class="text-sm text-gray-600 mb-4">{{ $description }}</p>
            <a href="{{ route('student.subscriptions.index') }}"
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M5 10l7-7m0 0l7 7m-7-7v18" />
                </svg>
                Upgrade Plan
            </a>
        </div>
    </div>

    <!-- Blurred Content in Background -->
    <div class="filter blur-sm pointer-events-none opacity-40">
        {{ $slot }}
    </div>
</div>