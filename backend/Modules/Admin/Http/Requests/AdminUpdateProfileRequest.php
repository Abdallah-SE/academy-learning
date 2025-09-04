<?php

namespace Modules\Admin\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdminUpdateProfileRequest extends FormRequest
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
        $adminId = auth('admin')->id();
        
        return [
            'name' => 'sometimes|string|max:255',
            'email' => [
                'sometimes',
                'email',
                'max:255',
                Rule::unique('admins', 'email')->ignore($adminId),
            ],
            'username' => [
                'sometimes',
                'string',
                'max:255',
                'alpha_dash',
                Rule::unique('admins', 'username')->ignore($adminId),
            ],
            'password' => 'sometimes|string|min:6|max:255|confirmed',
            'password_confirmation' => 'required_with:password|string|min:6|max:255',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.string' => 'Name must be a string',
            'name.max' => 'Name cannot exceed 255 characters',
            'email.email' => 'Please enter a valid email address',
            'email.max' => 'Email cannot exceed 255 characters',
            'email.unique' => 'This email is already taken',
            'username.string' => 'Username must be a string',
            'username.max' => 'Username cannot exceed 255 characters',
            'username.alpha_dash' => 'Username can only contain letters, numbers, dashes, and underscores',
            'username.unique' => 'This username is already taken',
            'password.min' => 'Password must be at least 6 characters',
            'password.max' => 'Password cannot exceed 255 characters',
            'password.confirmed' => 'Password confirmation does not match',
            'password_confirmation.required_with' => 'Password confirmation is required when changing password',
        ];
    }
}
