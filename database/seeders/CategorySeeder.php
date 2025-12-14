<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $cats = [
            ['name' => 'Engine Parts', 'slug' => 'engine-parts'],
            ['name' => 'Suspension', 'slug' => 'suspension'],
            ['name' => 'Exhaust System', 'slug' => 'exhaust-system'],
            ['name' => 'Exterior Kits', 'slug' => 'exterior-kits'],
            ['name' => 'Interior Mods', 'slug' => 'interior-mods'],


              ['name' => 'Universal Parts',  'slug' => 'universal-parts'],
                ['name' => 'Wheels',           'slug' => 'wheels'],
        ];

        foreach ($cats as $c) {
            Category::firstOrCreate(['slug' => $c['slug']], $c);
        }
    }
}
