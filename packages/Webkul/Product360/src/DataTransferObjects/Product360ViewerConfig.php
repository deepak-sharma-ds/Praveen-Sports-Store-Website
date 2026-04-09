<?php

namespace Webkul\Product360\DataTransferObjects;

/**
 * Data Transfer Object for Product360Viewer Configuration
 * 
 * Transfers viewer configuration options to frontend
 * Reads values from config/product360.php configuration file
 */
class Product360ViewerConfig
{
    /**
     * Viewer type (for future extensibility to 3D models)
     * 
     * Supported values:
     * - 'image_sequence': Display 360 view using ordered image frames (default)
     * - '3d_model': Display 360 view using 3D model (future enhancement)
     * - 'hybrid': Display both image sequence and 3D model (future enhancement)
     *
     * @var string
     */
    public string $viewerType;

    /**
     * Pixels to drag per frame
     *
     * @var int
     */
    public int $sensitivity;

    /**
     * Auto-rotate on load
     *
     * @var bool
     */
    public bool $autoRotate;

    /**
     * Milliseconds per frame for auto-rotate
     *
     * @var int
     */
    public int $autoRotateSpeed;

    /**
     * Preload strategy: 'progressive' | 'all' | 'lazy'
     *
     * @var string
     */
    public string $preloadStrategy;

    /**
     * Drag direction: 'horizontal' | 'vertical'
     *
     * @var string
     */
    public string $dragDirection;

    /**
     * Loop rotation
     *
     * @var bool
     */
    public bool $loop;

    /**
     * Constructor - Initialize configuration from config file
     */
    public function __construct()
    {
        // Load configuration from config/product360.php
        $this->viewerType = config('product360.viewer_type', 'image_sequence');
        $this->sensitivity = config('product360.viewer.sensitivity', 5);
        $this->autoRotate = config('product360.viewer.auto_rotate', false);
        $this->autoRotateSpeed = config('product360.viewer.auto_rotate_speed', 100);
        $this->preloadStrategy = config('product360.viewer.preload_strategy', 'progressive');
        $this->dragDirection = config('product360.viewer.drag_direction', 'horizontal');
        $this->loop = config('product360.viewer.loop', true);
    }

    /**
     * Convert configuration to array
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'viewerType' => $this->viewerType,
            'sensitivity' => $this->sensitivity,
            'autoRotate' => $this->autoRotate,
            'autoRotateSpeed' => $this->autoRotateSpeed,
            'preloadStrategy' => $this->preloadStrategy,
            'dragDirection' => $this->dragDirection,
            'loop' => $this->loop,
        ];
    }
}
