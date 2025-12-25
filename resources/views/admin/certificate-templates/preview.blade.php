@extends('layouts.admin')

@section('title', 'Preview Template')

@section('content')
    <div class="max-w-5xl mx-auto space-y-6">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold">Preview: {{ $certificateTemplate->name }}</h2>
                <p class="text-sm text-gray-600">Sample certificate with placeholder data</p>
            </div>
            <a href="{{ route('admin.certificate-templates.index') }}" class="text-gray-600 hover:text-gray-900">← Back</a>
        </div>

        <!-- Preview Card -->
        <div class="bg-white rounded-lg shadow-lg p-8 mx-auto"
            style="max-width: {{ $certificateTemplate->orientation == 'landscape' ? '842px' : '595px' }}">
            <div
                class="border-{{ $certificateTemplate->design['border']['style'] ?? 'double' }} border-{{ $certificateTemplate->design['border']['width'] ?? '10' }} border-{{ $certificateTemplate->design['border']['color'] ?? 'indigo-600' }} p-12">
                <!-- Header -->
                <div class="text-center mb-8">
                    <h1
                        class="text-4xl font-bold text-{{ $certificateTemplate->design['header']['title_color'] ?? 'indigo-900' }} uppercase tracking-wide">
                        {{ $certificateTemplate->design['header']['title'] ?? 'Certificate of Completion' }}
                    </h1>
                    <p class="mt-2 text-lg text-gray-600">
                        {{ $certificateTemplate->design['header']['subtitle'] ?? 'This certifies that' }}</p>
                </div>

                <!-- Body -->
                <div class="text-center my-12">
                    <div
                        class="text-3xl font-bold text-gray-900 border-b-2 border-{{ $certificateTemplate->design['header']['title_color'] ?? 'indigo-900' }} inline-block pb-2 mb-6">
                        {{ $sampleData['student_name'] }}
                    </div>

                    <p class="text-lg text-gray-700 mb-6">
                        {{ $certificateTemplate->design['body']['description_text'] ?? 'has successfully completed the course' }}
                    </p>

                    <div
                        class="text-2xl font-bold text-{{ $certificateTemplate->design['body']['course_name_color'] ?? 'indigo-700' }} mb-6">
                        {{ $sampleData['course_name'] }}
                    </div>

                    <p class="text-base text-gray-600">
                        on {{ $sampleData['completion_date'] }}
                    </p>
                </div>

                <!-- Footer -->
                <div class="mt-12 flex justify-between items-end">
                    <div class="text-sm text-gray-600">
                        <p class="font-mono">{{ $sampleData['certificate_number'] }}</p>
                        <p>{{ $sampleData['platform_name'] }}</p>
                    </div>

                    @if($certificateTemplate->design['footer']['signature_enabled'] ?? false)
                        <div class="text-center">
                            <div class="border-t-2 border-gray-900 w-48 mb-2"></div>
                            <p class="text-sm text-gray-700">{{ $sampleData['instructor_name'] }}</p>
                            <p class="text-xs text-gray-500">Course Instructor</p>
                        </div>
                    @endif

                    <div class="bg-gray-200 h-20 w-20 flex items-center justify-center text-xs text-gray-500">
                        QR Code
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex justify-center gap-3">
            <a href="{{ route('admin.certificate-templates.edit', $certificateTemplate) }}"
                class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                Edit Template
            </a>
            <a href="{{ route('admin.certificate-templates.index') }}"
                class="px-6 py-2 bg-gray-200 rounded hover:bg-gray-300">
                Back to Templates
            </a>
        </div>
    </div>
@endsection