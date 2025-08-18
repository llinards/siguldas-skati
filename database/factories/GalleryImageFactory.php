<?php

namespace Database\Factories;

use App\Models\Gallery;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\File;

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
        $destinationDir = public_path('storage/gallery-images');

        // Get file extension
        $extension = pathinfo($filename, PATHINFO_EXTENSION);

        // Generate random filename
        $randomFilename = $this->faker->uuid.'.'.$extension;
        $destinationPath = $destinationDir.'/'.$randomFilename;

        // Create the destination directory if it doesn't exist
        if (! File::exists($destinationDir)) {
            File::makeDirectory($destinationDir, 0755, true);
        }

        // Copy the file if source exists
        if (File::exists($sourcePath)) {
            File::copy($sourcePath, $destinationPath);
        }

        // Return the path relative to storage
        return 'gallery-images/'.$randomFilename;
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
