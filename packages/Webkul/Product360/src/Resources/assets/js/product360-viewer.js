/**
 * Product360Viewer - Interactive 360-degree product viewer
 * 
 * This viewer currently supports image sequence-based 360° rotation.
 * The viewer is designed to be extensible for future enhancements.
 * 
 * Extension Points:
 * - 3D Model Support: The viewer can be extended to support 3D model rendering
 *   by checking the viewerType option and initializing a different renderer
 * - Hybrid Views: Support both image sequence and 3D model simultaneously
 * - AR/VR Integration: Add viewer types for augmented/virtual reality
 * 
 * To add a new viewer type:
 * 1. Check options.viewerType in the init() method
 * 2. Create a separate initialization method for the new type
 * 3. Implement the appropriate rendering and interaction logic
 * 
 * Example:
 * if (this.options.viewerType === '3d_model') {
 *     this.init3DModelViewer();
 * } else if (this.options.viewerType === 'image_sequence') {
 *     this.initImageSequenceViewer();
 * }
 * 
 * @class
 * @param {HTMLElement} container - Container element for viewer
 * @param {Array} images - Array of image objects with url and position
 * @param {Object} options - Configuration options including viewerType
 */
class Product360Viewer {
    constructor(container, images, options = {}) {
        this.container = container;
        this.images = this.sortImages(images);
        this.options = this.mergeOptions(options);
        
        this.currentIndex = 0;
        this.isDragging = false;
        this.startX = 0;
        this.startY = 0;
        this.currentX = 0;
        this.currentY = 0;
        
        this.imageElements = [];
        this.loadedImages = new Set();
        
        this.init();
    }
    
    /**
     * Default configuration options
     * 
     * Note: viewerType defaults to 'image_sequence' for current implementation
     * Future viewer types: '3d_model', 'hybrid', 'ar', 'vr'
     */
    static get defaults() {
        return {
            viewerType: 'image_sequence',
            sensitivity: 5,
            autoRotate: false,
            autoRotateSpeed: 100,
            preloadStrategy: 'progressive',
            dragDirection: 'horizontal',
            loop: true,
            minImages: 2
        };
    }
    
    /**
     * Initialize viewer
     * 
     * Extension Point: Check viewerType here to initialize different viewer implementations
     * Currently only supports 'image_sequence' type
     */
    init() {
        try {
            // Extension Point: Add viewer type detection here
            // if (this.options.viewerType === '3d_model') {
            //     return this.init3DModelViewer();
            // } else if (this.options.viewerType === 'hybrid') {
            //     return this.initHybridViewer();
            // }
            
            // Current implementation: image_sequence viewer
            if (this.images.length < this.options.minImages) {
                console.warn('Product360Viewer: Not enough images to initialize');
                this.showFallbackMessage('Not enough images for 360° view');
                return;
            }
            
            this.createViewerStructure();
            this.loadFirstImage();
            this.setupEventListeners();
            this.preloadImages();
            
            if (this.options.autoRotate) {
                this.startAutoRotate();
            }
        } catch (error) {
            console.error('Product360Viewer: Initialization failed', error);
            this.showFallbackMessage('Unable to load 360° viewer');
        }
    }
    
    /**
     * Create DOM structure for viewer
     */
    createViewerStructure() {
        try {
            this.container.classList.add('product-360-viewer');
            this.container.innerHTML = `
                <div class="product-360-canvas">
                    <img class="product-360-image" alt="360 view" />
                    <div class="product-360-loader">Loading...</div>
                </div>
                <div class="product-360-controls">
                    <span class="product-360-hint">Drag to rotate</span>
                </div>
            `;
            
            this.canvas = this.container.querySelector('.product-360-canvas');
            this.imageElement = this.container.querySelector('.product-360-image');
            this.loader = this.container.querySelector('.product-360-loader');
            
            if (!this.canvas || !this.imageElement || !this.loader) {
                throw new Error('Failed to create viewer DOM structure');
            }
        } catch (error) {
            console.error('Product360Viewer: Failed to create viewer structure', error);
            this.showFallbackMessage('Unable to initialize viewer');
            throw error;
        }
    }
    
    /**
     * Setup event listeners for interaction
     */
    setupEventListeners() {
        try {
            if (!this.canvas) {
                throw new Error('Canvas element not available');
            }
            
            // Mouse events
            this.canvas.addEventListener('mousedown', this.onDragStart.bind(this));
            document.addEventListener('mousemove', this.onDragMove.bind(this));
            document.addEventListener('mouseup', this.onDragEnd.bind(this));
            
            // Touch events
            this.canvas.addEventListener('touchstart', this.onDragStart.bind(this), { passive: false });
            document.addEventListener('touchmove', this.onDragMove.bind(this), { passive: false });
            document.addEventListener('touchend', this.onDragEnd.bind(this));
            
            // Prevent context menu on long press
            this.canvas.addEventListener('contextmenu', (e) => e.preventDefault());
            
            // Responsive resize
            window.addEventListener('resize', this.onResize.bind(this));
        } catch (error) {
            console.error('Product360Viewer: Failed to setup event listeners', error);
            throw error;
        }
    }
    
    /**
     * Handle drag start
     */
    onDragStart(event) {
        try {
            this.isDragging = true;
            if (this.canvas) {
                this.canvas.classList.add('dragging');
            }
            
            const point = this.getEventPoint(event);
            this.startX = point.x;
            this.startY = point.y;
            this.currentX = point.x;
            this.currentY = point.y;
            
            if (this.autoRotateInterval) {
                this.stopAutoRotate();
            }
            
            event.preventDefault();
        } catch (error) {
            console.error('Product360Viewer: Error in onDragStart', error);
        }
    }
    
    /**
     * Handle drag move
     */
    onDragMove(event) {
        try {
            if (!this.isDragging) return;
            
            const point = this.getEventPoint(event);
            this.currentX = point.x;
            this.currentY = point.y;
            
            const dragDistance = this.options.dragDirection === 'horizontal'
                ? this.currentX - this.startX
                : this.currentY - this.startY;
            
            const newIndex = this.calculateImageIndex(dragDistance);
            
            if (newIndex !== this.currentIndex) {
                this.updateImage(newIndex);
            }
            
            event.preventDefault();
        } catch (error) {
            console.error('Product360Viewer: Error in onDragMove', error);
        }
    }
    
    /**
     * Handle drag end
     */
    onDragEnd(event) {
        try {
            if (!this.isDragging) return;
            
            this.isDragging = false;
            if (this.canvas) {
                this.canvas.classList.remove('dragging');
            }
            
            // Reset start position for next drag
            this.startX = this.currentX;
            this.startY = this.currentY;
        } catch (error) {
            console.error('Product360Viewer: Error in onDragEnd', error);
        }
    }
    
    /**
     * Calculate which image to display based on drag distance
     */
    calculateImageIndex(dragDistance) {
        const frameCount = this.images.length;
        const dragPerFrame = this.options.sensitivity;
        
        const frameOffset = Math.floor(dragDistance / dragPerFrame);
        let newIndex = this.currentIndex - frameOffset;
        
        if (this.options.loop) {
            newIndex = ((newIndex % frameCount) + frameCount) % frameCount;
        } else {
            newIndex = Math.max(0, Math.min(frameCount - 1, newIndex));
        }
        
        return newIndex;
    }
    
    /**
     * Update displayed image
     */
    updateImage(index) {
        try {
            if (index < 0 || index >= this.images.length) return;
            
            this.currentIndex = index;
            const image = this.images[index];
            
            if (!image) {
                console.warn(`Product360Viewer: No image found at index ${index}`);
                return;
            }
            
            if (this.loadedImages.has(index)) {
                if (this.imageElement) {
                    this.imageElement.src = image.url;
                }
            } else {
                this.loadImage(index);
            }
        } catch (error) {
            console.error('Product360Viewer: Error in updateImage', error);
        }
    }
    
    /**
     * Load first image
     */
    loadFirstImage() {
        try {
            if (!this.loader || !this.images || this.images.length === 0) {
                throw new Error('Cannot load first image: missing loader or images');
            }
            
            this.loader.style.display = 'block';
            this.loadImage(0, () => {
                if (this.loader) {
                    this.loader.style.display = 'none';
                }
            }, (error) => {
                console.error('Product360Viewer: Failed to load first image', error);
                this.showFallbackMessage('Failed to load 360° images');
                if (this.loader) {
                    this.loader.style.display = 'none';
                }
            });
        } catch (error) {
            console.error('Product360Viewer: Error in loadFirstImage', error);
            this.showFallbackMessage('Failed to load 360° images');
        }
    }
    
    /**
     * Load specific image
     */
    loadImage(index, callback, errorCallback) {
        try {
            if (index < 0 || index >= this.images.length) {
                const error = new Error(`Invalid image index: ${index}`);
                console.error('Product360Viewer:', error.message);
                if (errorCallback) errorCallback(error);
                return;
            }
            
            const image = this.images[index];
            if (!image || !image.url) {
                const error = new Error(`Invalid image data at index ${index}`);
                console.error('Product360Viewer:', error.message);
                if (errorCallback) errorCallback(error);
                return;
            }
            
            const img = new Image();
            
            img.onload = () => {
                try {
                    this.loadedImages.add(index);
                    if (index === this.currentIndex && this.imageElement) {
                        this.imageElement.src = image.url;
                    }
                    if (callback) callback();
                } catch (error) {
                    console.error('Product360Viewer: Error in image onload handler', error);
                    if (errorCallback) errorCallback(error);
                }
            };
            
            img.onerror = (error) => {
                console.error(`Product360Viewer: Failed to load image at index ${index}: ${image.url}`, error);
                
                // If this is the first image, show fallback
                if (index === 0) {
                    this.showFallbackMessage('Failed to load 360° images');
                }
                
                if (errorCallback) errorCallback(error);
            };
            
            img.src = image.url;
        } catch (error) {
            console.error('Product360Viewer: Error in loadImage', error);
            if (errorCallback) errorCallback(error);
        }
    }
    
    /**
     * Preload images based on strategy
     */
    preloadImages() {
        if (this.options.preloadStrategy === 'all') {
            this.preloadAllImages();
        } else if (this.options.preloadStrategy === 'progressive') {
            this.preloadProgressively();
        }
        // 'lazy' strategy loads on demand (default behavior)
    }
    
    /**
     * Preload all images at once
     */
    preloadAllImages() {
        this.images.forEach((image, index) => {
            if (index !== 0) {
                this.loadImage(index);
            }
        });
    }
    
    /**
     * Preload images progressively
     */
    preloadProgressively() {
        let index = 1;
        const loadNext = () => {
            if (index < this.images.length) {
                this.loadImage(index, () => {
                    index++;
                    setTimeout(loadNext, 100);
                });
            }
        };
        setTimeout(loadNext, 500);
    }
    
    /**
     * Start auto-rotation
     */
    startAutoRotate() {
        this.autoRotateInterval = setInterval(() => {
            const nextIndex = (this.currentIndex + 1) % this.images.length;
            this.updateImage(nextIndex);
        }, this.options.autoRotateSpeed);
    }
    
    /**
     * Stop auto-rotation
     */
    stopAutoRotate() {
        if (this.autoRotateInterval) {
            clearInterval(this.autoRotateInterval);
            this.autoRotateInterval = null;
        }
    }
    
    /**
     * Handle window resize
     */
    onResize() {
        // Viewer adapts automatically via CSS
        // This method is for future enhancements
    }
    
    /**
     * Get event point (mouse or touch)
     */
    getEventPoint(event) {
        if (event.touches && event.touches.length > 0) {
            return {
                x: event.touches[0].clientX,
                y: event.touches[0].clientY
            };
        }
        return {
            x: event.clientX,
            y: event.clientY
        };
    }
    
    /**
     * Sort images by position
     */
    sortImages(images) {
        return [...images].sort((a, b) => a.position - b.position);
    }
    
    /**
     * Merge user options with defaults
     */
    mergeOptions(options) {
        return { ...Product360Viewer.defaults, ...options };
    }
    
    /**
     * Show fallback message when viewer fails
     */
    showFallbackMessage(message) {
        try {
            if (!this.container) return;
            
            this.container.innerHTML = `
                <div class="product-360-fallback">
                    <p class="product-360-fallback-message">${message}</p>
                </div>
            `;
            
            // Log for debugging but don't show alerts to users
            console.warn('Product360Viewer: Showing fallback -', message);
        } catch (error) {
            console.error('Product360Viewer: Failed to show fallback message', error);
        }
    }
    
    /**
     * Cleanup and destroy viewer
     */
    destroy() {
        try {
            this.stopAutoRotate();
            
            // Remove event listeners safely
            if (this.canvas) {
                this.canvas.removeEventListener('mousedown', this.onDragStart);
                this.canvas.removeEventListener('touchstart', this.onDragStart);
                this.canvas.removeEventListener('contextmenu', (e) => e.preventDefault());
            }
            
            document.removeEventListener('mousemove', this.onDragMove);
            document.removeEventListener('mouseup', this.onDragEnd);
            document.removeEventListener('touchmove', this.onDragMove);
            document.removeEventListener('touchend', this.onDragEnd);
            window.removeEventListener('resize', this.onResize);
            
            // Clear container
            if (this.container) {
                this.container.innerHTML = '';
            }
        } catch (error) {
            console.error('Product360Viewer: Error during cleanup', error);
        }
    }
}

// Export for module systems
if (typeof module !== 'undefined' && module.exports) {
    module.exports = Product360Viewer;
}

// Global export for direct script inclusion
if (typeof window !== 'undefined') {
    window.Product360Viewer = Product360Viewer;
}
