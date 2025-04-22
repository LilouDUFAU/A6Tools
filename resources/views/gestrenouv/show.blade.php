@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto my-4 py-8 px-6 bg-white shadow-md rounded-lg">
    <h1 class="text-3xl font-extrabold text-gray-800 mb-8">Détails du PCRenouv</h1>

    <div class="border-l-4 border-green-600 pl-4 mb-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">Commande</h2>
        
        <div class="mb-4">
            <p class="text-gray-700 font-bold">Référence :</p>
            <p class="text-gray-900">{{ $pcrenouv->reference }}</p>
        </div>

        <div class="mb-4">
            <p class="text-gray-700 font-bold">Quantité :</p>
            <p class="text-gray-900">{{ $pcrenouv->stocks->first()?->pivot->quantite ?? '-' }}</p>
        </div>

        <div class="mb-4">
            <p class="text-gray-700 font-bold">Caractéristique :</p>
            <p class="text-gray-900">{{ $pcrenouv->caracteristique ?? 'N/A' }}</p>
        </div>

        <div class="mb-4">
            <p class="text-gray-700 font-bold">Type :</p>
            <p class="text-gray-900">{{ ucfirst($pcrenouv->type) }}</p>
        </div>

        <div class="mb-4">
            <p class="text-gray-700 font-bold">Statut :</p>
            <p class="text-gray-900">{{ ucfirst($pcrenouv->statut) }}</p>
        </div>
    </div>

    <div class="border-l-4 border-green-600 pl-4 mb-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">Magasin</h2>

        <div class="mb-4">
            <p class="text-gray-700 font-bold">Site :</p>
            <p class="text-gray-900">
                {{ $pcrenouv->stocks->first()?->lieux ?? 'Non renseigné' }}
            </p>
        </div>
    </div>

    <div class="flex justify-between mt-6">
        <a href="{{ route('gestrenouv.edit', $pcrenouv->id) }}" class="text-green-600 hover:underline">Modifier</a>
        <a href="{{ route('gestrenouv.index') }}" class="text-gray-600 hover:underline">Retour à la liste</a>
    </div>
</div>
@endsection
