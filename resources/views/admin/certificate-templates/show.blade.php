@extends('layouts.admin')

@section('title', $certificateTemplate->name)

@section('content')
    <div class="max-w-6xl mx-auto space-y-6">
        <!-- Header -->
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold">{{ $certificateTemplate->name }}</h2>
                <p class="text-sm text-gray-600">{{ $certificateTemplate->description }}</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.certificate-templates.preview', $certificateTemplate) }}"
                    class="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700">
                    Preview
                </a>
                <a href="{{ route('admin.certificate-templates.edit', $certificateTemplate) }}"
                    class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    Edit
                </a>
                <form method="POST" action="{{ route('admin.certificate-templates.destroy', $certificateTemplate) }}"
                    onsubmit="return confirm('Are you sure you want to delete this template? This action cannot be undone.');"
                    class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                        Delete
                    </button>
                </form>
                <a href="{{ route('admin.certificate-templates.index') }}"
                    class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">
                    Back
                </a>
            </div>
        </div>

        <!-- Template Info -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-4">Template Details</h3>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Orientation</dt>
                        <dd class="text-sm text-gray-900">{{ ucfirst($certificateTemplate->orientation) }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Page Size</dt>
                        <dd class="text-sm text-gray-900">{{ strtoupper($certificateTemplate->page_size) }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd class="text-sm">
                            @if($certificateTemplate->is_active)
                                <span
                                    class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                            @else
                                <span
                                    class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Inactive</span>
                            @endif
                            @if($certificateTemplate->is_default)
                                <span
                                    class="px-2 py-1 text-xs font-semibold rounded-full bg-indigo-100 text-indigo-800">Default</span>
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Certificates Issued</dt>
                        <dd class="text-sm text-gray-900">{{ $certificateTemplate->certificates_count ?? 0 }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Created</dt>
                        <dd class="text-sm text-gray-900">{{ $certificateTemplate->created_at->format('M j, Y') }}</dd>
                    </div>
                </dl>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-4">Background Image</h3>
                @if($certificateTemplate->background_image)
                    <img src="{{ asset('storage/' . $certificateTemplate->background_image) }}" alt="Background"
                        class="w-full rounded border">
                @else
                    <p class="text-sm text-gray-500 italic">No background image</p>
                @endif
            </div>
        </div>

        <!-- Design JSON -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Design Configuration</h3>
            <pre
                class="bg-gray-50 p-4 rounded text-xs overflow-auto max-h-64">{{ json_encode($certificateTemplate->design, JSON_PRETTY_PRINT) }}</pre>
        </div>

        <!-- Fields JSON -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Fields Configuration</h3>
            <pre
                class="bg-gray-50 p-4 rounded text-xs overflow-auto max-h-64">{{ json_encode($certificateTemplate->fields, JSON_PRETTY_PRINT) }}</pre>
        </div>

        <!-- Recent Certificates -->
        @if($certificateTemplate->certificates && $certificateTemplate->certificates->count() > 0)
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b">
                    <h3 class="text-lg font-semibold">Recent Certificates ({{ $certificateTemplate->certificates->count() }})
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Course</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Issued</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($certificateTemplate->certificates as $certificate)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $certificate->user->name }}</td>
                                    <td class="px-6 py-4 text-sm">{{ Str::limit($certificate->course->title, 40) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $certificate->issue_date->format('M j, Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @if($certificate->is_revoked)
                                            <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">Revoked</span>
                                        @else
                                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Active</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
@endsection