<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePackageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization handled by middleware
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $packageId = $this->route('package')->id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('packages', 'slug')->ignore($packageId)],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'sale_price' => ['nullable', 'numeric', 'min:0', 'lt:price'],
            'duration_days' => ['required_if:is_lifetime,false', 'nullable', 'integer', 'min:1'],
            'is_lifetime' => ['boolean'],
            'is_featured' => ['boolean'],
            'is_subscription_package' => ['boolean'],
            'subscription_plan_ids' => ['nullable', 'array'],
            'subscription_plan_ids.*' => ['exists:subscription_plans,id'],
            'auto_enroll_courses' => ['boolean'],
            'has_quiz_feature' => ['boolean'],
            'has_tutor_support' => ['boolean'],
            'status' => ['required', 'in:draft,published,archived'],
            'display_feature_keys' => ['nullable', 'array'],
            'display_feature_keys.*' => ['nullable', 'string'],
            'functional_feature_keys' => ['nullable', 'array'],
            'functional_feature_keys.*' => ['nullable', 'string'],
            'course_ids' => ['nullable', 'array'],
            'course_ids.*' => ['exists:courses,id'],
        ];
    }

    /**
     * Get custom error messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Package name is required.',
            'name.max' => 'Package name must not exceed 255 characters.',
            'slug.unique' => 'This slug is already in use by another package. Please choose a different one.',
            'price.required' => 'Regular price is required.',
            'price.numeric' => 'Price must be a valid number.',
            'price.min' => 'Price must be at least 0.',
            'sale_price.numeric' => 'Sale price must be a valid number.',
            'sale_price.min' => 'Sale price must be at least 0.',
            'sale_price.lt' => 'Sale price must be less than the regular price.',
            'duration_days.required_if' => 'Duration is required when lifetime access is not selected.',
            'duration_days.integer' => 'Duration must be a whole number.',
            'duration_days.min' => 'Duration must be at least 1 day.',
            'status.required' => 'Package status is required.',
            'status.in' => 'Invalid package status selected.',
            'course_ids.*.exists' => 'One or more selected courses do not exist.',
        ];
    }

    /**
     * Get custom attribute names for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'package name',
            'price' => 'regular price',
            'sale_price' => 'sale price',
            'duration_days' => 'duration',
            'course_ids' => 'courses',
        ];
    }
}
