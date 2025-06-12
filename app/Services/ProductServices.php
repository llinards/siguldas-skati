<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Collection;

class ProductServices
{
    public function getAllActiveProducts(): Collection
    {
        return Product::all()->where('is_active', 1);
    }

    public function getAllOtherProducts(Product $product): Collection
    {
        return Product::where('is_active', 1)
                      ->where('id', '!=', $product->id)
                      ->get();
    }

}
