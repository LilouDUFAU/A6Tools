@extends('layouts.app')

@section('content')

<div class="max-w-6xl mx-auto my-4 py-8 px-6 bg-white shadow-md rounded-lg">
    <h1 class="text-3xl font-extrabold text-gray-800 mb-8">Détails de la Panne</h1>

    <div class="space-y-6">
        <!-- Partie Panne -->
        <div class="border-l-4 border-green-600 pl-4">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Panne</h2>

            <div class="mb-4">
                <label for="numero_sav" class="block text-sm font-semibold text-gray-700">Numéro SAV</label>
                <p class="mt-2 text-gray-800">{{ $panne->numero_sav }}</p>
            </div>

            <div class="mb-4">
                <label for="date_commande" class="block text-sm font-semibold text-gray-700">Date de commande</label>
                <p class="mt-2 text-gray-800">{{ $panne->date_commande }}</p>
            </div>

            <div class="mb-4">
                <label for="date_panne" class="block text-sm font-semibold text-gray-700">Date de panne</label>
                <p class="mt-2 text-gray-800">{{ $panne->date_panne }}</p>
            </div>

            <div class="mb-4">
                <label for="categorie_materiel" class="block text-sm font-semibold text-gray-700">Catégorie matériel</label>
                <p class="mt-2 text-gray-800">{{ $panne->categorie_materiel }}</p>
            </div>

            <div class="mb-4">
                <label for="categorie_panne" class="block text-sm font-semibold text-gray-700">Catégorie panne</label>
                <p class="mt-2 text-gray-800">{{ $panne->categorie_panne }}</p>
            </div>

            <div class="mb-4">
                <label for="detail_panne" class="block text-sm font-semibold text-gray-700">Détail de la panne</label>
                <p class="mt-2 text-gray-800">{{ $panne->detail_panne }}</p>
            </div>

            <div class="mb-4">
                <label for="demande" class="block text-sm font-semibold text-gray-700">Demande</label>
                <p class="mt-2 text-gray-800">{{ $panne->demande ?? 'Non spécifiée' }}</p>
            </div>

            <div class="mb-4">
                <label for="statut" class="block text-sm font-semibold text-gray-700">Statut</label>
                <p class="mt-2 text-gray-800">{{ ucfirst($panne->statut) ?? 'Non défini' }}</p>
            </div>
        </div>

        <!-- Partie Client -->
        <div class="border-l-4 border-green-600 pl-4">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Client</h2>

            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700">Client</label>
                @if($panne->clients->isNotEmpty())
                    <p class="mt-2 text-gray-800">{{ $panne->clients->first()->nom }}</p>
                @else
                    <p class="mt-2 text-gray-800">Aucun client associé</p>
                @endif
            </div>

            <div class="mb-4">
                <label for="etat" class="block text-sm font-semibold text-gray-700">État côté client</label>
                <p class="mt-2 text-gray-800">{{ ucfirst($panne->etat_client) ?? 'Non défini' }}</p>
            </div>
        </div>

        <!-- Partie Fournisseur -->
        <div class="border-l-4 border-green-600 pl-4">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Fournisseur</h2>
            
            <div class="mb-4">
                <label for="fournisseur_id" class="block text-sm font-semibold text-gray-700">Fournisseur</label>
                <p class="mt-2 text-gray-800">{{ $panne->fournisseur->nom ?? 'Non défini' }}</p>
            </div>
        </div>


        <!-- Boutons -->
        <div class="flex justify-between mt-8">
            <a href="{{ route('gestsav.edit', $panne->id) }}" class="text-green-600 font-medium hover:underline">Modifier</a>
            <a href="{{ route('gestsav.index') }}" class="text-gray-600 hover:underline">Retour à la liste</a>
        </div>
    </div>
</div>
@endsection
