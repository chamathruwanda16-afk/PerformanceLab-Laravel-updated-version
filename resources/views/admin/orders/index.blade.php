<x-app-layout>
  <div class="max-w-7xl mx-auto px-6 py-10">
    <div class="flex items-end justify-between gap-4">
      <div>
        <h1 class="text-3xl font-extrabold">Orders</h1>
        <p class="text-gray-300 mt-1">View and update customer orders.</p>
      </div>
      <form method="GET" class="flex items-center gap-2">
        <select name="status" class="rounded-lg border border-white/10 bg-white/5 px-3 py-2">
          <option value="">All statuses</option>
          @foreach(['pending','processing','paid','successful','cancelled'] as $s)
            <option value="{{ $s }}" @selected(request('status')===$s)>{{ ucfirst($s) }}</option>
          @endforeach
        </select>
        <button class="rounded-lg border border-white/20 bg-white/10 px-4 py-2 hover:bg-white/20">Filter</button>
      </form>
    </div>

    <div class="mt-6 overflow-x-auto rounded-xl border border-white/10">
      <table class="min-w-full text-sm">
        <thead class="bg-white/5">
          <tr>
            <th class="px-4 py-2 text-left">#</th>
            <th class="px-4 py-2 text-left">Customer</th>
            <th class="px-4 py-2 text-left">Subtotal</th>
            <th class="px-4 py-2 text-left">Status</th>
            <th class="px-4 py-2"></th>
          </tr>
        </thead>
        <tbody>
          @foreach($orders as $o)
            <tr class="border-t border-white/10">
              <td class="px-4 py-2"><a class="underline" href="{{ route('admin.orders.show', $o) }}">#{{ $o->id }}</a></td>
              <td class="px-4 py-2">{{ $o->user->name ?? '—' }}</td>
              <td class="px-4 py-2">Rs. {{ number_format($o->subtotal ?? $o->total ?? 0, 2) }}</td>
              <td class="px-4 py-2">{{ ucfirst($o->status ?? 'pending') }}</td>
              <td class="px-4 py-2 text-right space-x-2">
                <form action="{{ route('admin.orders.status', $o) }}" method="POST" class="inline">
  @csrf @method('PATCH')
  <select name="status"
          class="rounded border border-white/20 px-2 py-1
                 bg-white text-gray-900   
                 focus:outline-none focus:ring-2 focus:ring-white/30">
    @foreach(['pending','processing','paid','successful','cancelled'] as $s)
      <option value="{{ $s }}" @selected($o->status===$s)>{{ ucfirst($s) }}</option>
    @endforeach
  </select>
  <button class="rounded border border-white/20 bg-white/10 px-2 py-1 hover:bg-white/20">Save</button>
</form>


                @if(($o->status ?? '') !== 'cancelled')
                  <form action="{{ route('admin.orders.cancel', $o) }}" method="POST" class="inline"
                        onsubmit="return confirm('Cancel this order?')">
                    @csrf @method('PATCH')
                    <button class="rounded border border-red-500/40 bg-red-500/10 px-3 py-1 text-red-300 hover:bg-red-500/20">Cancel</button>
                  </form>
                @endif
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    <div class="mt-6">{{ $orders->links() }}</div>
  </div>
</x-app-layout>
