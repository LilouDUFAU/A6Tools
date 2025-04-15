@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-6 px-4 min-h-screen">
    <h1 class="text-2xl font-bold mb-4">Détails de la commande</h1>

    <div class="bg-white shadow rounded p-6 space-y-4">
        <p><strong>Intitulé :</strong> {{ $commande->intitule }}</p>
        <p><strong>Client :</strong> {{ $commande->client?->nom }}</p>
        <p><strong>Employé :</strong> {{ $commande->employe?->prenom }} {{ $commande->employe?->nom }}</p>
        <p><strong>Prix total :</strong> {{ $commande->prix_total }} €</p>
        <p><strong>État :</strong> {{ $commande->etat }}</p>
        <p><strong>Remarque :</strong> {{ $commande->remarque }}</p>
        <p><strong>Urgence :</strong> {{ $commande->urgence ? 'Oui' : 'Non' }}</p>
        <p><strong>Date de livraison fournisseur :</strong> {{ $commande->date_livraison_fournisseur }}</p>
        <p><strong>Date d'installation prévue :</strong> {{ $commande->date_installation_prevue }}</p>
    </div>

    <div class="bg-white shadow rounded p-6 space-y-4 mt-6">
        <h2 class="text-xl font-bold mb-4">Produits de la commande</h2>
        @if($commande->produits->isNotEmpty())
            <ul class="list-disc pl-6">
                @foreach($commande->produits as $produit)
                    <li class="border-b pb-4">
                        <p><strong>Nom :</strong> {{ $produit->nom }}</p>
                        <p><strong>Quantité :</strong> {{ $produit->quantite_stock }}</p>
                        <p><strong>Prix unitaire :</strong> {{ $produit->prix }} €</p>
                        @if($produit->fournisseurs->isNotEmpty())
                            <p><strong>Fournisseurs :</strong></p>
                            <ul class="list-disc pl-6">
                                @foreach($produit->fournisseurs as $fournisseur)
                                    <li>
                                        <p><strong>Nom :</strong> {{ $fournisseur->nom }}</p>
                                        <p><strong>Email :</strong> {{ $fournisseur->email }}</p>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p>Aucun fournisseur associé à ce produit.</p>
                        @endif
                    </li>
                @endforeach
            </ul>
        @else
            <p>Aucun produit associé à cette commande.</p>
        @endif
    </div>

    <div class="mt-6">
        <a href="{{ route('commande.edit', $commande->id) }}" class="text-blue-600 hover:underline mr-4">Modifier</a>
        <a href="{{ route('commande.index') }}" class="text-gray-600 hover:underline">Retour à la liste</a>
    </div>
</div>
@endsection
