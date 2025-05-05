@extends('layouts.app')
@section('content')

<div class="min-h-screen">
<div class="max-w-6xl mx-auto my-4 py-8 px-6 bg-white shadow-md rounded-lg">
    <h1 class="text-3xl font-extrabold text-gray-800 mb-8">Nouvelle Panne</h1>
    <form action="{{ route('panne.store') }}" method="POST" class="space-y-6">
        @csrf
        <div class="border-l-4 border-green-600 pl-4">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Panne</h2>

            <div class="mb-4">
                <label for="date_commande" class="block text-sm font-semibold text-gray-700">Date de commande</label>
                <input type="date" id="date_commande" name="date_commande" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1">
            </div>
            <div class="mb-4">
                <label for="date_panne" class="block text-sm font-semibold text-gray-700">Date de panne</label>
                <input type="date" id="date_panne" name="date_panne" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1">
            </div>
            <div class="mb-4">
                <label for="categorie_materiel" class="block text-sm font-semibold text-gray-700">Catégorie matériel</label>
                <textarea id="categorie_materiel" name="categorie_materiel" rows="4" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1"></textarea>
            </div>
            <div class="mb-4">
                <label for="categorie_panne" class="block text-sm font-semibold text-gray-700">Catégorie panne</label>
                <textarea id="categorie_panne" name="categorie_panne" rows="4" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1"></textarea>
            </div>
            <div class="mb-4">
                <label for="detail_panne" class="block text-sm font-semibold text-gray-700">Détail de la panne</label>
                <textarea id="detail_panne" name="detail_panne" rows="4" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1"></textarea>
            </div>
        </div>
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
                            class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 pl-10 pr-4 py-2">
                        <input type="hidden" id="client_id" name="client_id">
                        <div class="absolute left-3 top-1/2 transform -translate-y-1/4 text-gray-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                        </div>
                    </div>
                    <div id="search_results" class="absolute z-10 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-60 overflow-y-auto hidden"></div>
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
            <div class="mb-4">
                <label for="etat" class="block text-sm font-semibold text-gray-700">État côté client</label>
                <select id="etat" name="etat" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1" required>
                    <option value="">-- Sélectionner un état --</option>
                    @foreach ($etat_clients as $etat_client)
                        <option value="{{ $etat_client }}">{{ ucfirst($etat_client) }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="border-l-4 border-green-600 pl-4">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Fournisseur</h2>
            <div class="mb-4">
                <label for="fournisseur_id" class="block text-sm font-semibold text-gray-700">Fournisseur</label>
                <div class="flex space-x-2">
                    <select id="fournisseur_id" name="fournisseur_id" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1">
                        <option value="">-- Choisir un fournisseur --</option>
                        @foreach($fournisseurs as $fournisseur)
                            <option value="{{ $fournisseur->id }}">{{ $fournisseur->nom }}</option>
                        @endforeach
                    </select>
                    <button type="button" onclick="toggleNewSupplierForm()" class="bg-green-600 text-white px-4 py-2 rounded-lg shadow hover:bg-green-700 focus:ring-2 focus:ring-green-500">Ajouter</button>
                </div>
            </div>
            <div id="new-supplier-form" class="mb-4 hidden bg-gray-50 p-4 rounded-lg">
                <h3 class="text-lg font-semibold text-gray-700 mb-2">Nouveau Fournisseur</h3>
                <div class="mb-4">
                    <label for="new_fournisseur_nom" class="block text-sm font-semibold text-gray-700">Nom</label>
                    <input type="text" id="new_fournisseur_nom" name="new_fournisseur[nom]" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1">
                </div>
            </div>
        </div>
        <div class="border-l-4 border-green-600 pl-4">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Actions</h2>
            <div class="mb-4">
            <label for="actions" class="block text-sm font-semibold text-gray-700">Actions</label>
            <div id="actions-container">
                <div class="flex space-x-2 mb-2">
                <input type="text" name="actions[]" placeholder="Action" class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1">
                <select name="status[]" class="border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1">
                    @foreach ($statut as $statut)
                    <option value="{{ $statut }}">{{ ucfirst($statut) }}</option>
                    @endforeach
                </select>
                </div>
            </div>
            <div class="text-right mt-4">
                <button type="button" onclick="addActionField()" class="bg-green-600 text-white px-4 py-2 rounded-lg shadow hover:bg-green-700 focus:ring-2 focus:ring-green-500">Ajouter une action</button>
            </div>
            </div>
        </div>


<script>
function addActionField() {
    const container = document.getElementById('actions-container');
    const newActionField = document.createElement('div');
    newActionField.className = 'flex space-x-2 mb-2';
    newActionField.innerHTML = `
        <input type="text" name="actions[]" placeholder="Action" class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1">
        <select name="status[]" class="border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1">
            <option value="A faire">A faire</option>
            <option value="En cours">En cours</option>
            <option value="Terminé">Terminé</option>
        </select>
        <button type="button" onclick="removeActionField(this)" class="bg-red-600 text-white px-4 py-2 rounded-lg shadow hover:bg-red-700 focus:ring-2 focus:ring-red-500">-</button>
    `;
    container.appendChild(newActionField);
}

function removeActionField(button) {
    button.parentElement.remove();
}
</script>
       
        <button type="submit" class="w-full bg-green-600 text-white px-6 py-3 rounded-lg shadow hover:bg-green-700 focus:ring-2 focus:ring-green-500 flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
            Enregistrer
        </button>
        <div class="text-right mt-4 p-4">
        <a href="{{ route('panne.index') }}" class="text-gray-500 hover:underline">Retour</a>
    </div>
        </div>
        
    </form>
    
</div>
</div>
<script>
let clients = @json($clients);
let searchTimeout;
function toggleNewClientForm() {
    const form = document.getElementById('new-client-form');
    form.classList.toggle('hidden');
}
function toggleNewSupplierForm() {
    const form = document.getElementById('new-supplier-form');
    form.classList.toggle('hidden');
}
function highlightMatch(text, query) {
    if (!query.trim()) return text;
    const regex = new RegExp(`(${query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'gi');
    return text.replace(regex, '<span class="bg-yellow-200 font-medium">$1</span>');
}
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('client_search');
    const searchResults = document.getElementById('search_results');
    const clientIdInput = document.getElementById('client_id');
    const selectedClientDiv = document.getElementById('selected_client');
    const selectedClientInfo = document.getElementById('selected_client_info');
    searchInput.addEventListener('input', function(e) {
        clearTimeout(searchTimeout);
        const query = e.target.value.toLowerCase();
        searchTimeout = setTimeout(() => {
            if (query.trim() === '') {
                searchResults.classList.add('hidden');
                return;
            }
            const filtered = clients
                .filter(client => client.nom.toLowerCase().includes(query))
                .slice(0, 10);
            searchResults.innerHTML = '';
            if (filtered.length > 0) {
                const ul = document.createElement('ul');
                filtered.forEach(client => {
                    const li = document.createElement('li');
                    li.className = 'px-4 py-2 hover:bg-gray-100 cursor-pointer transition-colors duration-150';
                    li.innerHTML = highlightMatch(client.nom, query);
                    li.onclick = () => selectClient(client);
                    ul.appendChild(li);
                });
                searchResults.appendChild(ul);
                searchResults.classList.remove('hidden');
            } else {
                searchResults.innerHTML = `
                    <div class="p-4 text-gray-500">
                        <p class="text-sm">Aucun client trouvé</p>
                        <button onclick="toggleNewClientForm()" class="mt-2 text-green-600 hover:text-green-800 font-semibold text-sm flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"/></svg>
                            Ajouter un nouveau client
                        </button>
                    </div>
                `;
                searchResults.classList.remove('hidden');
            }
        }, 300);
    });
    function selectClient(client) {
        searchInput.value = client.nom;
        clientIdInput.value = client.id;
        searchResults.classList.add('hidden');
        selectedClientInfo.textContent = `${client.nom} (ID: ${client.id})`;
        selectedClientDiv.classList.remove('hidden');
    }
    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
            searchResults.classList.add('hidden');
        }
    });
});
</script>
@endsection