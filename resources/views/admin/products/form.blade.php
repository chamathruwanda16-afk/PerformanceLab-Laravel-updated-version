<x-app-layout>
  <div class="max-w-3xl mx-auto px-6 py-10">
    <h1 class="text-3xl font-extrabold">
      {{ $mode === 'create' ? 'Add Product' : 'Edit Product' }}
    </h1>

    
    <form class="mt-6 space-y-4"
          action="{{ $mode==='create' ? route('admin.products.store') : route('admin.products.update', $product) }}"
          method="POST"
          enctype="multipart/form-data">
      @csrf
      @if($mode==='edit') @method('PUT') @endif

      <div>
        <label class="block text-sm text-gray-300">Name</label>
        <input name="name" value="{{ old('name', $product->name) }}"
               class="mt-1 w-full rounded-lg border border-white/10 bg-white/5 px-3 py-2">
        @error('name')<div class="text-sm text-red-400">{{ $message }}</div>@enderror
      </div>

      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-sm text-gray-300">Slug (optional)</label>
          <input name="slug" value="{{ old('slug', $product->slug) }}"
                 class="mt-1 w-full rounded-lg border border-white/10 bg-white/5 px-3 py-2">
          @error('slug')<div class="text-sm text-red-400">{{ $message }}</div>@enderror
        </div>

        <div>
          <label class="block text-sm text-gray-300">Category</label>
          <select name="category_id"
                  class="mt-1 w-full rounded-lg border border-white/10 bg-white/5 px-3 py-2">
            <option value="">— None —</option>
            @foreach($categories as $c)
              <option value="{{ $c->id }}" @selected(old('category_id',$product->category_id)==$c->id)>{{ $c->name }}</option>
            @endforeach
          </select>
        </div>
      </div>

      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-sm text-gray-300">Price</label>
          <input type="number" step="0.01" name="price" value="{{ old('price', $product->price) }}"
                 class="mt-1 w-full rounded-lg border border-white/10 bg-white/5 px-3 py-2">
        </div>
        <div>
          <label class="block text-sm text-gray-300">Stock</label>
          <input type="number" name="stock" value="{{ old('stock', $product->stock) }}"
                 class="mt-1 w-full rounded-lg border border-white/10 bg-white/5 px-3 py-2">
        </div>
      </div>

      {{--  Livewire Image Upload  --}}
      @livewire('admin.product-image-preview', [
          'existingImage' => ($mode === 'edit' && $product->image_path) ? $product->image_url : null
      ])

      <div>
        <label class="block text-sm text-gray-300">Description</label>
        <textarea name="description" rows="5"
                  class="mt-1 w-full rounded-lg border border-white/10 bg-white/5 px-3 py-2">{{ old('description', $product->description) }}</textarea>
      </div>

      <div class="pt-2">
        <button class="rounded-lg bg-red-600 px-5 py-2.5 font-semibold hover:bg-red-500">
          {{ $mode==='create' ? 'Create' : 'Save changes' }}
        </button>

        <a href="{{ route('admin.products.index') }}"
           class="ml-2 rounded-lg border border-white/20 bg-white/10 px-5 py-2.5 hover:bg-white/20">Cancel</a>
      </div>
    </form>
  </div>
</x-app-layout>
