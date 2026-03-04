<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

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
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        $randomFilename = $this->faker->uuid.'.'.$extension;
        $storagePath = 'feature-icons/'.$randomFilename;

        if (File::exists($sourcePath)) {
            Storage::disk('public')->put($storagePath, File::get($sourcePath));
        }

        return $storagePath;
    }
}
