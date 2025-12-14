<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
 <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <title>{{ config('app.name', 'Performance Lab') }}</title>
  @vite(['resources/css/app.css','resources/js/app.js'])
   @livewireStyles
</head>
<body class="bg-[#0a0d11] text-white min-h-screen">
  <div class="min-h-screen flex flex-col items-center justify-center px-6">
    <a href="{{ route('home') }}" class="mb-6">
      <div class="h-9 w-40 flex items-center justify-center rounded bg-white text-black font-extrabold tracking-widest">PERF</div>
    </a>
    <div class="w-full max-w-md rounded-2xl bg-white/5 border border-white/10 p-6 shadow-xl">
      {{ $slot }}
    </div>
    <p class="mt-8 text-xs text-gray-400">© {{ date('Y') }} Performance Lab</p>
  </div>
  @livewireScripts
</body>
</html>
