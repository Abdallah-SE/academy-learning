<?php

namespace Modules\Admin\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class CreateAdminRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:admins,email',
            'username' => 'nullable|string|max:255|alpha_dash|unique:admins,username',
            'password' => 'required|string|min:6|max:255|confirmed',
            'password_confirmation' => 'required|string|min:6|max:255',
            'status' => 'sometimes|string|in:active,inactive,suspended',
            'two_factor_enabled' => 'sometimes|boolean',
            'roles' => 'sometimes|array',
            'roles.*' => 'string|exists:roles,name',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Name is required',
            'name.string' => 'Name must be a string',
            'name.max' => 'Name cannot exceed 255 characters',
            'email.required' => 'Email is required',
            'email.email' => 'Please enter a valid email address',
            'email.max' => 'Email cannot exceed 255 characters',
            'email.unique' => 'This email is already taken',
            'username.string' => 'Username must be a string',
            'username.max' => 'Username cannot exceed 255 characters',
            'username.alpha_dash' => 'Username can only contain letters, numbers, dashes, and underscores',
            'username.unique' => 'This username is already taken',
            'password.required' => 'Password is required',
            'password.min' => 'Password must be at least 6 characters',
            'password.max' => 'Password cannot exceed 255 characters',
            'password.confirmed' => 'Password confirmation does not match',
            'password_confirmation.required' => 'Password confirmation is required',
            'password_confirmation.min' => 'Password confirmation must be at least 6 characters',
            'password_confirmation.max' => 'Password confirmation cannot exceed 255 characters',
            'status.in' => 'Status must be active, inactive, or suspended',
            'two_factor_enabled.boolean' => 'Two factor enabled must be true or false',
            'roles.array' => 'Roles must be an array',
            'roles.*.string' => 'Each role must be a string',
            'roles.*.exists' => 'One or more selected roles do not exist',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'name' => 'Full Name',
            'email' => 'Email Address',
            'username' => 'Username',
            'password' => 'Password',
            'password_confirmation' => 'Password Confirmation',
            'status' => 'Status',
            'roles' => 'Roles',
        ];
    }
}
