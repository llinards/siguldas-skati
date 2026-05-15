<?php

namespace Database\Factories;

use App\Models\HeaderImage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<HeaderImage>
 */
class HeaderImageFactory extends Factory
{
    public function definition(): array
    {
        return [
            'order' => 0,
            'filename' => 'header-images/'.$this->faker->uuid.'.jpg',
        ];
    }
}
