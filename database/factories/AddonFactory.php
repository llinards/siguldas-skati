<?php

namespace Database\Factories;

use App\Enums\AddonPricingType;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Addon>
 */
class AddonFactory extends Factory
{
    public function definition(): array
    {
        $word = $this->faker->unique()->word();

        return [
            'product_id' => Product::factory(),
            'name' => ['lv' => ucfirst($word), 'en' => ucfirst($word)],
            'price' => $this->faker->numberBetween(1000, 8000),
            'pricing_type' => $this->faker->randomElement(AddonPricingType::cases()),
            'is_active' => true,
            'order' => 0,
        ];
    }
}
