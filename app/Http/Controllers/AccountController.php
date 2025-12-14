<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;

class AccountController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // Recent orders for this user
        $orders = Order::where('user_id', $user->id)
            ->latest()
            ->paginate(10);

        return view('account.index', compact('orders', 'user'));
    }

    public function cancel(Request $request, Order $order)
    {
        // Authorize: only owner can cancel
        if ($order->user_id !== $request->user()->id) {
            abort(403);
        }

        if (! $order->is_cancellable) {
            return back()->with('error', 'This order can no longer be cancelled.');
        }

        $order->markCancelled();

        return back()->with('success', 'Order cancelled successfully.');


        // in index()
$orders = \App\Models\Order::where('user_id', $request->user()->id)
    ->with('items')     // 👈 add this
    ->latest()
    ->paginate(10);

    }
    
}
