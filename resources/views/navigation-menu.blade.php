<nav x-data="{ open:false }" class="sticky top-0 z-50 bg-[#0a0d11]/95 backdrop-blur border-b border-white/10">
  <div class="max-w-7xl mx-auto px-6">
    <div class="flex h-16 items-center justify-between">

      {{-- Logo --}}
      <a href="{{ route('home') }}" class="flex items-center">
        <img
          src="{{ asset('images/logo.png') }}"
          alt="Performance Lab"
          class="block object-contain h-[24px] w-auto max-w-[180px]"
          style="height:24px; max-width:350px; width:auto;"
        />
      </a>

      {{-- Primary links (desktop) --}}
      <div class="hidden sm:flex items-center gap-8">
        <a href="{{ route('home') }}" class="text-sm hover:text-white {{ request()->routeIs('home') ? 'text-white' : 'text-gray-300' }}">Home</a>
        <a href="{{ route('products.index') }}" class="text-sm hover:text-white {{ request()->routeIs('products.*') ? 'text-white' : 'text-gray-300' }}">Products</a>
        <a href="#" class="text-sm text-gray-300 hover:text-white">About</a>
      </div>

      {{-- Right side (desktop) --}}
      <div class="hidden sm:flex items-center gap-3">

        @auth
          @php
              $user     = Auth::user();
              $initial  = strtoupper(mb_substr($user->name ?? $user->email, 0, 1));
              $isAdmin  = isset($user->is_admin) ? (bool) $user->is_admin
                        : (isset($user->role) ? $user->role === 'admin' : false);
              $roleText = $isAdmin ? 'admin' : 'customer';
          @endphp

          {{-- Avatar dropdown --}}
          <div x-data="{ open:false }" class="relative" x-cloak>
            <button type="button" @click="open = !open" @keydown.escape.window="open = false"
                    class="inline-flex items-center focus:outline-none">
              <span class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-emerald-600 text-white font-bold">
                {{ $initial }}
              </span>
              <svg class="ml-1 h-4 w-4 text-white/70" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
              <span class="sr-only">Open user menu</span>
            </button>

            <div x-show="open" x-transition.origin.top.right @click.outside="open = false"
                 class="absolute right-0 z-50 mt-2 w-56 rounded-xl bg-white text-slate-900 shadow-xl ring-1 ring-black/5">
              <div class="px-4 py-2.5 text-sm border-b border-slate-100">
                <div class="font-medium text-slate-900 truncate">
                  {{ $user->name ?? $user->email }}
                </div>
                <div class="text-slate-500 text-xs">({{ $roleText }})</div>
              </div>

              {{--  Show admin link using same logic as roleText --}}
              @if($isAdmin)
                <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2.5 text-sm hover:bg-slate-50">
                  Admin Dashboard
                </a>
              @endif

              <a href="{{ route('account.index') }}" class="block px-4 py-2.5 text-sm hover:bg-slate-50">
                Account
              </a>

              <form method="POST" action="{{ route('logout') }}" class="border-t border-slate-100">
                @csrf
                <button type="submit"
                        class="w-full text-left px-4 py-2.5 text-sm text-red-600 hover:bg-red-50">
                  Logout
                </button>
              </form>
            </div>
          </div>
        @else
          <a href="{{ route('login') }}" class="text-sm text-gray-300 hover:text-white">Login</a>
          <a href="{{ route('register') }}" class="rounded-lg bg-white text-black px-3 py-1.5 text-sm font-semibold hover:bg-gray-100">Register</a>
        @endauth

      </div>

      {{-- Mobile menu button --}}
      <button @click="open=!open" class="sm:hidden p-2 rounded hover:bg-white/5">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
      </button>

    </div>
  </div>

  {{-- Mobile dropdown --}}
  <div x-show="open" x-cloak class="sm:hidden border-t border-white/10">
    <div class="px-6 py-4 space-y-3">
      <a href="{{ route('home') }}" class="block text-gray-300 hover:text-white">Home</a>
      <a href="{{ route('products.index') }}" class="block text-gray-300 hover:text-white">Products</a>
      <a href="#" class="block text-gray-300 hover:text-white">About</a>

      <div class="pt-3 border-t border-white/10">
        @auth
          @php
              $user     = Auth::user();
              $isAdmin  = isset($user->is_admin) ? (bool) $user->is_admin
                        : (isset($user->role) ? $user->role === 'admin' : false);
              $roleText = $isAdmin ? 'admin' : 'customer';
          @endphp

          <div class="text-sm text-gray-400 mb-2">
            <div class="font-semibold text-white">{{ $user->name ?? $user->email }}</div>
            <div>({{ $roleText }})</div>
          </div>

          {{--  Same fix for mobile --}}
          @if($isAdmin)
            <a href="{{ route('admin.dashboard') }}" class="block text-gray-300 hover:text-white">
              Admin Dashboard
            </a>
          @endif

          <a href="{{ route('account.index') }}" class="block text-gray-300 hover:text-white">Account</a>

          <form method="POST" action="{{ route('logout') }}" class="mt-2">
            @csrf
            <button class="w-full text-left text-red-400 hover:text-red-300">Logout</button>
          </form>
        @else
          <a href="{{ route('login') }}" class="block text-gray-300 hover:text-white">Login</a>
          <a href="{{ route('register') }}" class="block text-gray-300 hover:text-white">Register</a>
        @endauth
      </div>
    </div>
  </div>
</nav>
