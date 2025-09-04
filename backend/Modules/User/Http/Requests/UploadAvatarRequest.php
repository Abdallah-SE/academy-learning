<?php

namespace Modules\User\Http\Requests;

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
     */
    public function rules(): array
    {
        return [
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048|dimensions:min_width=100,min_height=100,max_width=2000,max_height=2000'
        ];
    }

    /**
     * Get custom error messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'avatar.required' => 'Please select an image to upload.',
            'avatar.image' => 'The file must be an image.',
            'avatar.mimes' => 'The image must be a file of type: jpeg, png, jpg, gif, webp.',
            'avatar.max' => 'The image may not be greater than 2MB.',
            'avatar.dimensions' => 'The image dimensions must be between 100x100 and 2000x2000 pixels.'
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'avatar' => 'profile picture'
        ];
    }
}
