<footer class="mt-20 border-t border-white/10 bg-[#0b0e12] text-gray-300">
  <div class="max-w-7xl mx-auto px-6 py-12">
    <div class="grid gap-10 md:grid-cols-3 lg:grid-cols-4">

      {{-- Brand --}}
      <div class="md:col-span-1">
        <a href="{{ route('home') }}" class="inline-flex items-center gap-3">
          {{-- <img src="{{ asset('images/logo.png') }}" class="h-8 w-auto" alt="Performance Lab"> --}}
          <span class="text-xl font-extrabold tracking-wider">PERFORMANCE</span>
        </a>
        <p class="mt-3 text-sm text-gray-400 max-w-sm">
          Premium JDM performance parts. Engineered for power, built for precision.
        </p>

        <div class="mt-5 flex items-center gap-3">
          <a href="#" class="p-2 rounded-lg bg-white/5 hover:bg-white/10" aria-label="Instagram">
            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M7 2h10a5 5 0 0 1 5 5v10a5 5 0 0 1-5 5H7a5 5 0 0 1-5-5V7a5 5 0 0 1 5-5Zm0 2a3 3 0 0 0-3 3v10a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3V7a3 3 0 0 0-3-3H7Zm5 3a5 5 0 1 1 0 10 5 5 0 0 1 0-10Zm0 2.2a2.8 2.8 0 1 0 0 5.6 2.8 2.8 0 0 0 0-5.6Zm5.4-.9a1.1 1.1 0 1 1 0 2.2 1.1 1.1 0 0 1 0-2.2Z"/></svg>
          </a>
          <a href="#" class="p-2 rounded-lg bg-white/5 hover:bg-white/10" aria-label="Facebook">
            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M13.4 22v-8h2.7l.4-3h-3.1V8.4c0-.9.2-1.5 1.5-1.5H16V4.2c-.7-.1-1.5-.2-2.2-.2-2.2 0-3.8 1.3-3.8 3.9V11H8v3h2v8h3.4Z"/></svg>
          </a>
          <a href="#" class="p-2 rounded-lg bg-white/5 hover:bg-white/10" aria-label="YouTube">
            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23.5 6.2a3.1 3.1 0 0 0-2.2-2.2C19.3 3.5 12 3.5 12 3.5s-7.3 0-9.3.5A3.1 3.1 0 0 0 .5 6.2 32.6 32.6 0 0 0 0 12a32.6 32.6 0 0 0 .5 5.8 3.1 3.1 0 0 0 2.2 2.2c2 .5 9.3.5 9.3.5s7.3 0 9.3-.5a3.1 3.1 0 0 0 2.2-2.2A32.6 32.6 0 0 0 24 12a32.6 32.6 0 0 0-.5-5.8ZM9.6 15.5V8.5l6.2 3.5-6.2 3.5Z"/></svg>
          </a>
        </div>
      </div>

      {{-- Links --}}
      <div>
        <h3 class="text-sm font-semibold text-white/90">Links</h3>
        <ul class="mt-4 space-y-2 text-sm">
          <li><a class="hover:text-white" href="{{ route('home') }}">Home</a></li>
          <li><a class="hover:text-white" href="{{ route('products.index') }}">Products</a></li>
          <li><a class="hover:text-white" href="{{ url('/about') }}">About</a></li>
          @auth
            <li><a class="hover:text-white" href="{{ route('account.index') }}">My Account</a></li>
          @endauth
        </ul>
      </div>

      {{-- Help --}}
      <div>
        <h3 class="text-sm font-semibold text-white/90">Help</h3>
        <ul class="mt-4 space-y-2 text-sm">
          <li><a class="hover:text-white" href="#">Shipping &amp; Returns</a></li>
          <li><a class="hover:text-white" href="#">Warranty</a></li>
          <li><a class="hover:text-white" href="#">FAQ</a></li>
          <li><a class="hover:text-white" href="mailto:support@performancelab.test">Contact</a></li>
        </ul>
      </div>

      {{-- Contact --}}
      <div>
        <h3 class="text-sm font-semibold text-white/90">Contact</h3>
        <ul class="mt-4 space-y-2 text-sm">
          <li class="text-gray-400">Mon–Fri 9:00–18:00</li>
          <li><a href="tel:+94771234567" class="hover:text-white">+94 77 123 4567</a></li>
          <li><a href="mailto:support@performancelab.test" class="hover:text-white">support@performancelab.test</a></li>
          <li class="text-gray-400">Colombo, Sri Lanka</li>
        </ul>
      </div>

    </div>

    <div class="mt-10 flex flex-col md:flex-row items-center justify-between gap-4 text-sm text-gray-400">
      <p>© {{ now()->year }} Performance Lab. All rights reserved.</p>
      <div class="flex items-center gap-4">
        <a href="#" class="hover:text-white">Privacy</a>
        <span class="opacity-30">•</span>
        <a href="#" class="hover:text-white">Terms</a>
        <span class="opacity-30">•</span>
        <a href="#" class="hover:text-white">Cookies</a>
      </div>
    </div>
  </div>
</footer>
