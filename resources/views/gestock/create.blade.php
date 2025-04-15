@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto my-4 py-8 px-6 bg-white shadow-md rounded-lg">
    <h1 class="text-3xl font-extrabold text-gray-800 mb-8">Nouvelle Commande</h1>

    <form action="{{ route('commande.store') }}" method="POST" class="space-y-6">
        @csrf

        <!-- Partie Commande -->
        <div class="border-l-4 border-green-600 pl-4">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Commande</h2>
            <div class="mb-4">
                <label for="intitule" class="block text-sm font-semibold text-gray-700">Intitulé</label>
                <input type="text" id="intitule" name="intitule" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1" required>
            </div>

            <div class="mb-4">
                <label for="prix_total" class="block text-sm font-semibold text-gray-700">Prix total (€)</label>
                <input type="number" id="prix_total" name="prix_total" step="0.01" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1" required>
            </div>

            <div class="mb-4">
                <label for="etat" class="block text-sm font-semibold text-gray-700">État</label>
                <select id="etat" name="etat" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1" required>
                    @foreach ($etats as $etat)
                        <option value="{{ $etat }}">{{ ucfirst($etat) }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label for="remarque" class="block text-sm font-semibold text-gray-700">Remarque</label>
                <textarea id="remarque" name="remarque" rows="4" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1"></textarea>
            </div>

            <div class="mb-4">
                <label for="date_livraison_fournisseur" class="block text-sm font-semibold text-gray-700">Date de livraison fournisseur</label>
                <input type="date" id="date_livraison_fournisseur" name="date_livraison_fournisseur" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1">
            </div>

            <div class="mb-4">
                <label for="date_installation_prevue" class="block text-sm font-semibold text-gray-700">Date d'installation prévue</label>
                <input type="date" id="date_installation_prevue" name="date_installation_prevue" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1">
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
                        <option value="{{ $client->id }}">{{ $client->nom }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label for="urgence" class="block text-sm font-semibold text-gray-700">Urgence</label>
                <select id="urgence" name="urgence" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1" required>
                    @foreach ($urgences as $urgence)
                        <option value="{{ $urgence }}">{{ ucfirst($urgence) }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Partie Produits -->
        <div class="border-l-4 border-green-600 pl-4">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Produit(s)</h2>
            <div class="space-y-6 mb-4" id="product-list">
                <div class="product-item bg-gray-50 p-4 rounded-lg shadow-sm">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="mb-4">
                            <label for="produits_0_nom" class="block text-sm font-semibold text-gray-700">Nom du produit</label>
                            <input type="text" id="produits_0_nom" name="produits[0][nom]" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1" required>
                        </div>
                        <div class="mb-4">
                            <label for="produits_0_fournisseur" class="block text-sm font-semibold text-gray-700">Fournisseur</label>
                            <input type="text" id="produits_0_fournisseur" name="produits[0][fournisseur]" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1" required>
                        </div>
                        <div class="mb-4">
                            <label for="produits_0_quantite" class="block text-sm font-semibold text-gray-700">Quantité</label>
                            <input type="number" id="produits_0_quantite" name="produits[0][quantite]" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1" required>
                        </div>
                        <div class="mb-4">
                            <label for="produits_0_prix_unitaire" class="block text-sm font-semibold text-gray-700">Prix unitaire (€)</label>
                            <input type="number" id="produits_0_prix_unitaire" name="produits[0][prix_unitaire]" step="0.01" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1" required>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bouton pour ajouter un produit -->
            <div class="text-right">
                <button type="button" onclick="addProduct()" class="bg-green-600 text-white px-4 py-2 rounded-lg shadow hover:bg-green-700 focus:ring-2 focus:ring-green-500">Ajouter un produit</button>
            </div>
        </div>

        <button type="submit" class="w-full bg-green-600 text-white px-6 py-3 rounded-lg shadow hover:bg-green-700 focus:ring-2 focus:ring-green-500">Enregistrer</button>
    </form>
</div>

<script>
    let productCount = 1;

    function addProduct() {
        const productList = document.getElementById('product-list');
        
        const newProduct = document.createElement('div');
        newProduct.classList.add('product-item', 'bg-gray-50', 'p-4', 'rounded-lg', 'shadow-sm', 'mt-4');
        newProduct.innerHTML = `
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="mb-4">
                    <label for="produits_${productCount}_nom" class="block text-sm font-semibold text-gray-700">Nom du produit</label>
                    <input type="text" id="produits_${productCount}_nom" name="produits[${productCount}][nom]" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500" required>
                </div>
                <div class="mb-4">
                    <label for="produits_${productCount}_fournisseur" class="block text-sm font-semibold text-gray-700">Fournisseur</label>
                    <input type="text" id="produits_${productCount}_fournisseur" name="produits[${productCount}][fournisseur]" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500" required>
                </div>
                <div class="mb-4">
                    <label for="produits_${productCount}_quantite" class="block text-sm font-semibold text-gray-700">Quantité</label>
                    <input type="number" id="produits_${productCount}_quantite" name="produits[${productCount}][quantite]" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500" required>
                </div>
                <div class="mb-4">
                    <label for="produits_${productCount}_prix_unitaire" class="block text-sm font-semibold text-gray-700">Prix unitaire (€)</label>
                    <input type="number" id="produits_${productCount}_prix_unitaire" name="produits[${productCount}][prix_unitaire]" step="0.01" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500" required>
                </div>
            </div>
        `;
        
        productList.appendChild(newProduct);
        productCount++;
    }
</script>

@endsection
