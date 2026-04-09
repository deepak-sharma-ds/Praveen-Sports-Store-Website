<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Product 360 Viewer Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration options for the Product 360 Viewer module.
    | These settings control viewer behavior, storage, and extensibility options.
    |
    */

    /**
     * Viewer type - determines which viewer implementation to use
     * 
     * Supported values:
     * - 'image_sequence': Display 360 view using ordered image frames (default)
     * - '3d_model': Display 360 view using 3D model (future enhancement)
     * - 'hybrid': Display both image sequence and 3D model (future enhancement)
     */
    'viewer_type' => env('PRODUCT360_VIEWER_TYPE', 'image_sequence'),

    /**
     * Storage configuration
     */
    'storage' => [
        /**
         * Storage disk to use for 360 images
         */
        'disk' => env('PRODUCT360_STORAGE_DISK', 'public'),

        /**
         * Base directory path for storing 360 images
         */
        'path' => env('PRODUCT360_STORAGE_PATH', 'product-360-images'),
    ],

    /**
     * Upload validation rules
     */
    'upload' => [
        /**
         * Maximum file size in kilobytes (5MB default)
         */
        'max_file_size' => env('PRODUCT360_MAX_FILE_SIZE', 5120),

        /**
         * Allowed MIME types for upload
         */
        'allowed_mime_types' => [
            'image/jpeg',
            'image/png',
            'image/webp',
        ],

        /**
         * Allowed file extensions
         */
        'allowed_extensions' => [
            'jpg',
            'jpeg',
            'png',
            'webp',
        ],
    ],

    /**
     * Viewer behavior configuration
     */
    'viewer' => [
        /**
         * Minimum number of images required to display viewer
         */
        'min_images' => env('PRODUCT360_MIN_IMAGES', 2),

        /**
         * Default drag sensitivity (pixels per frame)
         */
        'sensitivity' => env('PRODUCT360_SENSITIVITY', 5),

        /**
         * Enable auto-rotate on viewer load
         */
        'auto_rotate' => env('PRODUCT360_AUTO_ROTATE', false),

        /**
         * Auto-rotate speed in milliseconds per frame
         */
        'auto_rotate_speed' => env('PRODUCT360_AUTO_ROTATE_SPEED', 100),

        /**
         * Image preload strategy
         * 
         * Supported values:
         * - 'progressive': Load images progressively with delays (recommended)
         * - 'all': Load all images immediately
         * - 'lazy': Load images only when needed
         */
        'preload_strategy' => env('PRODUCT360_PRELOAD_STRATEGY', 'progressive'),

        /**
         * Drag direction for rotation
         * 
         * Supported values:
         * - 'horizontal': Drag left/right to rotate (default)
         * - 'vertical': Drag up/down to rotate
         */
        'drag_direction' => env('PRODUCT360_DRAG_DIRECTION', 'horizontal'),

        /**
         * Enable loop rotation (wrap around from last to first image)
         */
        'loop' => env('PRODUCT360_LOOP', true),
    ],

    /**
     * Cache configuration
     */
    'cache' => [
        /**
         * Enable caching of image data
         */
        'enabled' => env('PRODUCT360_CACHE_ENABLED', true),

        /**
         * Cache TTL in seconds (60 minutes default)
         */
        'ttl' => env('PRODUCT360_CACHE_TTL', 3600),

        /**
         * Cache key prefix
         */
        'prefix' => 'product_360_images',
    ],

    /**
     * Performance optimization settings
     */
    'performance' => [
        /**
         * Convert uploaded images to WebP format for better compression
         */
        'convert_to_webp' => env('PRODUCT360_CONVERT_TO_WEBP', true),

        /**
         * WebP quality (1-100, 85 recommended for balance)
         */
        'webp_quality' => env('PRODUCT360_WEBP_QUALITY', 85),

        /**
         * Maximum page load time impact in milliseconds
         */
        'max_load_time_impact' => 100,
    ],

    /**
     * Extension points for future enhancements
     * 
     * This section documents extension points for adding new viewer types
     * and features without breaking existing functionality.
     * 
     * Future enhancements:
     * - 3D model support: Add 'model_path' field to database schema
     * - Hybrid views: Support both image sequence and 3D model
     * - AR/VR integration: Add viewer type for augmented reality
     * - Video 360: Support video-based 360 views
     */
    'extensions' => [
        /**
         * Custom viewer implementations
         * 
         * Register custom viewer types here:
         * 'custom_type' => CustomViewerClass::class
         */
        'viewers' => [],

        /**
         * Custom storage handlers
         * 
         * Register custom storage implementations here:
         * 'custom_storage' => CustomStorageClass::class
         */
        'storage_handlers' => [],
    ],
];
