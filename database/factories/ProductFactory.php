<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\File;
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

    private array $productPricelist = [
        'lv' => '<p><strong>Cena par nakti</strong></p><ul><li>159 EUR – 2 viesiem</li><li>209 EUR – 2 viesiem nedēļas nogalēs</li><li>+20 EUR katru papildu viesi (par nakti)</li></ul><p><strong>Papildu maksa</strong></p><ul><li>10 EUR – uzkopšana uzturēšanās reizēs no 1 līdz 2 naktīm</li><li>30 EUR – uzkopšana uzturēšanās reizēs no 3 līdz 4 naktīm</li><li>20 EUR – mājdzīvnieku uzņemšana</li><li>70 EUR – kubls</li></ul><p>* Cenas svētku dienās, kā arī dienu pirms un pēc tām, var būt augstākas.</p><p>Pārbaudi pieejamos datumus un <a target=\"_blank\" rel=\"noopener noreferrer\" href=\"https://siguldasskati.slmedia.lv/lv/brivdienu-dizaina-maja-minus#\"><strong>rezervē Airbnb</strong></a> platformā vai rezervē rakstot uz <a href=\"mailto:info@siguldasskati.lv\"><strong>info@siguldasskati.lv</strong></a>.</p>',
        'en' => '<p><strong>Price per night</strong></p><ul><li>159 EUR – 2 guests</li><li>209 EUR – 2 guests on weekends</li><li>+20 EUR each additional guest (per night)</li></ul><p><strong>Additional fees</strong></p><ul><li>10 EUR – cleaning for stays from 1 to 2 nights</li><li>30 EUR – cleaning for stays from 3 to 4 nights</li><li>20 EUR – pet accommodation</li><li>70 EUR – hot tub</li></ul><p>* Prices on holidays, as well as the day before and after them, may be higher.</p><p>Check available dates and <a href=\"\\&quot;https://siguldasskati.slmedia.lv/lv/brivdienu-dizaina-maja-minus#\\&quot;\"><strong>book on Airbnb</strong></a> platform or book by writing to <a href=\"\\&quot;mailto:info@siguldasskati.lv\\&quot;\"><strong>info@siguldasskati.lv</strong></a>.</p>',
    ];

    private array $productCovers = [
        'siguldas-skati-product-1.jpg',
        'siguldas-skati-product-2.jpg',
        'siguldas-skati-product-3.jpg',
        'siguldas-skati-product-4.jpg',
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

        // Select a random cover image
        $selectedCover = $this->faker->randomElement($this->productCovers);

        // Copy the image to public/storage/product-images/
        $copiedImagePath = $this->copyImageToStorage($selectedCover);

        return [
            'title' => $title,
            'slug' => $slug,
            'description' => $this->productDescriptions,
            'pricelist' => $this->productPricelist,
            'is_active' => true,
            'cover' => $copiedImagePath,
            'person_count' => $this->faker->numberBetween(1, 4),
        ];
    }

    /**
     * Copy image from seeder assets to storage and return the new path
     */
    private function copyImageToStorage(string $filename): string
    {
        $sourcePath = public_path('images/assets/seeder-house-images/'.$filename);
        $destinationDir = public_path('storage/product-images');

        // Get file extension
        $extension = pathinfo($filename, PATHINFO_EXTENSION);

        // Generate random filename
        $randomFilename = $this->faker->uuid.'.'.$extension;
        $destinationPath = $destinationDir.'/'.$randomFilename;

        // Create the destination directory if it doesn't exist
        if (! File::exists($destinationDir)) {
            File::makeDirectory($destinationDir, 0755, true);
        }

        // Copy the file if source exists
        if (File::exists($sourcePath)) {
            File::copy($sourcePath, $destinationPath);
        }

        // Return the path relative to storage
        return 'product-images/'.$randomFilename;
    }
}
