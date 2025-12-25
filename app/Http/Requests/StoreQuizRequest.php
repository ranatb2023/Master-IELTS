<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreQuizRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->hasRole(['super_admin', 'tutor']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'course_id' => 'required|exists:courses,id',
            'topic_id' => 'nullable|exists:topics,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:practice,graded,final_exam,mock_test',
            'passing_score' => 'required|integer|min:0|max:100',
            'time_limit_minutes' => 'nullable|integer|min:1',
            'max_attempts' => 'nullable|integer|min:1',
            'show_correct_answers' => 'boolean',
            'randomize_questions' => 'boolean',
            'randomize_options' => 'boolean',
            'is_active' => 'boolean',
            'available_from' => 'nullable|date',
            'available_until' => 'nullable|date|after:available_from',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Please provide a quiz title.',
            'course_id.required' => 'Please select a course.',
            'type.required' => 'Please select a quiz type.',
            'passing_score.required' => 'Please set a passing score.',
            'passing_score.min' => 'Passing score must be at least 0.',
            'passing_score.max' => 'Passing score cannot exceed 100.',
            'available_until.after' => 'End date must be after start date.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Convert boolean checkboxes
        foreach (['show_correct_answers', 'randomize_questions', 'randomize_options', 'is_active'] as $field) {
            if ($this->has($field)) {
                $this->merge([
                    $field => filter_var($this->$field, FILTER_VALIDATE_BOOLEAN),
                ]);
            }
        }
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // If tutor, verify they own the course
            if ($this->user()->hasRole('tutor')) {
                $course = \App\Models\Course::find($this->course_id);
                if ($course && $course->instructor_id !== $this->user()->id) {
                    $validator->errors()->add('course_id', 'You can only create quizzes for your own courses.');
                }
            }
        });
    }
}
