<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductPrice>
 */
class ProductPriceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'date' => $this->faker->unique()->dateTimeBetween('now', '+1 year')->format('Y-m-d'),
            'price' => $this->faker->numberBetween(10000, 30000),
        ];
    }
}
