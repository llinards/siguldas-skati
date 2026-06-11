<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class ActivityFactory extends Factory
{
    private array $activityImages = [
        'siguldas-skati-todo-1.jpg',
        'siguldas-skati-todo-2.jpg',
        'siguldas-skati-todo-3.jpg',
        'siguldas-skati-todo-4.jpg',
    ];

    public function definition(): array
    {
        return [
            'title' => [
                'lv' => $this->faker->words(2, true),
                'en' => $this->faker->words(2, true),
            ],
            'modal_heading' => [
                'lv' => $this->faker->sentence(),
                'en' => $this->faker->sentence(),
            ],
            'modal_content' => [
                'lv' => '<p>'.$this->faker->paragraph().'</p>',
                'en' => '<p>'.$this->faker->paragraph().'</p>',
            ],
            'image' => $this->copyImageToStorage($this->faker->randomElement($this->activityImages)),
            'is_active' => true,
            'order' => 0,
        ];
    }

    private function copyImageToStorage(string $filename): string
    {
        $sourcePath = public_path('images/'.$filename);
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        $randomFilename = $this->faker->uuid.'.'.$extension;
        $storagePath = 'activity-images/'.$randomFilename;

        if (File::exists($sourcePath)) {
            Storage::disk('public')->put($storagePath, File::get($sourcePath));
        }

        return $storagePath;
    }
}
