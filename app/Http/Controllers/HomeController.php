<?php

namespace App\Http\Controllers;

use App\Services\GalleryServices;
use App\Services\HeaderMediaService;
use App\Services\ProductServices;
use Illuminate\View\View;

class HomeController extends Controller
{
    protected ProductServices $productServices;

    protected GalleryServices $galleryService;

    protected HeaderMediaService $headerMediaService;

    public function __construct(
        ProductServices $productServices,
        GalleryServices $galleryService,
        HeaderMediaService $headerMediaService,
    ) {
        $this->productServices = $productServices;
        $this->galleryService = $galleryService;
        $this->headerMediaService = $headerMediaService;
    }

    /**
     * Handle the incoming request.
     */
    public function __invoke(): View
    {
        $products = $this->productServices->getAllActiveProducts();
        $galleries = $this->galleryService->getAllActiveGalleries();
        $headerMedia = $this->headerMediaService->getAllOrdered();

        return view('home', compact('products', 'galleries', 'headerMedia'));
    }
}
