
  <div class="max-w-3xl mx-auto px-6 py-10">
    <h1 class="text-3xl font-extrabold mb-6">
      Checkout
    </h1>

    <div class="bg-white/5 border border-white/10 rounded-2xl p-6 space-y-6">

      {{-- Errors --}}
      @if ($errors->has('general'))
        <div class="rounded-lg bg-red-600/20 text-red-200 px-4 py-3">
          {{ $errors->first('general') }}
        </div>
      @endif

      @if (session('success'))
        <div class="rounded-lg bg-green-600/20 text-green-200 px-4 py-3">
          {{ session('success') }}
        </div>
      @endif

      {{-- CART SUMMARY --}}
      <div>
        <h2 class="text-xl font-semibold mb-3">Order Summary</h2>
       @forelse($items as $item)
  <div class="flex items-center justify-between py-2 border-b border-white/10">
    <div>
      <div class="font-medium">{{ $item['name'] }}</div>
      <div class="text-sm text-gray-400">Qty: {{ $item['qty'] }}</div>
    </div>
    <div class="font-semibold">
      Rs. {{ number_format($item['line_total'], 2) }}
    </div>
  </div>
@empty
  <p class="text-gray-400 text-sm">Your cart is empty.</p>
@endforelse


        @if($items->count())
          <div class="mt-4 text-right font-bold text-lg">
            Total: Rs. {{ number_format($total, 2) }}
          </div>
        @endif
      </div>

      {{-- CHECKOUT FORM --}}
      @if($items->count())
        <form wire:submit.prevent="placeOrder" class="space-y-4">

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm text-gray-300">Full Name</label>
              <input type="text" wire:model.defer="name"
                     class="mt-1 w-full rounded-lg border border-white/10 bg-white/5 px-3 py-2">
              @error('name')<div class="text-sm text-red-400">{{ $message }}</div>@enderror
            </div>

            <div>
              <label class="block text-sm text-gray-300">Email</label>
              <input type="email" wire:model.defer="email"
                     class="mt-1 w-full rounded-lg border border-white/10 bg-white/5 px-3 py-2">
              @error('email')<div class="text-sm text-red-400">{{ $message }}</div>@enderror
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm text-gray-300">Phone</label>
              <input type="text" wire:model.defer="phone"
                     class="mt-1 w-full rounded-lg border border-white/10 bg-white/5 px-3 py-2">
              @error('phone')<div class="text-sm text-red-400">{{ $message }}</div>@enderror
            </div>

            <div>
              <label class="block text-sm text-gray-300">City</label>
              <input type="text" wire:model.defer="city"
                     class="mt-1 w-full rounded-lg border border-white/10 bg-white/5 px-3 py-2">
              @error('city')<div class="text-sm text-red-400">{{ $message }}</div>@enderror
            </div>
          </div>

          <div>
            <label class="block text-sm text-gray-300">Address</label>
            <textarea rows="3" wire:model.defer="address"
                      class="mt-1 w-full rounded-lg border border-white/10 bg-white/5 px-3 py-2"></textarea>
            @error('address')<div class="text-sm text-red-400">{{ $message }}</div>@enderror
          </div>

          <div>
            <label class="block text-sm text-gray-300">Notes (optional)</label>
            <textarea rows="3" wire:model.defer="notes"
                      class="mt-1 w-full rounded-lg border border-white/10 bg-white/5 px-3 py-2"></textarea>
            @error('notes')<div class="text-sm text-red-400">{{ $message }}</div>@enderror
          </div>

          <div class="pt-2 flex items-center justify-between">
           <a href="{{ route('products.index') }}"
   class="text-sm text-gray-300 hover:text-white">
  ← Continue shopping
</a>


            <button type="submit"
                    class="rounded-lg bg-red-600 px-5 py-2.5 font-semibold hover:bg-red-500">
              Place Order
            </button>
          </div>

        </form>
      @endif
    </div>
  </div>

