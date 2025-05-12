@extends('layouts.app')

@section('content')
<div class="min-h-screen">
    <div class="max-w-6xl mx-auto my-4 py-8 px-6 bg-white shadow-md rounded-lg">
        <h1 class="text-3xl font-extrabold text-gray-800 mb-8">Nouvelle Commande</h1>

        <form action="{{ route('gestcommande.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Partie Commande -->
            <div class="border-l-4 border-green-600 pl-4">
                <h2 class="text-2xl font-bold text-gray-800 mb-4">Commande</h2>

                <div class="mb-4">
                    <label for="etat" class="block text-sm font-semibold text-gray-700">État</label>
                    <select id="etat" name="etat" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1" required>
                        <option value="">-- Sélectionner un état --</option>
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
                    <label for="delai_installation" class="block text-sm font-semibold text-gray-700">Délai d'installation prévu (en jours)</label>
                    <input type="number" id="delai_installation" name="delai_installation" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1">
                </div>

                <div class="mb-4">
                    <label for="date_installation_prevue" class="block text-sm font-semibold text-gray-700">Date d'installation prévue</label>
                    <input type="date" id="date_installation_prevue" name="date_installation_prevue" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1">
                </div>

                <div class="mb-4">
                    <label for="reference_devis" class="block text-sm font-semibold text-gray-700">Référence devis de la commande</label>
                    <input type="text" id="reference_devis" name="reference_devis" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1">
                </div>

                <div class="mb-4">
                    <label for="urgence" class="block text-sm font-semibold text-gray-700">Urgence</label>
                    <select id="urgence" name="urgence" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1" required>
                        <option value="">-- Sélectionner une urgence --</option>    
                        @foreach ($urgences as $urgence)
                            <option value="{{ $urgence }}" {{ $urgence === 'pas urgent' ? 'selected' : '' }}>{{ ucfirst($urgence) }}</option>
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
                        <option value="">-- Sélectionner un site --</option>
                        @foreach (\App\Models\Stock::all() as $stock)
                            <option value="{{ $stock->id }}">{{ ucfirst($stock->lieux) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Partie Client -->
            <div class="border-l-4 border-green-600 pl-4">
                <h2 class="text-2xl font-bold text-gray-800 mb-4">Client</h2>
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700">Client</label>
                    <div class="relative w-full">
                        <div class="relative">
                            <input
                                type="text"
                                id="client_search"
                                placeholder="Rechercher un client..."
                                class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 pl-10 pr-4 py-2"
                            >
                            <input type="hidden" id="client_id" name="client_id">
                            <div class="absolute left-3 top-1/2 transform -translate-y-1/4 text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                            </div>
                        </div>
                        <div id="client_search_results" class="absolute z-10 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-60 overflow-y-auto hidden"></div>
                    </div>
                    <div class="mt-2">
                        <button type="button" onclick="toggleNewClientForm()" class="text-green-600 hover:text-green-800 font-medium text-sm flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"/></svg>
                            Ajouter un nouveau client
                        </button>
                    </div>
                </div>

                <div id="new-client-form" class="mb-4 hidden bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-semibold text-gray-700 mb-2">Nouveau Client</h3>
                    <div class="mb-4">
                        <label for="new_client_nom" class="block text-sm font-semibold text-gray-700">Nom</label>
                        <input type="text" id="new_client_nom" name="new_client[nom]" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1">
                    </div>
                    <div class="mb-4">
                        <label for="code_client" class="block text-sm font-semibold text-gray-700">Code client</label>
                        <input type="text" id="code_client" name="new_client[code_client]" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1">
                    </div>
                </div>

                <div id="selected_client" class="mb-6 p-4 bg-green-50 rounded-lg border border-green-200 hidden">
                    <h3 class="font-medium text-green-800">Client sélectionné</h3>
                    <p id="selected_client_info" class="text-green-700"></p>
                </div>
            </div>

            <!-- Partie Produit -->
            <div class="border-l-4 border-green-600 pl-4">
                <h2 class="text-2xl font-bold text-gray-800 mb-4">Produit</h2>
                <div class="product-item bg-gray-50 p-4 rounded-lg shadow-sm">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="mb-4">
                            <label for="produit_nom" class="block text-sm font-semibold text-gray-700">Nom</label>
                            <input type="text" id="produit_nom" name="produit[nom]" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1" required>
                        </div>
                        <div class="mb-4">
                            <label for="produit_reference" class="block text-sm font-semibold text-gray-700">Référence produit</label>
                            <input type="text" id="produit_reference" name="produit[reference]" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1">
                        </div>
                        <div class="mb-4">
                            <label for="produit_prix_referencement" class="block text-sm font-semibold text-gray-700">Prix d'achat</label>
                            <input type="number" id="produit_prix_referencement" name="produit[prix_referencement]" step="0.01" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1">
                        </div>
                        <div class="mb-4">
                            <label for="produit_lien_produit_fournisseur" class="block text-sm font-semibold text-gray-700">Lien produit fournisseur</label>
                            <input type="text" id="produit_lien_produit_fournisseur" name="produit[lien_produit_fournisseur]" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1">
                        </div>
                        <div class="mb-4">
                            <label for="produit_date_livraison_fournisseur" class="block text-sm font-semibold text-gray-700">Date de Livraison Fournisseur</label>
                            <input type="date" id="produit_date_livraison_fournisseur" name="produit[date_livraison_fournisseur]" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1">
                        </div>
                        <div class="mb-4">
                            <label for="produit_quantite_totale" class="block text-sm font-semibold text-gray-700">Quantité totale</label>
                            <input type="number" id="produit_quantite_totale" name="produit[quantite_totale]" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1">
                        </div>
                        <div class="mb-4">
                            <label for="produit_quantite_client" class="block text-sm font-semibold text-gray-700">Quantité Client</label>
                            <input type="number" id="produit_quantite_client" name="produit[quantite_client]" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Partie Fournisseur -->
            <div class="border-l-4 border-green-600 pl-4">
                <h2 class="text-2xl font-bold text-gray-800 mb-4">Fournisseur</h2>
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700">Fournisseur</label>
                    <div class="relative w-full">
                        <div class="relative">
                            <input
                                type="text"
                                id="fournisseur_search"
                                placeholder="Rechercher un fournisseur..."
                                class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 pl-10 pr-4 py-2"
                            >
                            <input type="hidden" id="fournisseur_id" name="fournisseur_id">
                            <div class="absolute left-3 top-1/2 transform -translate-y-1/4 text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                            </div>
                        </div>
                        <div id="fournisseur_search_results" class="absolute z-10 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-60 overflow-y-auto hidden"></div>
                    </div>
                    <div class="mt-2">
                        <button type="button" onclick="toggleNewFournisseurForm()" class="text-green-600 hover:text-green-800 font-medium text-sm flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"/></svg>
                            Ajouter un nouveau fournisseur
                        </button>
                    </div>
                </div>

                <div id="new-fournisseur-form" class="mb-4 hidden bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-semibold text-gray-700 mb-2">Nouveau Fournisseur</h3>
                    <div class="mb-4">
                        <label for="new_fournisseur_nom" class="block text-sm font-semibold text-gray-700">Nom</label>
                        <input type="text" id="new_fournisseur_nom" name="new_fournisseur[nom]" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1">
                    </div>
                </div>

                <div id="selected_fournisseur" class="mb-6 p-4 bg-green-50 rounded-lg border border-green-200 hidden">
                    <h3 class="font-medium text-green-800">Fournisseur sélectionné</h3>
                    <p id="selected_fournisseur_info" class="text-green-700"></p>
                </div>
            </div>

            <button type="submit" class="w-full bg-green-600 text-white px-6 py-3 rounded-lg shadow hover:bg-green-700 focus:ring-2 focus:ring-green-500 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                Enregistrer
            </button>
        </form>
        <div class="text-right mt-4 p-4">
            <a href="{{ route('gestcommande.index') }}" class="text-gray-500 hover:underline">Retour</a>
        </div>
    </div>
</div>

<script>
let clients = @json($clients);
let fournisseurs = @json($fournisseurs);
let clientSearchTimeout;
let fournisseurSearchTimeout;

function toggleNewClientForm() {
    const form = document.getElementById('new-client-form');
    form.classList.toggle('hidden');
}

function toggleNewFournisseurForm() {
    const form = document.getElementById('new-fournisseur-form');
    form.classList.toggle('hidden');
}

function highlightMatch(text, query) {
    if (!query.trim()) return text;
    const regex = new RegExp(`(${query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'gi');
    return text.replace(regex, '<span class="bg-yellow-200 font-medium">$1</span>');
}

document.addEventListener('DOMContentLoaded', function() {
    // Client search setup
    const clientSearchInput = document.getElementById('client_search');
    const clientSearchResults = document.getElementById('client_search_results');
    const clientIdInput = document.getElementById('client_id');
    const selectedClientDiv = document.getElementById('selected_client');
    const selectedClientInfo = document.getElementById('selected_client_info');

    // Fournisseur search setup
    const fournisseurSearchInput = document.getElementById('fournisseur_search');
    const fournisseurSearchResults = document.getElementById('fournisseur_search_results');
    const fournisseurIdInput = document.getElementById('fournisseur_id');
    const selectedFournisseurDiv = document.getElementById('selected_fournisseur');
    const selectedFournisseurInfo = document.getElementById('selected_fournisseur_info');

    // Client search handler
    clientSearchInput.addEventListener('input', function(e) {
        clearTimeout(clientSearchTimeout);
        const query = e.target.value.toLowerCase();

        clientSearchTimeout = setTimeout(() => {
            if (query.trim() === '') {
                clientSearchResults.classList.add('hidden');
                return;
            }

            const filtered = clients
                .filter(client => client.nom.toLowerCase().includes(query))
                .slice(0, 10);

            clientSearchResults.innerHTML = '';
            
            if (filtered.length > 0) {
                const ul = document.createElement('ul');
                filtered.forEach(client => {
                    const li = document.createElement('li');
                    li.className = 'px-4 py-2 hover:bg-gray-100 cursor-pointer transition-colors duration-150';
                    li.innerHTML = highlightMatch(client.nom, query);
                    li.onclick = () => selectClient(client);
                    ul.appendChild(li);
                });
                clientSearchResults.appendChild(ul);
                clientSearchResults.classList.remove('hidden');
            } else {
                clientSearchResults.innerHTML = `
                    <div class="p-4 text-gray-500">
                        <p class="text-sm">Aucun client trouvé</p>
                        <button onclick="toggleNewClientForm()" class="mt-2 text-green-600 hover:text-green-800 font-semibold text-sm flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"/></svg>
                            Ajouter un nouveau client
                        </button>
                    </div>
                `;
                clientSearchResults.classList.remove('hidden');
            }
        }, 300);
    });

    // Fournisseur search handler
    fournisseurSearchInput.addEventListener('input', function(e) {
        clearTimeout(fournisseurSearchTimeout);
        const query = e.target.value.toLowerCase();

        fournisseurSearchTimeout = setTimeout(() => {
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
                    li.onclick = () => selectFournisseur(fournisseur);
                    ul.appendChild(li);
                });
                fournisseurSearchResults.appendChild(ul);
                fournisseurSearchResults.classList.remove('hidden');
            } else {
                fournisseurSearchResults.innerHTML = `
                    <div class="p-4 text-gray-500">
                        <p class="text-sm">Aucun fournisseur trouvé</p>
                        <button onclick="toggleNewFournisseurForm()" class="mt-2 text-green-600 hover:text-green-800 font-semibold text-sm flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"/></svg>
                            Ajouter un nouveau fournisseur
                        </button>
                    </div>
                `;
                fournisseurSearchResults.classList.remove('hidden');
            }
        }, 300);
    });

    function selectClient(client) {
        clientSearchInput.value = client.nom;
        clientIdInput.value = client.id;
        clientSearchResults.classList.add('hidden');
        
        selectedClientInfo.textContent = `${client.nom} (ID: ${client.id})`;
        selectedClientDiv.classList.remove('hidden');
    }

    function selectFournisseur(fournisseur) {
        fournisseurSearchInput.value = fournisseur.nom;
        fournisseurIdInput.value = fournisseur.id;
        fournisseurSearchResults.classList.add('hidden');
        
        selectedFournisseurInfo.textContent = `${fournisseur.nom} (ID: ${fournisseur.id})`;
        selectedFournisseurDiv.classList.remove('hidden');
    }

    // Close dropdowns when clicking outside
    document.addEventListener('click', function(e) {
        if (!clientSearchInput.contains(e.target) && !clientSearchResults.contains(e.target)) {
            clientSearchResults.classList.add('hidden');
        }
        if (!fournisseurSearchInput.contains(e.target) && !fournisseurSearchResults.contains(e.target)) {
            fournisseurSearchResults.classList.add('hidden');
        }
    });
});
</script>
@endsection
