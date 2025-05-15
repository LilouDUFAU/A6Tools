@extends('layouts.app')

@section('content')

<div class="max-w-6xl mx-auto my-4 py-8 px-6 bg-white shadow-md rounded-lg">
    <h1 class="text-3xl font-extrabold text-gray-800 mb-8">Détails du PC Renouvelé</h1>

    <div class="space-y-6">
        {{-- Partie Stock & Caractéristiques --}}
        <div class="border-l-4 border-green-600 pl-4">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Informations principales</h2>

            @if($pcRenouv->stocks->isNotEmpty())
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700">Stock</label>
                    <p class="mt-2 text-gray-800">
                        {{ $pcRenouv->stocks->first()->lieux }}
                    </p>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700">Quantité en stock</label>
                    <p class="mt-2 text-gray-800">
                        {{ $pcRenouv->stocks->first()->pivot->quantite ?? 0 }}
                    </p>
                </div>
            @endif

            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700">Caractéristiques</label>
                <p class="mt-2 text-gray-800">{{ $pcRenouv->caracteristiques }}</p>
            </div>
        </div>

        {{-- Partie Prêt / Location --}}
        <div class="border-l-4 border-green-600 pl-4">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Prêt / Location</h2>

            @if($pcRenouv->locprets->isNotEmpty())
                @php
                    $locpret = $pcRenouv->locprets->first();
                @endphp

                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700">Client</label>
                    <p class="mt-2 text-gray-800">
                        @if($locpret->clients)
                            {{ $locpret->clients->nom }} {{ $locpret->clients->prenom }}
                        @else
                            Non défini
                        @endif
                    </p>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700">Date de début</label>
                    <p class="mt-2 text-gray-800">{{ $locpret->date_debut }}</p>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700">Date de retour prévue</label>
                    <p class="mt-2 text-gray-800">{{ $locpret->date_retour }}</p>
                </div>

                <div class="mb-4">
                    <a href="{{ route('locpret.show', $locpret->id) }}" class="inline-block bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                        Voir la location / le prêt
                    </a>
                </div>

            <form action="{{ route('locpret.retourner', $locpret->id) }}" method="POST" class="mt-4">
                @csrf
                @method('PUT')
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">
                    Marquer comme retourné
                </button>
            </form>


            @else
                @if($pcRenouv->statut == 'en stock')
                    <form action="{{ route('gestrenouv.preterLouer', $pcRenouv) }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label for="client_id" class="block text-sm font-semibold text-gray-700">Client</label>
                            <select id="client_id" name="client_id" required
                                class="w-full border border-gray-300 rounded @error('client_id') border-red-500 @enderror">
                                <option value="">Sélectionner un client</option>
                                @foreach(App\Models\Client::all() as $client)
                                    <option value="{{ $client->id }}">{{ $client->nom }} {{ $client->prenom }}</option>
                                @endforeach
                            </select>
                            @error('client_id')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex gap-4">
                            <div class="flex-1">
                                <label for="date_debut" class="block text-sm font-semibold text-gray-700">Date de début</label>
                                <input type="date" id="date_debut" name="date_debut" value="{{ date('Y-m-d') }}" required
                                    class="w-full border border-gray-300 rounded @error('date_debut') border-red-500 @enderror">
                                @error('date_debut')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="flex-1">
                                <label for="date_retour" class="block text-sm font-semibold text-gray-700">Date de retour prévue</label>
                                <input type="date" id="date_retour" name="date_retour" value="{{ date('Y-m-d', strtotime('+30 days')) }}" required
                                    class="w-full border border-gray-300 rounded @error('date_retour') border-red-500 @enderror">
                                @error('date_retour')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Type d'opération</label>
                            <div class="flex items-center mb-2">
                                <input type="radio" id="type_pret" name="type_operation" value="prêt" checked class="mr-2">
                                <label for="type_pret">Prêt</label>
                            </div>
                            <div class="flex items-center">
                                <input type="radio" id="type_location" name="type_operation" value="location" class="mr-2">
                                <label for="type_location">Location</label>
                            </div>
                            @error('type_operation')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                            Confirmer
                        </button>
                    </form>
                @else
                    <p class="text-yellow-800 bg-yellow-100 px-4 py-2 rounded">
                        Ce PC n'est pas disponible actuellement pour prêt ou location.
                    </p>
                @endif
            @endif
        </div>

        {{-- Actions --}}
        <div class="flex justify-between mt-8 border-l-4 border-green-600 pl-4">
            <a href="{{ route('gestrenouv.edit', $pcRenouv->id) }}" class="text-green-600 font-medium hover:underline">Modifier</a>
            <button type="button" class="text-red-600 font-medium hover:underline" data-bs-toggle="modal" data-bs-target="#deleteModal">
                Supprimer
            </button>
        </div>
    </div>
</div>

@endsection
