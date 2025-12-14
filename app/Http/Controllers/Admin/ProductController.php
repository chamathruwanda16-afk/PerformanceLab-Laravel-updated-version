<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $q       = $request->input('q');
        $catSlug = $request->input('category');

        $query = Product::with('category')->latest();

        if ($q) {
            $query->where(function ($w) use ($q) {
                $w->where('name', 'like', "%{$q}%")
                  ->orWhere('description', 'like', "%{$q}%");
            });
        }

        $activeCategory = null;
        if ($catSlug) {
            $activeCategory = Category::where('slug', $catSlug)->first();
            if ($activeCategory) {
                $query->where('category_id', $activeCategory->id);
            }
        }

        $products   = $query->paginate(12)->withQueryString();
        $categories = Category::orderBy('name')->get(['id', 'name', 'slug']);

        return view('products.index', compact('products', 'categories', 'activeCategory', 'q'));
    }

    public function show(Product $product)
{
    // Product is resolved by slug (see step 2)
    return view('products.show', compact('product'));
}
}
