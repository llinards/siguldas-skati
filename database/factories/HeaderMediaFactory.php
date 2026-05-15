<?php

namespace Database\Factories;

use App\Models\HeaderMedia;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<HeaderMedia>
 */
class HeaderMediaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'order' => 0,
            'type' => HeaderMedia::TYPE_IMAGE,
            'filename' => 'header-images/'.$this->faker->uuid.'.jpg',
        ];
    }

    public function image(): static
    {
        return $this->state(fn () => [
            'type' => HeaderMedia::TYPE_IMAGE,
            'filename' => 'header-images/'.$this->faker->uuid.'.jpg',
        ]);
    }

    public function video(): static
    {
        return $this->state(fn () => [
            'type' => HeaderMedia::TYPE_VIDEO,
            'filename' => 'header-videos/'.$this->faker->uuid.'.mp4',
        ]);
    }
}
