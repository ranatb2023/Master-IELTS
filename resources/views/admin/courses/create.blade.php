@extends('layouts.admin')

@section('title', 'Create Course')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Create New Course</h1>
                <p class="mt-2 text-gray-600">Fill in the details below to create a new course</p>
            </div>
            <a href="{{ route('admin.courses.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Courses
            </a>
        </div>
    </div>

    <form action="{{ route('admin.courses.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content (Left Column) -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Basic Information -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h2 class="text-xl font-semibold text-gray-900">Basic Information</h2>
                        <p class="mt-1 text-sm text-gray-600">Core details about your course</p>
                    </div>
                    <div class="p-6 space-y-6">
                        <!-- Title -->
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700">Course Title <span class="text-red-500">*</span></label>
                            <input type="text" name="title" id="title" value="{{ old('title') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('title') border-red-500 @enderror">
                            @error('title')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @else
                                <p class="mt-1 text-sm text-gray-500">Choose a clear, descriptive title for your course</p>
                            @enderror
                        </div>

                        <!-- Slug -->
                        <div>
                            <label for="slug" class="block text-sm font-medium text-gray-700">URL Slug</label>
                            <input type="text" name="slug" id="slug" value="{{ old('slug') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('slug') border-red-500 @enderror">
                            @error('slug')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @else
                                <p class="mt-1 text-sm text-gray-500">Leave blank to auto-generate from title (e.g., course-title-here)</p>
                            @enderror
                        </div>

                        <!-- Subtitle -->
                        <div>
                            <label for="subtitle" class="block text-sm font-medium text-gray-700">Subtitle</label>
                            <input type="text" name="subtitle" id="subtitle" value="{{ old('subtitle') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <p class="mt-1 text-sm text-gray-500">Optional tagline or subtitle</p>
                        </div>

                        <!-- Short Description -->
                        <div>
                            <label for="short_description" class="block text-sm font-medium text-gray-700">Short Description</label>
                            <textarea name="short_description" id="short_description" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('short_description') }}</textarea>
                            <p class="mt-1 text-sm text-gray-500">A brief summary (160 characters recommended for SEO)</p>
                        </div>

                        <!-- Full Description -->
                        <x-quill-editor
                            name="description"
                            :value="old('description')"
                            label="Full Description"
                            :required="false"
                            height="400px"
                            placeholder="Write detailed course description with formatting, images, and links..."
                        />
                    </div>
                </div>

                <!-- Learning Outcomes -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h2 class="text-xl font-semibold text-gray-900">What Students Will Learn</h2>
                        <p class="mt-1 text-sm text-gray-600">List the key learning outcomes</p>
                    </div>
                    <div class="p-6" x-data="{ outcomes: {{ json_encode(old('learning_outcomes', [''])) }} }">
                        <div class="space-y-3">
                            <template x-for="(outcome, index) in outcomes" :key="index">
                                <div class="flex items-center gap-2">
                                    <div class="flex-shrink-0 w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </div>
                                    <input type="text" :name="'learning_outcomes[]'" x-model="outcomes[index]" placeholder="e.g., Master IELTS speaking techniques" class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <button type="button" @click="outcomes.splice(index, 1)" x-show="outcomes.length > 1" class="flex-shrink-0 p-2 text-red-600 hover:bg-red-50 rounded-md transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                            </template>
                        </div>
                        <button type="button" @click="outcomes.push('')" class="mt-4 inline-flex items-center px-4 py-2 bg-indigo-50 border border-indigo-200 rounded-md font-semibold text-xs text-indigo-700 uppercase tracking-widest hover:bg-indigo-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Add Learning Outcome
                        </button>
                    </div>
                </div>

                <!-- Requirements -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h2 class="text-xl font-semibold text-gray-900">Course Requirements</h2>
                        <p class="mt-1 text-sm text-gray-600">Prerequisites and requirements for students</p>
                    </div>
                    <div class="p-6" x-data="{ requirements: {{ json_encode(old('requirements', [''])) }} }">
                        <div class="space-y-3">
                            <template x-for="(requirement, index) in requirements" :key="index">
                                <div class="flex items-center gap-2">
                                    <div class="flex-shrink-0 w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <input type="text" :name="'requirements[]'" x-model="requirements[index]" placeholder="e.g., Basic English proficiency required" class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <button type="button" @click="requirements.splice(index, 1)" x-show="requirements.length > 1" class="flex-shrink-0 p-2 text-red-600 hover:bg-red-50 rounded-md transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                            </template>
                        </div>
                        <button type="button" @click="requirements.push('')" class="mt-4 inline-flex items-center px-4 py-2 bg-gray-50 border border-gray-200 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Add Requirement
                        </button>
                    </div>
                </div>

                <!-- SEO Settings -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h2 class="text-xl font-semibold text-gray-900">SEO Settings</h2>
                        <p class="mt-1 text-sm text-gray-600">Optimize for search engines</p>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <label for="meta_title" class="block text-sm font-medium text-gray-700">Meta Title</label>
                            <input type="text" name="meta_title" id="meta_title" value="{{ old('meta_title') }}" maxlength="60" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <p class="mt-1 text-sm text-gray-500">Recommended: 50-60 characters for optimal SEO</p>
                        </div>
                        <div>
                            <label for="meta_description" class="block text-sm font-medium text-gray-700">Meta Description</label>
                            <textarea name="meta_description" id="meta_description" rows="2" maxlength="160" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('meta_description') }}</textarea>
                            <p class="mt-1 text-sm text-gray-500">Recommended: 150-160 characters</p>
                        </div>
                        <div>
                            <label for="meta_keywords" class="block text-sm font-medium text-gray-700">Meta Keywords</label>
                            <input type="text" name="meta_keywords" id="meta_keywords" value="{{ old('meta_keywords') }}" placeholder="ielts, english, speaking, writing" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <p class="mt-1 text-sm text-gray-500">Comma-separated keywords</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar (Right Column) -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Publish Settings -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h3 class="text-lg font-semibold text-gray-900">Publish Settings</h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <!-- Status -->
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                            <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="review" {{ old('status') == 'review' ? 'selected' : '' }}>In Review</option>
                                <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>Published</option>
                                <option value="archived" {{ old('status') == 'archived' ? 'selected' : '' }}>Archived</option>
                            </select>
                        </div>

                        <!-- Visibility -->
                        <div>
                            <label for="visibility" class="block text-sm font-medium text-gray-700">Visibility</label>
                            <select name="visibility" id="visibility" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="public" {{ old('visibility') == 'public' ? 'selected' : '' }}>Public</option>
                                <option value="private" {{ old('visibility') == 'private' ? 'selected' : '' }}>Private</option>
                                <option value="unlisted" {{ old('visibility') == 'unlisted' ? 'selected' : '' }}>Unlisted</option>
                            </select>
                        </div>

                        <!-- Checkboxes -->
                        <div class="space-y-3 pt-2 border-t border-gray-200">
                            <label class="flex items-center">
                                <input type="checkbox" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <span class="ml-2 text-sm text-gray-700">Featured Course</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="drip_content" value="1" {{ old('drip_content') ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <span class="ml-2 text-sm text-gray-700">Drip Content</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="certificate_enabled" value="1" {{ old('certificate_enabled', true) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <span class="ml-2 text-sm text-gray-700">Certificate Enabled</span>
                            </label>
                        </div>

                        <!-- Schedule Publication -->
                        <div x-data="{ showSchedule: false }" class="pt-2 border-t border-gray-200">
                            <label class="flex items-center mb-2">
                                <input type="checkbox" x-model="showSchedule" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <span class="ml-2 text-sm text-gray-700">Schedule Publication</span>
                            </label>
                            
                            <div x-show="showSchedule" x-cloak>
                                <label for="published_at" class="block text-sm font-medium text-gray-700">Publish Date & Time</label>
                                <input type="datetime-local" name="published_at" id="published_at" 
                                       value="{{ old('published_at') }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <p class="mt-1 text-xs text-gray-500">Leave blank to publish immediately when status is "Published"</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Course Details -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h3 class="text-lg font-semibold text-gray-900">Course Details</h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <!-- Instructor -->
                        <div>
                            <label for="instructor_id" class="block text-sm font-medium text-gray-700">Instructor <span class="text-red-500">*</span></label>
                            <select name="instructor_id" id="instructor_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('instructor_id') border-red-500 @enderror">
                                <option value="">Select Instructor</option>
                                @foreach($instructors as $instructor)
                                    <option value="{{ $instructor->id }}" {{ old('instructor_id') == $instructor->id ? 'selected' : '' }}>
                                        {{ $instructor->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('instructor_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Course Categories (Multiple) -->
                        <div>
                            <label for="course_categories" class="block text-sm font-medium text-gray-700">Course Categories</label>
                            <select name="course_categories[]" id="course_categories" multiple class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" size="4">
                                @foreach($courseCategories as $courseCategory)
                                    <option value="{{ $courseCategory->id }}" {{ in_array($courseCategory->id, old('course_categories', [])) ? 'selected' : '' }}>
                                        {{ $courseCategory->name }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-xs text-gray-500">Hold Ctrl/Cmd to select multiple</p>
                        </div>

                        <!-- Course Tags (Multiple) -->
                        <div>
                            <label for="course_tags" class="block text-sm font-medium text-gray-700">Course Tags</label>
                            <select name="course_tags[]" id="course_tags" multiple class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" size="4">
                                @foreach($courseTags as $courseTag)
                                    <option value="{{ $courseTag->id }}" {{ in_array($courseTag->id, old('course_tags', [])) ? 'selected' : '' }}>
                                        {{ $courseTag->name }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-xs text-gray-500">Hold Ctrl/Cmd to select multiple</p>
                        </div>

                        <!-- Level -->
                        <div>
                            <label for="level" class="block text-sm font-medium text-gray-700">Level</label>
                            <select name="level" id="level" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Select Level</option>
                                <option value="beginner" {{ old('level') == 'beginner' ? 'selected' : '' }}>Beginner</option>
                                <option value="intermediate" {{ old('level') == 'intermediate' ? 'selected' : '' }}>Intermediate</option>
                                <option value="advanced" {{ old('level') == 'advanced' ? 'selected' : '' }}>Advanced</option>
                                <option value="all_levels" {{ old('level') == 'all_levels' ? 'selected' : '' }}>All Levels</option>
                            </select>
                        </div>

                        <!-- Language -->
                        <div>
                            <label for="language" class="block text-sm font-medium text-gray-700">Language</label>
                            <input type="text" name="language" id="language" value="{{ old('language', 'English') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <!-- Duration -->
                        <div>
                            <label for="duration_hours" class="block text-sm font-medium text-gray-700">Duration (Hours)</label>
                            <input type="number" step="0.5" name="duration_hours" id="duration_hours" value="{{ old('duration_hours') }}" placeholder="e.g., 40.5" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>
                </div>

                <!-- Pricing -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h3 class="text-lg font-semibold text-gray-900">Pricing</h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <!-- Is Free -->
                        <div>
                            <label class="flex items-center">
                                <input type="checkbox" name="is_free" value="1" {{ old('is_free') ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" x-data x-on:change="$refs.priceFields.style.display = $event.target.checked ? 'none' : 'block'">
                                <span class="ml-2 text-sm font-medium text-gray-700">This is a free course</span>
                            </label>
                        </div>

                        <div x-ref="priceFields" style="display: {{ old('is_free') ? 'none' : 'block' }}">
                            <!-- Currency -->
                            <div>
                                <label for="currency" class="block text-sm font-medium text-gray-700">Currency</label>
                                <select name="currency" id="currency" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="USD" {{ old('currency', 'USD') == 'USD' ? 'selected' : '' }}>USD ($)</option>
                                    <option value="EUR" {{ old('currency') == 'EUR' ? 'selected' : '' }}>EUR (€)</option>
                                    <option value="GBP" {{ old('currency') == 'GBP' ? 'selected' : '' }}>GBP (£)</option>
                                    <option value="INR" {{ old('currency') == 'INR' ? 'selected' : '' }}>INR (₹)</option>
                                </select>
                            </div>

                            <!-- Price -->
                            <div>
                                <label for="price" class="block text-sm font-medium text-gray-700">Regular Price</label>
                                <input type="number" step="0.01" name="price" id="price" value="{{ old('price') }}" placeholder="99.00" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>

                            <!-- Sale Price -->
                            <div>
                                <label for="sale_price" class="block text-sm font-medium text-gray-700">Sale Price</label>
                                <input type="number" step="0.01" name="sale_price" id="sale_price" value="{{ old('sale_price') }}" placeholder="79.00" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <p class="mt-1 text-xs text-gray-500">Optional discounted price</p>
                            </div>
                        </div>

                        <!-- Enrollment Limit -->
                        <div>
                            <label for="enrollment_limit" class="block text-sm font-medium text-gray-700">Enrollment Limit</label>
                            <input type="number" name="enrollment_limit" id="enrollment_limit" value="{{ old('enrollment_limit') }}" placeholder="Leave blank for unlimited" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <!-- Single Purchase Price -->
                        <div>
                            <label for="single_purchase_price" class="block text-sm font-medium text-gray-700">Single Purchase Price</label>
                            <input type="number" step="0.01" name="single_purchase_price" id="single_purchase_price" value="{{ old('single_purchase_price') }}" placeholder="Defaults to regular/sale price" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <p class="mt-1 text-xs text-gray-500">Price when purchased individually</p>
                        </div>

                        <!-- Purchase Options -->
                        <div class="space-y-3 border-t pt-4">
                            <p class="text-sm font-medium text-gray-700">Purchase Options:</p>
                            
                            <label class="flex items-center">
                                <input type="checkbox" name="allow_single_purchase" value="1" {{ old('allow_single_purchase', true) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <span class="ml-2 text-sm text-gray-700">Allow Individual Purchase</span>
                            </label>
                            <p class="ml-6 text-xs text-gray-500">Students can buy this course separately</p>

                            <label class="flex items-center">
                                <input type="checkbox" name="package_only" value="1" {{ old('package_only') ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <span class="ml-2 text-sm text-gray-700">Package Only</span>
                            </label>
                            <p class="ml-6 text-xs text-gray-500">Course can ONLY be accessed through a package</p>
                        </div>

                        <!-- Auto-Enrollment Settings -->
                        <div class="space-y-3 border-t pt-4">
                            <p class="text-sm font-medium text-gray-700">Auto-Enrollment:</p>
                            
                            <label class="flex items-start">
                                <input type="checkbox" name="auto_enroll_enabled" value="1" 
                                       {{ old('auto_enroll_enabled') ? 'checked' : '' }} 
                                       class="mt-1 rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <div class="ml-2">
                                    <span class="text-sm text-gray-700 font-medium">Make Available to All Users</span>
                                    <p class="text-xs text-gray-500 mt-1">
                                        When enabled, all users (new and existing) will be automatically enrolled in this course for free.
                                    </p>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Media -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h3 class="text-lg font-semibold text-gray-900">Media</h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <!-- Thumbnail -->
                        <div>
                            <label for="thumbnail" class="block text-sm font-medium text-gray-700">Course Thumbnail</label>
                            <input type="file" name="thumbnail" id="thumbnail" accept="image/*" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                            <p class="mt-1 text-xs text-gray-500">Recommended: 1280x720px (16:9 ratio)</p>
                        </div>

                        <!-- Preview Video -->
                        <div>
                            <label for="preview_video" class="block text-sm font-medium text-gray-700">Preview Video URL</label>
                            <input type="url" name="preview_video" id="preview_video" value="{{ old('preview_video') }}" placeholder="https://youtube.com/..." class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <p class="mt-1 text-xs text-gray-500">YouTube or Vimeo URL</p>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6 space-y-3">
                        <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-3 bg-indigo-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Create Course
                        </button>
                        <a href="{{ route('admin.courses.index') }}" class="w-full inline-flex justify-center items-center px-4 py-3 bg-white border border-gray-300 rounded-md font-semibold text-sm text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Cancel
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
