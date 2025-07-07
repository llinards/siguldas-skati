<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductImage;
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
            'name'  => 'Siguldas Skati',
            'email' => 'info@siguldasskati.lv',
        ]);

        Product::factory()
               ->count(5)
               ->has(ProductImage::factory()->count(3), 'images')
               ->create();
    }
}
