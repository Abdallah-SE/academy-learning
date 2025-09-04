<?php

namespace Modules\Admin\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class UploadAvatarRequest extends FormRequest
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
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048|dimensions:min_width=100,min_height=100'
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'avatar.required' => 'Avatar file is required',
            'avatar.image' => 'The file must be an image',
            'avatar.mimes' => 'The avatar must be a file of type: jpeg, png, jpg, gif, webp',
            'avatar.max' => 'The avatar may not be greater than 2MB',
            'avatar.dimensions' => 'The avatar must be at least 100x100 pixels',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'avatar' => 'avatar image'
        ];
    }
}
