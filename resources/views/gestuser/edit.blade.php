@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-100 py-8">
    <div class="w-full max-w-md">
        <div class="bg-white shadow-md rounded px-8 py-6">
            <div class="text-lg font-bold mb-4">{{ __('Modification d\'employé') }}</div>

            <form method="POST" action="{{ route('gestuser.update', $user->id) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label for="nom" class="block text-gray-700 text-sm font-bold mb-2">{{ __('Nom') }}</label>
                    <input id="nom" type="text" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('nom') border-red-500 @enderror" name="nom" value="{{ old('nom', $user->nom) }}" required autofocus>
                    @error('nom')
                        <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="prenom" class="block text-gray-700 text-sm font-bold mb-2">{{ __('Prénom') }}</label>
                    <input id="prenom" type="text" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('prenom') border-red-500 @enderror" name="prenom" value="{{ old('prenom', $user->prenom) }}" required>
                    @error('prenom')
                        <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="photo" class="block text-gray-700 text-sm font-bold mb-2">{{ __('Photo') }}</label>
                    <input id="photo" type="file" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('photo') border-red-500 @enderror" name="photo" accept="image/*">
                    @if($user->photo)
                        <img src="{{ asset('storage/' . $user->photo) }}" class="mt-2 w-24 h-24 object-cover rounded-full" alt="Photo actuelle">
                    @endif
                    @error('photo')
                        <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="telephone" class="block text-gray-700 text-sm font-bold mb-2">{{ __('Téléphone') }}</label>
                    <input id="telephone" type="text" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('telephone') border-red-500 @enderror" name="telephone" value="{{ old('telephone', $user->telephone) }}" required>
                    @error('telephone')
                        <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="email" class="block text-gray-700 text-sm font-bold mb-2">{{ __('Adresse e-mail') }}</label>
                    <input id="email" type="email" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('email') border-red-500 @enderror" name="email" value="{{ old('email', $user->email) }}" required>
                    @error('email')
                        <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Mot de passe facultatif en édition --}}
                <div class="mb-4">
    <label for="password" class="block text-gray-700 text-sm font-bold mb-2">{{ __('Mot de passe (laisser vide pour ne pas changer)') }}</label>
    <div class="relative">
        <input id="password" type="password" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('password') border-red-500 @enderror" name="password" autocomplete="new-password">
        <input type="checkbox" onclick="togglePasswordVisibility('password')" class="absolute right-2 top-1/2 transform -translate-y-1/2">
    </div>
    @error('password')
        <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
    @enderror
</div>

<div class="mb-4">
    <label for="password-confirm" class="block text-gray-700 text-sm font-bold mb-2">{{ __('Confirmer le mot de passe') }}</label>
    <div class="relative">
        <input id="password-confirm" type="password" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" name="password_confirmation" autocomplete="new-password">
        <input type="checkbox" onclick="togglePasswordVisibility('password-confirm')" class="absolute right-2 top-1/2 transform -translate-y-1/2">
    </div>
</div>


                <div class="mb-4">
                    <label for="service_id" class="block text-gray-700 text-sm font-bold mb-2">{{ __('Service') }}</label>
                    <select id="service_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('service_id') border-red-500 @enderror" name="service_id" required>
                        <option value="">{{ __('Sélectionnez un service') }}</option>
                        @foreach(\App\Models\Service::all() as $service)
                            <option value="{{ $service->id }}" {{ old('service_id', $user->service_id) == $service->id ? 'selected' : '' }}>{{ $service->nom }}</option>
                        @endforeach
                    </select>
                    @error('service_id')
                        <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="role_id" class="block text-gray-700 text-sm font-bold mb-2">{{ __('Rôle') }}</label>
                    <select id="role_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('role_id') border-red-500 @enderror" name="role_id" required>
                        <option value="">{{ __('Sélectionnez un rôle') }}</option>
                        @foreach(\App\Models\Role::all() as $role)
                            <option value="{{ $role->id }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>{{ $role->nom }}</option>
                        @endforeach
                    </select>
                    @error('role_id')
                        <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                    @enderror
                </div>

                                <div class="mb-4">
                    <label for="stock_id" class="block text-gray-700 text-sm font-bold mb-2">{{ __('Magasin') }}</label>
                    <select id="stock_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('stock_id') border-red-500 @enderror" name="stock_id">
                        <option value="">{{ __('Sélectionnez un magasin') }}</option>
                        @foreach ($stocks as $stock)
                            <option value="{{ $stock->id }}" {{ old('stock_id', $user->stock_id ?? '') == $stock->id ? 'selected' : '' }}>
                                {{ $stock->lieux }}
                            </option>
                        @endforeach
                    </select>
                    @error('stock_id')
                        <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between">
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        {{ __('Mettre à jour') }}
                    </button>
                    <a href="{{ route('gestuser.index') }}" class="text-gray-500 hover:text-gray-700 hover:underline py-2 px-4 focus:outline-none focus:shadow-outline">
                        {{ __('Retour') }}
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function togglePasswordVisibility(id) {
        const input = document.getElementById(id);
        input.type = input.type === 'password' ? 'text' : 'password';
    }
</script>

@endsection
