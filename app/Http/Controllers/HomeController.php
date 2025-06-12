<?php

namespace App\Http\Controllers;

use App\Services\ProductServices;
use Illuminate\View\View;

class HomeController extends Controller
{
  protected ProductServices $productServices;

  public function __construct(ProductServices $productServices)
  {
    $this->productServices = $productServices;
  }

  /**
   * Handle the incoming request.
   */
  public function __invoke(): View
  {
    $products = $this->productServices->getAllActiveProducts();

    return view('home', compact('products'));
  }
}
