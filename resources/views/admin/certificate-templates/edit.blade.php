@extends('layouts.admin')

@section('title', 'Edit Template')

@section('content')
    <div class="max-w-4xl mx-auto space-y-6">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-bold">Edit {{ $certificateTemplate->name }}</h2>
            <a href="{{ route('admin.certificate-templates.index') }}" class="text-gray-600 hover:text-gray-900">← Back</a>
        </div>

        <form method="POST" action="{{ route('admin.certificate-templates.update', $certificateTemplate) }}"
            enctype="multipart/form-data" class="bg-white rounded-lg shadow p-8">
            @csrf
            @method('PUT')
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium mb-2">Name *</label>
                    <input type="text" name="name" required class="w-full rounded-md border-gray-300"
                        value="{{ old('name', $certificateTemplate->name) }}">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">Description</label>
                    <textarea name="description" rows="3"
                        class="w-full rounded-md border-gray-300">{{ old('description', $certificateTemplate->description) }}</textarea>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-2">Orientation *</label>
                        <select name="orientation" required class="w-full rounded-md border-gray-300">
                            <option value="landscape" {{ $certificateTemplate->orientation == 'landscape' ? 'selected' : '' }}>Landscape</option>
                            <option value="portrait" {{ $certificateTemplate->orientation == 'portrait' ? 'selected' : '' }}>
                                Portrait</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2">Page Size *</label>
                        <select name="page_size" required class="w-full rounded-md border-gray-300">
                            <option value="a4" {{ $certificateTemplate->page_size == 'a4' ? 'selected' : '' }}>A4</option>
                            <option value="letter" {{ $certificateTemplate->page_size == 'letter' ? 'selected' : '' }}>Letter
                            </option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">Background Image</label>
                    @if($certificateTemplate->background_image)
                        <p class="text-sm text-gray-600 mb-2">Current: {{ basename($certificateTemplate->background_image) }}
                        </p>
                    @endif
                    <input type="file" name="background_image" accept="image/*" class="w-full">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">Design JSON</label>
                    <textarea name="design" rows="10"
                        class="w-full rounded-md border-gray-300 font-mono text-sm">{{ json_encode($certificateTemplate->design, JSON_PRETTY_PRINT) }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">Fields JSON</label>
                    <textarea name="fields" rows="10"
                        class="w-full rounded-md border-gray-300 font-mono text-sm">{{ json_encode($certificateTemplate->fields, JSON_PRETTY_PRINT) }}</textarea>
                </div>

                <div class="flex items-center gap-4">
                    <label class="flex items-center">
                        <input type="checkbox" name="is_active" value="1" {{ $certificateTemplate->is_active ? 'checked' : '' }} class="rounded border-gray-300">
                        <span class="ml-2 text-sm">Active</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="is_default" value="1" {{ $certificateTemplate->is_default ? 'checked' : '' }} class="rounded border-gray-300">
                        <span class="ml-2 text-sm">Set as Default</span>
                    </label>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t">
                    <a href="{{ route('admin.certificate-templates.index') }}"
                        class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">Cancel</a>
                    <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">Update
                        Template</button>
                </div>
            </div>
        </form>
    </div>
@endsection