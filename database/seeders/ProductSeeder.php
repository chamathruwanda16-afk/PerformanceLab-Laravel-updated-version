<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;

class ProductSeeder extends Seeder
{
    public function run()
    {
        $products = [
            // Engine Parts
            [
                'category' => 'Engine Parts',
                'name' => 'HKS Turbo Kit',
                'slug' => 'hks-turbo-kit',
                'description' => 'High-performance JDM turbo kit designed for maximum boost and reliability.',
                'price' => 320000.00,
                'stock' => 5,
                'image_path' => 'hks-turbo.jpg',
            ],
            [
                'category' => 'Engine Parts',
                'name' => 'Greddy Blow Off Valve',
                'slug' => 'greddy-blow-off-valve',
                'description' => 'Premium Greddy Type-RZ blow off valve for smooth boost control and signature sound.',
                'price' => 78000.00,
                'stock' => 8,
                'image_path' => 'greddy-bov.jpg',
            ],

            // Suspension
            [
                'category' => 'Suspension',
                'name' => 'Tein Coilovers',
                'slug' => 'tein-coilovers',
                'description' => 'Adjustable coilover suspension for street and track performance.',
                'price' => 185000.00,
                'stock' => 6,
                'image_path' => 'tein-coilovers.jpg',
            ],
            [
                'category' => 'Suspension',
                'name' => 'Cusco Strut Bar',
                'slug' => 'cusco-strut-bar',
                'description' => 'Front tower bar to reduce body flex and improve cornering stability.',
                'price' => 54000.00,
                'stock' => 10,
                'image_path' => 'cusco-strut.jpg',
            ],

            // Exhaust System
            [
                'category' => 'Exhaust System',
                'name' => 'Invidia N1 Exhaust',
                'slug' => 'invidia-n1-exhaust',
                'description' => 'Aggressive stainless steel cat-back exhaust system for JDM performance cars.',
                'price' => 145000.00,
                'stock' => 4,
                'image_path' => 'invidia-n1.jpg',
            ],
            [
                'category' => 'Exhaust System',
                'name' => 'HKS Hi-Power Exhaust',
                'slug' => 'hks-hi-power-exhaust',
                'description' => 'Durable, deep-tone exhaust with titanium tip for true JDM sound.',
                'price' => 159000.00,
                'stock' => 3,
                'image_path' => 'hks-hipower.jpg',
            ],

            // Exterior Kits
            [
                'category' => 'Exterior Kits',
                'name' => 'Carbon Fiber Spoiler',
                'slug' => 'carbon-fiber-spoiler',
                'description' => 'Lightweight rear spoiler for improved aerodynamics and aggressive styling.',
                'price' => 95000.00,
                'stock' => 7,
                'image_path' => 'carbon-spoiler.jpg',
            ],
            [
                'category' => 'Exterior Kits',
                'name' => 'Varis Front Bumper',
                'slug' => 'varis-front-bumper',
                'description' => 'Authentic JDM Varis front bumper kit with aerodynamic design.',
                'price' => 185000.00,
                'stock' => 2,
                'image_path' => 'varis-bumper.jpg',
            ],

            // Interior Mods
            [
                'category' => 'Interior Mods',
                'name' => 'Bride Racing Seat',
                'slug' => 'bride-racing-seat',
                'description' => 'Lightweight racing seat with carbon-Kevlar frame and superior comfort.',
                'price' => 120000.00,
                'stock' => 3,
                'image_path' => 'bride-seat.jpg',
            ],
            [
                'category' => 'Interior Mods',
                'name' => 'Nardi Steering Wheel',
                'slug' => 'nardi-steering-wheel',
                'description' => 'Classic Nardi Deep Corn steering wheel for authentic JDM interiors.',
                'price' => 58000.00,
                'stock' => 5,
                'image_path' => 'nardi-wheel.jpg',
            ],
        ];

        foreach ($products as $p) {
            $category = Category::where('name', $p['category'])->first();
            if ($category) {
                Product::firstOrCreate(
                    ['slug' => $p['slug']],
                    [
                        'category_id' => $category->id,
                        'name' => $p['name'],
                        'description' => $p['description'],
                        'price' => $p['price'],
                        'stock' => $p['stock'],
                        'image_path' => $p['image_path'],
                    ]
                );
            }
        }
    }
}
