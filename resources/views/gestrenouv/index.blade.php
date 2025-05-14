@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8 px-4 sm:px-6 lg:px-8">
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
    $userStock = Auth::user()->stock ? strtolower(Auth::user()->stock->lieux) : null;
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
    const userDefaultStock = @json($userStock);
    let activeFilters = { lieu: new Set(), type: new Set(), statut: new Set() };
    let searchTerm = '';
    let groupByReference = true;
    const body = document.getElementById('pcrenouv-body');
    const header = document.getElementById('table-header');
    const searchInput = document.getElementById('searchInput');
    const groupByReferenceCheckbox = document.getElementById('groupByReference');
        const storedGroupBy = localStorage.getItem('groupByReference');
    if (storedGroupBy !== null) {
        groupByReference = storedGroupBy === 'true';
        groupByReferenceCheckbox.checked = groupByReference;
    }

    // Initialiser le filtre par défaut basé sur le stock de l'utilisateur
    if (userDefaultStock) {
        activeFilters.lieu.add(userDefaultStock);
    }

    const modal = document.getElementById('delete-modal');
    const closeModal = document.getElementById('closeModal');
    const cancelModal = document.getElementById('cancelModal');
    const deleteForm = document.getElementById('deleteForm');
    const deleteRouteTemplate = "{{ route('gestrenouv.destroy', ['id' => 'REPLACE_ID']) }}";

    function openDeleteModal(id) {
        deleteForm.action = deleteRouteTemplate.replace('REPLACE_ID', id);
        modal.classList.remove('hidden');
    }

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

    const generateItemRow = (r) => {
        const louerUrl = `{{ route('gestrenouv.louer', ['id' => 'REPLACE_ID']) }}`.replace('REPLACE_ID', r.id);
        const preterUrl = `{{ route('gestrenouv.preter', ['id' => 'REPLACE_ID']) }}`.replace('REPLACE_ID', r.id);
        const retourUrl = `{{ route('gestrenouv.retour', ['id' => 'REPLACE_ID']) }}`.replace('REPLACE_ID', r.id);
        const showUrl = `{{ route('gestrenouv.show', ['id' => 'REPLACE_ID']) }}`.replace('REPLACE_ID', r.id);
        const editUrl = `{{ route('gestrenouv.edit', ['id' => 'REPLACE_ID']) }}`.replace('REPLACE_ID', r.id);
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
                <form action="${retourUrl}" method="POST">
                    @csrf
                    @method('PUT')
                    <button type="submit"
                        ${r.statut === 'en stock' ? 'disabled' : ''}
                        class='${r.statut === 'en stock' ? 'text-gray-400 cursor-not-allowed' : 'text-orange-600 font-semibold hover:underline'}'>
                        Retour
                    </button>
                </form>
            </div>
        `;

        const actions = `
            <form method='POST' class='inline-flex flex-wrap space-x-2'>
                @csrf
                @method('DELETE')
                <button type='button' class='text-green-600 hover:text-green-700 font-semibold mr-2' onclick="window.location.href='${showUrl}'">Détails</button>
                <button type='button' class='text-yellow-600 hover:text-yellow-700 font-semibold mr-2' onclick="window.location.href='${editUrl}'">Modifier</button>
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

const generateGroupRow = (reference, items) => {
    const totalQuantity = items.reduce((sum, r) => sum + r.quantite, 0);
    if (totalQuantity <= 0) return '';

    const sites = [...new Set(items.map(r => r.lieux))].join(', ');
    const types = [...new Set(items.map(r => r.type))].join(', ');
    const statuts = [...new Set(items.map(r => r.statut))];

    const statutBadges = statuts.map(s => {
        const base = "px-2 py-1 rounded-full text-xs font-semibold mr-1 ";
        if (s === 'en stock') return `<span class="${base}bg-green-100 text-green-800">${s}</span>`;
        if (s === 'prêté') return `<span class="${base}bg-amber-100 text-amber-800">${s}</span>`;
        return `<span class="${base}bg-red-100 text-red-800">${s}</span>`;
    }).join('');

    const firstItemId = items[0]?.id;
    const louerUrl = `{{ route('gestrenouv.louer', ['id' => 'REPLACE_ID']) }}?isGroup=true&reference=${encodeURIComponent(reference)}`.replace('REPLACE_ID', firstItemId);
    const preterUrl = `{{ route('gestrenouv.preter', ['id' => 'REPLACE_ID']) }}?isGroup=true&reference=${encodeURIComponent(reference)}`.replace('REPLACE_ID', firstItemId);

    const option = `
        <div class='inline-flex flex-wrap space-x-2'>
            <button onclick="window.location.href='${louerUrl}'" class='text-blue-600 font-semibold hover:underline'>Louer</button>
            <button onclick="window.location.href='${preterUrl}'" class='text-purple-600 font-semibold hover:underline'>Prêter</button>
        </div>
    `;

    const actions = `<div class="text-center"><span class="text-gray-500 text-sm">Actions sur les items individuels</span></div>`;

    return `
        <tr class="border-t hover:bg-gray-50 bg-gray-50">
            <td class="py-3 px-4 border border-gray-200 font-semibold">${reference}</td>
            <td class="py-3 px-4 border border-gray-200">${sites}</td>
            <td class="py-3 px-4 border border-gray-200">${types}</td>
            <td class="py-3 px-4 border border-gray-200">${statutBadges}</td>
            <td class="py-3 px-4 border border-gray-200 font-bold">${totalQuantity}</td>
            <td class="py-3 px-4 border border-gray-200">${option}</td>
            <td class="py-3 px-4 border border-gray-200">${actions}</td>
        </tr>
    `;
};


    function renderTable() {
        header.innerHTML = createTableHeader();
        let filteredData = données.filter(filtreOK);
        let tableRows = '';

        if (groupByReference) {
            const referenceGroups = {};
            filteredData.forEach(item => {
                if (!referenceGroups[item.reference]) referenceGroups[item.reference] = [];
                referenceGroups[item.reference].push(item);
            });

            tableRows = Object.entries(referenceGroups)
                .map(([reference, items]) => generateGroupRow(reference, items))
                .filter(row => row)
                .join('');
        } else {
            tableRows = filteredData.map(generateItemRow).join('');
        }

        body.innerHTML = tableRows.length > 0
            ? tableRows
            : `<tr><td colspan="${groupByReference ? 7 : 9}" class="py-4 text-center text-gray-500">Aucun résultat trouvé. Veuillez modifier votre recherche ou réinitialiser les filtres.</td></tr>`;

        updateFilterCounts();

        const tableTitle = document.getElementById('table-title');
        tableTitle.textContent = groupByReference ? `PCRenouv groupés par référence` : `Liste des PCRenouvs`;
    }

    searchInput.addEventListener('input', (e) => {
        searchTerm = e.target.value;
        renderTable();
    });

    groupByReferenceCheckbox.addEventListener('change', (e) => {
        groupByReference = e.target.checked;
        localStorage.setItem('groupByReference', groupByReference); // sauvegarde
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

    // Réapplique le filtre de lieu par défaut après réinitialisation
    if (userDefaultStock) {
        activeFilters.lieu.add(userDefaultStock);
        document.querySelectorAll('.filter-btn[data-type="lieu"]').forEach(btn => {
            if (btn.dataset.filter.toLowerCase() === userDefaultStock) {
                btn.classList.add('ring-4', 'ring-blue-600');
            }
        });
    }

    renderTable();
});

document.addEventListener('DOMContentLoaded', function() {
    // Appliquer le filtre de lieu de l'utilisateur dès le chargement
    if (userDefaultStock) {
        activeFilters.lieu.add(userDefaultStock);
        document.querySelectorAll('.filter-btn[data-type="lieu"]').forEach(btn => {
            if (btn.dataset.filter.toLowerCase() === userDefaultStock) {
                btn.classList.add('ring-4', 'ring-blue-600');
            }
        });
    }

    renderTable();
});

    closeModal.addEventListener('click', closeDeleteModal);
    cancelModal.addEventListener('click', closeDeleteModal);
    window.addEventListener('click', (e) => {
        if (e.target === modal) closeDeleteModal();
    });


</script>

@endsection