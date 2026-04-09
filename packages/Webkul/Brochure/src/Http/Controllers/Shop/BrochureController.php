<?php

namespace Webkul\Brochure\Http\Controllers\Shop;

use Illuminate\View\View;
use Webkul\Brochure\Repositories\BrochureRepository;
use Webkul\Shop\Http\Controllers\Controller;

class BrochureController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(protected BrochureRepository $brochureRepository) {}

    /**
     * Display the listing page of all active brochures.
     */
    public function index(): View
    {
        $brochures = $this->brochureRepository->getActiveBrochures();

        return view('brochure::shop.brochure.index', compact('brochures'));
    }
}
