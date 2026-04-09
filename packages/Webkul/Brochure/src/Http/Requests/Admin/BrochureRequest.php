<?php

namespace Webkul\Brochure\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Http\UploadedFile;

class BrochureRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules.
     */
    public function rules(): array
    {
        $isPdfType = $this->input('type') === 'pdf';

        $rules = [
            'title'            => 'required|string|max:255',
            'type'             => 'required|in:pdf,images',
            'status'           => 'required|in:0,1',
            'sort_order'       => 'nullable|integer|min:0',
            'meta_title'       => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:1000',
            'page_images'      => [
                Rule::excludeIf($isPdfType),
                'nullable',
                'array',
            ],
            'page_images.*'    => [
                Rule::excludeIf($isPdfType),
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,gif,webp',
                'max:5120',
            ],
        ];

        $rules['pdf_file'] = [
            Rule::excludeIf(! $isPdfType),
            Rule::requiredIf($isPdfType && $this->isMethod('POST')),
            'nullable',
            'file',
            'max:51200',
            function (string $attribute, mixed $value, \Closure $fail): void {
                if (! $value instanceof UploadedFile) {
                    return;
                }

                $allowedMimeTypes = [
                    'application/pdf',
                    'application/x-pdf',
                    'application/acrobat',
                    'applications/vnd.pdf',
                    'text/pdf',
                    'text/x-pdf',
                ];

                $extension = strtolower((string) $value->getClientOriginalExtension());
                $clientMime = strtolower((string) $value->getClientMimeType());
                $guessedMime = strtolower((string) $value->getMimeType());

                if (
                    $extension !== 'pdf'
                    && ! in_array($clientMime, $allowedMimeTypes, true)
                    && ! in_array($guessedMime, $allowedMimeTypes, true)
                ) {
                    $fail(trans('brochure::app.admin.validation.pdf-mimes'));
                }
            },
        ];

        return $rules;
    }

    /**
     * Custom error messages.
     */
    public function messages(): array
    {
        return [
            'title.required'        => trans('brochure::app.admin.validation.title-required'),
            'type.required'         => trans('brochure::app.admin.validation.type-required'),
            'type.in'               => trans('brochure::app.admin.validation.type-invalid'),
            'pdf_file.required'     => trans('brochure::app.admin.validation.pdf-required'),
            'pdf_file.max'          => trans('brochure::app.admin.validation.pdf-max'),
            'page_images.*.image'   => trans('brochure::app.admin.validation.image-invalid'),
            'page_images.*.max'     => trans('brochure::app.admin.validation.image-max'),
        ];
    }
}
