@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-6 px-4 min-h-screen">
    <h1 class="text-2xl font-bold mb-4">Détails de la commande</h1>

    <div class="bg-white shadow rounded p-6 space-y-4">
        <p><strong>Intitulé :</strong> {{ $commande->intitule ?? '/' }}</p>
        <p><strong>Client :</strong> {{ $commande->client?->nom ?? '/' }}</p>
        <p><strong>Telephone client :</strong> {{ $commande->client?->telephone ?? '/' }}</p>
        <p><strong>Email client :</strong> {{ $commande->client?->email ?? '/' }}</p>
        <p><strong>Employé :</strong> {{ $commande->employe?->prenom ?? '/' }} {{ $commande->employe?->nom ?? '/' }}</p>
        <p><strong>Prix total :</strong> {{ $commande->prix_total ?? '0' }} €</p>
        <p><strong>État :</strong> {{ $commande->etat ?? '/' }}</p>
        <p><strong>Remarque :</strong> {{ $commande->remarque ?? '/' }}</p>
        <p><strong>Urgence :</strong> {{ $commande->urgence ?? '/' }}</p>
        <p><strong>Date de livraison fournisseur :</strong> {{ $commande->date_livraison_fournisseur ?? '/' }}</p>
        <p><strong>Date d'installation prévue :</strong> {{ $commande->date_installation_prevue ?? '/' }}</p>
    </div>

    <div class="bg-white shadow rounded p-6 space-y-4 mt-6">
        <h2 class="text-xl font-bold mb-4">Produits de la commande</h2>
        @if($commande->produits->isNotEmpty())
            <ul class="list-disc pl-6">
                @foreach($commande->produits as $produit)
                <h3 class="text-lg font-semibold mb-2 underline">Produit {{ $loop->iteration }}</h3>
                    <li class="border-b pb-4">
                        <p><strong>Nom :</strong> {{ $produit->nom ?? '/' }}</p>
                        <p><strong>Référence :</strong> {{ $produit->reference ?? '/' }}</p>
                        <p><strong>Quantité totale :</strong> {{ $produit->pivot->quantite ?? 0 }}</p>
                        <p><strong>Quantité stock :</strong> {{ $produit->pivot->quantite_stock ?? 0 }}</p>
                        <p><strong>Quantité client :</strong> {{ $produit->pivot->quantite_client ?? 0 }}</p>
                        <p><strong>Prix unitaire :</strong> {{ $produit->prix ?? '0' }} €</p>
                        <p><strong>Lien produit fournisseur :</strong> 
                            @if(!empty($produit->lien_produit_fournisseur))
                                <a href="{{ $produit->lien_produit_fournisseur }}" target="_blank" class="text-blue-600 hover:underline">
                                    Voir le produit
                                </a>
                            @else
                                /
                            @endif
                        </p>
                        @php
                            $fournisseurProduitCommande = DB::table('fournisseur_produit')
                                ->join('fournisseurs', 'fournisseur_produit.fournisseur_id', '=', 'fournisseurs.id')
                                ->where('fournisseur_produit.produit_id', $produit->id)
                                ->where('fournisseur_produit.commande_id', $commande->id)
                                ->select('fournisseurs.nom', 'fournisseurs.email')
                                ->first();
                        @endphp

                        @if($fournisseurProduitCommande)
                            <p><strong>Fournisseur :</strong></p>
                            <ul class="list-disc pl-6">
                                <li>
                                    <p><strong>Nom :</strong> {{ $fournisseurProduitCommande->nom ?? '/' }}</p>
                                    <p><strong>Email :</strong> {{ $fournisseurProduitCommande->email ?? '/' }}</p>
                                </li>
                            </ul>
                        @else
                            <p>Aucun fournisseur associé à ce produit pour cette commande.</p>
                        @endif
                        @php
                            $stockProduitCommande = DB::table('produit_stock')
                                ->join('stocks', 'produit_stock.stock_id', '=', 'stocks.id')
                                ->where('produit_stock.commande_id', $commande->id)
                                ->where('produit_stock.produit_id', $produit->id)
                                ->select('stocks.lieux')
                                ->first();
                        @endphp

                        @if($stockProduitCommande)
                            <p><strong>Site lié à cette commande :</strong> {{ $stockProduitCommande->lieux ?? '/' }}</p>
                        @else
                            <p>Aucun site associé à ce produit pour cette commande.</p>
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
