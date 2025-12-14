<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class CheckoutForm extends Component
{
    public $name;
    public $email;
    public $phone;
    public $address;
    public $city;
    public $notes;

    public $items;
    public $total = 0;

   public function mount()
{
    // Get cart from SESSION (same as CartController & OrderController)
    $cart = session()->get('cart', []);

    // Turn into a collection and calculate line totals
    $items = collect($cart)->map(function ($row) {
        $row['line_total'] = ($row['price'] ?? 0) * ($row['qty'] ?? 1);
        return $row;
    });

    // Store in Livewire properties
    $this->items = $items->values();      // collection/array of rows
    $this->total = $items->sum('line_total');
}


    public function placeOrder()
    {
        if ($this->items->isEmpty()) {
            $this->addError('general', 'Your cart is empty.');
            return;
        }

        $data = $this->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email',
            'phone'   => 'required|string|max:30',
            'address' => 'required|string|max:500',
            'city'    => 'required|string|max:100',
            'notes'   => 'nullable|string|max:1000',
        ]);

        $user = auth()->user();
        $cart = Cart::firstOrCreate(['user_id' => $user->id]);

        DB::transaction(function () use ($user, $cart, $data) {
            // Create order
$order = Order::create([
    'user_id'  => $user->id,
    'status'   => 'pending',

    // Your table has subtotal, tax, shipping, total
    'subtotal' => $this->total,
    'tax'      => 0,
    'shipping' => 0,
    'total'    => $this->total,
]);



   // Create order items
// Create order items
foreach ($this->items as $item) {
    OrderItem::create([
        'order_id'   => $order->id,
        'product_id' => $item['product_id'],
        'name'       => $item['name'],                // 👈 NEW – satisfies NOT NULL column
        'qty'        => $item['qty'],
        'unit_price' => $item['price'],               // uses price from session cart
        'line_total' => $item['price'] * $item['qty'],
    ]);

    // Optional: reduce stock
    $product = \App\Models\Product::find($item['product_id']);
    if ($product && $product->stock !== null) {
        $product->decrement('stock', $item['qty']);
    }
}



            // Clear cart
            $cart->items()->delete();
        });

        session()->flash('success', 'Order placed successfully!');

        // Redirect to wherever you show orders or thank-you page
       return redirect()->route('account.index');
 
    }

    public function render()
    {
        return view('livewire.checkout-form');
    }
}
