<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Assignment;

class SubmitAssignmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $assignment = $this->route('assignment');

        return $this->user()->can('submit', $assignment);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $assignment = $this->route('assignment');

        return [
            'submission_text' => 'required_if:type,essay,text|nullable|string',
            'files' => 'nullable|array|max:10',
            'files.*' => 'file|max:' . ($assignment->max_file_size_mb * 1024),
            'notes' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'submission_text.required_if' => 'Please provide your submission text.',
            'files.max' => 'You can upload a maximum of 10 files.',
            'files.*.file' => 'Each upload must be a valid file.',
            'files.*.max' => 'Each file must not exceed the maximum allowed size.',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $assignment = $this->route('assignment');

            // Check if deadline passed and late submission not allowed
            if ($assignment->due_date &&
                now()->gt($assignment->due_date) &&
                !$assignment->allow_late_submission) {
                $validator->errors()->add('submission', 'The deadline for this assignment has passed and late submissions are not allowed.');
            }

            // Check enrollment
            $enrollment = $this->user()->enrollments()
                ->where('course_id', $assignment->course_id)
                ->where('status', 'active')
                ->first();

            if (!$enrollment) {
                $validator->errors()->add('enrollment', 'You must be enrolled in this course to submit assignments.');
            }
        });
    }
}
