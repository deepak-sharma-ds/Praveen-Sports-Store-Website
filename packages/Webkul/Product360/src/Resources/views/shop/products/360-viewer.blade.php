{{--
    Product 360 Viewer - Frontend Blade Template
    
    This template renders the 360-degree product viewer on the product detail page.
    It conditionally displays the viewer only when at least 2 images exist.
    
    Expected variables:
    - $product: The product model (passed from ViewRenderEventManager params)
--}}

@php
    // Check if product is available
    if (!isset($product) || !$product) {
        return;
    }
    
    // Fetch 360 images for this product using the service
    $product360Service = app(\Webkul\Product360\Services\Product360Service::class);
    $product360Images = $product360Service->getImagesForViewer($product->id);
    
    // Get viewer configuration
    $product360Config = (new \Webkul\Product360\DataTransferObjects\Product360ViewerConfig())->toArray();
@endphp

@if(count($product360Images) >= 2)
    {{-- Include CSS directly --}}
    <link rel="stylesheet" href="{{ asset('vendor/product360/css/product360-viewer.css') }}">

    <div class="product-360-section">
        <h3 class="product-360-section-title">360° View</h3>
        
        <div id="product-360-viewer-container" 
             class="product-360-viewer-wrapper"
             data-images="{{ json_encode(array_values($product360Images)) }}"
             data-config="{{ json_encode($product360Config) }}">
            {{-- Viewer will be initialized here by JavaScript --}}
        </div>
    </div>

    {{-- Include scripts directly instead of using @push --}}
    <script src="{{ asset('vendor/product360/js/product360-viewer.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('product-360-viewer-container');
            
            if (!container) {
                console.warn('Product 360 viewer container not found');
                return;
            }
            
            try {
                // Parse image data from data attribute
                const imagesData = container.dataset.images;
                const configData = container.dataset.config;
                
                if (!imagesData) {
                    console.warn('No image data found for 360 viewer');
                    return;
                }
                
                const images = JSON.parse(imagesData);
                const config = configData ? JSON.parse(configData) : {};
                
                // Validate minimum image count
                if (!Array.isArray(images) || images.length < 2) {
                    console.warn('Insufficient images for 360 viewer (minimum 2 required)');
                    container.innerHTML = '<p class="product-360-error">360° view not available</p>';
                    return;
                }
                
                // Initialize the Product360Viewer
                if (typeof Product360Viewer !== 'undefined') {
                    new Product360Viewer(container, images, config);
                } else {
                    console.error('Product360Viewer class not found');
                }
                
            } catch (error) {
                console.error('Failed to initialize Product 360 viewer:', error);
                container.innerHTML = '<p class="product-360-error">Failed to load 360° view</p>';
            }
        });
    </script>
@endif
