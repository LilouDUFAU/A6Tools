@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto my-4 py-8 px-6 bg-white shadow-md rounded-lg">
    <h1 class="text-3xl font-extrabold text-gray-800 mb-8">Éditer Commande</h1>

    <form action="{{ route('commande.update', $commande->id) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

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

            <div class="mb-4">
                <label for="urgence" class="block text-sm font-semibold text-gray-700">Urgence</label>
                <select id="urgence" name="urgence" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1">
                    @foreach($urgences as $urgence)
                        <option value="{{ $urgence }}" {{ $urgence == $commande->urgence ? 'selected' : '' }}>{{ ucfirst($urgence) }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Partie Stock -->
        <div class="border-l-4 border-green-600 pl-4">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Magasin</h2>
            <div class="mb-4">
                <label for="stock_id" class="block text-sm font-semibold text-gray-700">Choisir un site</label>
                <select id="stock_id" name="stock_id" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1" required>
                    @foreach ($stocks as $stock)
                        <option value="{{ $stock->id }}" {{ $stock->id == $commande->stock_id ? 'selected' : '' }}>{{ $stock->lieux }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Partie Client -->
        <div class="border-l-4 border-green-600 pl-4">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Client</h2>
            @if($commande->client)
            <div class="mb-4">
                <label for="client_id" class="block text-sm font-semibold text-gray-700">Client</label>
                <div class="flex items-center space-x-4">
                <select id="client_id" name="client_id" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1">
                    <option value="">-- Choisir un client --</option>
                    @foreach($clients as $client)
                    <option value="{{ $client->id }}" {{ $client->id == $commande->client_id ? 'selected' : '' }}>{{ $client->nom }}</option>
                    @endforeach
                </select>
                <button type="button" onclick="toggleEditClientForm()" class="bg-green-600 text-white px-4 py-2 rounded-lg shadow hover:bg-green-700">Modifier</button>
                </div>
            </div>

            <!-- Formulaire pour modifier un client existant -->
            <div id="edit-client-form" class="hidden mt-4 bg-gray-50 p-4 rounded-lg shadow-sm">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Modifier le client</h3>
                <input type="hidden" name="client[id]" value="{{ $commande->client->id ?? '' }}">
                <div class="mb-4">
                <label for="client_nom" class="block text-sm font-semibold text-gray-700">Nom</label>
                <input type="text" id="client_nom" name="client[nom]" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1" value="{{ $commande->client->nom ?? '' }}">
                </div>
                <div class="mb-4">
                <label for="client_email" class="block text-sm font-semibold text-gray-700">Email</label>
                <input type="email" id="client_email" name="client[email]" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1" value="{{ $commande->client->email ?? '' }}">
                </div>
                <div class="mb-4">
                <label for="client_telephone" class="block text-sm font-semibold text-gray-700">Téléphone</label>
                <input type="text" id="client_telephone" name="client[telephone]" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1" value="{{ $commande->client->telephone ?? '' }}">
                </div>
                <div class="mb-4">
                <label for="client_adresse" class="block text-sm font-semibold text-gray-700">Adresse postale</label>
                <textarea id="client_adresse" name="client[adresse_postale]" rows="2" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1">{{ $commande->client->adresse_postale ?? '' }}</textarea>
                </div>
                <div class="mb-4">
                <label for="client_type" class="block text-sm font-semibold text-gray-700">Type</label>
                <select id="client_type" name="client[type]" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1">
                    @foreach ($types as $type)
                    <option value="{{ $type }}" {{ ($commande->client->type ?? '') == $type ? 'selected' : '' }}>{{ ucfirst($type) }}</option>
                    @endforeach
                </select>
                </div>
            </div>
            @else
            <p class="text-sm text-gray-600">Pas de client attribué à cette commande.</p>
            @endif
        </div>

        <!-- Partie Produits -->
        <div class="border-l-4 border-green-600 pl-4">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Produit(s)</h2>
            <div class="space-y-6 mb-4" id="product-list">
                @foreach ($commande->produits as $index => $produit)
                <div class="product-item bg-gray-50 p-4 rounded-lg shadow-sm">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Informations Produit -->
                        <div class="space-y-4">
                            <input type="hidden" name="produits[{{ $index }}][id]" value="{{ $produit->id }}">
                            <div class="mb-4">
                                <label for="produits_{{ $index }}_nom" class="block text-sm font-semibold text-gray-700">Nom</label>
                                <input type="text" id="produits_{{ $index }}_nom" name="produits[{{ $index }}][nom]" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1" value="{{ old('produits.' . $index . '.nom', $produit->nom) }}" required>
                            </div>
                            <div class="mb-4">
                                <label for="produits_{{ $index }}_description" class="block text-sm font-semibold text-gray-700">Description</label>
                                <textarea id="produits_{{ $index }}_description" name="produits[{{ $index }}][description]" rows="2" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1">{{ old('produits.' . $index . '.description', $produit->description) }}</textarea>
                            </div>
                            <div class="mb-4">
                                <label for="produits_{{ $index }}_caracteristiques_techniques" class="block text-sm font-semibold text-gray-700">Caractéristiques techniques</label>
                                <textarea id="produits_{{ $index }}_caracteristiques_techniques" name="produits[{{ $index }}][caracteristiques_techniques]" rows="2" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1">{{ old('produits.' . $index . '.caracteristiques_techniques', $produit->caracteristiques_techniques) }}</textarea>
                            </div>
                            <div class="mb-4">
                                <label for="produits_{{ $index }}_reference" class="block text-sm font-semibold text-gray-700">Référence</label>
                                <input type="text" id="produits_{{ $index }}_reference" name="produits[{{ $index }}][reference]" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1" value="{{ old('produits.' . $index . '.reference', $produit->reference) }}" required>
                            </div>
                            <div class="mb-4">
                                <label for="produits_{{ $index }}_prix" class="block text-sm font-semibold text-gray-700">Prix unitaire (€)</label>
                                <input type="number" id="produits_{{ $index }}_prix" name="produits[{{ $index }}][prix]" step="0.01" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1" value="{{ old('produits.' . $index . '.prix', $produit->prix) }}">
                            </div>
                            <div class="mb-4">
                                <label for="produits_{{ $index }}_lien_produit_fournisseur" class="block text-sm font-semibold text-gray-700">Lien produit fournisseur</label>
                                <input type="url" id="produits_{{ $index }}_lien_produit_fournisseur" name="produits[{{ $index }}][lien_produit_fournisseur]" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1" value="{{ old('produits.' . $index . '.lien_produit_fournisseur', $produit->lien_produit_fournisseur) }}">
                            </div>
                        </div>

                        <!-- Informations Quantités et Fournisseur -->
                        <div class="space-y-4">
                            <div class="mb-4">
                                <label for="produits_{{ $index }}_quantite_stock" class="block text-sm font-semibold text-gray-700">Quantité en stock</label>
                                <input type="number" id="produits_{{ $index }}_quantite_stock" name="produits[{{ $index }}][quantite_stock]" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1" value="{{ old('produits.' . $index . '.quantite_stock', $produit->pivot->quantite_stock) }}">
                            </div>
                            <div class="mb-4">
                                <label for="produits_{{ $index }}_quantite_client" class="block text-sm font-semibold text-gray-700">Quantité client</label>
                                <input type="number" id="produits_{{ $index }}_quantite_client" name="produits[{{ $index }}][quantite_client]" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1" value="{{ old('produits.' . $index . '.quantite_client', $produit->pivot->quantite_client) }}" required>
                            </div>

                            <!-- Fournisseur fields -->
                            <div class="mt-6 border-t pt-4">
                                <h4 class="text-lg font-semibold text-gray-800 mb-4">Informations Fournisseur</h4>
                                <input type="hidden" name="produits[{{ $index }}][fournisseur][id]" value="{{ $produit->fournisseurs->first()->id ?? '' }}">
                                <div class="mb-4">
                                    <label for="produits_{{ $index }}_fournisseur_nom" class="block text-sm font-semibold text-gray-700">Nom fournisseur</label>
                                    <input type="text" id="produits_{{ $index }}_fournisseur_nom" name="produits[{{ $index }}][fournisseur][nom]" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1" value="{{ $produit->fournisseurs->first()->nom ?? '' }}">
                                </div>
                                <div class="mb-4">
                                    <label for="produits_{{ $index }}_fournisseur_email" class="block text-sm font-semibold text-gray-700">Email fournisseur</label>
                                    <input type="email" id="produits_{{ $index }}_fournisseur_email" name="produits[{{ $index }}][fournisseur][email]" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1" value="{{ $produit->fournisseurs->first()->email ?? '' }}">
                                </div>
                                <div class="mb-4">
                                    <label for="produits_{{ $index }}_fournisseur_telephone" class="block text-sm font-semibold text-gray-700">Téléphone fournisseur</label>
                                    <input type="text" id="produits_{{ $index }}_fournisseur_telephone" name="produits[{{ $index }}][fournisseur][telephone]" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1" value="{{ $produit->fournisseurs->first()->telephone ?? '' }}">
                                </div>
                                <div class="mb-4">
                                    <label for="produits_{{ $index }}_fournisseur_adresse" class="block text-sm font-semibold text-gray-700">Adresse postale fournisseur</label>
                                    <textarea id="produits_{{ $index }}_fournisseur_adresse" name="produits[{{ $index }}][fournisseur][adresse_postale]" rows="2" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1">{{ $produit->fournisseurs->first()->adresse_postale ?? '' }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <div class="mt-8">
            <button type="submit" class="w-full bg-green-600 text-white px-6 py-3 rounded-lg shadow hover:bg-green-700 focus:ring-2 focus:ring-green-500">
                Mettre à jour la commande
            </button>
        </div>
    </form>
</div>

<script>
function toggleEditClientForm() {
    const form = document.getElementById('edit-client-form');
    form.classList.toggle('hidden');
}
</script>
@endsection