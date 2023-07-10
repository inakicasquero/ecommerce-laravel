<?php

namespace Webkul\Shop\Http\Controllers;

use Webkul\Product\Repositories\SearchRepository;
use Webkul\Shop\Repositories\ThemeCustomizationRepository;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        protected SearchRepository $searchRepository,
        protected ThemeCustomizationRepository $themeCustomizationRepository,
    )
    {
        parent::__construct();
    }

    /**
     * Loads the home page for the storefront.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $customizations = $this->themeCustomizationRepository->all();

        return view($this->_config['view'], compact('customizations'));
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
     * Upload image for product search with machine learning.
     *
     * @return \Illuminate\Http\Response
     */
    public function upload()
    {
        return $this->searchRepository->uploadSearchImage(request()->all());
    }
}
