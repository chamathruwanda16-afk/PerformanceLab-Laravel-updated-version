<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Cart;

class CartPanel extends Component
{
    public $items = [];

    public function mount()
    {
        $this->loadItems();
    }

    /**
     * Reload cart items from the database for the current user.
     */
    protected function loadItems()
    {
        if (! auth()->check()) {
            $this->items = collect(); // empty collection
            return;
        }

        $cart = Cart::firstOrCreate(['user_id' => auth()->id()]);

        $this->items = $cart->items()
            ->with('product')
            ->get();
    }

    /**
     * Increase quantity for a given cart item.
     */
    public function increment($itemId)
    {
        if (! auth()->check()) return;

        $cart = Cart::firstOrCreate(['user_id' => auth()->id()]);
        $item = $cart->items()->where('id', $itemId)->first();

        if ($item) {
            $item->qty += 1;
            $item->save();
            $this->loadItems();
        }
    }

    /**
     * Decrease quantity for a given cart item (min 1).
     */
    public function decrement($itemId)
    {
        if (! auth()->check()) return;

        $cart = Cart::firstOrCreate(['user_id' => auth()->id()]);
        $item = $cart->items()->where('id', $itemId)->first();

        if ($item && $item->qty > 1) {
            $item->qty -= 1;
            $item->save();
            $this->loadItems();
        }
    }

    /**
     * Remove a single item from the cart.
     */
    public function remove($itemId)
    {
        if (! auth()->check()) return;

        $cart = Cart::firstOrCreate(['user_id' => auth()->id()]);
        $item = $cart->items()->where('id', $itemId)->first();

        if ($item) {
            $item->delete();
            $this->loadItems();
        }
    }

    /**
     * Clear all items in the cart.
     */
    public function clear()
    {
        if (! auth()->check()) return;

        $cart = Cart::firstOrCreate(['user_id' => auth()->id()]);
        $cart->items()->delete();

        $this->loadItems();
    }

    /**
     * Computed property for total cart value.
     */
    public function getTotalProperty()
    {
        return $this->items->sum(function ($item) {
            return $item->product->price * $item->qty;
        });
    }

    public function render()
    {
        return view('livewire.cart-panel');
    }
}
