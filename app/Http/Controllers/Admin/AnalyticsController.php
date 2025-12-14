<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SearchLog;
use App\Models\ProductView;
use MongoDB\BSON\UTCDateTime;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        // Default quick range = last 30 days
        $days = (int) $request->get('days', 30);

        // Optional custom date range
        $startDate = $request->get('start_date'); // YYYY-MM-DD
        $endDate   = $request->get('end_date');   // YYYY-MM-DD

        // Build MongoDB date range
        if (!empty($startDate) && !empty($endDate)) {
            $from = new UTCDateTime(strtotime($startDate . ' 00:00:00') * 1000);
            $to   = new UTCDateTime(strtotime($endDate . ' 23:59:59') * 1000);
        } else {
            $from = new UTCDateTime(now()->subDays($days)->getTimestamp() * 1000);
            $to   = new UTCDateTime(now()->getTimestamp() * 1000);
        }

        // 1) Aggregate number of searches per query (filtered by created_at)
        $searchStats = SearchLog::raw(function ($collection) use ($from, $to) {
            return $collection->aggregate([
                [
                    '$match' => [
                        'created_at' => [
                            '$gte' => $from,
                            '$lte' => $to,
                        ],
                    ],
                ],
                [
                    '$group' => [
                        '_id'      => '$query',
                        'searches' => ['$sum' => 1],
                    ],
                ],
            ]);
        });

        // 2) Aggregate number of clicks per search_query (filtered by viewed_at)
        $clickStats = ProductView::raw(function ($collection) use ($from, $to) {
            return $collection->aggregate([
                [
                    '$match' => [
                        'search_query' => ['$ne' => null],
                        'viewed_at' => [
                            '$gte' => $from,
                            '$lte' => $to,
                        ],
                    ],
                ],
                [
                    '$group' => [
                        '_id'    => '$search_query',
                        'clicks' => ['$sum' => 1],
                    ],
                ],
            ]);
        });

        // 3) Merge them in PHP (same as your logic)
        $searchMap = [];

        foreach ($searchStats as $row) {
            $query = (string) $row->_id;
            $searchMap[$query] = [
                'query'    => $query,
                'searches' => (int) $row->searches,
                'clicks'   => 0,
                'ctr'      => 0,
            ];
        }

        foreach ($clickStats as $row) {
            $query = (string) $row->_id;

            if (!isset($searchMap[$query])) {
                $searchMap[$query] = [
                    'query'    => $query,
                    'searches' => 0,
                    'clicks'   => 0,
                    'ctr'      => 0,
                ];
            }

            $searchMap[$query]['clicks'] = (int) $row->clicks;
        }

        // 4) Compute CTR = clicks / searches
        foreach ($searchMap as &$item) {
            $item['ctr'] = $item['searches'] > 0
                ? round(($item['clicks'] / $item['searches']) * 100, 2)
                : 0;
        }
        unset($item);

        // 5) Sort by searches desc (same as before)
        usort($searchMap, function ($a, $b) {
            return $b['searches'] <=> $a['searches'];
        });

        // Limit to top 50 for now
        $rows = array_slice($searchMap, 0, 50);

        // ✅ send filter values to the view so the dropdown stays selected
        return view('admin.analytics', compact('rows', 'days', 'startDate', 'endDate'));
    }
}
