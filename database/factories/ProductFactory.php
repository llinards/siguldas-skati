<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    private array $productTitles = [
        'lv' => 'Brīvdienu dizaina māja',
        'en' => 'Weekended house',
    ];

    private array $productSlugs = [
        'lv' => 'brivdienu-dizaina-maja',
        'en' => 'weekended-house',
    ];

    private array $productCovers = [
        'images/assets/seeder-house-images/siguldas-skati-product-1.jpg',
        'images/assets/seeder-house-images/siguldas-skati-product-2.jpg',
        'images/assets/seeder-house-images/siguldas-skati-product-3.jpg',
        'images/assets/seeder-house-images/siguldas-skati-product-4.jpg',
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $randomWord = $this->faker->word;

        $title = [
            'lv' => $this->productTitles['lv'].' '.ucfirst($randomWord),
            'en' => $this->productTitles['en'].' '.ucfirst($randomWord),
        ];

        $slug = [
            'lv' => $this->productSlugs['lv'].'-'.Str::slug($randomWord),
            'en' => $this->productSlugs['en'].'-'.Str::slug($randomWord),
        ];

        return [
            'title'        => $title,
            'slug'         => $slug,
            'is_active'    => true,
            'cover'        => $this->faker->randomElement($this->productCovers),
            'person_count' => $this->faker->numberBetween(1, 4),
        ];
    }
}
