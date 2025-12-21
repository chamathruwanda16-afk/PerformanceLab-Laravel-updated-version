<div>
  <label class="block text-sm text-gray-300">Product Image</label>

  {{-- IMPORTANT: name="image" stays so the normal form submit works --}}
  <input type="file"
         name="image"
         wire:model="image"
         accept="image/*"
         class="mt-1 w-full rounded-lg border border-white/10 bg-white/5 px-3 py-2" />

  @error('image')<div class="text-sm text-red-400">{{ $message }}</div>@enderror

  <div wire:loading wire:target="image" class="text-xs text-gray-400 mt-2">
    Uploading preview...
  </div>

  <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-4 items-start">

    @if($existingImage)
      <div>
        <p class="text-sm text-gray-400 mb-1">Current Image:</p>
        <img src="{{ $existingImage }}" class="h-28 rounded border border-white/10 object-cover">
      </div>
    @endif

    <div>
      <p class="text-sm text-gray-400 mb-1">New Preview:</p>

      @if($image)
        <img src="{{ $image->temporaryUrl() }}" class="h-28 rounded border border-white/10 object-cover">
      @else
        <p class="text-xs text-gray-500 mt-1">Choose an image to preview.</p>
      @endif
    </div>

  </div>
</div>
