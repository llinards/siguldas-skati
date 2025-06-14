<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\ProductServices;
use Illuminate\View\View;

class ProductController extends Controller
{
    protected ProductServices $productServices;

    public function __construct(ProductServices $productServices)
    {
        $this->productServices = $productServices;
    }

    public function index()
    {
        $products = Product::all();
        return view('products', compact('products'));
    }

    public function show(Product $product): View
    {
        $products = $this->productServices->getAllOtherProducts($product);

        return view('product', compact('products'));
    }
}
