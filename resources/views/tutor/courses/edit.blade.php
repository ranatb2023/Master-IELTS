@extends('layouts.tutor')

@section('title', 'Edit Course')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Edit Course</h1>
                <p class="mt-2 text-gray-600">Update course information</p>
            </div>
            <a href="{{ route('tutor.courses.show', $course) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Course
            </a>
        </div>
    </div>

    <form action="{{ route('tutor.courses.update', $course) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

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
                            <input type="text" name="title" id="title" value="{{ old('title', $course->title) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('title') border-red-500 @enderror">
                            @error('title')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @else
                                <p class="mt-1 text-sm text-gray-500">Choose a clear, descriptive title for your course</p>
                            @enderror
                        </div>

                        <!-- Slug -->
                        <div>
                            <label for="slug" class="block text-sm font-medium text-gray-700">URL Slug</label>
                            <input type="text" name="slug" id="slug" value="{{ old('slug', $course->slug) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('slug') border-red-500 @enderror">
                            @error('slug')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @else
                                <p class="mt-1 text-sm text-gray-500">Leave blank to auto-generate from title (e.g., course-title-here)</p>
                            @enderror
                        </div>

                        <!-- Subtitle -->
                        <div>
                            <label for="subtitle" class="block text-sm font-medium text-gray-700">Subtitle</label>
                            <input type="text" name="subtitle" id="subtitle" value="{{ old('subtitle', $course->subtitle) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <p class="mt-1 text-sm text-gray-500">Optional tagline or subtitle</p>
                        </div>

                        <!-- Short Description -->
                        <div>
                            <label for="short_description" class="block text-sm font-medium text-gray-700">Short Description</label>
                            <textarea name="short_description" id="short_description" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('short_description', $course->short_description) }}</textarea>
                            <p class="mt-1 text-sm text-gray-500">A brief summary (160 characters recommended for SEO)</p>
                        </div>

                        <!-- Full Description -->
                        <x-quill-editor
                            name="description"
                            :value="old('description', $course->description)"
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
                    <div class="p-6" x-data="{ outcomes: {{ json_encode(old('learning_outcomes', is_array($course->learning_outcomes) ? $course->learning_outcomes : json_decode($course->learning_outcomes ?? '[]', true))) }} }">
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
                        <button type="button" @click="outcomes.push('')" class="mt-4 inline-flex items-center px-4 py-2 bg-indigo-50 text-indigo-700 rounded-md hover:bg-indigo-100 transition">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Add Learning Outcome
                        </button>
                    </div>
                </div>

                <!-- Requirements -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h2 class="text-xl font-semibold text-gray-900">Requirements</h2>
                        <p class="mt-1 text-sm text-gray-600">What students need before taking this course</p>
                    </div>
                    <div class="p-6" x-data="{ requirements: {{ json_encode(old('requirements', is_array($course->requirements) ? $course->requirements : json_decode($course->requirements ?? '[]', true))) }} }">
                        <div class="space-y-3">
                            <template x-for="(requirement, index) in requirements" :key="index">
                                <div class="flex items-center gap-2">
                                    <div class="flex-shrink-0 w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <input type="text" :name="'requirements[]'" x-model="requirements[index]" placeholder="e.g., Basic English proficiency" class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <button type="button" @click="requirements.splice(index, 1)" x-show="requirements.length > 1" class="flex-shrink-0 p-2 text-red-600 hover:bg-red-50 rounded-md transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                            </template>
                        </div>
                        <button type="button" @click="requirements.push('')" class="mt-4 inline-flex items-center px-4 py-2 bg-green-50 text-green-700 rounded-md hover:bg-green-100 transition">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
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
                            <input type="text" name="meta_title" id="meta_title" value="{{ old('meta_title', $course->meta_title) }}" maxlength="60" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <p class="mt-1 text-sm text-gray-500">60 characters max</p>
                        </div>

                        <div>
                            <label for="meta_description" class="block text-sm font-medium text-gray-700">Meta Description</label>
                            <textarea name="meta_description" id="meta_description" rows="3" maxlength="160" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('meta_description', $course->meta_description) }}</textarea>
                            <p class="mt-1 text-sm text-gray-500">160 characters max</p>
                        </div>

                        <div>
                            <label for="meta_keywords" class="block text-sm font-medium text-gray-700">Meta Keywords</label>
                            <input type="text" name="meta_keywords" id="meta_keywords" value="{{ old('meta_keywords', $course->meta_keywords) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <p class="mt-1 text-sm text-gray-500">Separate keywords with commas</p>
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
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                            <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="draft" {{ old('status', $course->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="review" {{ old('status', $course->status) == 'review' ? 'selected' : '' }}>In Review</option>
                                <option value="published" {{ old('status', $course->status) == 'published' ? 'selected' : '' }}>Published</option>
                                <option value="archived" {{ old('status', $course->status) == 'archived' ? 'selected' : '' }}>Archived</option>
                            </select>
                        </div>

                        <div>
                            <label for="visibility" class="block text-sm font-medium text-gray-700">Visibility</label>
                            <select name="visibility" id="visibility" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="public" {{ old('visibility', $course->visibility) == 'public' ? 'selected' : '' }}>Public</option>
                                <option value="private" {{ old('visibility', $course->visibility) == 'private' ? 'selected' : '' }}>Private</option>
                                <option value="unlisted" {{ old('visibility', $course->visibility) == 'unlisted' ? 'selected' : '' }}>Unlisted</option>
                            </select>
                        </div>

                        <div class="space-y-3 pt-3 border-t border-gray-200">
                            <label class="flex items-center">
                                <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $course->is_featured) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <span class="ml-2 text-sm text-gray-700">Featured Course</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="drip_content" value="1" {{ old('drip_content', $course->drip_content) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <span class="ml-2 text-sm text-gray-700">Drip Content</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="certificate_enabled" value="1" {{ old('certificate_enabled', $course->certificate_enabled) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <span class="ml-2 text-sm text-gray-700">Certificate Enabled</span>
                            </label>
                        </div>

                        <!-- Schedule Publication -->
                        <div x-data="{ showSchedule: {{ $course->published_at ? 'true' : 'false' }} }" class="pt-2 border-t border-gray-200">
                            <label class="flex items-center mb-2">
                                <input type="checkbox" x-model="showSchedule" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <span class="ml-2 text-sm text-gray-700">Schedule/Update Publication Date</span>
                            </label>
                            
                            <div x-show="showSchedule" x-cloak>
                                <label for="published_at" class="block text-sm font-medium text-gray-700">Publish Date & Time</label>
                                <input type="datetime-local" name="published_at" id="published_at" 
                                       value="{{ old('published_at', $course->published_at ? $course->published_at->format('Y-m-d\TH:i') : '') }}"
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
                        <!-- Course Categories (Multiple) -->
                        <div>
                            <label for="course_categories" class="block text-sm font-medium text-gray-700">Course Categories</label>
                            <select name="course_categories[]" id="course_categories" multiple class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" size="4">
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ in_array($category->id, old('course_categories', $course->courseCategories->pluck('id')->toArray())) ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-xs text-gray-500">Hold Ctrl/Cmd to select multiple</p>
                        </div>

                        <!-- Course Tags (Multiple) -->
                        <div>
                            <label for="course_tags" class="block text-sm font-medium text-gray-700">Course Tags</label>
                            <select name="course_tags[]" id="course_tags" multiple class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" size="4">
                                @foreach($tags as $tag)
                                    <option value="{{ $tag->id }}" {{ in_array($tag->id, old('course_tags', $course->courseTags->pluck('id')->toArray())) ? 'selected' : '' }}>
                                        {{ $tag->name }}
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
                                <option value="beginner" {{ old('level', $course->level) == 'beginner' ? 'selected' : '' }}>Beginner</option>
                                <option value="intermediate" {{ old('level', $course->level) == 'intermediate' ? 'selected' : '' }}>Intermediate</option>
                                <option value="advanced" {{ old('level', $course->level) == 'advanced' ? 'selected' : '' }}>Advanced</option>
                                <option value="all_levels" {{ old('level', $course->level) == 'all_levels' ? 'selected' : '' }}>All Levels</option>
                            </select>
                        </div>

                        <!-- Language -->
                        <div>
                            <label for="language" class="block text-sm font-medium text-gray-700">Language</label>
                            <input type="text" name="language" id="language" value="{{ old('language', $course->language) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <!-- Duration -->
                        <div>
                            <label for="duration_hours" class="block text-sm font-medium text-gray-700">Duration (Hours)</label>
                            <input type="number" step="0.5" name="duration_hours" id="duration_hours" value="{{ old('duration_hours', $course->duration_hours) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <!-- Enrollment Limit -->
                        <div>
                            <label for="enrollment_limit" class="block text-sm font-medium text-gray-700">Max Students</label>
                            <input type="number" name="enrollment_limit" id="enrollment_limit" value="{{ old('enrollment_limit', $course->enrollment_limit) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <p class="mt-1 text-xs text-gray-500">Leave blank for unlimited</p>
                        </div>
                    </div>
                </div>

                <!-- Pricing -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h3 class="text-lg font-semibold text-gray-900">Pricing</h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <label class="flex items-center mb-4">
                                <input type="checkbox" name="is_free" value="1" {{ old('is_free', $course->is_free) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <span class="ml-2 text-sm font-medium text-gray-700">Free Course</span>
                            </label>
                        </div>

                        <div>
                            <label for="price" class="block text-sm font-medium text-gray-700">Price</label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">$</span>
                                </div>
                                <input type="number" step="0.01" name="price" id="price" value="{{ old('price', $course->price) }}" class="pl-7 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                        </div>

                        <div>
                            <label for="sale_price" class="block text-sm font-medium text-gray-700">Sale Price</label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">$</span>
                                </div>
                                <input type="number" step="0.01" name="sale_price" id="sale_price" value="{{ old('sale_price', $course->sale_price) }}" class="pl-7 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Optional discounted price</p>
                        </div>

                        <div>
                            <label for="currency" class="block text-sm font-medium text-gray-700">Currency</label>
                            <select name="currency" id="currency" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="USD" {{ old('currency', $course->currency) == 'USD' ? 'selected' : '' }}>USD ($)</option>
                                <option value="EUR" {{ old('currency', $course->currency) == 'EUR' ? 'selected' : '' }}>EUR (€)</option>
                                <option value="GBP" {{ old('currency', $course->currency) == 'GBP' ? 'selected' : '' }}>GBP (£)</option>
                                <option value="INR" {{ old('currency', $course->currency) == 'INR' ? 'selected' : '' }}>INR (₹)</option>
                            </select>
                        </div>

                        <!-- Single Purchase Price -->
                        <div>
                            <label for="single_purchase_price" class="block text-sm font-medium text-gray-700">Single Purchase Price</label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">$</span>
                                </div>
                                <input type="number" step="0.01" name="single_purchase_price" id="single_purchase_price" value="{{ old('single_purchase_price', $course->single_purchase_price) }}" class="pl-7 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Price when purchased individually (defaults to regular/sale price)</p>
                        </div>

                        <!-- Purchase Options -->
                        <div class="space-y-3 border-t pt-4">
                            <p class="text-sm font-medium text-gray-700">Purchase Options:</p>
                            
                            <label class="flex items-center">
                                <input type="checkbox" name="allow_single_purchase" value="1" {{ old('allow_single_purchase', $course->allow_single_purchase ?? true) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <span class="ml-2 text-sm text-gray-700">Allow Individual Purchase</span>
                            </label>
                            <p class="ml-6 text-xs text-gray-500">Students can buy this course separately</p>

                            <label class="flex items-center">
                                <input type="checkbox" name="package_only" value="1" {{ old('package_only', $course->package_only) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <span class="ml-2 text-sm text-gray-700">Package Only</span>
                            </label>
                            <p class="ml-6 text-xs text-gray-500">Course can ONLY be accessed through a package</p>
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
                            @if($course->thumbnail)
                            <div class="mt-2 mb-3">
                                <img src="{{ asset('storage/' . $course->thumbnail) }}" alt="Current thumbnail" class="w-full h-48 object-cover rounded-lg border-2 border-gray-200">
                                <p class="mt-1 text-xs text-gray-500">Current thumbnail</p>
                            </div>
                            @endif
                            <input type="file" name="thumbnail" id="thumbnail" accept="image/*" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                            <p class="mt-1 text-xs text-gray-500">{{ $course->thumbnail ? 'Upload new image to replace current' : 'Recommended: 1280x720px' }}</p>
                        </div>

                        <!-- Preview Video -->
                        <div>
                            <label for="preview_video" class="block text-sm font-medium text-gray-700">Preview Video URL</label>
                            <input type="url" name="preview_video" id="preview_video" value="{{ old('preview_video', $course->preview_video) }}" placeholder="https://youtube.com/..." class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <p class="mt-1 text-xs text-gray-500">YouTube or Vimeo URL</p>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg sticky top-6">
                    <div class="p-6 space-y-3">
                        <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-3 bg-indigo-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Update Course
                        </button>
                        <a href="{{ route('tutor.courses.show', $course) }}" class="w-full inline-flex justify-center items-center px-4 py-3 bg-white border border-gray-300 rounded-md font-semibold text-sm text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Cancel
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection