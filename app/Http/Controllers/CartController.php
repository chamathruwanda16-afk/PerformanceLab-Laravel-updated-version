<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class CartController extends Controller
{
    // Show cart
    public function index(Request $request)
    {
        $cart = $this->getCart($request);
        $items = collect($cart)->map(function ($row) {
            // compute line total
            $row['line_total'] = $row['price'] * $row['qty'];
            return $row;
        });

        $subtotal = $items->sum('line_total');

        return view('cart.index', [
            'items'    => $items,
            'subtotal' => $subtotal,
        ]);
    }

    // POST /cart/add/{product}
    public function add(Request $request, Product $product)
    {
        // If Product uses slug binding, this will resolve by slug (you added getRouteKeyName()).
        $qty = max(1, (int) $request->input('qty', 1));

        $cart = $this->getCart($request);

        $key = (string) $product->id; // use product id as key
        if (isset($cart[$key])) {
            $cart[$key]['qty'] += $qty;
        } else {
            $cart[$key] = [
                'product_id' => $product->id,
                'name'       => $product->name,
                'price'      => (float) $product->price,
                'qty'        => $qty,
                'image'      => $product->image_path,
                'slug'       => $product->slug,
            ];
        }

        $this->putCart($request, $cart);

        return redirect()->route('cart.index')->with('success', 'Item added to cart.');
    }

    // PATCH /cart/item/{item}  (item is product_id here)
    public function update(Request $request, $item)
    {
        $qty = max(1, (int) $request->input('qty', 1));

        $cart = $this->getCart($request);
        if (isset($cart[$item])) {
            $cart[$item]['qty'] = $qty;
            $this->putCart($request, $cart);
            return back()->with('success', 'Cart updated.');
        }
        return back()->with('error', 'Item not found in cart.');
    }

    // DELETE /cart/item/{item}
    public function remove(Request $request, $item)
    {
        $cart = $this->getCart($request);
        if (isset($cart[$item])) {
            unset($cart[$item]);
            $this->putCart($request, $cart);
            return back()->with('success', 'Item removed.');
        }
        return back()->with('error', 'Item not found.');
    }

    // Helpers
    protected function getCart(Request $request): array
    {
        return $request->session()->get('cart', []);
    }

    protected function putCart(Request $request, array $cart): void
    {
        $request->session()->put('cart', $cart);
    }
}
