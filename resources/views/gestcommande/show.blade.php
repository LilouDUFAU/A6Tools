@extends('layouts.app')

@section('content')

    <div class="max-w-6xl mx-auto my-4 py-8 px-6 bg-white shadow-md rounded-lg">
        <h1 class="text-3xl font-extrabold text-gray-800 mb-8 text-center">Récapitulatif de la commande</h1>

        <!-- Partie Commande -->
        <div class="border-l-4 border-green-600 pl-4 mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Commande</h2>
            <p><strong>Numéro de commande fournisseur :</strong> {{ $commande->numero_commande_fournisseur ?? '/' }}</p>
            <p><strong>N° de devis/commande ou BL client :</strong> {{ $commande->doc_client ?? '/' }}</p>
            <p><strong>État :</strong> {{ $commande->etat ?? '/' }}</p>
            <p><strong>Remarque :</strong> {{ $commande->remarque ?? '/' }}</p>
            <p><strong>Délai d'installation prévu (en jours) :</strong> {{ $commande->delai_installation ?? '/' }}</p>
            <p><strong>Date d'installation prévue :</strong> {{ $commande->date_installation_prevue ?? '/' }}</p>
            <p><strong>Référence devis :</strong> {{ $commande->reference_devis ?? '/' }}</p>
            <p><strong>Urgence :</strong> {{ $commande->urgence ?? '/' }}</p>
        </div>

        <!-- Partie Client -->
        <div class="border-l-4 border-green-600 pl-4 mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Client</h2>
            @if($commande->client)
                <p><strong>Nom :</strong> {{ $commande->client->nom }}</p>
                <p><strong>Code client :</strong> {{ $commande->client->code_client ?? '/' }}</p>
                <p><strong>Numéro téléphone :</strong> {{ $commande->client->numero_telephone ?? '/' }}</p>
            @else
                <p>Aucun client associé.</p>
            @endif
        </div>

        <!-- Partie Site -->
        <div class="border-l-4 border-green-600 pl-4 mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Magasin</h2>
            @php
                $lieuxStockCommande = DB::table('produit_stock')
                    ->join('stocks', 'produit_stock.stock_id', '=', 'stocks.id')
                    ->where('produit_stock.commande_id', $commande->id)
                    ->distinct()
                    ->pluck('stocks.lieux');
            @endphp

            @if($lieuxStockCommande && $lieuxStockCommande->isNotEmpty())
                <ul class="list-disc pl-5">
                    @foreach($lieuxStockCommande as $lieu)
                        <li><strong>Site :</strong> {{ $lieu ?? '/' }}</li>
                    @endforeach
                </ul>
            @else
                <p>Aucun site associé à cette commande.</p>
            @endif
        </div>

        <!-- Partie Produit -->
        <div class="border-l-4 border-green-600 pl-4 mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Produits</h2>
            @forelse($commande->produits as $produit)
                <div class="bg-gray-50 p-4 rounded-lg shadow mb-6">
                    <h3 class="text-xl font-bold underline mb-2 uppercase"> {{ $produit->nom ?? '/' }}</h3>
                    <p><strong>Référence :</strong> {{ $produit->reference ?? '/' }}</p>
                    <p><strong>Quantité totale :</strong> {{ $produit->pivot->quantite_totale ?? '0' }}</p>
                    <p><strong>Quantité en stock :</strong> {{ $produit->pivot->quantite_stock ?? '0' }}</p>
                    <p><strong>Quantité client :</strong> {{ $produit->pivot->quantite_client ?? '0' }}</p>
                    <p><strong>Prix de référencement :</strong> {{ $produit->prix_referencement ?? '0' }} €</p>
                    <p><strong>Lien produit fournisseur :</strong>
                        @if($produit->lien_produit_fournisseur)
                            <a href="{{ $produit->lien_produit_fournisseur }}" class="text-blue-600 hover:underline" target="_blank">Voir le produit</a>
                        @else
                            /
                        @endif
                    </p>
                    <p><strong>Date livraison fournisseur :</strong> {{ $produit->date_livraison_fournisseur ?? '/' }}</p>
                </div>
            @empty
                <p>Aucun produit associé à cette commande.</p>
            @endforelse
        </div>

        <!-- Partie Fournisseur -->
        <div class="border-l-4 border-green-600 pl-4 mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Fournisseur</h2>
            @foreach($commande->produits as $produit)
                @php
                    $fournisseurProduitCommande = DB::table('fournisseur_produit')
                        ->join('fournisseurs', 'fournisseur_produit.fournisseur_id', '=', 'fournisseurs.id')
                        ->where('fournisseur_produit.produit_id', $produit->id)
                        ->where('fournisseur_produit.commande_id', $commande->id)
                        ->select('fournisseurs.nom')
                        ->first();
                @endphp

                @if($fournisseurProduitCommande)
                    <p><strong>Fournisseur :</strong> {{ $fournisseurProduitCommande->nom ?? '/' }}</p>
                @else
                    <p class="text-red-500"><strong>Aucun fournisseur associé</strong></p>
                @endif
            @endforeach
        </div>

        <!-- Préparation Atelier -->
        @if (auth()->check() && auth()->user()->stock_id === 1)
        <div class="border-l-4 border-green-600 pl-4 mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Préparation Atelier</h2>
            @php
                $preparation = \App\Models\PrepAtelier::where('commande_id', $commande->id)->first();
            @endphp

            @if($preparation)
                <div class="card mb-4">
                    <div class="card-body">
                    <p><strong>ID :</strong> {{ $preparation->id }}</p>
                    @php
                        $technicien = \App\Models\User::find($preparation->employe_id);
                    @endphp
                    <p><strong>Technicien :</strong> {{ $technicien ? $technicien->prenom : '/' }} {{ $technicien ? $technicien->nom : '/' }}</p>
                        <p><strong>Voir la préparation :</strong>
                            <a href="{{ route('gestatelier.show', $preparation->id) }}" class="text-blue-600 hover:underline">Voir la Préparation</a>
                        </p>
                    </div>
                </div>
            @else
                <p>Aucune préparation liée à cette commande.</p>
                @if($commande->client)
                    <a href="{{ route('gestatelier.create', ['commande_id' => $commande->id]) }}" class="text-blue-600 font-medium hover:underline">Créer une Préparation</a>
                @endif
            @endif
        </div>
        @endif

        <!-- Boutons -->
        <div class="flex flex-col sm:flex-row justify-between mt-8 space-y-4 sm:space-y-0">
            <a href="{{ route('gestcommande.edit', $commande->id) }}" class="text-green-600 font-medium hover:underline">Modifier</a>
            <a href="{{ route('gestcommande.index') }}" class="text-gray-600 hover:underline">Retour à la liste</a>
        </div>
    </div>
@endsection
