@extends('layouts.app')

@section('content')

    <div class="max-w-6xl mx-auto my-4 py-8 px-6 bg-white shadow-md rounded-lg">
        <h1 class="text-3xl font-extrabold text-gray-800 mb-8 text-center">Détails du PC Renouvelé</h1>

        <!-- Partie Informations Principales -->
        <div class="border-l-4 border-green-600 pl-4 mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Informations de Base</h2>

            <p><strong>Numéro de série :</strong> {{ $pcRenouv->numero_serie }}</p>
            <p><strong>Référence :</strong> {{ $pcRenouv->reference }}</p>
            <p><strong>Quantité :</strong> {{ $pcRenouv->quantite }}</p>
            <p><strong>Type :</strong> {{ $pcRenouv->type }}</p>
            <p><strong>Statut :</strong> {{ $pcRenouv->statut }}</p>
        </div>

        <div class="border-l-4 border-green-600 pl-4 mb-8">            
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Magasin</h2>
            @if($pcRenouv->stocks->isNotEmpty())
                <p><strong>Stock :</strong> {{ $pcRenouv->stocks->first()->lieux }}</p>
                <p><strong>Quantité en stock :</strong> {{ $pcRenouv->stocks->first()->pivot->quantite ?? 0 }}</p>
            @endif
        </div>

        <div class="border-l-4 border-green-600 pl-4 mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Caractéristiques Techniques</h2>
           
            <p><strong>Caractéristiques :</strong> {{ $pcRenouv->caracteristiques }}</p>
        </div>

        <!-- Partie Prêt / Location -->
        <div class="border-l-4 border-green-600 pl-4 mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Prêt / Location</h2>

            @if($pcRenouv->locprets->isNotEmpty())
                @php
                    $locpret = $pcRenouv->locprets->first();
                @endphp
                
                <p><strong>Client :</strong> 
                    @if($locpret->clients)
                        {{ $locpret->clients->nom }} {{ $locpret->clients->prenom }}
                    @else
                        Non défini
                    @endif
                </p>
                <p><strong>Date de début :</strong> {{ $locpret->date_debut }}</p>
                <p><strong>Date de retour prévue :</strong> {{ $locpret->date_retour }}</p>
                
                <div class="mt-4">
                    <a href="{{ route('locpret.show', $locpret->id) }}" class="inline-block bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">
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
                    <a href="{{ route('locpret.create', ['pc_renouv_id' => $pcRenouv->id]) }}" class="inline-block bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">
                        Créer une location / un prêt
                    </a>
                @else
                    <p class="text-yellow-800 bg-yellow-100 px-4 py-2 rounded">
                        Ce PC n'est pas disponible actuellement pour prêt ou location.
                    </p>
                @endif
            @endif
        </div>

        <!-- Boutons -->
        <div class="flex flex-col sm:flex-row justify-between mt-8 space-y-4 sm:space-y-0">
            <a href="{{ route('gestrenouv.edit', $pcRenouv->id) }}" class="text-green-600 font-medium hover:underline">Modifier</a>
            <a href="{{ route('gestrenouv.index') }}" class="text-gray-600 hover:underline">Retour à la liste</a>
        </div>
    </div>

@endsection