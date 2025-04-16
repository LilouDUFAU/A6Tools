@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto my-4 py-8 px-6 bg-white shadow-md rounded-lg">
    <h1 class="text-3xl font-extrabold text-gray-800 mb-8">Éditer Commande</h1>

    <form action="{{ route('commande.update', $commande->id) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT') <!-- Utiliser PUT pour la mise à jour -->

        <!-- Partie Commande -->
        <div class="border-l-4 border-green-600 pl-4">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Commande</h2>
            <div class="mb-4">
                <label for="intitule" class="block text-sm font-semibold text-gray-700">Intitulé</label>
                <input type="text" id="intitule" name="intitule" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1" value="{{ old('intitule', $commande->intitule) }}" required>
            </div>

            <div class="mb-4">
                <label for="prix_total" class="block text-sm font-semibold text-gray-700">Prix total (€)</label>
                <input type="number" id="prix_total" name="prix_total" step="0.01" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1" value="{{ old('prix_total', $commande->prix_total) }}" required>
            </div>

            <div class="mb-4">
                <label for="etat" class="block text-sm font-semibold text-gray-700">État</label>
                <select id="etat" name="etat" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1" required>
                    @foreach ($etats as $etat)
                        <option value="{{ $etat }}" {{ $etat == $commande->etat ? 'selected' : '' }}>{{ ucfirst($etat) }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label for="remarque" class="block text-sm font-semibold text-gray-700">Remarque</label>
                <textarea id="remarque" name="remarque" rows="4" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1">{{ old('remarque', $commande->remarque) }}</textarea>
            </div>

            <div class="mb-4">
                <label for="date_livraison_fournisseur" class="block text-sm font-semibold text-gray-700">Date de livraison fournisseur</label>
                <input type="date" id="date_livraison_fournisseur" name="date_livraison_fournisseur" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1" value="{{ old('date_livraison_fournisseur', $commande->date_livraison_fournisseur) }}">
            </div>

            <div class="mb-4">
                <label for="date_installation_prevue" class="block text-sm font-semibold text-gray-700">Date d'installation prévue</label>
                <input type="date" id="date_installation_prevue" name="date_installation_prevue" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1" value="{{ old('date_installation_prevue', $commande->date_installation_prevue) }}">
            </div>
        </div>

        <!-- Partie Stock -->
        <div class="border-l-4 border-green-600 pl-4">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Stock</h2>
            <div class="mb-4">
                <label for="stock_id" class="block text-sm font-semibold text-gray-700">Choisir un Stock</label>
                <select id="stock_id" name="stock_id" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1" required>
                    <option value="">-- Sélectionner un stock --</option>
                    @foreach (\App\Models\Stock::all() as $stock)
                        <option value="{{ $stock->id }}" {{ $stock->id == $commande->stock_id ? 'selected' : '' }}>{{ ucfirst($stock->lieux) }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Partie Client -->
        <div class="border-l-4 border-green-600 pl-4">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Client</h2>
            <div class="mb-4">
                <label for="client_id" class="block text-sm font-semibold text-gray-700">Client</label>
                <select id="client_id" name="client_id" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1">
                    <option value="">-- Choisir un client --</option>
                    @foreach($clients as $client)
                        <option value="{{ $client->id }}" {{ $client->id == $commande->client_id ? 'selected' : '' }}>{{ $client->nom }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Partie Produits -->
        <div class="border-l-4 border-green-600 pl-4">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Produit(s)</h2>
            <div class="space-y-6 mb-4" id="product-list">
                @foreach ($commande->produits as $index => $produit)
                <div class="product-item bg-gray-50 p-4 rounded-lg shadow-sm">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="mb-4">
                            <label for="produits_{{ $index }}_nom" class="block text-sm font-semibold text-gray-700">Nom</label>
                            <input type="text" id="produits_{{ $index }}_nom" name="produits[{{ $index }}][nom]" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1" value="{{ old('produits.' . $index . '.nom', $produit->nom) }}" required>
                        </div>
                        <div class="mb-4">
                            <label for="produits_{{ $index }}_description" class="block text-sm font-semibold text-gray-700">Description</label>
                            <textarea id="produits_{{ $index }}_description" name="produits[{{ $index }}][description]" rows="2" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1">{{ old('produits.' . $index . '.description', $produit->description) }}</textarea>
                        </div>
                        <div class="mb-4">
                            <label for="produits_{{ $index }}_prix" class="block text-sm font-semibold text-gray-700">Prix unitaire (€)</label>
                            <input type="number" id="produits_{{ $index }}_prix" name="produits[{{ $index }}][prix]" step="0.01" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1" value="{{ old('produits.' . $index . '.prix', $produit->prix) }}">
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <button type="submit" class="w-full bg-green-600 text-white px-6 py-3 rounded-lg shadow hover:bg-green-700 focus:ring-2 focus:ring-green-500">Mettre à jour</button>
    </form>
</div>

<script>
    let productCount = {{ count($commande->produits) }};

    function addProduct() {
        const productList = document.getElementById('product-list');
        const addProductButtonContainer = document.getElementById('add-product-button-container');

        const newProduct = document.createElement('div');
        newProduct.classList.add('product-item', 'bg-gray-50', 'p-4', 'rounded-lg', 'shadow-sm', 'mt-4');
        newProduct.innerHTML = `
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="mb-4">
            <label for="produits_${productCount}_nom" class="block text-sm font-semibold text-gray-700">Nom</label>
            <input type="text" id="produits_${productCount}_nom" name="produits[${productCount}][nom]" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1" required>
            </div>
            <div class="mb-4">
            <label for="produits_${productCount}_description" class="block text-sm font-semibold text-gray-700">Description</label>
            <textarea id="produits_${productCount}_description" name="produits[${productCount}][description]" rows="2" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1"></textarea>
            </div>
            <div class="mb-4">
            <label for="produits_${productCount}_prix" class="block text-sm font-semibold text-gray-700">Prix unitaire (€)</label>
            <input type="number" id="produits_${productCount}_prix" name="produits[${productCount}][prix]" step="0.01" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1">
            </div>
            </div>
        `;

        productList.appendChild(newProduct);
        productCount++;
    }
</script>
@endsection
