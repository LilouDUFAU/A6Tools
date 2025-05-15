@extends('layouts.app')

@section('content')
<div class="h-full flex items-center justify-center bg-gray-100">
    <div class="w-full max-w-md">
        <div class="bg-white shadow-md rounded-lg">
            <div class="bg-gray-800 text-white text-center py-4 rounded-t-lg">
                <h2 class="text-lg font-semibold">{{ __('Confirmer le mot de passe') }}</h2>
            </div>

            <div class="p-6">
                <p class="text-gray-700 mb-4">{{ __('Veuillez confirmer votre mot de passe avant de continuer.') }}</p>

                <form method="POST" action="{{ route('password.confirm') }}">
                    @csrf

                    <div class="mb-4">
                        <label for="password" class="block text-gray-700 font-medium mb-2">{{ __('Mot de passe') }}</label>
                        <input id="password" type="password" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-green-200 focus:border-green-500 @error('password') border-red-500 @enderror" name="password" required autocomplete="current-password">

                        @error('password')
                            <span class="text-red-500 text-sm mt-1">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="flex items-center justify-between">
                        <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 focus:outline-none focus:ring focus:ring-green-200">
                            {{ __('Confirmer le mot de passe') }}
                        </button>

                        @if (Route::has('password.request'))
                            <a class="text-green-500 hover:underline text-sm" href="{{ route('password.request') }}">
                                {{ __('Mot de passe oublié ?') }}
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
