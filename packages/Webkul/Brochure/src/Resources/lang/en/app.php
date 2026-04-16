<?php

return [

    /* ----------------------------------------------------------------
     * Admin — System Settings (legacy, kept for backward compat)
     * ---------------------------------------------------------------- */
    'admin' => [
        'system' => [
            'title'                    => 'Brochure',
            'title-info'               => 'Configure Brochure settings',
            'settings'                 => 'Settings',
            'settings-info'            => 'Brochure Settings',
            'brochure-credentials'     => 'Brochure Flipbook settings',
            'brochure-credentials-info'=> 'Provide your required details for brochure integration.',
        ],

        /* ---- Listing (DataGrid) ---- */
        'index' => [
            'title'      => 'Brochures',
            'create-btn' => 'Add Brochure',
        ],

        /* ---- DataGrid columns / actions ---- */
        'datagrid' => [
            'id'           => 'ID',
            'title'        => 'Title',
            'slug'         => 'Slug',
            'type'         => 'Type',
            'status'       => 'Status',
            'sort-order'   => 'Sort Order',
            'created-at'   => 'Created At',
            'active'       => 'Active',
            'inactive'     => 'Inactive',
            'edit'         => 'Edit',
            'delete'       => 'Delete',
            'mass-delete'  => 'Delete Selected',
        ],

        /* ---- Create form ---- */
        'create' => [
            'title'       => 'Add New Brochure',
            'general'     => 'Brochure Details',
            'seo'         => 'SEO',
            'settings'    => 'Settings',
            'cover-image' => 'Cover Image',
            'save-btn'    => 'Save Brochure',
            'cancel-btn'  => 'Cancel',
            'back-btn'    => 'Back',
        ],

        /* ---- Edit form ---- */
        'edit' => [
            'title'          => 'Edit Brochure',
            'general'        => 'Brochure Details',
            'seo'            => 'SEO',
            'settings'       => 'Settings',
            'cover-image'    => 'Cover Image',
            'save-btn'       => 'Update Brochure',
            'cancel-btn'     => 'Cancel',
            'back-btn'       => 'Back',
            'preview-btn'    => 'Preview',
            'current-pdf'    => 'Current PDF',
            'replace-pdf'    => 'Replace PDF (optional)',
            'replace-images' => 'Replace Page Images (optional)',
            'current-pages'  => 'Current Pages',
            'current-cover'  => 'Current cover image',
            'pages'          => 'pages',
        ],

        /* ---- Shared form field labels ---- */
        'fields' => [
            'title'                    => 'Title',
            'title-placeholder'        => 'e.g. Summer Catalog 2025',
            'slug'                     => 'URL Slug',
            'type'                     => 'Brochure Type',
            'type-pdf'                 => 'PDF (rendered at runtime)',
            'type-images'              => 'Pre-rendered Images (WebP)',
            'pdf-file'                 => 'PDF File',
            'pdf-hint'                 => 'Max 50 MB. PDF will be rendered page-by-page.',
            'pdf-replace-hint'         => 'Leave blank to keep the existing PDF.',
            'page-images'              => 'Page Images',
            'images-hint'              => 'Upload one image per page in order. Images are auto-converted to WebP (max 5 MB each).',
            'images-replace-hint'      => 'Leave blank to keep existing images. Uploading replaces ALL pages.',
            'cover-image'              => 'Cover Image',
            'cover-image-hint'         => 'Shown on the brochure listing page. JPG/PNG/WebP, max 2 MB. Optional.',
            'cover-image-replace'      => 'Replace Cover Image (optional)',
            'cover-image-replace-hint' => 'Leave blank to keep the existing cover image.',
            'status'                   => 'Status',
            'status-active'            => 'Active',
            'status-inactive'          => 'Inactive',
            'sort-order'               => 'Sort Order',
            'meta-title'               => 'Meta Title',
            'meta-description'         => 'Meta Description',
        ],

        /* ---- Validation messages ---- */
        'validation' => [
            'title-required'   => 'The brochure title is required.',
            'type-required'    => 'Please select a brochure type.',
            'type-invalid'     => 'Invalid brochure type selected.',
            'pdf-required'     => 'Please upload a PDF file.',
            'pdf-mimes'        => 'Only PDF files are accepted.',
            'pdf-max'          => 'PDF file may not be larger than 50 MB.',
            'image-invalid'    => 'Each page image must be a valid image file.',
            'image-max'        => 'Each image may not be larger than 5 MB.',
        ],

        /* ---- Flash messages ---- */
        'create-success'      => 'Brochure created successfully.',
        'update-success'      => 'Brochure updated successfully.',
        'delete-success'      => 'Brochure deleted successfully.',
        'mass-delete-success' => 'Selected brochures have been deleted.',
    ],

    /* ----------------------------------------------------------------
     * Shop (public-facing)
     * ---------------------------------------------------------------- */
    'shop' => [
        'not-found' => 'The requested brochure could not be found.',

        'index' => [
            'title'            => 'Our Brochures',
            'heading'          => 'Our Brochures',
            'subheading'       => 'Browse and flip through our product catalogs.',
            'meta-description' => 'Browse our digital product brochures and catalogs.',
            'no-brochures'     => 'No brochures are available at the moment.',
            'view-btn'         => 'View Brochure',
        ],
    ],
];
