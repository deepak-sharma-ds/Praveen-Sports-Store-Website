<?php

return [
    'admin' => [
        'catalog' => [
            'products' => [
                '360-view' => [
                    'title'       => '360° View Images',
                    'description' => 'Upload images in sequence for 360° rotation. Minimum 2 images required. Drag to reorder.',
                    
                    'upload' => [
                        'button'       => 'Add 360° Images',
                        'hint'         => 'JPEG, PNG, WebP (max 5MB)',
                        'drag-hint'    => 'Drag to rotate',
                        'delete-title' => 'Delete image',
                    ],
                    
                    'messages' => [
                        'no-images'           => 'No 360° images uploaded yet. Upload at least 2 images to enable the 360° viewer on the product page.',
                        'need-more-images'    => 'Upload at least one more image to enable the 360° viewer (minimum 2 images required).',
                        'delete-confirmation' => 'Are you sure you want to delete this image?',
                    ],
                ],
            ],
        ],
    ],

    'shop' => [
        'products' => [
            '360-viewer' => [
                'title'         => '360° View',
                'not-available' => '360° view not available',
                'load-failed'   => 'Failed to load 360° view',
            ],
        ],
    ],

    'validation' => [
        'upload' => [
            'images-required'    => 'Please select at least one image',
            'invalid-file-type'  => 'Only JPEG, PNG, and WebP images are allowed',
            'file-too-large'     => 'Each image must not exceed 5MB',
            'invalid-file-name'  => 'Invalid file type: :filename. Only JPEG, PNG, and WebP are allowed.',
            'file-size-exceeded' => 'File too large: :filename. Maximum size is 5MB.',
        ],
        
        'reorder' => [
            'order-required'       => 'Order data is required',
            'order-must-be-array'  => 'Order data must be an array',
            'order-min'            => 'At least one image must be provided',
            'id-required'          => 'Image ID is required',
            'id-must-be-integer'   => 'Image ID must be an integer',
            'id-not-exists'        => 'Image does not exist',
            'position-required'    => 'Position is required',
            'position-must-be-int' => 'Position must be an integer',
            'position-min'         => 'Position must be at least 1',
        ],
    ],

    'response' => [
        'success' => [
            'upload'   => 'Images uploaded successfully',
            'uploaded' => 'Successfully uploaded :count image(s)',
            'reorder'  => 'Images reordered successfully',
            'delete'   => 'Image deleted successfully',
        ],
        
        'error' => [
            'upload-failed'           => 'Upload failed. Please try again.',
            'retrieve-failed'         => 'Failed to retrieve images: :message',
            'reorder-failed'          => 'Failed to reorder images. Please try again.',
            'delete-failed'           => 'Failed to delete image. Please try again.',
            'image-not-found'         => 'Image not found',
            'unexpected-error'        => 'An unexpected error occurred. Please try again.',
            'insufficient-images'     => 'Insufficient images for 360 viewer (minimum 2 required)',
            'no-image-data'           => 'No image data found for 360 viewer',
            'container-not-found'     => 'Product 360 viewer container not found',
            'initialization-failed'   => 'Failed to initialize Product 360 viewer',
        ],
    ],
];

