<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCourseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $course = $this->route('course');

        return $this->user()->can('update', $course);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $courseId = $this->route('course')->id;

        return [
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:courses,slug,' . $courseId,
            'instructor_id' => 'sometimes|exists:users,id',
            'category_id' => 'required|exists:categories,id',
            'level' => 'required|in:beginner,intermediate,advanced,all_levels',
            'description' => 'required|string',
            'short_description' => 'nullable|string|max:500',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0|lt:price',
            'is_free' => 'boolean',
            'status' => 'sometimes|in:draft,review,published,archived',
            'language' => 'nullable|string|max:50',
            'duration_hours' => 'nullable|integer|min:0',
            'max_students' => 'nullable|integer|min:0',
            'requirements' => 'nullable|json',
            'learning_outcomes' => 'nullable|json',
            'target_audience' => 'nullable|json',
            'thumbnail' => 'nullable|string|max:500',
            'preview_video_url' => 'nullable|url|max:500',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Please provide a course title.',
            'slug.unique' => 'This course slug is already taken.',
            'category_id.required' => 'Please select a category.',
            'level.required' => 'Please select a difficulty level.',
            'description.required' => 'Please provide a course description.',
            'price.required' => 'Please set a price for the course.',
            'sale_price.lt' => 'Sale price must be less than the regular price.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Convert is_free checkbox
        if ($this->has('is_free')) {
            $this->merge([
                'is_free' => filter_var($this->is_free, FILTER_VALIDATE_BOOLEAN),
            ]);
        }
    }
}
