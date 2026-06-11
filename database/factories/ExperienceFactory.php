<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class ExperienceFactory extends Factory
{
    private array $experienceIcons = [
        'wave.svg',
        'check.svg',
        'happy_face.svg',
        'location.svg',
    ];

    public function definition(): array
    {
        $title = [
            'lv' => $this->faker->words(2, true),
            'en' => $this->faker->words(2, true),
        ];
        $description = [
            'lv' => '<p>'.$this->faker->sentence().'</p>',
            'en' => '<p>'.$this->faker->sentence().'</p>',
        ];
        $selectedIcon = $this->faker->randomElement($this->experienceIcons);
        $iconPath = $this->copyImageToStorage($selectedIcon);

        return [
            'title' => $title,
            'description' => $description,
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
        $storagePath = 'experience-icons/'.$randomFilename;

        if (File::exists($sourcePath)) {
            Storage::disk('public')->put($storagePath, File::get($sourcePath));
        }

        return $storagePath;
    }
}
