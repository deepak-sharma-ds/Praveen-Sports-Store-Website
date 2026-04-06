<?php

namespace Webkul\Product360\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Product360ReorderRequest extends FormRequest
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
            'order'              => ['required', 'array', 'min:1'],
            'order.*.id'         => ['required', 'integer', 'exists:product_360_images,id'],
            'order.*.position'   => ['required', 'integer', 'min:1'],
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
            'order.required'          => 'Order data is required',
            'order.array'             => 'Order data must be an array',
            'order.min'               => 'At least one image must be provided',
            'order.*.id.required'     => 'Image ID is required',
            'order.*.id.integer'      => 'Image ID must be an integer',
            'order.*.id.exists'       => 'Image does not exist',
            'order.*.position.required' => 'Position is required',
            'order.*.position.integer'  => 'Position must be an integer',
            'order.*.position.min'      => 'Position must be at least 1',
        ];
    }
}
