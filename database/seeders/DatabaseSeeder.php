<?php

namespace Database\Seeders;

use App\Models\Feature;
use App\Models\Gallery;
use App\Models\GalleryImage;
use App\Models\Newsletter;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Rule;
use App\Models\User;
use Illuminate\Database\Seeder;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Siguldas Skati',
            'email' => 'info@siguldasskati.lv',
        ]);

        Product::factory()
            ->count(5)
            ->has(ProductImage::factory()->count(10), 'images')
            ->has(Feature::factory()->count(5), 'features')
            ->has(Rule::factory()->count(6), 'rules')
            ->create();

        Gallery::factory()
            ->count(5)
            ->has(GalleryImage::factory()->count(3), 'images')
            ->create();

        Newsletter::factory()->count(10)->create();
    }
}
