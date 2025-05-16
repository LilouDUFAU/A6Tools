@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto my-4 py-8 px-6 bg-white shadow-md rounded-lg">
    <h1 class="text-3xl font-extrabold text-gray-800 mb-8">Ajouter une Location/Prêt</h1>
    
    <form method="POST" action="{{ route('locpret.store') }}" class="space-y-6">
        @csrf

        <!-- Partie Client -->
        <div class="border-l-4 border-green-600 pl-4">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Information Client</h2>
            
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
                @error('client_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div id="new-client-form" class="mb-4 hidden bg-gray-50 p-4 rounded-lg">
                <h3 class="text-lg font-semibold text-gray-700 mb-2">Nouveau Client</h3>
                <div class="mb-4">
                    <label for="new_client_nom" class="block text-sm font-semibold text-gray-700">* Nom</label>
                    <input type="text" id="new_client_nom" name="new_client[nom]" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1">
                </div>
                <div class="mb-4">
                    <label for="code_client" class="block text-sm font-semibold text-gray-700">* Code client</label>
                    <input type="text" id="code_client" name="new_client[code_client]" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1">
                </div>
                <div class="mb-4">
                    <label for="numero_telephone" class="block text-sm font-semibold text-gray-700">Numéro téléphone client</label>
                    <input type="text" id="numero_telephone" name="new_client[numero_telephone]" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1">
                </div>
            </div>

            <div id="selected_client" class="mb-6 p-4 bg-green-50 rounded-lg border border-green-200 hidden">
                <h3 class="font-medium text-green-800">Client sélectionné</h3>
                <p id="selected_client_info" class="text-green-700"></p>
            </div>
        </div>

        <!-- Partie Dates -->
        <div class="border-l-4 border-green-600 pl-4">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Informations de Période</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="mb-4">
                    <label for="date_debut" class="block text-sm font-semibold text-gray-700">* Date de début</label>
                    <input type="date" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1" id="date_debut" name="date_debut" value="{{ old('date_debut', date('Y-m-d')) }}" required>
                    @error('date_debut')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mb-4">
                    <label for="date_retour" class="block text-sm font-semibold text-gray-700">* Date de retour prévue</label>
                    <input type="date" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1" id="date_retour" name="date_retour" value="{{ old('date_retour', date('Y-m-d', strtotime('+30 days'))) }}" required>
                    @error('date_retour')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Partie Type d'opération -->
        <div class="border-l-4 border-green-600 pl-4">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Type d'Opération</h2>
            
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">* Sélectionner le type</label>
                <div class="flex items-center space-x-6">
                    <div class="flex items-center">
                        <input class="h-4 w-4 text-green-600 border-gray-300 focus:ring-green-500" type="radio" name="type_operation" id="type_pret" value="prêt" {{ old('type_operation', 'prêt') == 'prêt' ? 'checked' : '' }}>
                        <label class="ml-2 block text-sm text-gray-700" for="type_pret">Prêt</label>
                    </div>
                    <div class="flex items-center">
                        <input class="h-4 w-4 text-green-600 border-gray-300 focus:ring-green-500" type="radio" name="type_operation" id="type_location" value="location" {{ old('type_operation') == 'location' ? 'checked' : '' }}>
                        <label class="ml-2 block text-sm text-gray-700" for="type_location">Location</label>
                    </div>
                </div>
                @error('type_operation')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Partie PC disponibles -->
        <div class="border-l-4 border-green-600 pl-4">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Sélection des PC</h2>
            
            <div class="mb-4">
                <div class="bg-green-50 border border-green-200 text-green-800 rounded px-4 py-3 mb-4 text-sm">
                    Sélectionnez au moins un PC à prêter ou louer.
                </div>

                @if($pcrenouvs->count() > 0)
                    <div class="overflow-x-auto rounded-lg shadow">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-3 text-left font-medium text-gray-500">#</th>
                                    <th class="px-3 py-3 text-left font-medium text-gray-500">Numéro de série</th>
                                    <th class="px-3 py-3 text-left font-medium text-gray-500">Référence</th>
                                    <th class="px-3 py-3 text-left font-medium text-gray-500">Caractéristiques</th>
                                    <th class="px-3 py-3 text-left font-medium text-gray-500">Type</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                @foreach($pcrenouvs as $pcrenouv)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-3 py-3">
                                            <input class="h-4 w-4 text-green-600 border-gray-300 focus:ring-green-500" type="checkbox" name="pcrenouv_ids[]" value="{{ $pcrenouv->id }}" id="pc{{ $pcrenouv->id }}" {{ (is_array(old('pcrenouv_ids')) && in_array($pcrenouv->id, old('pcrenouv_ids'))) ? 'checked' : '' }}>
                                        </td>
                                        <td class="px-3 py-3">{{ $pcrenouv->numero_serie }}</td>
                                        <td class="px-3 py-3">{{ $pcrenouv->reference }}</td>
                                        <td class="px-3 py-3">{{ Str::limit($pcrenouv->caracteristiques, 50) }}</td>
                                        <td class="px-3 py-3">{{ $pcrenouv->type }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @error('pcrenouv_ids')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    @error('pcrenouv_ids.*')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                @else
                    <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 rounded px-4 py-3 text-sm">
                        Aucun PC disponible en stock pour le moment.
                    </div>
                @endif
            </div>
        </div>

        <button type="submit" class="w-full bg-green-600 text-white px-6 py-3 rounded-lg shadow hover:bg-green-700 focus:ring-2 focus:ring-green-500 flex items-center justify-center" {{ $pcrenouvs->count() > 0 ? '' : 'disabled' }}>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                <polyline points="17 21 17 13 7 13 7 21"/>
                <polyline points="7 3 7 8 15 8"/>
            </svg>
            Enregistrer
        </button>
    </form>
    
    <div class="text-right mt-4 p-4">
        <a href="{{ route('locpret.index') }}" class="text-gray-500 hover:underline">Retour</a>
    </div>
</div>

<script>
let clients = @json($clients);
let clientSearchTimeout;

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
    // Client search setup
    const clientSearchInput = document.getElementById('client_search');
    const clientSearchResults = document.getElementById('client_search_results');
    const clientIdInput = document.getElementById('client_id');
    const selectedClientDiv = document.getElementById('selected_client');
    const selectedClientInfo = document.getElementById('selected_client_info');

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
                .filter(client => (client.nom && client.nom.toLowerCase().includes(query)) || 
                                  (client.prenom && client.prenom.toLowerCase().includes(query)))
                .slice(0, 10);

            clientSearchResults.innerHTML = '';
            
            if (filtered.length > 0) {
                const ul = document.createElement('ul');
                filtered.forEach(client => {
                    const li = document.createElement('li');
                    li.className = 'px-4 py-2 hover:bg-gray-100 cursor-pointer transition-colors duration-150';
                    const displayName = client.prenom ? `${client.nom} ${client.prenom}` : client.nom;
                    li.innerHTML = highlightMatch(displayName, query);
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

    function selectClient(client) {
        const displayName = client.prenom ? `${client.nom} ${client.prenom}` : client.nom;
        clientSearchInput.value = displayName;
        clientIdInput.value = client.id;
        clientSearchResults.classList.add('hidden');
        
        selectedClientInfo.textContent = `${displayName} (ID: ${client.id})`;
        selectedClientDiv.classList.remove('hidden');
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!clientSearchInput.contains(e.target) && !clientSearchResults.contains(e.target)) {
            clientSearchResults.classList.add('hidden');
        }
    });
});
</script>
@endsection