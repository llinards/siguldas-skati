<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;

class ProductServices
{
    public function getAllActiveProducts(): Collection
    {
        return Product::where('is_active', true)->get();
    }

    public function getAllOtherProducts(Product $excludeProduct): Collection
    {
        return Product::where('is_active', true)
                      ->where('id', '!=', $excludeProduct->id)
                      ->get();
    }

    public function getAllProducts(): Collection
    {
        return Product::all();
    }

    public function deleteProduct(int $productId): bool
    {
        $product = Product::find($productId);

        if ( ! $product) {
            return false;
        }

        return $product->delete();
    }

    public function toggleProductStatus(int $productId): bool
    {
        $product = Product::find($productId);

        if ( ! $product) {
            return false;
        }

        $product->is_active = ! $product->is_active;

        return $product->save();
    }
}
