@extends('layouts.admin')

@section('title', 'Certificate Templates')
@section('page-title', 'Certificate Templates')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Certificate Templates</h2>
                <p class="mt-1 text-sm text-gray-600">Manage certificate design templates</p>
            </div>
            <a href="{{ route('admin.certificate-templates.create') }}"
                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Create Template
            </a>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white p-6 rounded-lg shadow">
                <p class="text-sm font-medium text-gray-500">Total Templates</p>
                <p class="text-3xl font-bold text-gray-900">{{ $stats['total'] }}</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow">
                <p class="text-sm font-medium text-gray-500">Active</p>
                <p class="text-3xl font-bold text-green-600">{{ $stats['active'] }}</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow">
                <p class="text-sm font-medium text-gray-500">Default Template</p>
                <p class="text-lg font-bold text-indigo-600">{{ $stats['default'] }}</p>
            </div>
        </div>

        <!-- Templates Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($templates as $template)
                <div class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow">
                    <!-- Template Header -->
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-gray-900">{{ $template->name }}</h3>
                                <p class="mt-1 text-sm text-gray-500">{{ Str::limit($template->description, 60) }}</p>
                            </div>
                            @if ($template->is_default)
                                <span
                                    class="ml-2 px-2 py-1 text-xs font-semibold text-indigo-800 bg-indigo-100 rounded-full">Default</span>
                            @endif
                        </div>
                    </div>

                    <!-- Template Info -->
                    <div class="p-6 space-y-3">
                        <div class="flex items-center text-sm">
                            <svg class="h-4 w-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z">
                                </path>
                            </svg>
                            <span class="text-gray-700">{{ ucfirst($template->orientation) }}</span>
                            <span class="mx-2">•</span>
                            <span class="text-gray-700">{{ strtoupper($template->page_size) }}</span>
                        </div>

                        <div class="flex items-center text-sm">
                            <svg class="h-4 w-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z">
                                </path>
                            </svg>
                            <span class="text-gray-700">{{ $template->certificates_count ?? 0 }} certificates</span>
                        </div>

                        <div class="flex items-center">
                            @if ($template->is_active)
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                            @else
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Inactive</span>
                            @endif
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex flex-wrap gap-2">
                        <a href="{{ route('admin.certificate-templates.preview', $template) }}"
                            class="flex-1 text-center px-3 py-2 bg-purple-600 text-white text-sm rounded hover:bg-purple-700">
                            Preview
                        </a>
                        <a href="{{ route('admin.certificate-templates.show', $template) }}"
                            class="px-3 py-2 bg-indigo-600 text-white text-sm rounded hover:bg-indigo-700">
                            View
                        </a>
                        <a href="{{ route('admin.certificate-templates.edit', $template) }}"
                            class="px-3 py-2 bg-blue-600 text-white text-sm rounded hover:bg-blue-700">
                            Edit
                        </a>

                        @if (!$template->is_default)
                            <form method="POST" action="{{ route('admin.certificate-templates.setDefault', $template) }}">
                                @csrf
                                <button type="submit" class="px-3 py-2 bg-green-600 text-white text-sm rounded hover:bg-green-700">
                                    Set Default
                                </button>
                            </form>
                        @endif

                        <form method="POST" action="{{ route('admin.certificate-templates.duplicate', $template) }}">
                            @csrf
                            <button type="submit" class="px-3 py-2 bg-gray-600 text-white text-sm rounded hover:bg-gray-700">
                                Duplicate
                            </button>
                        </form>

                        <form method="POST" action="{{ route('admin.certificate-templates.destroy', $template) }}"
                            onsubmit="return confirm('Are you sure you want to delete this template?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-3 py-2 bg-red-600 text-white text-sm rounded hover:bg-red-700">
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="col-span-3 bg-white rounded-lg shadow p-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z">
                        </path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No templates</h3>
                    <p class="mt-1 text-sm text-gray-500">Get started by creating a new template.</p>
                    <div class="mt-6">
                        <a href="{{ route('admin.certificate-templates.create') }}"
                            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            New Template
                        </a>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if ($templates->hasPages())
            <div class="mt-6">
                {{ $templates->links() }}
            </div>
        @endif
    </div>
@endsection