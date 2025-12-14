<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\SearchLog;       // MongoDB: search logs
use App\Models\ProductView;     // MongoDB: product views
use MongoDB\BSON\UTCDateTime;   // For date filtering in MongoDB

class ProductController extends Controller
{
    public function index(Request $request)
    {
        // Base query with category relationship
        $productsQuery = Product::with('category');

        // Read filters from query string
        $categorySlug = $request->input('category'); // e.g. ?category=wheels
        $search       = $request->input('q');        // e.g. ?q=coilover  (matches your Blade)

        // Filter by category (if provided)
        if (!empty($categorySlug)) {
            $productsQuery->whereHas('category', function ($q) use ($categorySlug) {
                $q->where('slug', $categorySlug);
            });
        }

        // Filter by search term (if provided)
        if (!empty($search)) {
            $productsQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
                // You can add more fields, e.g.:
                // ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        // Paginate products
        $products = $productsQuery->latest()->paginate(12);

        // 🔐 Log search into MongoDB only if a term was used
        if (!empty($search)) {
            $normalized = strtolower(trim($search));

            SearchLog::create([
                'query'         => $normalized,
                'user_id'       => optional($request->user())->id,
                'session_id'    => $request->session()->getId(),
                'results_count' => $products->total(),
                'ip'            => $request->ip(),
                'created_at'    => now(),
            ]);

            // 🔥 remember this search in the session so we can link clicks to it
            $request->session()->put('last_search_query', $normalized);
        } else {
            // If no search term, clear any previous "last search"
            $request->session()->forget('last_search_query');
        }

        // ⭐ Popular searches from last 30 days
        $popularSearches = SearchLog::raw(function ($collection) {
            return $collection->aggregate([
                [
                    '$match' => [
                        'created_at' => [
                            '$gte' => new UTCDateTime(
                                now()->subDays(30)->getTimestamp() * 1000
                            ),
                        ],
                    ],
                ],
                [
                    '$group' => [
                        '_id'   => '$query',
                        'count' => ['$sum' => 1],
                    ],
                ],
                [
                    '$sort' => ['count' => -1],
                ],
                [
                    '$limit' => 8,
                ],
            ]);
        });

        // 👥 “Users also searched for…” (based on same sessions)
        $alsoSearched = [];
        if (!empty($search)) {
            $normalized = strtolower(trim($search));

            $alsoSearched = SearchLog::raw(function ($collection) use ($normalized) {
                return $collection->aggregate([
                    // sessions where this query was used
                    [
                        '$match' => [
                            'query' => $normalized,
                        ],
                    ],
                    [
                        '$group' => [
                            '_id' => '$session_id',
                        ],
                    ],
                    // pull all searches from those sessions
                    [
                        '$lookup' => [
                            'from'         => 'search_logs',
                            'localField'   => '_id',
                            'foreignField' => 'session_id',
                            'as'           => 'session_searches',
                        ],
                    ],
                    [
                        '$unwind' => '$session_searches',
                    ],
                    // exclude the original query itself
                    [
                        '$match' => [
                            'session_searches.query' => [
                                '$ne' => $normalized,
                            ],
                        ],
                    ],
                    // count how often each other query appears
                    [
                        '$group' => [
                            '_id'   => '$session_searches.query',
                            'count' => ['$sum' => 1],
                        ],
                    ],
                    [
                        '$sort' => ['count' => -1],
                    ],
                    [
                        '$limit' => 5,
                    ],
                ]);
            });
        }

        // Categories for sidebar
        $categories = Category::orderBy('name')->get(['id', 'name', 'slug']);

        // keep q for the Blade input
        $q = $search;

        return view('products.index', compact(
            'products',
            'categories',
            'q',
            'popularSearches',
            'alsoSearched'
        ));
    }

    public function show(Request $request, Product $product)
    {
        // 🔗 Fetch last search query from the session (if user came from a search)
        $searchQuery = $request->session()->get('last_search_query');

        // 👁 Track product view in MongoDB, including which search led to this click
        ProductView::create([
            'product_id'   => $product->id,
            'slug'         => $product->slug,
            'user_id'      => optional($request->user())->id,
            'session_id'   => $request->session()->getId(),
            'ip'           => $request->ip(),
            'user_agent'   => substr((string) $request->userAgent(), 0, 255),
            'viewed_at'    => now(),
            'search_query' => $searchQuery, // 🔥 NULL if they didn’t come from a search
        ]);

        return view('products.show', compact('product'));
    }
}
