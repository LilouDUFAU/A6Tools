@extends('layouts.app')

@section('content')
<div class="h-full flex items-center justify-center bg-gray-100">
    <div class="w-full max-w-md">
        <div class="bg-white shadow-md rounded px-8 py-6">
            <div class="text-lg font-semibold text-gray-700 mb-4">{{ __('Réinitialiser le mot de passe') }}</div>

            <form method="POST" action="{{ route('password.update') }}">
                @csrf

                <input type="hidden" name="token" value="{{ $token }}">

                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700">{{ __('Adresse e-mail') }}</label>
                    <input id="email" type="email" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-600 focus:border-green-600 @error('email') border-red-500 @enderror" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus>
                    @error('email')
                        <p class="text-red-500 text-sm mt-1">
                            <strong>{{ $message }}</strong>
                        </p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium text-gray-700">{{ __('Mot de passe') }}</label>
                    <input id="password" type="password" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-600 focus:border-green-600 @error('password') border-red-500 @enderror" name="password" required autocomplete="new-password">
                    @error('password')
                        <p class="text-red-500 text-sm mt-1">
                            <strong>{{ $message }}</strong>
                        </p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="password-confirm" class="block text-sm font-medium text-gray-700">{{ __('Confirmer le mot de passe') }}</label>
                    <input id="password-confirm" type="password" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-600 focus:border-green-600" name="password_confirmation" required autocomplete="new-password">
                </div>

                <div class="flex items-center justify-end">
                    <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-600 focus:ring-offset-2">
                        {{ __('Réinitialiser le mot de passe') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
