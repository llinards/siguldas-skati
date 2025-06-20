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

    private array $productDescriptions = [
        'lv' => 'Pārvietojama koka karkasa moduļu māja. Šāda veida māju būvniecība ir salīdzinoši ātra un neaizņem ilgu projekta saskaņošanas laiku. Šim projektam nav nepieciešama būvatļauja (līdz apbūves platībai 60m²) un ir iespējams dzīvot uzreiz. Projektos ir iespējami dažādi iekšējās un ārējās apdares risinājumi, kā arī ir iespējams veikt izmaiņas telpu plānojumos.',
        'en' => 'Portable wooden frame modular house. This type of house construction is relatively fast and does not require a long project approval time. This project does not require a building permit (up to a building area of 60m²) and it is possible to live in it immediately. The projects allow for various interior and exterior finishing solutions, and it is also possible to make changes to room layouts.',
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
            'description'  => $this->productDescriptions,
            'is_active'    => true,
            'cover'        => $this->faker->randomElement($this->productCovers),
            'person_count' => $this->faker->numberBetween(1, 4),
        ];
    }
}
