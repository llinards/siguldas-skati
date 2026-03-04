<?php

namespace Database\Factories;

use App\Models\Gallery;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class GalleryImageFactory extends Factory
{
    private array $imageFiles = [
        '1.jpg',
        '2.jpg',
        '3.jpg',
        '4.jpg',
        '5.jpg',
        '6.jpg',
        '7.jpg',
        '8.jpg',
        '9.jpg',
        '10.jpg',
    ];

    public function definition(): array
    {
        // Select a random image
        $selectedImage = $this->faker->randomElement($this->imageFiles);

        // Copy the image to storage and get the path
        $imagePath = $this->copyImageToStorage($selectedImage);

        return [
            'gallery_id' => Gallery::factory(),
            'order' => 0,
            'filename' => $imagePath,
        ];
    }

    private function copyImageToStorage(string $filename): string
    {
        $sourcePath = public_path('images/assets/seeder-gallery-images/'.$filename);
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        $randomFilename = $this->faker->uuid.'.'.$extension;
        $storagePath = 'gallery-images/'.$randomFilename;

        if (File::exists($sourcePath)) {
            Storage::disk('public')->put($storagePath, File::get($sourcePath));
        }

        return $storagePath;
    }

    public function forGallery(Gallery $gallery): static
    {
        return $this->state(function (array $attributes) use ($gallery) {
            return [
                'gallery_id' => $gallery->id,
            ];
        });
    }
}
