<?php

namespace Webkul\Brochure\Http\Controllers\Shop;

use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Webkul\Brochure\Repositories\BrochureRepository;
use Webkul\Shop\Http\Controllers\Controller;

class BrochureViewController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(protected BrochureRepository $brochureRepository) {}

    /**
     * Display the flipbook viewer for a specific brochure.
     */
    public function show(string $slug): View|RedirectResponse
    {
        $brochure = $this->brochureRepository->findActiveBySlug($slug);

        if (! $brochure) {
            session()->flash('error', trans('brochure::app.shop.not-found'));

            return redirect()->route('shop.brochure.index');
        }

        // Pass the initial page from query string (deep linking)
        $initialPage = max(1, (int) request()->query('page', 1));

        return view('brochure::shop.brochure.view', compact('brochure', 'initialPage'));
    }
}
