@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8 px-4 sm:px-6 lg:px-8 min-h-screen">
    <h1 class="text-3xl font-bold mb-8 px-4 pt-10 text-gray-800">Tableau de Bord des PCRenouvs</h1>

    <h2 class="text-2xl font-semibold px-2 sm:px-4 py-2 text-gray-700">Nombre de PCRenouv par Site</h2>
    {{-- Filtres par lieu --}}
    <div class='grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6 mb-8 px-2 sm:px-4'>
        @foreach(['Mont de Marsan', 'Aire sur Adour'] as $lieu)
        <div class="filter-btn {{ $lieu === 'Mont de Marsan' ? 'bg-green-600 hover:bg-green-700 ring-blue-500' : 'bg-red-600 hover:bg-red-700 ring-blue-500' }} text-white text-center py-4 sm:py-6 rounded-lg shadow-md cursor-pointer" data-filter="{{ strtolower($lieu) }}" data-type="lieu">
            <div class="text-3xl sm:text-3xl font-bold count-display">
                {{ $pcrenouvs->filter(fn($r) => optional($r->stocks->first())->lieux === $lieu && strtolower($r->statut) === 'en stock')->sum(function($r) { return $r->stocks->first()?->pivot->quantite ?? 0; }) }}
            </div>
            <div class="text-lg">{{ $lieu }}</div>
        </div>
        @endforeach
    </div>

    <h2 class="text-2xl font-semibold px-2 sm:px-4 py-2 text-gray-700">Nombre de PCRenouv par Type</h2>
    {{-- Filtres par type --}}
    <div class='grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6 mb-8 px-2 sm:px-4'>
        @foreach(App\Models\PCRenouv::TYPES as $type)
        <div class="filter-btn {{ $loop->index % 2 === 0 ? 'bg-green-600 hover:bg-green-700 ring-blue-500' : 'bg-red-600 hover:bg-red-700 ring-blue-500' }} text-white text-center py-4 sm:py-6 rounded-lg shadow-md cursor-pointer" data-filter="{{ strtolower($type) }}" data-type="type">
            <div class="text-3xl sm:text-3xl font-bold count-display">
                {{ $pcrenouvs->filter(fn($r) => strtolower($r->type) === strtolower($type) && strtolower($r->statut) === 'en stock')->sum(function($r) { return $r->stocks->first()?->pivot->quantite ?? 0; }) }}
            </div>
            <div class="text-lg">{{ $type }}</div>
        </div>
        @endforeach
    </div>

    <h2 class="text-2xl font-semibold px-2 sm:px-4 py-2 text-gray-700">Nombre de PCRenouv par Statut</h2>
    {{-- Filtres par statut --}}
    <div class='grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 sm:gap-6 mb-8 px-2 sm:px-4'>
        @foreach(App\Models\PCRenouv::STATUTS as $statut)
        <div class="filter-btn 
            {{ strtolower($statut) === 'en stock' ? 'bg-green-600 hover:bg-green-700 ring-blue-500' : 
            (strtolower($statut) === 'prêté' ? 'bg-amber-600 hover:bg-amber-700 ring-blue-500' : 
            (strtolower($statut) === 'loué' ? 'bg-red-600 hover:bg-red-700 ring-blue-500' : 
            'bg-red-600 hover:bg-red-700 ring-blue-500')) }} 
            text-white text-center py-4 sm:py-6 rounded-lg shadow-md cursor-pointer" 
            data-filter="{{ strtolower($statut) }}" data-type="statut">
            <div class="text-3xl sm:text-3xl font-bold count-display">
                {{ $pcrenouvs->filter(fn($r) => strtolower($r->statut) === strtolower($statut))->sum(function($r) { return $r->stocks->first()?->pivot->quantite ?? 0; }) }}
            </div>
            <div class="text-lg">{{ $statut }}</div>
        </div>
        @endforeach
    </div>

    <div class="flex flex-col sm:flex-row justify-between items-center mb-4 px-4 space-y-4 sm:space-y-0">
        <button id="resetFilters" class="bg-gray-600 text-white px-6 py-3 rounded-lg shadow-md hover:bg-gray-700 w-full sm:w-auto">Réinitialiser les filtres</button>

        <div class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4 w-full sm:w-auto">
            <a href="{{ route('gestrenouv.create') }}" class="bg-green-600 text-white px-6 py-3 rounded-lg shadow-md hover:bg-green-700 w-full sm:w-auto text-center">Ajouter un PCRenouv</a>
        </div>
    </div>

    {{-- Barre de recherche --}}
    <div class="mb-6 px-4">
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                </svg>
            </div>
            <input
                type="text"
                id="searchInput"
                class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 shadow-sm"
                placeholder="Rechercher par référence ou numéro de série..."
            >
        </div>
    </div>

    {{-- Affichage par référence --}}
    <div class="mb-6 px-4">
        <label for="groupByReference" class="flex items-center cursor-pointer">
            <input type="checkbox" id="groupByReference" class="form-checkbox h-5 w-5 text-green-600" checked>
            <span class="ml-2 text-gray-700">Regrouper par référence</span>
        </label>
    </div>

    <div class="bg-white shadow rounded-lg p-4 sm:p-6">
        <h2 id="table-title" class="text-xl sm:text-2xl font-semibold text-gray-800 mb-4">Liste des PCRenouv</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full border-collapse border border-gray-200">
                <thead id="table-header">
                    <!-- Le contenu de l'en-tête sera généré dynamiquement par JavaScript -->
                </thead>
                <tbody id="pcrenouv-body">
                    {{-- Rempli par JS --}}
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modale de confirmation de suppression -->
<div id="delete-modal" class="fixed inset-0 z-50 hidden bg-gray-800/40 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-lg w-11/12 sm:w-1/2 lg:w-1/3">
        <div class="px-4 py-2 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-800">Confirmation de Suppression</h3>
            <button id="closeModal" class="text-gray-600 hover:text-gray-800">&times;</button>
        </div>
        <div class="p-4">
            <p class="text-gray-700">Êtes-vous sûr de vouloir supprimer ce PC Renouvo ? Cette action est irréversible.</p>
        </div>
        <div class="px-4 py-2 flex justify-end space-x-4">
            <button id="cancelModal" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">Annuler</button>
            <form id="deleteForm" method="POST" action="{{ route('gestrenouv.destroy', 0) }}">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700">Supprimer</button>
            </form>
        </div>
    </div>
</div>

@php
    $csrf = csrf_token();
    $renouvellements = $pcrenouvs->map(function($r) use($csrf) {
        $site = optional($r->stocks->first())->lieux ?? 'Non défini';
        $quantity = $r->stocks->first()?->pivot->quantite ?? 0;

        return [
            'id' => $r->id,
            'reference' => $r->reference,
            'numero_serie' => $r->numero_serie ?? '<span class="block text-center font-bold">-</span>',
            'code_client' => $r->clients->isNotEmpty() ? $r->clients->pluck('code_client')->join(', ') : '<span class="block text-center font-bold">-</span>',
            'lieux' => strtolower($site),
            'type' => strtolower($r->type),
            'statut' => strtolower($r->statut),
            'quantite' => $quantity,
        ];
    });
@endphp

<script>
    const données = @json($renouvellements);
    let activeFilters = { lieu: new Set(), type: new Set(), statut: new Set() };
    let searchTerm = '';
    let groupByReference = true;
    const body = document.getElementById('pcrenouv-body');
    const header = document.getElementById('table-header');
    const searchInput = document.getElementById('searchInput');
    const groupByReferenceCheckbox = document.getElementById('groupByReference');

    const modal = document.getElementById('delete-modal');
    const closeModal = document.getElementById('closeModal');
    const cancelModal = document.getElementById('cancelModal');
    const deleteForm = document.getElementById('deleteForm');
    const deleteRouteTemplate = "{{ route('gestrenouv.destroy', ':id') }}";

    // Fonction pour ouvrir la modale de confirmation de suppression
    function openDeleteModal(id) {
        deleteForm.action = deleteRouteTemplate.replace(':id', id);
        modal.classList.remove('hidden');
    }

    // Fonction pour fermer la modale
    function closeDeleteModal() {
        modal.classList.add('hidden');
    }

    const calculateCount = (type, value) => {
        const filteredData = données.filter(r => {
            const searchMatch = 
                searchTerm === '' || 
                r.reference.toLowerCase().includes(searchTerm.toLowerCase()) ||
                (r.numero_serie && r.numero_serie.toLowerCase().includes(searchTerm.toLowerCase()));

            const lieuMatch = !activeFilters.lieu.size || [...activeFilters.lieu].some(f => r.lieux.includes(f));
            const typeMatch = !activeFilters.type.size || activeFilters.type.has(r.type);
            const statutMatch = !activeFilters.statut.size || activeFilters.statut.has(r.statut);
            return r.quantite > 0 && searchMatch && lieuMatch && typeMatch && statutMatch;
        });

        return filteredData.filter(r => {
            if (type === 'lieu') return r.lieux.includes(value.toLowerCase());
            if (type === 'type') return r.type === value.toLowerCase();
            return r.statut === value.toLowerCase();
        }).reduce((sum, r) => sum + r.quantite, 0);
    };

    const updateFilterCounts = () => {
        document.querySelectorAll('.filter-btn').forEach(btn => {
            const type = btn.dataset.type;
            const value = btn.dataset.filter;
            const count = calculateCount(type, value);
            btn.querySelector('.count-display').textContent = count;
        });
    };

    const filtreOK = r => {
        const searchMatch = 
            searchTerm === '' || 
            r.reference.toLowerCase().includes(searchTerm.toLowerCase()) ||
            (r.numero_serie && r.numero_serie.toLowerCase().includes(searchTerm.toLowerCase()));

        return r.quantite > 0 &&
            searchMatch &&
            (!activeFilters.lieu.size || [...activeFilters.lieu].some(f => r.lieux.includes(f))) &&
            (!activeFilters.type.size || activeFilters.type.has(r.type)) &&
            (!activeFilters.statut.size || activeFilters.statut.has(r.statut));
    };

    // Create appropriate table header based on grouping mode
    const createTableHeader = () => {
        if (groupByReference) {
            return `
                <tr class="bg-gray-100 text-left font-semibold text-gray-700">
                    <th class="py-3 px-4 border border-gray-200">Référence</th>
                    <th class="py-3 px-4 border border-gray-200">Site</th>
                    <th class="py-3 px-4 border border-gray-200">Type</th>
                    <th class="py-3 px-4 border border-gray-200">Statut</th>
                    <th class="py-3 px-4 border border-gray-200">Quantité</th>
                    <th class="py-3 px-4 border border-gray-200">Options</th>
                    <th class="py-3 px-4 border border-gray-200">Actions</th>
                </tr>
            `;
        } else {
            return `
                <tr class="bg-gray-100 text-left font-semibold text-gray-700">
                    <th class="py-3 px-4 border border-gray-200">Référence</th>
                    <th class="py-3 px-4 border border-gray-200">N° série</th>
                    <th class="py-3 px-4 border border-gray-200">Client</th>
                    <th class="py-3 px-4 border border-gray-200">Site</th>
                    <th class="py-3 px-4 border border-gray-200">Type</th>
                    <th class="py-3 px-4 border border-gray-200">Statut</th>
                    <th class="py-3 px-4 border border-gray-200">Quantité</th>
                    <th class="py-3 px-4 border border-gray-200">Options</th>
                    <th class="py-3 px-4 border border-gray-200">Actions</th>
                </tr>
            `;
        }
    };

    // Generate an individual row for a PCRenouv
    const generateItemRow = (r) => {
        const louerUrl = "{{ route('gestrenouv.louer', ':id') }}".replace(':id', r.id);
        const preterUrl = "{{ route('gestrenouv.preter', ':id') }}".replace(':id', r.id);
        const isDisabled = r.statut !== 'en stock';
        const canLoan = !isDisabled && r.quantite > 0;
        
        const option = `
            <div class='inline-flex flex-wrap space-x-2'>
                <button onclick="window.location.href='${louerUrl}'" 
                    class='${canLoan ? "text-blue-600 font-semibold hover:underline" : "text-gray-400 cursor-not-allowed"}' 
                    ${canLoan ? "" : "disabled"}>
                    Louer
                </button>
                <button onclick="window.location.href='${preterUrl}'" 
                    class='${canLoan ? "text-purple-600 font-semibold hover:underline" : "text-gray-400 cursor-not-allowed"}' 
                    ${canLoan ? "" : "disabled"}>
                    Prêter
                </button>
                <form action='{{ route("gestrenouv.retour", ":id") }}'.replace(':id', r.id) method='POST'>
                    @csrf
                    @method('PUT')
                    <button type='submit'
                        ${r.statut === 'en stock' ? 'disabled' : ''}
                        class='${r.statut === 'en stock' ? 'text-gray-400 cursor-not-allowed' : 'text-orange-600 font-semibold hover:underline'}'>
                        Retour
                    </button>
                </form>
            </div>
        `;
        
        const actions = `
            <form action='{{ route("gestrenouv.destroy", ":id") }}'.replace(':id', r.id) method='POST' class='inline-flex flex-wrap space-x-2'>
                @csrf
                @method('DELETE')
                <button type='button' class='text-green-600 hover:text-green-700 font-semibold mr-2' onclick="window.location.href='{{ route("gestrenouv.show", ":id") }}'.replace(':id', r.id)">Détails</button>
                <button type='button' class='text-yellow-600 hover:text-yellow-700 font-semibold mr-2' onclick="window.location.href='{{ route("gestrenouv.edit", ":id") }}'.replace(':id', r.id)">Modifier</button>
                <button type='button' class='text-red-600 hover:text-red-600 font-semibold' onclick='openDeleteModal(${r.id})'>Supprimer</button>
            </form>
        `;
        
        if (!groupByReference) {
            return `
                <tr class="border-t hover:bg-gray-50">
                    <td class="py-3 px-4 border border-gray-200">${r.reference}</td>
                    <td class="py-3 px-4 border border-gray-200">${r.numero_serie}</td>
                    <td class="py-3 px-4 border border-gray-200">${r.code_client}</td>
                    <td class="py-3 px-4 border border-gray-200">${r.lieux}</td>
                    <td class="py-3 px-4 border border-gray-200">${r.type}</td>
                    <td class="py-3 px-4 border border-gray-200">
                        <span class="${
                            r.statut === 'en stock' ? 'bg-green-100 text-green-800' : 
                            r.statut === 'prêté' ? 'bg-amber-100 text-amber-800' : 
                            'bg-red-100 text-red-800'
                        } px-2 py-1 rounded-full text-xs font-semibold">${r.statut}</span>
                    </td>
                    <td class="py-3 px-4 border border-gray-200">${r.quantite}</td>
                    <td class="py-3 px-4 border border-gray-200">${option}</td>
                    <td class="py-3 px-4 border border-gray-200">${actions}</td>
                </tr>
            `;
        }
    };

    // Generate a row for a group of PCRenouvs with the same reference
    const generateGroupRow = (reference, items) => {
        // Calculate total quantity and filter only in-stock items
        const inStockItems = items.filter(r => r.statut === 'en stock');
        const totalQuantity = inStockItems.reduce((sum, r) => sum + r.quantite, 0);
        
        if (totalQuantity <= 0) {
            return ''; // Don't show groups with no in-stock items
        }
        
        // Get unique sites
        const sites = [...new Set(inStockItems.map(r => r.lieux))].join(', ');
        
        // Get the type (should be the same for all items in the group)
        const type = inStockItems[0]?.type || '';
        
        // Only one item in the group? Use the individual row display instead
        if (inStockItems.length === 1 && !groupByReference) {
            return generateItemRow(inStockItems[0]);
        }
        
        // Get the ID of the first item for loaning/lending actions
        const firstItemId = inStockItems[0]?.id;
        
        // Build URLs with isGroup=true and reference parameters
        const louerUrl = `{{ route('gestrenouv.louer', ':id') }}?isGroup=true&reference=${encodeURIComponent(reference)}`.replace(':id', firstItemId);
        const preterUrl = `{{ route('gestrenouv.preter', ':id') }}?isGroup=true&reference=${encodeURIComponent(reference)}`.replace(':id', firstItemId);
        
        const option = `
            <div class='inline-flex flex-wrap space-x-2'>
                <button onclick="window.location.href='${louerUrl}'" 
                    class='text-blue-600 font-semibold hover:underline'>
                    Louer
                </button>
                <button onclick="window.location.href='${preterUrl}'" 
                    class='text-purple-600 font-semibold hover:underline'>
                    Prêter
                </button>
            </div>
        `;
        
        const actions = `
            <div class="text-center">
                <span class="text-gray-500 text-sm">Actions sur les items individuels</span>
            </div>
        `;
        
        return `        
            <tr class="border-t hover:bg-gray-50 bg-gray-50">
                <td class="py-3 px-4 border border-gray-200 font-semibold">${reference}</td>
                <td class="py-3 px-4 border border-gray-200">${sites}</td>
                <td class="py-3 px-4 border border-gray-200">${type}</td>
                <td class="py-3 px-4 border border-gray-200 text-center">
                    <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs font-semibold">en stock</span>
                </td>
                <td class="py-3 px-4 border border-gray-200 font-bold">${totalQuantity}</td>
                <td class="py-3 px-4 border border-gray-200">${option}</td>
                <td class="py-3 px-4 border border-gray-200">${actions}</td>
            </tr>
        `;
    };

    function renderTable() {
        // Create the appropriate header
        header.innerHTML = createTableHeader();
        
        let filteredData = données.filter(filtreOK);
        let tableRows = '';
        
        if (groupByReference) {
            // Group by reference
            const referenceGroups = {};
            
            filteredData.forEach(item => {
                if (!referenceGroups[item.reference]) {
                    referenceGroups[item.reference] = [];
                }
                referenceGroups[item.reference].push(item);
            });
            
            // Convert groups to HTML rows
            tableRows = Object.entries(referenceGroups)
                .map(([reference, items]) => generateGroupRow(reference, items))
                .filter(row => row) // Remove empty rows (groups with no in-stock items)
                .join('');
        } else {
            // Individual items view
            tableRows = filteredData.map(generateItemRow).join('');
        }
        
        body.innerHTML = tableRows.length > 0 
            ? tableRows
            : `<tr><td colspan="${groupByReference ? 7 : 9}" class="py-4 text-center text-gray-500">Aucun résultat trouvé. Veuillez modifier votre recherche ou réinitialiser les filtres.</td></tr>`;
        
        updateFilterCounts();
        
        // Mise à jour du titre avec le nombre de résultats
        const tableTitle = document.getElementById('table-title');
        if (groupByReference) {
            const groupCount = tableRows.split('<tr').length - 1;
            tableTitle.textContent = `PCRenouv groupés par référence`;
        } else {
            tableTitle.textContent = `Liste des PCRenouvs`;
        }
    }

    // Écouteurs d'événements
    searchInput.addEventListener('input', (e) => {
        searchTerm = e.target.value;
        renderTable();
    });

    groupByReferenceCheckbox.addEventListener('change', (e) => {
        groupByReference = e.target.checked;
        renderTable();
    });

    document.querySelectorAll('.filter-btn').forEach(btn =>
        btn.addEventListener('click', () => {
            const t = btn.dataset.type;
            const v = btn.dataset.filter.toLowerCase();
            if (activeFilters[t].has(v)) activeFilters[t].delete(v);
            else activeFilters[t].add(v);
            btn.classList.toggle('ring-4');
            btn.classList.toggle('ring-blue-600');
            renderTable();
        })
    );

    document.getElementById('resetFilters').addEventListener('click', () => {
        activeFilters = { lieu: new Set(), type: new Set(), statut: new Set() };
        searchTerm = '';
        searchInput.value = '';
        document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('ring-4', 'ring-blue-600'));
        renderTable();
    });

    // Écouteurs pour la modale
    closeModal.addEventListener('click', closeDeleteModal);
    cancelModal.addEventListener('click', closeDeleteModal);
    window.addEventListener('click', (e) => {
        if (e.target === modal) closeDeleteModal();
    });

    // Initialisation
    renderTable();
</script>

@endsection