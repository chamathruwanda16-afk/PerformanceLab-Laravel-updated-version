<x-app-layout>
  <div class="max-w-7xl mx-auto px-6 py-10">
    <div class="grid md:grid-cols-2 gap-10">
      <div class="rounded-3xl overflow-hidden bg-white/5 border border-white/10">
        @php
          $raw = $product->image_path;
          $img = $raw
            ? (\Illuminate\Support\Str::startsWith($raw, ['http://','https://']) ? $raw : asset(ltrim($raw, '/')))
            : asset('images/placeholder.jpg'); // make sure this exists
        @endphp
        <img
    src="{{ $product->image_url }}"
    alt="{{ $product->name }}"
    class="w-full h-[420px] object-cover rounded-3xl bg-neutral-900"
/>

      </div>

      <div>
        <h1 class="text-3xl font-extrabold">{{ $product->name }}</h1>
        <div class="mt-2 text-gray-300">{{ $product->description }}</div>
        <div class="mt-5 text-2xl font-extrabold">Rs. {{ number_format($product->price, 2) }}</div>

        @auth
          <form action="{{ route('cart.add', $product) }}" method="POST" class="mt-6 flex items-center gap-3">
            @csrf
            <label class="text-sm text-gray-300">Qty
              {{-- IMPORTANT: name="qty" to match CartController --}}
              <input type="number" name="qty" value="1" min="1"
                     class="ml-2 w-20 rounded-lg bg-white/5 border border-white/10 px-3 py-2 focus:outline-none">
            </label>
            <button class="rounded-xl bg-red-600 px-5 py-2.5 font-semibold hover:bg-red-500">
              Add to cart
            </button>
          </form>
        @else
          <a href="{{ route('login') }}"
             class="mt-6 inline-block rounded-xl bg-white/10 border border-white/20 px-5 py-2.5 hover:bg-white/20">
            Login to add to cart
          </a>
        @endauth



        <dl class="mt-8 grid grid-cols-2 gap-4 text-sm">
          <div class="rounded-xl bg-white/5 border border-white/10 p-4">
            <dt class="text-gray-400">SKU</dt>
            <dd class="font-semibold">{{ $product->sku ?? '—' }}</dd>
          </div>
          <div class="rounded-xl bg-white/5 border border-white/10 p-4">
            <dt class="text-gray-400">Category</dt>
            <dd class="font-semibold">{{ $product->category->name ?? '—' }}</dd>
          </div>
        </dl>
      </div>
    </div>
  </div>
</x-app-layout>
