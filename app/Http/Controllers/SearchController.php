<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SearchLog;

class SearchController extends Controller
{
    public function suggestions(Request $request)
    {
        $term = strtolower(trim($request->input('q', '')));

        if ($term === '') {
            return response()->json([]);
        }

        $suggestions = SearchLog::where('query', 'like', $term . '%')
            ->groupBy('query')
            ->orderBy('query')
            ->limit(8)
            ->pluck('query');

        return response()->json($suggestions);
    }
}
