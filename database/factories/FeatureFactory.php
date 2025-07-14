<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\File;

class FeatureFactory extends Factory
{
    private array $featureIcons = [
        'ac.svg',
        'bicycle.svg',
        'bone.svg',
        'camera.svg',
        'clock.svg',
        'door-enter.svg',
        'fire.svg',
        'fridge.svg',
        'microwave.svg',
        'washing-machine.svg',
        'wi-fi.svg',
    ];

    public function definition(): array
    {
        $title = [
            'lv' => $this->faker->word,
            'en' => $this->faker->word,
        ];
        $selectedIcon = $this->faker->randomElement($this->featureIcons);
        $iconPath = $this->copyImageToStorage($selectedIcon);

        return [
            'title' => $title,
            'icon_image' => $iconPath,
            'is_active' => true,
            'order' => 0,
        ];
    }

    private function copyImageToStorage(string $filename): string
    {
        $sourcePath = public_path('icons/'.$filename);
        $destinationDir = public_path('storage/product-icons');

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
        return 'product-icons/'.$randomFilename;
    }
}
