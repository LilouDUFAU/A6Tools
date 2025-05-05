@extends('layouts.app')

@section('content')

<div class="min-h-screen">
<div class="max-w-6xl mx-auto my-4 py-8 px-6 bg-white shadow-md rounded-lg">
    <h1 class="text-3xl font-extrabold text-gray-800 mb-8">Détails de la Panne</h1>

    <div class="space-y-6">
        <!-- Partie Panne -->
        <div class="border-l-4 border-green-600 pl-4">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Panne</h2>

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

        <div class="border-l-4 border-green-600 pl-4 mt-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Historique des Actions</h2>
            @if($panne->actions->isNotEmpty())
            <div class="space-y-6">
                @foreach($panne->actions->sortBy('created_at') as $action)
                <div class="p-4 bg-gray-100 rounded-lg shadow-md">
                    <h3 class="text-lg font-semibold text-gray-700 justify-between flex uppercase">
                        {{ $action->intitule }}
                        <span class="rounded-full border px-2 py-1 text-base normal-case
                            @if($action->statut === 'A faire') bg-red-600 bg-opacity-75 text-white 
                            @elseif($action->statut === 'En cours') bg-yellow-600 bg-opacity-75 text-white 
                            @elseif($action->statut === 'Terminé') bg-green-600 bg-opacity-75 text-white 
                            @endif">
                            {{ $action->statut }}
                        </span>
                    </h3>
                    <p class="text-sm text-gray-600">
                    <strong>Posté par :</strong>
                    {{ $action->employe->prenom ?? 'Utilisateur' }}
                    {{ $action->employe->nom ?? '' }}
                    </p>
                    <p class="text-sm text-gray-600">
                    <strong>Créé le :</strong>
                    {{ $action->created_at->format('d/m/Y H:i') }}
                    </p>
                    @if($action->updated_at->ne($action->created_at))
                    <p class="text-sm text-gray-600">
                        <strong>Modifié le :</strong>
                        {{ $action->updated_at->format('d/m/Y H:i') }}
                    </p>
                    @endif
                </div>
                @endforeach
            </div>
            @else
            <p class="mt-2 text-gray-800">Aucune action associée</p>
            @endif
        </div>

        <!-- Boutons -->
        <div class="flex justify-between mt-8">
            <a href="{{ route('panne.edit', $panne->id) }}" class="text-green-600 font-medium hover:underline">Modifier</a>
            <a href="{{ route('panne.index') }}" class="text-gray-600 hover:underline">Retour à la liste</a>
        </div>
    </div>
</div>
</div>
@endsection
