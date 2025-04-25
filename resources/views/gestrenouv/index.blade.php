@extends('layouts.app')

@section('content')

<div class="container mx-auto py-8 px-4 sm:px-6 lg:px-8 min-h-screen">
    <h1 class="text-3xl font-bold mb-8 px-4 pt-10 text-gray-800">Tableau de Bord des PCRenouvs</h1>

    <h2 class="text-xl sm:text-2xl font-semibold px-2 sm:px-4 py-2 text-gray-700 ">Nombre de PCRenouv par Site</h2>
    {{-- Filtres par lieu --}}
    <div class='grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6 mb-8 px-2 sm:px-4'>
        @foreach(['Mont de Marsan', 'Aire sur Adour'] as $lieu)
        <div class="filter-btn {{ $lieu === 'Mont de Marsan' ? 'bg-green-600 hover:bg-green-700 ring-blue-500' : 'bg-red-600 hover:bg-red-700 ring-blue-500' }} text-white text-center py-4 sm:py-6 rounded-lg shadow-md cursor-pointer" data-filter="{{ strtolower($lieu) }}" data-type="lieu">
            <div class="text-2xl sm:text-3xl font-bold count-display">
                {{ $pcrenouvs->filter(fn($r) => optional($r->stocks->first())->lieux === $lieu && strtolower($r->statut) === 'en stock')->sum('quantite') }}
            </div>
            <div class="text-sm sm:text-lg">{{ $lieu }}</div>
        </div>
        @endforeach
    </div>

    <h2 class="text-xl sm:text-2xl font-semibold px-2 sm:px-4 py-2 text-gray-700">Nombre de PCRenouv par Type</h2>
    {{-- Filtres par type --}}
    <div class='grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6 mb-8 px-2 sm:px-4'>
        @foreach(App\Models\PCRenouv::TYPES as $type)
        <div class="filter-btn {{ $loop->index % 2 === 0 ? 'bg-green-600 hover:bg-green-700 ring-blue-500' : 'bg-red-600 hover:bg-red-700 ring-blue-500' }} text-white text-center py-4 sm:py-6 rounded-lg shadow-md cursor-pointer" data-filter="{{ strtolower($type) }}" data-type="type">
            <div class="text-2xl sm:text-3xl font-bold count-display">
                {{ $pcrenouvs->filter(fn($r) => strtolower($r->type) === strtolower($type) && in_array(strtolower($type), ['portable', 'fixe']) && $r->quantite > 0)->sum('quantite') }}
            </div>
            <div class="text-sm sm:text-lg">{{ $type }}</div>
        </div>
        @endforeach
    </div>

    <h2 class="text-xl sm:text-2xl font-semibold px-2 sm:px-4 py-2 text-gray-700">Nombre de PCRenouv par Statut</h2>
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
            <div class="text-2xl sm:text-3xl font-bold count-display">
                {{ $pcrenouvs->filter(fn($r) => strtolower($r->statut) === strtolower($statut) && $r->quantite > 0)->sum('quantite') }}
            </div>
            <div class="text-sm sm:text-lg">{{ $statut }}</div>
        </div>
        @endforeach
    </div>

    <div class="flex justify-between items-center mb-4 px-4">
        <button id="resetFilters" class="bg-gray-600 text-white px-6 py-3 rounded-lg shadow-md hover:bg-gray-700">Réinitialiser les filtres</button>

        <div class="flex space-x-4 ml-auto">
            <a href="{{ route('gestrenouv.create') }}" class="bg-green-600 text-white px-6 py-3 rounded-lg shadow-md hover:bg-green-700">Ajouter un PCRenouv</a>
        </div>
    </div>

    <div class="bg-white shadow rounded-lg p-4 sm:p-6">
        <h2 id="table-title" class="text-xl sm:text-2xl font-semibold text-gray-800 mb-4">Liste des PCRenouv</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full border-collapse border border-gray-200">
                <thead>
                    <tr class="bg-gray-100 text-left text-sm font-semibold text-gray-700">
                        <th class="py-3 px-4 border border-gray-200">Référence</th>
                        <th class="py-3 px-4 border border-gray-200">Client</th>
                        <th class="py-3 px-4 border border-gray-200">Site</th>
                        <th class="py-3 px-4 border border-gray-200">Type</th>
                        <th class="py-3 px-4 border border-gray-200">Statut</th>
                        <th class="py-3 px-4 border border-gray-200">Quantité</th>
                        <th class="py-3 px-4 border border-gray-200">Options</th>
                        <th class="py-3 px-4 border border-gray-200">Actions</th>
                    </tr>
                </thead>
                <tbody id="pcrenouv-body">
                    {{-- Rempli par JS --}}
                </tbody>
            </table>
        </div>
    </div>
</div>

@php
    $csrf = csrf_token();
    $renouvellements = $pcrenouvs->map(function($r) use($csrf) {
        $site = optional($r->stocks->first())->lieux ?? 'Non défini';
        $louerUrl = route('gestrenouv.louer', ['id' => $r->id]);
        $preterUrl = route('gestrenouv.preter', ['id' => $r->id]);

        $isDisabled = in_array(strtolower($r->statut), ['loué', 'prêté']);

        $option = "
        <div class='inline-flex space-x-2'>
            <button onclick=\"window.location.href='{$louerUrl}'\" 
                class='" . ($isDisabled ? "text-gray-400 cursor-not-allowed" : "text-blue-600 font-semibold hover:underline") . "' 
                " . ($isDisabled ? "disabled" : "") . ">
                Louer
            </button>

            <button onclick=\"window.location.href='{$preterUrl}'\" 
                class='" . ($isDisabled ? "text-gray-400 cursor-not-allowed" : "text-purple-600 font-semibold hover:underline") . "' 
                " . ($isDisabled ? "disabled" : "") . ">
                Prêter
            </button>

            <form action='".route('gestrenouv.retour', ['id' => $r->id])."' method='POST' id='retour-form'>
                ".csrf_field()."
                ".method_field('PUT')."
                <button type='submit'
                    ".(strtolower($r->statut) === 'en stock' ? 'disabled' : '')."
                    class='".(strtolower($r->statut) === 'en stock'
                        ? 'text-gray-400 cursor-not-allowed'
                        : 'text-orange-600 font-semibold hover:underline')."'>
                    Retour
                </button>
            </form>
        </div>";

        $actions = "
            <form action='".route('gestrenouv.destroy',$r->id)."' method='POST' class='inline-flex space-x-2'>
            <input type='hidden' name='_token' value='{$csrf}'>
            <input type='hidden' name='_method' value='DELETE'>
            <button type='button' class='text-green-600 hover:text-green-700 font-semibold mr-2' onclick=\"window.location.href='".route('gestrenouv.show',$r->id)."'\">Détails</button>
            <button type='button' class='text-yellow-600 hover:text-yellow-700 font-semibold mr-2' onclick=\"window.location.href='".route('gestrenouv.edit',$r->id)."'\">Modifier</button>
            <button type='submit' onclick=\"return confirm('Confirmer la suppression ?')\" class='text-red-600 hover:text-red-600 font-semibold'>Supprimer</button>
            </form>";
        return [
            'reference' => $r->reference,
            'code_client' => $r->clients->isNotEmpty() ? $r->clients->pluck('code_client')->join(', ') : '<span class="block text-center font-bold">-</span>',
            'lieux' => strtolower($site),
            'type' => strtolower($r->type),
            'statut' => strtolower($r->statut) === 'en stock' ?  strtolower($r->statut)  : strtolower($r->statut),
            'quantite' => $r->quantite,
            'option' => $option,
            'actions' => $actions
        ];
    });
@endphp

<script>
    const données = @json($renouvellements);
    let activeFilters = { lieu: new Set(), type: new Set(), statut: new Set() };
    const body = document.getElementById('pcrenouv-body');

    const calculateCount = (type, value) => {
        const filteredData = données.filter(r => {
            const lieuMatch = !activeFilters.lieu.size || [...activeFilters.lieu].some(f => r.lieux.includes(f));
            const typeMatch = !activeFilters.type.size || activeFilters.type.has(r.type);
            const statutMatch = !activeFilters.statut.size || activeFilters.statut.has(r.statut);
            return r.quantite > 0 && lieuMatch && typeMatch && statutMatch;
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

    const filtreOK = r =>
        r.quantite > 0 &&
        (!activeFilters.lieu.size || [...activeFilters.lieu].some(f => r.lieux.includes(f))) &&
        (!activeFilters.type.size || activeFilters.type.has(r.type)) &&
        (!activeFilters.statut.size || activeFilters.statut.has(r.statut));

    const rowHTML = r => `        
        <tr class="border-t hover:bg-gray-50">
            <td class="py-3 px-4 border border-gray-200">${r.reference}</td>
            <td class="py-3 px-4 border border-gray-200">${r.code_client}</td>
            <td class="py-3 px-4 border border-gray-200">${r.lieux}</td>
            <td class="py-3 px-4 border border-gray-200">${r.type}</td>
            <td class="py-3 px-4 border border-gray-200">${r.statut}</td>
            <td class="py-3 px-4 border border-gray-200">${r.quantite}</td>
            <td class="py-3 px-4 border border-gray-200">${r.option}</td>
            <td class="py-3 px-4 border border-gray-200">${r.actions}</td>
        </tr>
    `;

    function renderTable() {
        body.innerHTML = données.filter(filtreOK).map(rowHTML).join('');
        updateFilterCounts();
    }

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
        document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('ring-4', 'ring-blue-600'));
        renderTable();
    });

    renderTable();
</script>

@endsection