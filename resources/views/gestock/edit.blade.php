@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto my-4 py-8 px-6 bg-white shadow-md rounded-lg">
    <h1 class="text-3xl font-extrabold text-gray-800 mb-8">Modifier la Commande</h1>

    <form action="{{ route('commande.update', $commande) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Partie Commande -->
        <div class="border-l-4 border-green-600 pl-4">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Commande</h2>

            <div class="mb-4">
                <label for="etat" class="block text-sm font-semibold text-gray-700">État</label>
                <select id="etat" name="etat" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm   px-2 py-1" required>
                    @foreach ($etats as $etat)
                        <option value="{{ $etat }}" @if($commande->etat === $etat) selected @endif>{{ ucfirst($etat) }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label for="remarque" class="block text-sm font-semibold text-gray-700">Remarque</label>
                <textarea id="remarque" name="remarque" rows="4" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm   px-2 py-1">{{ $commande->remarque }}</textarea>
            </div>

            <div class="mb-4">
                <label for="delai_installation" class="block text-sm font-semibold text-gray-700">Délai d'installation prévu (en jours)</label>
                <input type="number" id="delai_installation" name="delai_installation" value="{{ $commande->delai_installation }}" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm   px-2 py-1">
            </div>

            <div class="mb-4">
                <label for="date_installation_prevue" class="block text-sm font-semibold text-gray-700">Date d'installation prévue</label>
                <input type="date" id="date_installation_prevue" name="date_installation_prevue" value="{{ $commande->date_installation_prevue ? \Carbon\Carbon::parse($commande->date_installation_prevue)->format('Y-m-d') : '' }}" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm   px-2 py-1">
            </div>

            <div class="mb-4">
                <label for="reference_devis" class="block text-sm font-semibold text-gray-700">Référence devis de la commande</label>
                <input type="text" id="reference_devis" name="reference_devis" value="{{ $commande->reference_devis }}" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm   px-2 py-1">
            </div>

            <div class="mb-4">
                <label for="urgence" class="block text-sm font-semibold text-gray-700">Urgence</label>
                <select id="urgence" name="urgence" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm   px-2 py-1" required>
                    @foreach ($urgences as $urgence)
                        <option value="{{ $urgence }}" @if($commande->urgence === $urgence) selected @endif>{{ ucfirst($urgence) }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Partie Magasin -->
        <div class="border-l-4 border-green-600 pl-4">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Magasin</h2>
            <div class="mb-4">
                <label for="stock_id" class="block text-sm font-semibold text-gray-700">Choisir un site</label>
                @php
                    $stockProduitCommande = DB::table('produit_stock')
                        ->join('stocks', 'produit_stock.stock_id', '=', 'stocks.id')
                        ->where('produit_stock.commande_id', $commande->id)
                        ->select('stocks.id')
                        ->first();
                @endphp
                <select id="stock_id" name="stock_id" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm   px-2 py-1" required>
                    @foreach ($stocks as $stock)
                        <option value="{{ $stock->id }}" {{ $stock->id == ($stockProduitCommande->id ?? $commande->stock_id) ? 'selected' : '' }}>{{ $stock->lieux }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Partie Client -->
        <div class="border-l-4 border-green-600 pl-4">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Client</h2>
            <div class="mb-4">
            <label for="client_id" class="block text-sm font-semibold text-gray-700">Client</label>
            <select id="client_id" name="client_id" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm   px-2 py-1" onchange="fetchClientDetails(this.value)">
            @foreach ($clients as $client)
            <option value="{{ $client->id }}" @if($commande->client_id === $client->id) selected @endif>{{ $client->nom }}</option>
            @endforeach
            </select>
            </div>

            <div id="client-details" class="space-y-4 hidden">
            <div class="mb-4">
            <label for="client_nom" class="block text-sm font-semibold text-gray-700">Nom du Client</label>
            <input type="text" id="client_nom" name="new_client[nom]" value="{{ $commande->client->nom ?? '' }}" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm   px-2 py-1">
            </div>

            <div class="mb-4">
            <label for="client_code" class="block text-sm font-semibold text-gray-700">Code Client</label>
            <input type="text" id="client_code" name="new_client[code_client]" value="{{ $commande->client->code_client ?? '' }}" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm   px-2 py-1">
            </div>
            </div>

            <div class="flex justify-end">
            <button type="button" onclick="toggleClientDetails()" class="bg-green-600 text-white px-4 py-2 rounded-lg shadow hover:bg-green-700  ">Modifier le Client</button>
            </div>
        </div>

        <script>
            function fetchClientDetails(clientId) {
            // Fetch client details via AJAX
            fetch(`/clients/${clientId}`)
                .then(response => response.json())
                .then(data => {
                document.getElementById('client_nom').value = data.nom || '';
                document.getElementById('client_code').value = data.code_client || '';
                })
                .catch(error => console.error('Error fetching client details:', error));
            }

            function toggleClientDetails() {
            // Toggle visibility of client details
            const clientDetails = document.getElementById('client-details');
            if (clientDetails.classList.contains('hidden')) {
                clientDetails.classList.remove('hidden');
            } else {
                clientDetails.classList.add('hidden');
            }
            }
        </script>

        <!-- Partie Produit -->
    <div class="border-l-4 border-green-600 pl-4">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">Produit</h2>
        <div class="product-item bg-gray-50 p-4 rounded-lg shadow-sm">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                @foreach($commande->produits as $produit)
                    <div class="mb-4">
                        <label for="produit_nom" class="block text-sm font-semibold text-gray-700">Nom</label>
                        <input type="text" id="produit_nom" name="produit[nom]" value="{{ old('produit.nom', $produit->pivot->nom ?? $produit->nom) }}" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm   px-2 py-1" required>
                    </div>

                    <div class="mb-4">
                        <label for="produit_reference" class="block text-sm font-semibold text-gray-700">Référence produit</label>
                        <input type="text" id="produit_reference" name="produit[reference]" value="{{ old('produit.reference', $produit->pivot->reference ?? $produit->reference) }}" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm   px-2 py-1">
                    </div>

                    <div class="mb-4">
                        <label for="produit_prix_referencement" class="block text-sm font-semibold text-gray-700">Prix de réferencement</label>
                        <input type="number" id="produit_prix_referencement" name="produit[prix_referencement]" value="{{ old('produit.prix_referencement', $produit->pivot->prix_referencement ?? $produit->prix_referencement) }}" step="0.01" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm   px-2 py-1">
                    </div>

                    <div class="mb-4">
                        <label for="produit_lien_produit_fournisseur" class="block text-sm font-semibold text-gray-700">Lien produit fournisseur</label>
                        <input type="text" id="produit_lien_produit_fournisseur" name="produit[lien_produit_fournisseur]" value="{{ old('produit.lien_produit_fournisseur', $produit->pivot->lien_produit_fournisseur ?? $produit->lien_produit_fournisseur) }}" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm   px-2 py-1">
                    </div>

                    <div class="mb-4">
                        <label for="produit_date_livraison_fournisseur" class="block text-sm font-semibold text-gray-700">Date de Livraison Fournisseur</label>
                        <input type="date" id="produit_date_livraison_fournisseur" name="produit[date_livraison_fournisseur]" value="{{ old('produit.date_livraison_fournisseur', $produit->pivot->date_livraison_fournisseur ?? $produit->date_livraison_fournisseur ? \Carbon\Carbon::parse($produit->pivot->date_livraison_fournisseur ?? $produit->date_livraison_fournisseur)->format('Y-m-d') : '') }}" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm   px-2 py-1">
                    </div>

                    <div class="mb-4">
                        <label for="produit_quantite_totale" class="block text-sm font-semibold text-gray-700">Quantité totale</label>
                        <input type="number" id="produit_quantite_totale" name="produit[quantite_totale]" value="{{ old('produit.quantite_totale', $produit->pivot->quantite_totale ?? '') }}" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm   px-2 py-1">
                    </div>

                    <div class="mb-4">
                        <label for="produit_quantite_client" class="block text-sm font-semibold text-gray-700">Quantité Client</label>
                        <input type="number" id="produit_quantite_client" name="produit[quantite_client]" value="{{ old('produit.quantite_client', $produit->pivot->quantite_client ?? '') }}" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm   px-2 py-1">
                    </div>
                @endforeach
            </div>
        </div>
    </div>



        <!-- Partie Fournisseur -->
        <div class="border-l-4 border-green-600 pl-4">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Fournisseur</h2>
            <div class="mb-4">
            <label for="fournisseur_id" class="block text-sm font-semibold text-gray-700">Fournisseur</label>
            <select id="fournisseur_id" name="fournisseur_id" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm   px-2 py-1" onchange="fetchFournisseurDetails(this.value)">
            @foreach ($fournisseurs as $fournisseur)
            <option value="{{ $fournisseur->id }}" @if($commande->fournisseur_id === $fournisseur->id) selected @endif>{{ $fournisseur->nom }}</option>
            @endforeach
            </select>
            </div>

            <div id="fournisseur-details" class="space-y-4 hidden">
                <div class="mb-4">
                    <label for="fournisseur_nom" class="block text-sm font-semibold text-gray-700">Nom du Fournisseur</label>
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
                            <input type="text" id="fournisseur_nom" name="new_fournisseur[nom]" value="{{ $fournisseurProduitCommande->nom }}" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm   px-2 py-1">
                            @break
                        @endif
                    @endforeach
                </div>
            </div>

            <div class="flex justify-end">
            <button type="button" onclick="toggleFournisseurDetails()" class="bg-green-600 text-white px-4 py-2 rounded-lg shadow hover:bg-green-700  ">Modifier le Fournisseur</button>
            </div>

        <script>
            function fetchFournisseurDetails(fournisseurId) {
            // Fetch fournisseur details via AJAX
            fetch(`/fournisseurs/${fournisseurId}`)
            .then(response => response.json())
            .then(data => {
            document.getElementById('fournisseur_nom').value = data.nom || '';
            })
            .catch(error => console.error('Error fetching fournisseur details:', error));
            }

            function toggleFournisseurDetails() {
            // Toggle visibility of fournisseur details
            const fournisseurDetails = document.getElementById('fournisseur-details');
            if (fournisseurDetails.classList.contains('hidden')) {
            fournisseurDetails.classList.remove('hidden');
            } else {
            fournisseurDetails.classList.add('hidden');
            }
            }
        </script>
        </div>

        <button type="submit" class="w-full bg-green-600 text-white px-6 py-3 rounded-lg shadow hover:bg-green-700  ">Mettre à jour</button>
    </form>
</div>
@endsection
