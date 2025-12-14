<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductFactorySeeder extends Seeder
{
    public function run(): void
    {
        // Generate 20 random products
        Product::factory()->count(20)->create();
    }
}
