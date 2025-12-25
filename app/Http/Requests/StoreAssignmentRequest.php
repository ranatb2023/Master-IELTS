<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAssignmentRequest extends FormRequest
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
            'description' => 'required|string',
            'instructions' => 'nullable|string',
            'type' => 'required|in:essay,project,presentation,code,file_upload',
            'max_points' => 'required|integer|min:1',
            'passing_points' => 'required|integer|min:0|lte:max_points',
            'due_date' => 'nullable|date|after:now',
            'allow_late_submission' => 'boolean',
            'late_penalty_percentage' => 'nullable|required_if:allow_late_submission,true|integer|min:0|max:100',
            'max_file_size_mb' => 'nullable|integer|min:1|max:100',
            'allowed_file_types' => 'nullable|json',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Please provide an assignment title.',
            'course_id.required' => 'Please select a course.',
            'description.required' => 'Please provide assignment description.',
            'type.required' => 'Please select an assignment type.',
            'max_points.required' => 'Please set maximum points.',
            'passing_points.required' => 'Please set passing points.',
            'passing_points.lte' => 'Passing points cannot exceed maximum points.',
            'due_date.after' => 'Due date must be in the future.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Convert boolean checkboxes
        if ($this->has('allow_late_submission')) {
            $this->merge([
                'allow_late_submission' => filter_var($this->allow_late_submission, FILTER_VALIDATE_BOOLEAN),
            ]);
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
                    $validator->errors()->add('course_id', 'You can only create assignments for your own courses.');
                }
            }
        });
    }
}
