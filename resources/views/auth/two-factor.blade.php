<x-app-layout>
  <div class="max-w-md mx-auto mt-10 bg-white/5 border border-white/10 rounded-2xl p-6">
    <h1 class="text-2xl font-bold mb-4 text-white">Two-Factor Verification</h1>

    {{-- ✅ Success message (your controller uses 'success') --}}
    @if (session('success'))
      <div class="mb-4 rounded-lg bg-green-600/20 text-green-200 px-4 py-3">
        {{ session('success') }}
      </div>
    @endif

    {{-- Optional: some Laravel features use 'status' --}}
    @if (session('status'))
      <div class="mb-4 rounded-lg bg-green-600/20 text-green-200 px-4 py-3">
        {{ session('status') }}
      </div>
    @endif

    {{-- ✅ Show any errors (ex: Mail sending fails) --}}
    @if ($errors->any())
      <div class="mb-4 rounded-lg bg-red-600/20 text-red-200 px-4 py-3">
        {{ $errors->first() }}
      </div>
    @endif

    <p class="text-sm text-gray-300 mb-4">
      For extra security, please verify this login with a one-time code.
    </p>

    {{-- Send code --}}
    <form method="POST" action="{{ route('twofactor.send') }}" class="mb-4">
      @csrf
      <button type="submit"
              class="w-full rounded-lg bg-gray-800 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-700">
        Send verification code to my email
      </button>
    </form>

    {{-- Verify code --}}
    <form method="POST" action="{{ route('twofactor.verify') }}" class="space-y-3">
      @csrf

      <div>
        <label class="block text-sm text-gray-300">Enter 6-digit code</label>
        <input type="text" name="code"
               class="mt-1 w-full rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-white">
        @error('code')
          <div class="text-sm text-red-400 mt-1">{{ $message }}</div>
        @enderror
      </div>

      <button type="submit"
              class="w-full rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-500">
        Verify & Continue
      </button>
    </form>
  </div>
</x-app-layout>
