<x-guest-layout>
  <h1 class="text-xl font-extrabold">Create account</h1>

  <x-validation-errors class="mt-4" />

  <form method="POST" action="{{ route('register') }}" class="mt-6 space-y-4">
    @csrf
    <div>
      <x-label class="text-gray-300" for="name" value="Name" />
      <x-input id="name" class="block mt-1 w-full bg-white/5 border-white/10 text-white" type="text" name="name" required autofocus autocomplete="name" />
    </div>

    <div>
      <x-label class="text-gray-300" for="email" value="Email" />
      <x-input id="email" class="block mt-1 w-full bg-white/5 border-white/10 text-white" type="email" name="email" required />
    </div>

    <div>
      <x-label class="text-gray-300" for="password" value="Password" />
      <x-input id="password" class="block mt-1 w-full bg-white/5 border-white/10 text-white" type="password" name="password" required autocomplete="new-password" />
    </div>

    <div>
      <x-label class="text-gray-300" for="password_confirmation" value="Confirm Password" />
      <x-input id="password_confirmation" class="block mt-1 w-full bg-white/5 border-white/10 text-white" type="password" name="password_confirmation" required autocomplete="new-password" />
    </div>

    @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
    <div class="text-sm text-gray-300">
      <x-label for="terms">
        <div class="flex items-center">
          <x-checkbox name="terms" id="terms" required />
          <div class="ms-2">
            I agree to the <a target="_blank" href="{{ route('terms.show') }}" class="underline">Terms</a> & <a target="_blank" href="{{ route('policy.show') }}" class="underline">Privacy</a>
          </div>
        </div>
      </x-label>
    </div>
    @endif

    <button class="w-full rounded-xl bg-white text-black py-2.5 font-semibold hover:bg-gray-100">Create account</button>
  </form>

  <p class="mt-4 text-sm text-gray-300">Already have an account?
    <a href="{{ route('login') }}" class="underline hover:text-white">Sign in</a>
  </p>
</x-guest-layout>
