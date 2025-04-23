@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8 px-4 sm:px-6 lg:px-8 min-h-screen">
    <h1>Louer un PC</h1>
    <form action="{{ route('gestrenouv.addLoc', $pcrenouv->id) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="border-l-4 border-green-600 pl-4">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">PCRenouv</h2>

            <div class="mb-4">
                <label for="reference" class="block text-gray-700 font-bold mb-2">Référence</label>
                <input type="text" name="reference" id="reference"
                    value="{{ old('reference', 'location-' . $pcrenouv->reference . '-' . now()->format('YmdHis')) }}"
                    class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1"
                    required maxlength="255">
            </div>

            <div class="mb-4">
                <label for="quantite" class="block text-gray-700 font-bold mb-2">Quantité</label>
                <input type="number" name="quantite" id="quantite"
                    value=""
                    class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1"
                    required>
            </div>

            <div class="mb-4">
                <label for="caracteristiques" class="block text-gray-700 font-bold mb-2">Caractéristique</label>
                <input type="text" name="caracteristiques" id="caracteristiques"
                    value="{{ old('caracteristiques', $pcrenouv->caracteristiques) }}"
                    class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1"
                    maxlength="255">
            </div>

            <div class="mb-4">
                <label for="type" class="block text-gray-700 font-bold mb-2">Type</label>
                <select id="type" name="type"
                    class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1"
                    required>
                    <option value="">-- Sélectionner un type --</option>
                    @foreach ($type as $typeOption)
                        <option value="{{ $typeOption }}" {{ old('type', $pcrenouv->type) == $typeOption ? 'selected' : '' }}>
                            {{ ucfirst($typeOption) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label for="statut" class="block text-gray-700 font-bold mb-2">Statut</label>
                <select id="statut" name="statut"
                    class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1"
                    required>
                    <option value="" selected>-- Sélectionner un statut --</option>
                    @foreach ($statut as $statutOption)
                        <option value="{{ $statutOption }}" {{ old('statut', 'loué') == $statutOption ? 'selected' : '' }}>
                            {{ ucfirst($statutOption) }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="border-l-4 border-green-600 pl-4">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Magasin</h2>
            <div class="mb-4">
                <label for="stock_id" class="block text-sm font-semibold text-gray-700">Choisir un site</label>
                <select id="stock_id" name="stock_id"
                    class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1"
                    required>
                    <option value="">-- Sélectionner un site --</option>
                    @foreach (\App\Models\Stock::all() as $stock)
                        <option value="{{ $stock->id }}" {{ old('stock_id', $pcrenouv->stocks->first()?->id) == $stock->id ? 'selected' : '' }}>
                            {{ ucfirst($stock->lieux) }}
                        </option>
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
        </div>

        

        <div class="flex items-center justify-between">
            <button type="submit"
                class="w-full bg-green-600 text-white px-6 py-3 rounded-lg shadow hover:bg-green-700 focus:ring-2 focus:ring-green-500 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M5 13l4 4L19 7" />
                </svg>
                Louer
            </button>
        </div>
    </form>

    <div class="text-right mt-4 p-4">
        <a href="{{ route('gestrenouv.index') }}" class="text-gray-500 hover:underline">Retour</a>
</div>
</div>

<script>
    let clients = @json($clients);
    let searchTimeout;

    function toggleNewClientForm() {
        const form = document.getElementById('new-client-form');
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
