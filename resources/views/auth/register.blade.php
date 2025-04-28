@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-100 py-8">
    <div class="w-full max-w-md">
        <div class="bg-white shadow-md rounded px-8 py-6">
            <div class="text-lg font-bold mb-4">{{ __('Inscription') }}</div>

            <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data">
                @csrf

                <div class="mb-4">
                    <label for="nom" class="block text-gray-700 text-sm font-bold mb-2">{{ __('Nom') }}</label>
                    <input id="nom" type="text" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('nom') border-red-500 @enderror" name="nom" value="{{ old('nom') }}" required autocomplete="nom" autofocus>
                    @error('nom')
                        <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="prenom" class="block text-gray-700 text-sm font-bold mb-2">{{ __('Prénom') }}</label>
                    <input id="prenom" type="text" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('prenom') border-red-500 @enderror" name="prenom" value="{{ old('prenom') }}" required autocomplete="prenom">
                    @error('prenom')
                        <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="photo" class="block text-gray-700 text-sm font-bold mb-2">{{ __('Photo') }}</label>
                    <input id="photo" type="file" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('photo') border-red-500 @enderror" name="photo">
                    @error('photo')
                        <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="telephone" class="block text-gray-700 text-sm font-bold mb-2">{{ __('Téléphone') }}</label>
                    <input id="telephone" type="text" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('telephone') border-red-500 @enderror" name="telephone" value="{{ old('telephone') }}" required autocomplete="telephone">
                    @error('telephone')
                        <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="email" class="block text-gray-700 text-sm font-bold mb-2">{{ __('Adresse e-mail') }}</label>
                    <input id="email" type="email" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('email') border-red-500 @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">
                    @error('email')
                        <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="password" class="block text-gray-700 text-sm font-bold mb-2">{{ __('Mot de passe') }}</label>
                    <input id="password" type="password" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('password') border-red-500 @enderror" name="password" required autocomplete="new-password">
                    @error('password')
                        <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="password-confirm" class="block text-gray-700 text-sm font-bold mb-2">{{ __('Confirmer le mot de passe') }}</label>
                    <input id="password-confirm" type="password" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" name="password_confirmation" required autocomplete="new-password">
                </div>

                <div class="mb-4">
                    <label for="service_id" class="block text-gray-700 text-sm font-bold mb-2">{{ __('Service') }}</label>
                    <select id="service_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('service_id') border-red-500 @enderror" name="service_id" required>
                        <option value="">{{ __('Sélectionnez un service') }}</option>
                        @foreach(\App\Models\Service::all() as $service)
                            <option value="{{ $service->id }}">{{ $service->nom }}</option>
                        @endforeach
                    </select>
                    @error('service_id')
                        <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between">
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        {{ __('Inscription') }}
                    </button>
                </div>
            </form>
            </div>
        </div>
    </div>
</div>
@endsection
