<?php

namespace Webkul\GoogleMerchant\Http\Controllers\Admin;

use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\GoogleMerchant\Services\MerchantService;

class GoogleMerchantController extends Controller
{
    /**
     * GoogleMerchantController constructor.
     */
    public function __construct(protected MerchantService $merchantService) {}

    /**
     * Display the Google Merchant dashboard page.
     */
    public function index(): View
    {
        return view('googlemerchant::admin.index');
    }

    /**
     * Sync all active Bagisto products to Google Merchant Center.
     *
     * GET /admin/google-merchant/sync
     */
    public function sync(): JsonResponse
    {
        $result = $this->merchantService->syncProducts();
        dd($result);

        return response()->json([
            'message' => 'Products synced successfully',
            'synced'  => $result['synced'],
            'errors'  => $result['errors'],
        ]);
    }
}
