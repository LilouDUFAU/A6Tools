@extends('layouts.app')
@section('content')
<div class="max-w-6xl mx-auto my-4 py-8 px-6 bg-white shadow-md rounded-lg">
    <h1 class="text-3xl font-extrabold text-gray-800 mb-8">Nouvelle Préparation</h1>
    <form action="{{ route('prepatelier.store') }}" method="POST" class="space-y-6">
        @csrf
        <div class="border-l-4 border-green-600 pl-4">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Préparation</h2>
            <div class="mb-4">
                <label for="notes" class="block text-gray-700 font-bold mb-2">Notes</label>
                <textarea name="notes" id="notes" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm   px-2 py-1" maxlength="255" rows="4"></textarea>
            </div>
            <!-- Partie Employé -->
    <h2 class="text-2xl font-bold text-gray-800 mb-4">Employé</h2>
    <div class="mb-4">
        <label class="block text-sm font-semibold text-gray-700">Affecter un employé</label>
        <div class="relative w-full">
            <div class="relative">
                <input
                    type="text"
                    id="employe_search"
                    placeholder="Rechercher un employé..."
                    class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 pl-10 pr-4 py-2"
                >
                <input type="hidden" id="employe_id" name="employe_id">
                <div class="absolute left-3 top-1/2 transform -translate-y-1/4 text-gray-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                </div>
            </div>
            <div id="employe_results" class="absolute z-10 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-60 overflow-y-auto hidden"></div>
        </div>
    </div>

    <div id="selected_employe" class="mb-6 p-4 bg-green-50 rounded-lg border border-green-200 hidden">
        <h3 class="font-medium text-green-800">Employé sélectionné</h3>
        <p id="selected_employe_info" class="text-green-700"></p>
    </div>


        </div>

        <div class="border-l-4 border-green-600 pl-4">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Commande client</h2>
            <div class="mb-4">
                <label for="commande_id" class="block text-sm font-semibold text-gray-700">Choisir une commande client</label>
                <select id="commande_id" name="commande_id" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm px-2 py-1">
                    <option value="">-- Sélectionner une commande --</option>
                    @foreach ($commandes as $commande)
                        @if ($commande->client)
                            <option value="{{ $commande->id }}" {{ old('commande_id') == $commande->id ? 'selected' : '' }}>
                                Commande n°{{ $commande->id }} : {{ $commande->client->nom }} ({{ $commande->client->code_client }})
                            </option>
                        @endif
                    @endforeach
                </select>
            </div>
        </div>

        <div class="border-l-4 border-green-600 pl-4">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">To do list</h2>
        <div id="etapes-container">
            <div class="etape-item mb-4 flex items-center">
            <input type="checkbox" name="etapes_done[]" value="0" class="mr-2">
            <input type="text" name="etapes[]" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm   px-2 py-1" placeholder="Description de l'étape" required>
            </div>
        </div>
        <div class="text-right">
            <button type="button" id="add-etape-btn" class="mt-2 bg-green-600 text-white px-4 py-2 rounded-lg shadow hover:bg-green-700  ">
            Ajouter une étape
            </button>
        </div>

        <script>
            document.getElementById('add-etape-btn').addEventListener('click', function () {
            const container = document.getElementById('etapes-container');
            const newEtape = document.createElement('div');
            newEtape.classList.add('etape-item', 'mb-4', 'flex', 'items-center');
            newEtape.innerHTML = `
            <input type="checkbox" name="etapes_done[]" value="0" class="mr-2">
            <input type="text" name="etapes[]" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm   px-2 py-1" placeholder="Description de l'étape" required>
            `;
            container.appendChild(newEtape);
            });
        </script>
        </div>

        <div class="flex items-center justify-between">
            <button type="submit" class="w-full bg-green-600 text-white px-6 py-3 rounded-lg shadow hover:bg-green-700   flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                Enregistrer
            </button>
        </div>
    </form>
    
    <div class="text-right mt-4 p-4">
        <a href="{{ route('prepatelier.index') }}" class="text-gray-500 hover:underline">Retour</a>
    </div>
</div>

<script>
let employes = @json($employes); // assure-toi que $employes est bien passé depuis le contrôleur
let searchEmployeTimeout;

function highlightMatch(text, query) {
    if (!query.trim()) return text;
    const regex = new RegExp(`(${query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'gi');
    return text.replace(regex, '<span class="bg-yellow-200 font-medium">$1</span>');
}

document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('employe_search');
    const searchResults = document.getElementById('employe_results');
    const employeIdInput = document.getElementById('employe_id');
    const selectedEmployeDiv = document.getElementById('selected_employe');
    const selectedEmployeInfo = document.getElementById('selected_employe_info');

    searchInput.addEventListener('input', function(e) {
        clearTimeout(searchEmployeTimeout);
        const query = e.target.value.toLowerCase();

        searchEmployeTimeout = setTimeout(() => {
            if (query.trim() === '') {
                searchResults.classList.add('hidden');
                return;
            }

            const filtered = employes
                .filter(emp =>
                    (emp.nom && emp.nom.toLowerCase().includes(query)) ||
                    (emp.prenom && emp.prenom.toLowerCase().includes(query))
                )
                .slice(0, 10);

            searchResults.innerHTML = '';
            
            if (filtered.length > 0) {
                const ul = document.createElement('ul');
                filtered.forEach(emp => {
                    const li = document.createElement('li');
                    li.className = 'px-4 py-2 hover:bg-gray-100 cursor-pointer transition-colors duration-150';
                    li.innerHTML = highlightMatch(`${emp.prenom} ${emp.nom}`, query);
                    li.onclick = () => selectEmploye(emp);
                    ul.appendChild(li);
                });
                searchResults.appendChild(ul);
                searchResults.classList.remove('hidden');
            } else {
                searchResults.innerHTML = `<div class="p-4 text-gray-500 text-sm">Aucun employé trouvé</div>`;
                searchResults.classList.remove('hidden');
            }
        }, 300);
    });

    function selectEmploye(employe) {
        searchInput.value = `${employe.prenom} ${employe.nom}`;
        employeIdInput.value = employe.id;
        searchResults.classList.add('hidden');

        selectedEmployeInfo.textContent = `${employe.prenom} ${employe.nom} (ID: ${employe.id})`;
        selectedEmployeDiv.classList.remove('hidden');
    }

    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
            searchResults.classList.add('hidden');
        }
    });
});
</script>


@endsection
