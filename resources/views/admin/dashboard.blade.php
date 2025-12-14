<x-app-layout>
  <div class="max-w-7xl mx-auto px-6 py-10">
    <h1 class="text-3xl font-extrabold">Admin</h1>

    <div class="mt-6 grid sm:grid-cols-2 gap-6">

      {{-- Manage Products --}}
      <a href="{{ route('admin.products.index') }}"
         class="rounded-xl border border-white/10 bg-white/5 p-6 hover:bg-white/10 transition">
        <div class="text-xl font-semibold">Manage Products</div>
        <div class="text-gray-400 mt-1">Add, edit, delete</div>
      </a>

      {{-- Manage Orders --}}
      <a href="{{ route('admin.orders.index') }}"
         class="rounded-xl border border-white/10 bg-white/5 p-6 hover:bg-white/10 transition">
        <div class="text-xl font-semibold">Manage Orders</div>
        <div class="text-gray-400 mt-1">View & update statuses</div>
      </a>

      {{-- ✅ Analytics (MongoDB) --}}
      <a href="{{ route('admin.analytics') }}"
         class="rounded-xl border border-white/10 bg-white/5 p-6 hover:bg-white/10 transition">
        <div class="text-xl font-semibold">Analytics</div>
        <div class="text-gray-400 mt-1">
          Searches, popular queries & product views
        </div>
      </a>

    </div>
  </div>
</x-app-layout>
