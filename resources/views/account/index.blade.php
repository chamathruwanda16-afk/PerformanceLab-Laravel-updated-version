<x-app-layout>
  <div class="max-w-5xl mx-auto px-6 py-10">
    <h1 class="text-3xl font-bold">My Account</h1>
    <p class="text-gray-400 mt-1">Welcome back, {{ $user->name }}.</p>

    {{-- Alerts --}}
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

    <div class="mt-8 rounded-xl border border-white/10 bg-white/5 p-4">
      <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold">Recent Orders</h2>
      </div>

      <div class="mt-4 overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead class="text-left text-gray-400">
            <tr>
              <th class="py-2 pr-4">Order #</th>
              <th class="py-2 pr-4">Date</th>
              <th class="py-2 pr-4">Total</th>
              <th class="py-2 pr-4">Status</th>
              <th class="py-2 pr-4">Action</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-white/10">
            @forelse ($orders as $order)
              <tr class="align-top">
                <td class="py-3 pr-4 font-semibold">#{{ $order->id }}</td>
                <td class="py-3 pr-4">{{ optional($order->created_at)->format('Y-m-d H:i') }}</td>
                <td class="py-3 pr-4">Rs. {{ number_format($order->total ?? 0, 2) }}</td>
                <td class="py-3 pr-4 capitalize">
                  <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs
                    @if($order->status === 'cancelled') bg-red-500/20 text-red-300
                    @elseif(in_array($order->status, ['pending','paid','processing'])) bg-yellow-500/20 text-yellow-300
                    @elseif(in_array($order->status, ['shipped','delivered'])) bg-green-500/20 text-green-300
                    @else bg-white/10 text-gray-300 @endif">
                    {{ $order->status }}
                  </span>
                </td>
                <tr>
  {{-- ⚠️ Set colspan to the number of columns in your table header (e.g., 5) --}}
  <td colspan="5" class="py-2 pl-4">
    @if($order->items && $order->items->count())
      <div class="mt-2 grid gap-2">
        @foreach($order->items as $it)
          <div class="flex items-center justify-between rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-sm">
            <div class="truncate">
              {{ $it->name }}
              <span class="text-gray-400">× {{ $it->qty }}</span>
            </div>
            <div class="font-semibold">
              Rs. {{ number_format($it->total ?? ($it->price * $it->qty), 2) }}
            </div>
          </div>
        @endforeach
      </div>
    @else
      <div class="text-sm text-gray-400">No items recorded for this order.</div>
    @endif
  </td>
</tr>

                <td class="py-3 pr-4">
                  @if($order->is_cancellable)
                    <form method="POST" action="{{ route('account.orders.cancel', $order) }}"
                          onsubmit="return confirm('Cancel this order?');" class="inline-block">
                      @csrf
                      @method('PATCH')
                      <button type="submit"
                        class="rounded-lg border border-red-500/40 bg-red-500/10 px-3 py-1 text-sm text-red-300 hover:bg-red-500/20">
                        Cancel
                      </button>
                    </form>
                  @else
                    <button disabled
                      class="rounded-lg border border-white/10 bg-white/5 px-3 py-1 text-sm text-gray-400 cursor-not-allowed">
                      Cancel
                    </button>
                  @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="py-6 text-center text-gray-300">You don’t have any orders yet.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div class="mt-6">
        {{ $orders->links() }}
      </div>
    </div>
  </div>
</x-app-layout>
