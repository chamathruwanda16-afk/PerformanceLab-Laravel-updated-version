<x-app-layout>
  <div class="max-w-7xl mx-auto px-6 py-10">
    <div class="flex items-end justify-between gap-4">
      <div>
        <h1 class="text-3xl font-extrabold">Products</h1>
        <p class="text-gray-300 mt-1">Create, edit and delete products.</p>
      </div>
      <a href="{{ route('admin.products.create') }}" class="rounded-lg bg-red-600 px-4 py-2 font-semibold hover:bg-red-500">Add Product</a>
    </div>

    <form method="GET" class="mt-6">
      <input name="q" value="{{ $q }}" placeholder="Search…" class="rounded-lg border border-white/10 bg-white/5 px-3 py-2"/>
    </form>

    <div class="mt-6 overflow-x-auto rounded-xl border border-white/10">
      <table class="min-w-full text-sm">
        <thead class="bg-white/5">
          <tr>
            <th class="px-4 py-2 text-left">Name</th>
            <th class="px-4 py-2 text-left">Category</th>
            <th class="px-4 py-2 text-left">Price</th>
            <th class="px-4 py-2 text-left">Stock</th>
            <th class="px-4 py-2"></th>
          </tr>
        </thead>
        <tbody>
          @foreach($products as $p)
            <tr class="border-t border-white/10">
              <td class="px-4 py-2">{{ $p->name }}</td>
              <td class="px-4 py-2">{{ $p->category->name ?? '—' }}</td>
              <td class="px-4 py-2">Rs. {{ number_format($p->price,2) }}</td>
              <td class="px-4 py-2">{{ $p->stock ?? '—' }}</td>
              <td class="px-4 py-2 text-right space-x-2">
                <a href="{{ route('admin.products.edit', $p) }}" class="rounded border border-white/20 px-3 py-1 hover:bg-white/10">Edit</a>
                <form action="{{ route('admin.products.destroy', $p) }}" method="POST" class="inline"
                      onsubmit="return confirm('Delete this product?')">
                  @csrf @method('DELETE')
                  <button class="rounded border border-red-500/40 bg-red-500/10 px-3 py-1 text-red-300 hover:bg-red-500/20">Delete</button>
                </form>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    <div class="mt-6">{{ $products->links() }}</div>
  </div>
</x-app-layout>
