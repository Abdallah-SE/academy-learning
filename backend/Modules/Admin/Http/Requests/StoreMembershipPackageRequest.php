<?php

namespace Modules\Admin\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMembershipPackageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Add admin authorization logic here
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:membership_packages,name',
            'description' => 'required|string|max:1000',
            'price' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
            'features' => 'sometimes|array',
            'features.*' => 'string|max:255',
            'status' => 'sometimes|string|in:active,inactive',
            'is_popular' => 'sometimes|boolean'
        ];
    }

    /**
     * Get custom error messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The package name is required.',
            'name.max' => 'The package name may not be greater than 255 characters.',
            'name.unique' => 'A package with this name already exists.',
            'description.required' => 'The package description is required.',
            'description.max' => 'The description may not be greater than 1000 characters.',
            'price.required' => 'The package price is required.',
            'price.numeric' => 'The price must be a valid number.',
            'price.min' => 'The price must be at least 0.',
            'duration_days.required' => 'The duration in days is required.',
            'duration_days.integer' => 'The duration must be a whole number.',
            'duration_days.min' => 'The duration must be at least 1 day.',
            'features.array' => 'Features must be provided as a list.',
            'features.*.string' => 'Each feature must be a string.',
            'features.*.max' => 'Each feature may not be greater than 255 characters.',
            'status.in' => 'The selected status is invalid.',
            'is_popular.boolean' => 'The popular flag must be true or false.'
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'name' => 'package name',
            'description' => 'package description',
            'price' => 'package price',
            'duration_days' => 'duration in days',
            'features' => 'package features',
            'status' => 'package status',
            'is_popular' => 'popular flag'
        ];
    }
}

