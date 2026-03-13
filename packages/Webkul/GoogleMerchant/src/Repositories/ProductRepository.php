<?php

namespace Webkul\GoogleMerchant\Repositories;

use Webkul\Product\Models\ProductFlat;

class ProductRepository
{
    public function getMerchantProducts()
    {
        return ProductFlat::where('status', 1)
            ->where('visible_individually', 1)
            ->get();
    }
}
