<?php

namespace Webkul\GoogleMerchant\Http\Controllers\Shop;

use Illuminate\View\View;
use Webkul\Shop\Http\Controllers\Controller;

class GoogleMerchantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        return view('googlemerchant::shop.index');
    }
}
