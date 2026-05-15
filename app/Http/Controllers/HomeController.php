<?php

namespace App\Http\Controllers;

use App\Services\GalleryServices;
use App\Services\HeaderImageService;
use App\Services\ProductServices;
use Illuminate\View\View;

class HomeController extends Controller
{
    protected ProductServices $productServices;

    protected GalleryServices $galleryService;

    protected HeaderImageService $headerImageService;

    public function __construct(
        ProductServices $productServices,
        GalleryServices $galleryService,
        HeaderImageService $headerImageService,
    ) {
        $this->productServices = $productServices;
        $this->galleryService = $galleryService;
        $this->headerImageService = $headerImageService;
    }

    /**
     * Handle the incoming request.
     */
    public function __invoke(): View
    {
        $products = $this->productServices->getAllActiveProducts();
        $galleries = $this->galleryService->getAllActiveGalleries();
        $headerImages = $this->headerImageService->getAllOrdered();

        return view('home', compact('products', 'galleries', 'headerImages'));
    }
}
