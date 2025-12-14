<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\Order;

class OrderController extends Controller
{
    // GET /checkout
    public function create(Request $request)
    {
        $cart = $request->session()->get('cart', []);

        $items = collect($cart)->map(function ($row) {
            $row['line_total'] = ($row['price'] ?? 0) * ($row['qty'] ?? 1);
            return $row;
        });

        if ($items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $subtotal = $items->sum('line_total');

        return view('checkout.create', [
            'items'    => $items,
            'subtotal' => $subtotal,
        ]);
    }

    // POST /checkout
    public function store(Request $request)
    {
        $cart = $request->session()->get('cart', []);
        $items = collect($cart)->map(function ($row) {
            $row['line_total'] = ($row['price'] ?? 0) * ($row['qty'] ?? 1);
            return $row;
        });

        if ($items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        // Minimal validation
        $data = $request->validate([
            'shipping_name'    => 'nullable|string|max:255',
            'shipping_address' => 'nullable|string|max:1000',
        ]);

        $subtotal = $items->sum('line_total');

        // Columns available in orders table
        $columns = Schema::getColumnListing('orders');

        $order = new Order();

        // Helper to set only columns that exist
        $set = function (string $col, $value) use ($columns, $order) {
            if (in_array($col, $columns)) {
                $order->{$col} = $value;
            }
        };

        $set('user_id', optional($request->user())->id);

        // 🔹 Important: your DB requires 'subtotal'
        $set('subtotal', $subtotal);

        // Safe extras (only set if columns exist)
        $set('total', $subtotal);
        $set('grand_total', $subtotal);
        $set('status', 'pending');
        $set('shipping_name', $data['shipping_name'] ?? null);
        $set('shipping_address', $data['shipping_address'] ?? null);

        // Keep cart snapshot if you have a 'meta' column
        if (in_array('meta', $columns)) {
            $order->meta = json_encode(['items' => $items->values()], JSON_UNESCAPED_UNICODE);
        }

        $order->save();

        // Optionally write order_items if table exists
        if (Schema::hasTable('order_items')) {
            $rows = [];
            foreach ($items as $row) {
                $rows[] = [
                    'order_id'   => $order->id,
                    'product_id' => $row['product_id'] ?? null,
                    'name'       => $row['name'] ?? '',
                    'price'      => $row['price'] ?? 0,
                    'qty'        => $row['qty'] ?? 1,
                    'total'      => $row['line_total'] ?? 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            if ($rows) {
                DB::table('order_items')->insert($rows);
            }
        }

        // Clear cart
        $request->session()->forget('cart');

        return redirect()->route('account.index')->with('success', 'Order placed successfully.');
    }
}
