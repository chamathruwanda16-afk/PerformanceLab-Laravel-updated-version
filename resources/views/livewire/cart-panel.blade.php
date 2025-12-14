<div>
  <h2 class="text-lg font-semibold mb-3">Your Cart</h2>

  @forelse($items as $item)
    <div class="flex items-center justify-between py-2 border-b">

      {{-- Product name + Qty --}}
      <div>
        <div class="font-medium">{{ $item->product->name }}</div>

        <div class="flex items-center space-x-3 mt-1 text-sm text-gray-500">
          {{-- Decrease qty --}}
          <button wire:click="decrement({{ $item->id }})"
                  class="px-2 py-1 bg-gray-200 rounded hover:bg-gray-300">−</button>

          <span>{{ $item->qty }}</span>

          {{-- Increase qty --}}
          <button wire:click="increment({{ $item->id }})"
                  class="px-2 py-1 bg-gray-200 rounded hover:bg-gray-300">+</button>
        </div>
      </div>

      {{-- Price + Remove --}}
      <div class="text-right">
        <div class="font-semibold">
          Rs. {{ number_format($item->qty * $item->unit_price, 2) }}
        </div>

        <button wire:click="remove({{ $item->id }})"
                class="text-red-500 text-sm hover:text-red-600 mt-1">
          Remove
        </button>
      </div>

    </div>
  @empty
    <p class="text-gray-500">Cart is empty.</p>
  @endforelse


  {{-- TOTAL --}}
  @if($items->count())
    <div class="mt-4 text-right font-bold">
      Total: Rs. {{ number_format($this->total, 2) }}
    </div>

    {{-- CLEAR CART --}}
    <button wire:click="clear"
            class="mt-3 inline-block rounded-lg bg-red-600 text-white px-4 py-2 hover:bg-red-500">
      Clear Cart
    </button>

    {{-- CHECKOUT BUTTON --}}
    <a href="{{ route('checkout.create') }}"
       class="mt-3 ml-2 inline-block rounded-lg bg-black text-white px-4 py-2 hover:bg-gray-800">
      Checkout
    </a>
  @endif
</div>
