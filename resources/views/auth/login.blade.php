<x-guest-layout>

  {{-- Logo --}}
  <div class="mb-6 flex justify-center">
    <a href="{{ route('home') }}">
      <img
        src="{{ asset('images/logo.png') }}"
        alt="Performance Lab"
        class="h-10 w-auto object-contain"
        style="height:40px; max-width:220px;"
      >
    </a>
  </div>

  <h1 class="text-xl font-extrabold">Sign in</h1>

  <x-validation-errors class="mt-4" />

  @session('status')
    <div class="mt-3 text-green-400 text-sm">{{ $value }}</div>
  @endsession

  <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-4">
    @csrf
    <div>
      <x-label class="text-gray-300" for="email" value="Email" />
      <x-input id="email" class="block mt-1 w-full bg-white/5 border-white/10 text-white" type="email" name="email" required autofocus />
    </div>

    <div>
      <x-label class="text-gray-300" for="password" value="Password" />
      <x-input id="password" class="block mt-1 w-full bg-white/5 border-white/10 text-white" type="password" name="password" required autocomplete="current-password" />
    </div>

    <div class="flex items-center justify-between">
      <label class="inline-flex items-center gap-2 text-sm text-gray-300">
        <x-checkbox id="remember_me" name="remember" />
        <span>Remember me</span>
      </label>

      @if (Route::has('password.request'))
        <a class="text-sm text-gray-300 hover:text-white" href="{{ route('password.request') }}">Forgot?</a>
      @endif
    </div>

    <button class="w-full rounded-xl bg-white text-black py-2.5 font-semibold hover:bg-gray-100">Sign in</button>
  </form>

<a href="{{ route('google.redirect') }}"
   class="mt-3 flex w-full items-center justify-center gap-3 rounded-lg border border-white/20 bg-white px-4 py-2.5 text-sm font-semibold text-black transition hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-white/40">
    
    <!-- Google logo -->
    <svg class="h-5 w-5" viewBox="0 0 48 48">
        <path fill="#EA4335" d="M24 9.5c3.54 0 6.7 1.22 9.19 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.05 17.74 9.5 24 9.5z"/>
        <path fill="#4285F4" d="M46.5 24c0-1.64-.15-3.22-.43-4.75H24v9h12.7c-.55 2.9-2.18 5.36-4.64 7.02l7.15 5.56C43.98 36.44 46.5 30.7 46.5 24z"/>
        <path fill="#FBBC05" d="M10.54 28.41c-.48-1.45-.76-2.99-.76-4.41s.27-2.96.76-4.41l-7.98-6.19C.92 16.46 0 20.12 0 24s.92 7.54 2.56 10.6l7.98-6.19z"/>
        <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.9-5.8l-7.15-5.56c-1.99 1.34-4.55 2.13-8.75 2.13-6.26 0-11.57-3.55-13.46-8.59l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>
    </svg>

    <span>Continue with Google</span>
</a>





  <p class="mt-4 text-sm text-gray-300">No account?
    <a href="{{ route('register') }}" class="underline hover:text-white">Create one</a>
  </p>
</x-guest-layout>
