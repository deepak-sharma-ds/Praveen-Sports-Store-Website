<?php

namespace Webkul\Product360\Http\ViewComposers;

use Illuminate\View\View;

/**
 * AdminProductEditComposer
 * 
 * View composer for admin product edit page.
 * Ensures product data is available for the 360 view tab.
 */
class AdminProductEditComposer
{
    /**
     * Bind data to the view.
     *
     * @param  \Illuminate\View\View  $view
     * @return void
     */
    public function compose(View $view): void
    {
        // The admin product edit page already has $product available
        // This composer ensures the data is accessible and can be extended
        // in the future if additional processing is needed
        
        $product = $view->getData()['product'] ?? null;
        
        if (! $product) {
            return;
        }
        
        // Product is already available in the view
        // The 360-view.blade.php template queries product360Images inline
        // No additional data processing needed at this time
    }
}
