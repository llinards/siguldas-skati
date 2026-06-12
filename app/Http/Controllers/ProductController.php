<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\SiteSetting;
use App\Services\ProductServices;
use App\Services\SiteSettingService;
use Illuminate\View\View;

class ProductController extends Controller
{
    protected ProductServices $productServices;

    protected SiteSettingService $siteSettingService;

    public function __construct(ProductServices $productServices, SiteSettingService $siteSettingService)
    {
        $this->productServices = $productServices;
        $this->siteSettingService = $siteSettingService;
    }

    public function index(): View
    {
        $productsTitle = $this->siteSettingService->get(SiteSetting::KEY_PRODUCTS_TITLE, __('Dizaina mājas, sauna un džakuzi'));
        $productsSubtitle = $this->siteSettingService->get(SiteSetting::KEY_PRODUCTS_SUBTITLE, __('Izsmalcināta atpūta starp pilsētu un dabu!'));
        $products = $this->productServices->getAllActiveProducts();

        return view('products', compact('products', 'productsTitle', 'productsSubtitle'));
    }

    public function show(Product $product): View
    {
        $products = $this->productServices->getAllOtherProducts($product);

        return view('product', compact('products', 'product'));
    }
}
