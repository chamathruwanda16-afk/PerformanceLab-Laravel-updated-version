<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class HomeController extends Controller
{
    public function index()
    {
        // Use the actual filenames that are in public/images
       $bestSellers = Product::latest()
        ->take(3)
        ->get(['id','slug','name','price','image_path']);

        $categories = [
            ['name' => 'Turbo Chargers',  'slug' => 'turbochargers',   'image' => 'borg.jpg'],
            ['name' => 'Wheels & Tires',  'slug' => 'wheels',          'image' => 'wheels.webp'],
            ['name' => 'Universal Parts', 'slug' => 'universal-parts', 'image' => 'gearnob.jpg'],
            ['name' => 'Accessories',     'slug' => 'accessories',     'image' => 'ecu.jpeg'],
        ];

        return view('home', compact('bestSellers', 'categories'));
    }
}
