<?php

namespace Webkul\Shop\Http\Controllers;

use Illuminate\Support\Facades\Mail;
use Webkul\Category\Repositories\CategoryRepository;
use Webkul\Shop\Http\Requests\ContactRequest;
use Webkul\Shop\Http\Resources\CategoryTreeResource;
use Webkul\Shop\Mail\ContactUs;
use Webkul\Theme\Repositories\ThemeCustomizationRepository;

class HomeController extends Controller
{
    /**
     * Using const variable for status
     */
    const STATUS = 1;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(protected ThemeCustomizationRepository $themeCustomizationRepository, protected CategoryRepository $categoryRepository) {}

    /**
     * Loads the home page for the storefront.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        visitor()->visit();

        $customizations = $this->themeCustomizationRepository->orderBy('sort_order')->findWhere([
            'status'     => self::STATUS,
            'channel_id' => core()->getCurrentChannel()->id,
            'theme_code' => core()->getCurrentChannel()->theme,
        ]);

        $categories = $this->categoryRepository->getVisibleCategoryTree(core()->getCurrentChannel()->root_category_id);

        $categories = CategoryTreeResource::collection($categories);

        return view('shop::home.index', compact('customizations', 'categories'));
    }

    /**
     * Loads the home page for the storefront if something wrong.
     *
     * @return \Exception
     */
    public function notFound()
    {
        abort(404);
    }

    /**
     * Summary of contact.
     *
     * @return \Illuminate\View\View
     */
    public function contactUs()
    {
        return view('shop::home.contact-us');
    }

    /**
     * Summary of store.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendContactUsMail(ContactRequest $contactRequest)
    {
        try {
            Mail::queue(new ContactUs($contactRequest->only([
                'name',
                'email',
                'contact',
                'location',
                'message',
            ])));

            session()->flash('success', trans('shop::app.home.thanks-for-contact'));
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());

            report($e);
        }

        return back();
    }

    /**
     * Summary of Site Map.
     *
     * @return \Illuminate\View\View
     */
    public function siteMap()
    {
        $xmlPath = public_path('storage/sitemap.xml');

        if (!file_exists($xmlPath)) {
            abort(404, "Sitemap XML not found.");
        }

        $index = simplexml_load_file($xmlPath);

        $allUrls = [];

        // Loop each child sitemap file
        foreach ($index->sitemap as $sitemap) {
            $fileUrl = (string) $sitemap->loc;

            // Convert full URL → local path
            $localPath = public_path(parse_url($fileUrl, PHP_URL_PATH));

            if (file_exists($localPath)) {
                $childXml = simplexml_load_file($localPath);

                foreach ($childXml->url as $urlEntry) {
                    $allUrls[] = (string) $urlEntry->loc;
                }
            }
        }
        return view('shop::home.site-map', compact('allUrls'));
    }
}
