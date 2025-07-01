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

    public function getAllProducts(): Collection
    {
        return Product::all();
    }

    public function getProductById(int $id): ?Product
    {
        return Product::find($id);
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

        return $product->update(['is_active' => ! $product->is_active]);
    }
}
