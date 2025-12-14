<x-app-layout>
  <div class="max-w-7xl mx-auto px-6 py-10">
    <div class="flex items-end justify-between gap-4">
      <div>
        <h1 class="text-3xl font-extrabold">Your Cart</h1>
        <p class="text-gray-300 mt-1">Review items and proceed to checkout.</p>
      </div>
      <a href="{{ route('products.index') }}"
         class="rounded-lg border border-white/20 bg-white/10 px-4 py-2 text-sm hover:bg-white/20">
        Continue shopping
      </a>
    </div>

    {{-- Flash messages --}}
    @if (session('success'))
      <div class="mt-4 rounded-lg border border-green-500/30 bg-green-500/10 px-4 py-3 text-green-300">
        {{ session('success') }}
      </div>
    @endif
    @if (session('error'))
      <div class="mt-4 rounded-lg border border-red-500/30 bg-red-500/10 px-4 py-3 text-red-300">
        {{ session('error') }}
      </div>
    @endif

    @if ($items->isEmpty())
      <div class="mt-8 rounded-xl border border-white/10 bg-white/5 p-8 text-center">
        <p class="text-gray-300">Your cart is empty.</p>
        <a href="{{ route('products.index') }}"
           class="mt-4 inline-block rounded-lg border border-white/20 bg-white/10 px-4 py-2 hover:bg-white/20">
          Browse products
        </a>
      </div>
    @else
      <div class="mt-8 space-y-4">
        @foreach($items as $id => $row)
          @php
            // Option A image logic: full URL or asset($path), with placeholder fallback
            $raw = $row['image'] ?? null;
            $img = $raw
              ? (\Illuminate\Support\Str::startsWith($raw, ['http://','https://']) ? $raw : asset(ltrim($raw, '/')))
              : asset('images/placeholder.jpg'); // ensure this exists
          @endphp

          <div class="flex items-center gap-4 rounded-xl border border-white/10 bg-white/5 p-4">
            <img src="{{ $img }}" alt="{{ $row['name'] }}" class="h-16 w-16 rounded object-cover">

            <div class="flex-1">
              <a href="{{ route('products.show', $row['slug']) }}" class="font-semibold hover:underline">
                {{ $row['name'] }}
              </a>
              <div class="text-sm text-gray-400 mt-1">Rs. {{ number_format($row['price'], 2) }}</div>
            </div>

            {{-- Update qty --}}
            <form method="POST" action="{{ route('cart.update', $id) }}" class="flex items-center gap-2">
              @csrf
              @method('PATCH')
              <input type="number" name="qty" value="{{ $row['qty'] }}" min="1"
                     class="w-20 rounded-lg border border-white/20 bg-white/10 px-3 py-2">
              <button class="rounded-lg border border-white/20 bg-white/10 px-3 py-2 text-sm hover:bg-white/20">
                Update
              </button>
            </form>

            {{-- Remove --}}
            <form method="POST" action="{{ route('cart.remove', $id) }}">
              @csrf
              @method('DELETE')
              <button class="rounded-lg border border-red-500/40 bg-red-500/10 px-3 py-2 text-sm text-red-300 hover:bg-red-500/20">
                Remove
              </button>
            </form>

            <div class="w-28 text-right font-semibold">
              Rs. {{ number_format($row['line_total'], 2) }}
            </div>
          </div>
        @endforeach
      </div>

      <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="md:col-span-2"></div>
        <div class="rounded-xl border border-white/10 bg-white/5 p-4">
          <div class="flex items-center justify-between">
            <span class="text-lg">Subtotal</span>
            <span class="text-xl font-semibold">Rs. {{ number_format($subtotal, 2) }}</span>
          </div>
          <a href="{{ route('checkout.create') }}"
             class="mt-4 block text-center rounded-lg border border-white/20 bg-white/10 px-5 py-2 hover:bg-white/20">
            Proceed to Checkout
          </a>
        </div>
      </div>
    @endif
  </div>
</x-app-layout>
