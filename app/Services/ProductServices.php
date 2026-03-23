<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class ProductServices
{
    private FileStorageService $fileStorageService;

    public function __construct(FileStorageService $fileStorageService)
    {
        $this->fileStorageService = $fileStorageService;
    }

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

    public function deleteProduct(Product $product): bool
    {
        if ($product->cover) {
            $this->fileStorageService->deleteFile($product->cover);
        }

        foreach ($product->images as $image) {
            if ($image->filename) {
                $this->fileStorageService->deleteFile($image->filename);
            }
        }

        return $product->delete();
    }

    public function toggleProductStatus(int $productId): bool
    {
        $product = Product::find($productId);

        if (! $product) {
            return false;
        }

        return $product->update(['is_active' => ! $product->is_active]);
    }

    public function generateSlug(string $title): string
    {
        return Str::slug($title);
    }

    public function createProduct(array $productData): Product
    {
        return Product::create($productData);
    }

    public function updateProduct(Product $product, array $productData): bool
    {
        return $product->update($productData);
    }

    public function storeProductCover(UploadedFile $file): string
    {
        return $this->fileStorageService->storeFile(
            $file,
            FileStorageService::PRODUCT_IMAGE_PATH
        );
    }

    public function updateProductCover(Product $product, UploadedFile $file): string
    {
        if ($product->cover) {
            $this->fileStorageService->deleteFile($product->cover);
        }

        return $this->storeProductCover($file);
    }

    public function storeProductGalleryImage(int $productId, UploadedFile $file): ProductImage
    {
        $path = $this->fileStorageService->storeFile(
            $file,
            FileStorageService::PRODUCT_GALLERY_PATH
        );

        return ProductImage::create([
            'product_id' => $productId,
            'filename' => $path,
        ]);
    }

    public function deleteProductImage(int $imageId): bool
    {
        $image = ProductImage::findOrFail($imageId);
        $this->fileStorageService->deleteFile($image->filename);

        return $image->delete();
    }

    public function updateProductOrder(int $id, int $position): void
    {
        $product = Product::findOrFail($id);
        $products = Product::orderBy('order')->get()->reject(fn ($p) => $p->id === $product->id)->values();
        $products->splice($position, 0, [$product]);

        foreach ($products as $index => $p) {
            if ($p->order !== $index) {
                $p->update(['order' => $index]);
            }
        }
    }

    public function updateImageOrder(int $id, int $position): void
    {
        $image = ProductImage::findOrFail($id);
        $images = ProductImage::where('product_id', $image->product_id)->orderBy('order')->get()
            ->reject(fn ($i) => $i->id === $image->id)->values();
        $images->splice($position, 0, [$image]);

        foreach ($images as $index => $i) {
            if ($i->order !== $index) {
                $i->update(['order' => $index]);
            }
        }
    }

    public function syncProductFeatures(Product $product, array $featureIds): void
    {
        $product->features()->sync($featureIds);
    }

    public function syncProductRules(Product $product, array $ruleIds): void
    {
        $product->rules()->sync($ruleIds);
    }
}
