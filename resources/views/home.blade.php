<x-app-layout>
  {{-- HERO --}}
  <section class="bg-[#0b0e12]">
    <div class="max-w-7xl mx-auto px-6 py-16 lg:py-24 grid lg:grid-cols-2 gap-12 items-center">
      <div>
        <h1 class="text-5xl md:text-6xl font-extrabold leading-tight">Unleash<br/>Your Machine</h1>
        <p class="mt-5 text-gray-300 max-w-xl">Premium JDM performance parts. Engineered for power, built for precision.</p>
        <div class="mt-8">
          <a href="{{ route('products.index') }}" class="inline-flex items-center rounded-xl bg-red-600 px-5 py-3 text-sm font-semibold hover:bg-red-500">
            Shop Now
          </a>
        </div>
      </div>

      <div class="relative">
        <div class="aspect-[6/3] w-full rounded-3xl bg-gradient-to-br from-white/10 to-white/0 border border-white/10 flex items-center justify-center overflow-hidden">
          @php
            $heroImg = 'mark2.jpeg'; 
            $heroSrc = $heroImg
              ? asset('images/'.ltrim($heroImg,'/'))
              : asset('images/placeholder.jpg');
          @endphp
          <img src="{{ $heroSrc }}" alt="Hero" class="h-64 md:h-80 w-auto object-contain" loading="lazy" decoding="async">
        </div>
      </div>
    </div>
  </section>

 {{-- BEST SELLERS --}}
<section class="max-w-7xl mx-auto px-6 py-14">
  <h2 class="text-3xl font-extrabold text-red-400">Best Sellers</h2>

  <div class="mt-6 grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($bestSellers as $p)
      <a href="{{ route('products.show', $p->slug) }}"
         class="group rounded-2xl overflow-hidden bg-white/5 border border-white/10 hover:border-white/20">
        <div class="aspect-[4/3] overflow-hidden">
          <img src="{{ $p->image_url }}" alt="{{ $p->name }}"
               class="w-full h-full object-cover group-hover:scale-105 transition" loading="lazy" decoding="async">
        </div>
        <div class="p-4">
          <div class="font-semibold">{{ $p->name }}</div>
          <div class="text-sm text-gray-300 mt-1">Rs. {{ number_format($p->price, 2) }}</div>
        </div>
      </a>
    @empty
      <p class="text-gray-300">No products yet.</p>
    @endforelse
  </div>
</section>


  {{-- CATEGORIES --}}
  <section class="max-w-7xl mx-auto px-6 pb-20">
    <h2 class="text-3xl font-extrabold">Shop by category</h2>

    <div class="mt-6 grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
      @foreach(($categories ?? [
          ['name'=>'Turbo Chargers','image'=>'borg.jpg','slug'=>'turbochargers'],
          ['name'=>'Wheels & Tires','image'=>'wheels.webp','slug'=>'wheels'],
          ['name'=>'Universal Parts','image'=>'gearnob.jpg','slug'=>'universal-parts'],
          ['name'=>'Accessories','image'=>'ecu.jpeg','slug'=>'accessories'],
      ]) as $c)

        @php
          // support both arrays and Eloquent models
          $name = is_array($c) ? ($c['name'] ?? '') : ($c->name ?? '');
          $img  = is_array($c) ? ($c['image'] ?? null) : ($c->image_path ?? $c->image ?? null);
          $slug = is_array($c) ? ($c['slug'] ?? null) : ($c->slug ?? null);

          $src = $img
            ? (\Illuminate\Support\Str::startsWith($img, ['http://','https://'])
                ? $img
                : asset('images/'.ltrim($img,'/')))
            : asset('images/placeholder.jpg');

          $href = $slug ? route('products.index', ['category' => $slug]) : '#';
        @endphp

        <a href="{{ $href }}" class="rounded-2xl bg-white/5 border border-white/10 overflow-hidden block hover:border-white/20">
          <div class="aspect-video">
            <img src="{{ $src }}" class="w-full h-full object-cover" alt="{{ $name }}" loading="lazy" decoding="async">
          </div>
          <div class="p-4 font-medium">{{ $name }}</div>
        </a>
      @endforeach
    </div>
  </section>
</x-app-layout>
