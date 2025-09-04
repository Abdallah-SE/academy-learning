<?php

namespace Modules\User\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // You can add authorization logic here
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $userId = $this->route('user') ?? $this->route('id');
        
        return [
            'name' => 'sometimes|string|max:255',
            'email' => [
                'sometimes',
                'email',
                Rule::unique('users', 'email')->ignore($userId)
            ],
            'password' => 'sometimes|string|min:8|confirmed',
            'password_confirmation' => 'required_with:password|string|min:8',
            'role' => 'sometimes|string|in:admin,moderator,user',
            'status' => 'sometimes|string|in:active,inactive,suspended'
        ];
    }

    /**
     * Get custom error messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.max' => 'The name may not be greater than 255 characters.',
            'email.email' => 'The email must be a valid email address.',
            'email.unique' => 'The email has already been taken.',
            'password.min' => 'The password must be at least 8 characters.',
            'password.confirmed' => 'The password confirmation does not match.',
            'password_confirmation.required_with' => 'The password confirmation field is required when password is present.',
            'role.in' => 'The selected role is invalid.',
            'status.in' => 'The selected status is invalid.'
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'name' => 'name',
            'email' => 'email address',
            'password' => 'password',
            'password_confirmation' => 'password confirmation',
            'role' => 'user role',
            'status' => 'user status'
        ];
    }
}

