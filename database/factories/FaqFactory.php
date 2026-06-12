<?php

namespace Database\Factories;

use App\Models\Faq;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Faq>
 */
class FaqFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'question' => [
                'lv' => rtrim($this->faker->sentence(), '.').'?',
                'en' => rtrim($this->faker->sentence(), '.').'?',
            ],
            'answer' => [
                'lv' => '<p>'.$this->faker->sentence().'</p>',
                'en' => '<p>'.$this->faker->sentence().'</p>',
            ],
            'is_active' => true,
            'order' => 0,
        ];
    }
}
