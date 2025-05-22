@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto my-4 py-8 px-6 bg-white shadow-md rounded-lg">
    <h1 class="text-3xl font-extrabold text-gray-800 mb-8">Modifier la Commande</h1>

    <form action="{{ route('gestcommande.update', $commande) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Partie Commande -->
        <div class="border-l-4 border-green-600 pl-4">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Commande</h2>

            <div class="mb-4">
                <label for="numero_commande_fournisseur" class="block text-sm font-semibold text-gray-700">* Numéro de commande fournisseur</label>
                <input type="text" id="numero_commande_fournisseur" name="numero_commande_fournisseur" value="{{ $commande->numero_commande_fournisseur }}" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1">
            </div>

            <div class="mb-4">
                <label for="doc_client" class="block text-sm font-semibold text-gray-700">N° de devis/commande ou BL client</label>
                <input type="text" id="doc_client" name="doc_client" value="{{ $commande->doc_client }}" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1">
            </div>  

            <div class="mb-4">
                <label for="etat" class="block text-sm font-semibold text-gray-700">* État</label>
                <select id="etat" name="etat" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1" required>
                    @foreach ($etats as $etat)
                        <option value="{{ $etat }}" @if($commande->etat === $etat) selected @endif>{{ ucfirst($etat) }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label for="remarque" class="block text-sm font-semibold text-gray-700">Remarque</label>
                <textarea id="remarque" name="remarque" rows="4" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1">{{ $commande->remarque }}</textarea>
            </div>

            <div class="mb-4">
                <label for="delai_installation" class="block text-sm font-semibold text-gray-700">Délai d'installation prévu (en jours)</label>
                <input type="number" id="delai_installation" name="delai_installation" value="{{ $commande->delai_installation }}" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1">
            </div>

            <div class="mb-4">
                <label for="date_installation_prevue" class="block text-sm font-semibold text-gray-700">Date d'installation prévue</label>
                <input type="date" id="date_installation_prevue" name="date_installation_prevue" value="{{ $commande->date_installation_prevue ? \Carbon\Carbon::parse($commande->date_installation_prevue)->format('Y-m-d') : '' }}" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1">
            </div>

            <div class="mb-4">
                <label for="reference_devis" class="block text-sm font-semibold text-gray-700">* Référence devis de la commande</label>
                <input type="text" id="reference_devis" name="reference_devis" value="{{ $commande->reference_devis }}" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1">
            </div>

            <div class="mb-4">
                <label for="urgence" class="block text-sm font-semibold text-gray-700">* Urgence</label>
                <select id="urgence" name="urgence" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1" required>
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
                <label for="stock_id" class="block text-sm font-semibold text-gray-700">* Choisir un site</label>
                @php
                    $stockProduitCommande = DB::table('produit_stock')
                        ->join('stocks', 'produit_stock.stock_id', '=', 'stocks.id')
                        ->where('produit_stock.commande_id', $commande->id)
                        ->select('stocks.id')
                        ->first();
                @endphp
                <select id="stock_id" name="stock_id" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1" required>
                    @foreach ($stocks as $stock)
                        <option value="{{ $stock->id }}" {{ $stock->id == ($stockProduitCommande->id ?? $commande->stock_id) ? 'selected' : '' }}>{{ $stock->lieux }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Partie Client -->
        <div class="border-l-4 border-green-600 pl-4">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Client</h2>
            @if($commande->client)
            <div id="client-details" class="space-y-4">
                <div class="mb-4">
                    <label for="client_nom" class="block text-sm font-semibold text-gray-700">* Nom du Client</label>
                    <input type="text" id="client_nom" name="client[nom]" value="{{ $commande->client->nom ?? '' }}" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1">
                </div>

                <div class="mb-4">
                    <label for="client_code" class="block text-sm font-semibold text-gray-700">* Code Client</label>
                    <input type="text" id="client_code" name="client[code_client]" value="{{ $commande->client->code_client ?? '' }}" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1">
                </div>

                <div class="mb-4">
                    <label for="numero_telephone" class="block text-sm font-semibold text-gray-700">Numéro téléphone Client</label>
                    <input type="text" id="numero_telephone" name="client[numero_telephone]" value="{{ $commande->client->numero_telephone ?? '' }}" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1">
                </div>
            </div>
            @else
            <p class="text-sm text-gray-700">Aucun client n'est associé à cette commande.</p>
            @endif
        </div>

        <!-- Partie Produits -->
        <div class="border-l-4 border-green-600 pl-4">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-bold text-gray-800">Produits</h2>
                <button type="button" onclick="addProduct()" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 flex items-center">
                    <i data-lucide="plus" class="h-4 w-4 mr-2"></i>
                    Ajouter un produit
                </button>
            </div>
            
            <div id="products-container">
                @foreach($commande->produits as $index => $produit)
                @php
                    $fournisseurProduit = DB::table('fournisseur_produit')
                        ->join('fournisseurs', 'fournisseur_produit.fournisseur_id', '=', 'fournisseurs.id')
                        ->where('fournisseur_produit.produit_id', $produit->id)
                        ->where('fournisseur_produit.commande_id', $commande->id)
                        ->select('fournisseurs.*')
                        ->first();
                @endphp
                <div class="product-item bg-gray-50 p-4 rounded-lg shadow-sm mb-4" data-product-index="{{ $index }}">
                    <div class="flex justify-between items-center mb-3">
                        <h3 class="text-lg font-semibold text-gray-700">Produit {{ $index + 1 }}</h3>
                        @if($index > 0)
                        <button type="button" onclick="removeProduct({{ $index }})" class="text-red-600 hover:text-red-800 font-medium">
                            <i data-lucide="x" class="h-5 w-5"></i>
                        </button>
                        @endif
                    </div>
                    
                    <!-- Informations produit -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                        <div class="mb-4">
                            <label class="block text-sm font-semibold text-gray-700">* Nom</label>
                            <input type="text" name="produits[{{ $index }}][nom]" value="{{ old('produits.'.$index.'.nom', $produit->pivot->nom ?? $produit->nom) }}" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1" required>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-semibold text-gray-700">* Référence produit</label>
                            <input type="text" name="produits[{{ $index }}][reference]" value="{{ old('produits.'.$index.'.reference', $produit->pivot->reference ?? $produit->reference) }}" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1" required>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-semibold text-gray-700">Prix d'achat</label>
                            <input type="number" name="produits[{{ $index }}][prix_referencement]" value="{{ old('produits.'.$index.'.prix_referencement', $produit->pivot->prix_referencement ?? $produit->prix_referencement) }}" step="0.01" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1">
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-semibold text-gray-700">Lien produit fournisseur</label>
                            <input type="text" name="produits[{{ $index }}][lien_produit_fournisseur]" value="{{ old('produits.'.$index.'.lien_produit_fournisseur', $produit->pivot->lien_produit_fournisseur ?? $produit->lien_produit_fournisseur) }}" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1">
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-semibold text-gray-700">Date de Livraison Fournisseur</label>
                            <input type="date" name="produits[{{ $index }}][date_livraison_fournisseur]" value="{{ old('produits.'.$index.'.date_livraison_fournisseur', $produit->pivot->date_livraison_fournisseur ?? $produit->date_livraison_fournisseur ? \Carbon\Carbon::parse($produit->pivot->date_livraison_fournisseur ?? $produit->date_livraison_fournisseur)->format('Y-m-d') : '') }}" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1">
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-semibold text-gray-700">* Quantité totale</label>
                            <input type="number" name="produits[{{ $index }}][quantite_totale]" value="{{ old('produits.'.$index.'.quantite_totale', $produit->pivot->quantite_totale ?? '') }}" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1" required>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-semibold text-gray-700">Quantité Client</label>
                            <input type="number" name="produits[{{ $index }}][quantite_client]" value="{{ old('produits.'.$index.'.quantite_client', $produit->pivot->quantite_client ?? '') }}" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1">
                        </div>

                        <div class="mb-4 flex items-center">
                            <input 
                                type="checkbox" 
                                name="produits[{{ $index }}][is_derMinute]" 
                                value="1" 
                                class="mr-2"
                                {{ old('produits.'.$index.'.is_derMinute', $produit->is_derMinute ?? 0) == 1 ? 'checked' : '' }}
                            >
                            <label class="text-sm font-semibold text-gray-700">Mise en place de dernière minute ?</label>
                        </div>
                    </div>

                    <!-- Section Fournisseur pour ce produit -->
                    <div class="border-t border-gray-200 pt-4">
                        <h4 class="text-md font-semibold text-gray-700 mb-3">Fournisseur pour ce produit</h4>
                        <div class="mb-4">
                            <label class="block text-sm font-semibold text-gray-700">* Choisir un fournisseur</label>
                            <div class="relative w-full">
                                <div class="relative">
                                    <input
                                        type="text"
                                        id="fournisseur_search_{{ $index }}"
                                        placeholder="Rechercher un fournisseur..."
                                        value="{{ $fournisseurProduit->nom ?? '' }}"
                                        class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 pl-10 pr-4 py-2"
                                    >
                                    <input type="hidden" id="fournisseur_id_{{ $index }}" name="produits[{{ $index }}][fournisseur_id]" value="{{ $fournisseurProduit->id ?? '' }}">
                                    <div class="absolute left-3 top-1/2 transform -translate-y-1/4 text-gray-400">
                                        <i data-lucide="search" class="h-5 w-5"></i>
                                    </div>
                                </div>
                                <div id="fournisseur_search_results_{{ $index }}" class="absolute z-10 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-60 overflow-y-auto hidden"></div>
                            </div>
                            <div class="mt-2">
                                <button type="button" onclick="toggleNewFournisseurForm({{ $index }})" class="text-green-600 hover:text-green-800 font-medium text-sm flex items-center">
                                    <i data-lucide="plus" class="h-4 w-4 mr-1"></i>
                                    Ajouter un nouveau fournisseur
                                </button>
                            </div>
                        </div>

                        <div id="new-fournisseur-form-{{ $index }}" class="mb-4 hidden bg-gray-50 p-4 rounded-lg">
                            <h5 class="text-md font-semibold text-gray-700 mb-2">Nouveau Fournisseur</h5>
                            <div class="mb-4">
                                <label for="new_fournisseur_nom_{{ $index }}" class="block text-sm font-semibold text-gray-700">* Nom</label>
                                <div class="flex">
                                    <input 
                                        type="text" 
                                        id="new_fournisseur_nom_{{ $index }}" 
                                        name="produits[{{ $index }}][new_fournisseur][nom]" 
                                        class="mt-2 block w-full border-gray-300 rounded-l-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1"
                                    >
                                    <button 
                                        type="button" 
                                        onclick="saveNewFournisseur({{ $index }})" 
                                        class="mt-2 bg-green-600 text-white px-4 rounded-r-lg hover:bg-green-700 flex items-center"
                                    >
                                        <i class="fa-solid fa-check"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div id="selected_fournisseur_{{ $index }}" class="mb-4 p-4 bg-blue-50 rounded-lg border border-blue-200 {{ $fournisseurProduit ? '' : 'hidden' }}">
                            <h5 class="font-medium text-blue-800">Fournisseur actuellement sélectionné</h5>
                            <p id="selected_fournisseur_info_{{ $index }}" class="text-blue-700">
                                @if($fournisseurProduit)
                                    {{ $fournisseurProduit->nom }} (ID: {{ $fournisseurProduit->id }})
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <button type="submit"
                class="w-full bg-green-600 text-white px-6 py-3 rounded-lg shadow hover:bg-green-700 focus:ring-2 focus:ring-green-500 flex items-center justify-center">
                <i data-lucide="check" class="h-5 w-5 mr-2"></i>
                Mettre à jour
        </button>
    </form>
    <div class="text-right mt-4 p-4">
        <a href="{{ route('gestcommande.index') }}" class="text-gray-500 hover:underline">Retour</a>
    </div>
</div>

<script>
let fournisseurs = @json($fournisseurs);
let fournisseurSearchTimeouts = {};
let productCount = {{ count($commande->produits) }};

function toggleNewFournisseurForm(productIndex) {
    const form = document.getElementById(`new-fournisseur-form-${productIndex}`);
    const searchInput = document.getElementById(`fournisseur_search_${productIndex}`);
    const fournisseurId = document.getElementById(`fournisseur_id_${productIndex}`);
    const selectedFournisseur = document.getElementById(`selected_fournisseur_${productIndex}`);
    
    if (form.classList.contains('hidden')) {
        form.classList.remove('hidden');
        searchInput.value = '';
        fournisseurId.value = '';
        selectedFournisseur.classList.add('hidden');
    } else {
        form.classList.add('hidden');
    }
}

function saveNewFournisseur(productIndex) {
    const newFournisseurInput = document.getElementById(`new_fournisseur_nom_${productIndex}`);
    const selectedFournisseurDiv = document.getElementById(`selected_fournisseur_${productIndex}`);
    const selectedFournisseurInfo = document.getElementById(`selected_fournisseur_info_${productIndex}`);
    const searchInput = document.getElementById(`fournisseur_search_${productIndex}`);
    
    if (newFournisseurInput.value.trim()) {
        searchInput.value = '';
        document.getElementById(`fournisseur_id_${productIndex}`).value = '';
        
        selectedFournisseurDiv.classList.remove('hidden');
        selectedFournisseurDiv.className = 'mb-4 p-4 bg-green-50 rounded-lg border border-green-200';
        
        const title = selectedFournisseurDiv.querySelector('h5');
        title.textContent = 'Nouveau fournisseur à créer';
        title.className = 'font-medium text-green-800';
        
        selectedFournisseurInfo.textContent = newFournisseurInput.value;
        selectedFournisseurInfo.className = 'text-green-700';
        
        document.getElementById(`new-fournisseur-form-${productIndex}`).classList.add('hidden');
    }
}

function highlightMatch(text, query) {
    if (!query.trim()) return text;
    const regex = new RegExp(`(${query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'gi');
    return text.replace(regex, '<span class="bg-yellow-200 font-medium">$1</span>');
}

function initializeFournisseurSearch(productIndex) {
    const fournisseurSearchInput = document.getElementById(`fournisseur_search_${productIndex}`);
    const fournisseurSearchResults = document.getElementById(`fournisseur_search_results_${productIndex}`);
    
    if (!fournisseurSearchInput || !fournisseurSearchResults) return;

    fournisseurSearchInput.addEventListener('input', function(e) {
        clearTimeout(fournisseurSearchTimeouts[productIndex]);
        const query = e.target.value.toLowerCase();

        fournisseurSearchTimeouts[productIndex] = setTimeout(() => {
            if (query.trim() === '') {
                fournisseurSearchResults.classList.add('hidden');
                return;
            }

            const filtered = fournisseurs
                .filter(fournisseur => fournisseur.nom.toLowerCase().includes(query))
                .slice(0, 10);

            fournisseurSearchResults.innerHTML = '';
            
            if (filtered.length > 0) {
                const ul = document.createElement('ul');
                filtered.forEach(fournisseur => {
                    const li = document.createElement('li');
                    li.className = 'px-4 py-2 hover:bg-gray-100 cursor-pointer transition-colors duration-150';
                    li.innerHTML = highlightMatch(fournisseur.nom, query);
                    li.onclick = () => selectFournisseur(fournisseur, productIndex);
                    ul.appendChild(li);
                });
                fournisseurSearchResults.appendChild(ul);
                fournisseurSearchResults.classList.remove('hidden');
            } else {
                fournisseurSearchResults.innerHTML = `
                    <div class="p-4 text-gray-500">
                        <p class="text-sm">Aucun fournisseur trouvé</p>
                        <button type="button" onclick="toggleNewFournisseurForm(${productIndex})" class="mt-2 text-green-600 hover:text-green-800 font-semibold text-sm flex items-center">
                            <i data-lucide="plus" class="h-4 w-4 mr-1"></i>
                            Ajouter un nouveau fournisseur
                        </button>
                    </div>
                `;
                fournisseurSearchResults.classList.remove('hidden');
            }
            
            lucide.createIcons();
        }, 300);
    });
}

function selectFournisseur(fournisseur, productIndex) {
    const fournisseurSearchInput = document.getElementById(`fournisseur_search_${productIndex}`);
    const fournisseurIdInput = document.getElementById(`fournisseur_id_${productIndex}`);
    const fournisseurSearchResults = document.getElementById(`fournisseur_search_results_${productIndex}`);
    const selectedFournisseurDiv = document.getElementById(`selected_fournisseur_${productIndex}`);
    const selectedFournisseurInfo = document.getElementById(`selected_fournisseur_info_${productIndex}`);
    const newFournisseurInput = document.getElementById(`new_fournisseur_nom_${productIndex}`);
    
    fournisseurSearchInput.value = fournisseur.nom;
    fournisseurIdInput.value = fournisseur.id;
    fournisseurSearchResults.classList.add('hidden');
    
    if (newFournisseurInput) {
        newFournisseurInput.value = '';
        document.getElementById(`new-fournisseur-form-${productIndex}`).classList.add('hidden');
    }
    
    selectedFournisseurDiv.classList.remove('hidden');
    selectedFournisseurDiv.className = 'mb-4 p-4 bg-blue-50 rounded-lg border border-blue-200';
    
    const title = selectedFournisseurDiv.querySelector('h5');
    title.textContent = 'Fournisseur sélectionné';
    title.className = 'font-medium text-blue-800';
    
    selectedFournisseurInfo.textContent = `${fournisseur.nom} (ID: ${fournisseur.id})`;
    selectedFournisseurInfo.className = 'text-blue-700';
}

function addProduct() {
    const container = document.getElementById('products-container');
    const productIndex = productCount;
    
    const productHtml = `
        <div class="product-item bg-gray-50 p-4 rounded-lg shadow-sm mb-4" data-product-index="${productIndex}">
            <div class="flex justify-between items-center mb-3">
                <h3 class="text-lg font-semibold text-gray-700">Produit ${productIndex + 1}</h3>
                <button type="button" onclick="removeProduct(${productIndex})" class="text-red-600 hover:text-red-800 font-medium">
                    <i data-lucide="x" class="h-5 w-5"></i>
                </button>
            </div>
            
            <!-- Informations produit -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700">* Nom</label>
                    <input type="text" name="produits[${productIndex}][nom]" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1" required>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700">* Référence produit</label>
                    <input type="text" name="produits[${productIndex}][reference]" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700">Prix d'achat</label>
                    <input type="number" name="produits[${productIndex}][prix_referencement]" step="0.01" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700">Lien produit fournisseur</label>
                    <input type="text" name="produits[${productIndex}][lien_produit_fournisseur]" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700">Date de Livraison Fournisseur</label>
                    <input type="date" name="produits[${productIndex}][date_livraison_fournisseur]" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700">* Quantité totale</label>
                    <input type="number" name="produits[${productIndex}][quantite_totale]" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700">Quantité Client</label>
                    <input type="number" name="produits[${productIndex}][quantite_client]" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1">
                </div>
                <div class="mb-4 flex items-center">
                    <input type="checkbox" name="produits[${productIndex}][is_derMinute]" value="1" class="mr-2">
                    <label class="text-sm font-semibold text-gray-700">Mise en place de dernière minute ?</label>
                </div>
            </div>

            <!-- Section Fournisseur pour ce produit -->
            <div class="border-t border-gray-200 pt-4">
                <h4 class="text-md font-semibold text-gray-700 mb-3">Fournisseur pour ce produit</h4>
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700">* Choisir un fournisseur</label>
                    <div class="relative w-full">
                        <div class="relative">
                            <input
                                type="text"
                                id="fournisseur_search_${productIndex}"
                                placeholder="Rechercher un fournisseur..."
                                class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 pl-10 pr-4 py-2"
                            >
                            <input type="hidden" id="fournisseur_id_${productIndex}" name="produits[${productIndex}][fournisseur_id]">
                            <div class="absolute left-3 top-1/2 transform -translate-y-1/4 text-gray-400">
                                <i data-lucide="search" class="h-5 w-5"></i>
                            </div>
                        </div>
                        
                        <div id="fournisseur_search_results_${productIndex}" class="absolute z-10 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-60 overflow-y-auto hidden"></div>
                    </div>
                    <div class="mt-2">
                        <button type="button" onclick="toggleNewFournisseurForm(${productIndex})" class="text-green-600 hover:text-green-800 font-medium text-sm flex items-center">
                            <i data-lucide="plus" class="h-4 w-4 mr-1"></i>
                            Ajouter un nouveau fournisseur
                        </button>
                    </div>
                </div>

                <div id="new-fournisseur-form-${productIndex}" class="mb-4 hidden bg-gray-50 p-4 rounded-lg">
                    <h5 class="text-md font-semibold text-gray-700 mb-2">Nouveau Fournisseur</h5>
                    <div class="mb-4">
                        <label for="new_fournisseur_nom_${productIndex}" class="block text-sm font-semibold text-gray-700">* Nom</label>
                        <div class="flex">
                            <input 
                                type="text" 
                                id="new_fournisseur_nom_${productIndex}" 
                                name="produits[${productIndex}][new_fournisseur][nom]" 
                                class="mt-2 block w-full border-gray-300 rounded-l-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1"
                            >
                            <button 
                                type="button" 
                                onclick="saveNewFournisseur(${productIndex})" 
                                class="mt-2 bg-green-600 text-white px-4 rounded-r-lg hover:bg-green-700 flex items-center"
                            >
                                <i data-lucide="check" class="h-5 w-5"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div id="selected_fournisseur_${productIndex}" class="mb-4 p-4 bg-blue-50 rounded-lg border border-blue-200 hidden">
                    <h5 class="font-medium text-blue-800">Fournisseur sélectionné</h5>
                    <p id="selected_fournisseur_info_${productIndex}" class="text-blue-700"></p>
                </div>
            </div>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', productHtml);
    
    setTimeout(() => {
        initializeFournisseurSearch(productIndex);
        lucide.createIcons();
    }, 100);
    
    productCount++;
}

function removeProduct(index) {
    const productItem = document.querySelector(`[data-product-index="${index}"]`);
    if (productItem) {
        productItem.remove();
        if (fournisseurSearchTimeouts[index]) {
            clearTimeout(fournisseurSearchTimeouts[index]);
            delete fournisseurSearchTimeouts[index];
        }
        updateProductNumbers();
    }
}

function updateProductNumbers() {
    const productItems = document.querySelectorAll('.product-item');
    productItems.forEach((item, index) => {
        const title = item.querySelector('h3');
        if (title) {
            title.textContent = `Produit ${index + 1}`;
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {
    @foreach($commande->produits as $index => $produit)
        initializeFournisseurSearch({{ $index }});
    @endforeach

    document.addEventListener('click', function(e) {
        Object.keys(fournisseurSearchTimeouts).forEach(index => {
            const fournisseurSearchInput = document.getElementById(`fournisseur_search_${index}`);
            const fournisseurSearchResults = document.getElementById(`fournisseur_search_results_${index}`);
            if (fournisseurSearchInput && fournisseurSearchResults) {
                if (!fournisseurSearchInput.contains(e.target) && !fournisseurSearchResults.contains(e.target)) {
                    fournisseurSearchResults.classList.add('hidden');
                }
            }
        });
    });

    lucide.createIcons();
});
</script>
@endsection