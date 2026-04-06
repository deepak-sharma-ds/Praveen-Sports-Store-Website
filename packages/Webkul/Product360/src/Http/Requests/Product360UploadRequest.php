<?php

namespace Webkul\Product360\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Product360UploadRequest extends FormRequest
{
    /**
     * Determine if the request is authorized.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'images'   => ['required', 'array', 'min:1'],
            'images.*' => ['required', 'file', 'mimes:jpeg,jpg,png,webp', 'max:5120'],
        ];
    }

    /**
     * Get custom error messages for validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'images.required'  => 'Please select at least one image',
            'images.*.mimes'   => 'Only JPEG, PNG, and WebP images are allowed',
            'images.*.max'     => 'Each image must not exceed 5MB',
        ];
    }
}
