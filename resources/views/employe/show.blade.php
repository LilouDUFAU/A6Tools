@extends('layouts.app')

@section('content')
<div class="min-h-screen py-10 flex items-center justify-center bg-gray-100">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white shadow rounded-2xl py-8 px-16">
            @if(auth()->user()->id === $user->id)
                <h2 class="text-2xl font-bold mb-6 text-gray-800">Mon compte</h2>
            @else
                <h2 class="text-2xl font-bold mb-6 text-gray-800">Détails de l'utilisateur</h2>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p class="text-sm text-gray-500">Nom :</p>
                    <p class="text-lg text-gray-900 font-semibold">{{ $user->nom }}</p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">Prénom :</p>
                    <p class="text-lg text-gray-900 font-semibold">{{ $user->prenom }}</p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">Téléphone :</p>
                    <p class="text-lg text-gray-900 font-semibold">{{ $user->telephone }}</p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">Email :</p>
                    <p class="text-lg text-gray-900 font-semibold">{{ $user->email }}</p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">Service :</p>
                    <p class="text-lg text-gray-900 font-semibold">{{ $user->service->nom ?? 'N/A' }}</p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">Rôle :</p>
                    <p class="text-lg text-gray-900 font-semibold">{{ $user->role->nom ?? 'N/A' }}</p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">Photo :</p>
                    @if($user->photo)
                        <img src="{{ asset('storage/' . $user->photo) }}" alt="Photo de {{ $user->nom }}" class="mt-2 w-32 h-32 object-cover rounded-full shadow">
                    @else
                        <p class="text-gray-600 italic">Pas de photo</p>
                    @endif
                </div>
            </div>

            <div class="mt-8">
                <a href="{{ route('employe.index') }}" class="inline-block px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold rounded-xl transition">← Retour</a>
            </div>
        </div>
    </div>
</div>
@endsection
