<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;

class OrderApiController extends Controller
{
    public function store(Request $request)
    {
        // Validate incoming JSON payload
        $data = $request->validate([
            'items'                 => ['required', 'array', 'min:1'],
            'items.*.product_id'    => ['required', 'integer', 'exists:products,id'],
            'items.*.qty'           => ['required', 'integer', 'min:1'],
        ]);

        // --------------------------------------------------------
        // 🔐 Sanctum: Authenticated API user
        // --------------------------------------------------------
        $user = $request->user();   // THIS shows Sanctum is working
        // If no user, Sanctum blocked them before this point.

        $subtotal = 0;

        // --------------------------------------------------------
        // 💰 Calculate subtotal from items
        // --------------------------------------------------------
        foreach ($data['items'] as $item) {
            $product = Product::findOrFail($item['product_id']);
            $subtotal += $product->price * $item['qty'];
        }

        // --------------------------------------------------------
        // 🧾 Create Order for authenticated user
        // --------------------------------------------------------
        $order = Order::create([
            'user_id'  => $user->id,
            'status'   => 'pending',
            'subtotal' => $subtotal,
            'tax'      => 0,
            'shipping' => 0,
            'total'    => $subtotal,
        ]);

        // --------------------------------------------------------
        // 📦 Create order item records
        // --------------------------------------------------------
        foreach ($data['items'] as $item) {
            $product = Product::findOrFail($item['product_id']);

            OrderItem::create([
                'order_id'   => $order->id,
                'product_id' => $item['product_id'],
                'qty'        => $item['qty'],
                'unit_price' => $product->price,
            ]);
        }

        // --------------------------------------------------------
        // 🔄 Return order with items + product details
        // --------------------------------------------------------
        return response()->json(
            $order->load('items.product'),
            201
        );
    }
}
