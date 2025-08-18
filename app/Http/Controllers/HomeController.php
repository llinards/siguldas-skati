<?php

namespace App\Http\Controllers;

use App\Services\GalleryServices;
use App\Services\ProductServices;
use Illuminate\View\View;

class HomeController extends Controller
{
    protected ProductServices $productServices;

    protected GalleryServices $galleryService;

    public function __construct(ProductServices $productServices, GalleryServices $galleryService)
    {
        $this->productServices = $productServices;
        $this->galleryService = $galleryService;
    }

    /**
     * Handle the incoming request.
     */
    public function __invoke(): View
    {
        $products = $this->productServices->getAllActiveProducts();
        $galleries = $this->galleryService->getAllActiveGalleries();

        return view('home', compact('products', 'galleries'));
    }
}
