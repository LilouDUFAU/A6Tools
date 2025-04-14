@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-6 px-4">
    <h1 class="text-2xl font-bold mb-4">Détails de la commande</h1>

    <div class="bg-white shadow rounded p-6 space-y-4">
        <p><strong>Intitulé :</strong> {{ $commande->intitule }}</p>
        <p><strong>Client :</strong> {{ $commande->client?->nom }}</p>
        <p><strong>Employé :</strong> {{ $commande->employe?->name }}</p>
        <p><strong>Prix total :</strong> {{ $commande->prix_total }} €</p>
        <p><strong>État :</strong> {{ $commande->etat }}</p>
        <p><strong>Remarque :</strong> {{ $commande->remarque }}</p>
        <p><strong>Urgence :</strong> {{ $commande->urgence ? 'Oui' : 'Non' }}</p>
        <p><strong>Date de livraison fournisseur :</strong> {{ $commande->date_livraison_fournisseur }}</p>
        <p><strong>Date d'installation prévue :</strong> {{ $commande->date_installation_prevue }}</p>
    </div>

    <div class="mt-6">
        <a href="{{ route('commande.edit', $commande->id) }}" class="text-blue-600 hover:underline mr-4">Modifier</a>
        <a href="{{ route('commande.index') }}" class="text-gray-600 hover:underline">Retour à la liste</a>
    </div>
</div>
@endsection
