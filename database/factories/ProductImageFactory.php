<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductImage>
 */
class ProductImageFactory extends Factory
{
    private array $imageFiles = [
        'siguldas-skati-house-1-1.jpg',
        'siguldas-skati-house-1-2.jpg',
        'siguldas-skati-house-2-1.jpg',
        'siguldas-skati-house-2-2.jpg',
        'siguldas-skati-house-3-1.jpg',
        'siguldas-skati-house-3-2.jpg',
        'siguldas-skati-house-4-1.jpg',
        'siguldas-skati-house-4-2.jpg',
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Select a random image
        $selectedImage = $this->faker->randomElement($this->imageFiles);

        // Copy the image to storage and get the path
        $imagePath = $this->copyImageToStorage($selectedImage);

        return [
            'product_id' => Product::factory(),
            'order' => 0,
            'filename' => $imagePath,
        ];
    }

    /**
     * Copy image from seeder assets to storage and return the new path
     */
    private function copyImageToStorage(string $filename): string
    {
        $sourcePath = public_path('images/gallery/'.$filename);
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        $randomFilename = $this->faker->uuid.'.'.$extension;
        $storagePath = 'product-images/gallery/'.$randomFilename;

        if (File::exists($sourcePath)) {
            Storage::disk('public')->put($storagePath, File::get($sourcePath));
        }

        return $storagePath;
    }

    /**
     * Create a product image for a specific product
     */
    public function forProduct(Product $product): static
    {
        return $this->state(function (array $attributes) use ($product) {
            return [
                'product_id' => $product->id,
            ];
        });
    }
}
