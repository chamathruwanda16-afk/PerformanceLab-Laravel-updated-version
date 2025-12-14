@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto p-6 bg-gray-900 rounded-lg text-white">

    <h2 class="text-2xl font-bold mb-6">Security Settings</h2>

    {{-- Enable / Disable 2FA --}}
    @if (auth()->user()->two_factor_secret)
        <div class="mb-4">
            <p class="text-green-400">Two-factor authentication is ENABLED.</p>

            <form method="POST" action="{{ route('two-factor.disable') }}">
                @csrf
                @method('DELETE')
                <button class="bg-red-600 px-4 py-2 rounded mt-3">Disable 2FA</button>
            </form>
        </div>

        {{-- Recovery Codes --}}
        <div class="mb-4">
            <h3 class="font-semibold">Recovery Codes</h3>
            <pre class="bg-black p-3 rounded mt-2 text-sm">
@foreach (json_decode(decrypt(auth()->user()->two_factor_recovery_codes), true) as $code)
{{ $code }}
@endforeach
            </pre>

            <form method="POST" action="{{ route('two-factor.recovery') }}">
                @csrf
                <button class="bg-blue-600 px-4 py-2 rounded mt-3">Regenerate Codes</button>
            </form>
        </div>

    @else
        <p class="text-yellow-400 mb-4">Two-factor authentication is DISABLED.</p>

        <form method="POST" action="{{ route('two-factor.enable') }}">
            @csrf
            <button class="bg-green-600 px-4 py-2 rounded">Enable 2FA</button>
        </form>
    @endif

</div>
@endsection
