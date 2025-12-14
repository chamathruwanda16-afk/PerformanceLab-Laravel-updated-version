<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderAdminController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->input('status');
        $orders = Order::with(['user','items'])
            ->when($status, fn($q) => $q->where('status', $status))
            ->latest()->paginate(20)->withQueryString();

        return view('admin.orders.index', compact('orders','status'));
    }

    public function show(Order $order)
    {
        $order->loadMissing(['user','items.product']);
        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $data = $request->validate([
            'status' => 'required|in:pending,processing,successful,cancelled,paid',
        ]);

        $order->update(['status' => $data['status']]);

        return back()->with('success','Status updated.');
    }

    public function cancel(Request $request, Order $order)
    {
        if ($order->status === 'cancelled') {
            return back()->with('error','Order already cancelled.');
        }

        // Optional: restock items
        $order->loadMissing('items');
        foreach ($order->items as $it) {
            if ($it->product_id && method_exists($it->product,'increment')) {
                $it->product()->increment('stock', (int)$it->qty);
            }
        }

        $order->update(['status' => 'cancelled']);

        return back()->with('success','Order cancelled.');
    }
}
