@extends('layouts.app')

@section('content')

<div class="container mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <h1 class="text-2xl sm:text-4xl font-extrabold mb-6 text-gray-900 text-center sm:text-left">Tableau de Bord des PCRenouv</h1>

    <h2 class="text-xl sm:text-2xl font-semibold px-2 sm:px-4 py-2 text-gray-700">Nombre de commandes par Site</h2>
    {{-- Filtres par lieu --}}
    <div class='grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6 mb-8 px-2 sm:px-4'>
    @foreach(['Mont de Marsan', 'Aire sur Adour'] as $lieu)
    <div class="filter-btn {{ $lieu === 'Mont de Marsan' ? 'bg-green-700 hover:bg-green-800 ring-blue-500' : 'bg-red-700 hover:bg-red-800 ring-blue-500' }} text-white text-center py-4 sm:py-6 rounded-lg shadow-md cursor-pointer ring-4" data-filter="{{ strtolower($lieu) }}" data-type="lieu">
        <div class="text-2xl sm:text-3xl font-bold">
            {{ $pcrenouvs->filter(fn($r) => optional($r->stocks->first())->lieux === $lieu)->count() }}
        </div>
        <div class="text-sm sm:text-lg">{{ $lieu }}</div>
    </div>
    @endforeach
    </div>

    <h2 class="text-xl sm:text-2xl font-semibold px-2 sm:px-4 py-2 text-gray-700">Nombre de commandes par Type</h2>
    {{-- Filtres par type --}}
    <div class='grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6 mb-8 px-2 sm:px-4'>
    @foreach(App\Models\PCRenouv::TYPES as $type)
    <div class="filter-btn {{ $loop->index % 2 === 0 ? 'bg-green-700 hover:bg-green-800 ring-blue-500' : 'bg-red-700 hover:bg-red-800 ring-blue-500' }} text-white text-center py-4 sm:py-6 rounded-lg shadow-md cursor-pointer ring-4" data-filter="{{ strtolower($type) }}" data-type="type">
        <div class="text-2xl sm:text-3xl font-bold">
            {{ $pcrenouvs->filter(fn($r) => strtolower($r->type) === strtolower($type))->count() }}
        </div>
        <div class="text-sm sm:text-lg">{{ $type }}</div>
    </div>
    @endforeach
    </div>

    <h2 class="text-xl sm:text-2xl font-semibold px-2 sm:px-4 py-2 text-gray-700">Nombre de commandes par Statut</h2>
    {{-- Filtres par statut --}}
    <div class='grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6 mb-8 px-2 sm:px-4'>
    @foreach(App\Models\PCRenouv::STATUTS as $statut)
    <div class="filter-btn {{ $loop->index % 2 === 0 ? 'bg-green-700 hover:bg-green-800 ring-blue-500' : 'bg-red-700 hover:bg-red-800 ring-blue-500' }} text-white text-center py-4 sm:py-6 rounded-lg shadow-md cursor-pointer ring-4" data-filter="{{ strtolower($statut) }}" data-type="statut">
        <div class="text-2xl sm:text-3xl font-bold">
            {{ $pcrenouvs->filter(fn($r) => strtolower($r->statut) === strtolower($statut))->count() }}
        </div>
        <div class="text-sm sm:text-lg">{{ $statut }}</div>
    </div>
    @endforeach
    </div>

    <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
        <button id="resetFilters" class="bg-gray-500 text-white px-4 py-2 rounded-md shadow hover:bg-gray-600 w-full sm:w-auto">Réinitialiser les filtres</button>
        <a href="{{ route('gestrenouv.create') }}" class="bg-green-600 text-white px-4 py-2 rounded-md shadow hover:bg-green-600 w-full sm:w-auto text-center">Ajouter un PCRenouv</a>
    </div>

    <div class="bg-white shadow rounded-lg p-4 sm:p-6">
        <h2 id="table-title" class="text-xl sm:text-2xl font-semibold text-gray-800 mb-4">Liste des PCRenouv</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full border-collapse border border-gray-200">
                <thead>
                    <tr class="bg-gray-100 text-left text-sm font-semibold text-gray-700">
                        <th class="py-3 px-4 border border-gray-200">Référence</th>
                        <th class="py-3 px-4 border border-gray-200">Type</th>
                        <th class="py-3 px-4 border border-gray-200">Site</th>
                        <th class="py-3 px-4 border border-gray-200">Statut</th>
                        <th class="py-3 px-4 border border-gray-200">Quantité</th>
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
        $actions = "
            <form action='".route('gestrenouv.destroy',$r->id)."' method='POST' class='inline'>
            <input type='hidden' name='_token' value='{$csrf}'>
            <input type='hidden' name='_method' value='DELETE'>
            <button type='button' class='text-green-600 hover:text-green-700 font-semibold mr-2' onclick=\"window.location.href='".route('gestrenouv.show',$r->id)."'\">Détails</button>
            <button type='button' class='text-yellow-600 hover:text-yellow-700 font-semibold mr-2' onclick=\"window.location.href='".route('gestrenouv.edit',$r->id)."'\">Modifier</button>
            <button type='submit' onclick=\"return confirm('Confirmer la suppression ?')\" class='text-red-600 hover:text-red-700 font-semibold'>Supprimer</button>
            </form>";
        return [
            'reference' => $r->reference,
            'type' => strtolower($r->type),
            'lieux' => strtolower($site),
            'statut' => strtolower($r->statut),
            'quantite' => $r->quantite,
            'actions' => $actions
        ];
    });
@endphp

<script>
    const données = @json($renouvellements);
    let activeFilters = { lieu: new Set(), type: new Set(), statut: new Set() };
    const body = document.getElementById('pcrenouv-body');

    const filtreOK = r =>
        (!activeFilters.lieu.size || [...activeFilters.lieu].some(f => r.lieux.includes(f))) &&
        (!activeFilters.type.size || activeFilters.type.has(r.type)) &&
        (!activeFilters.statut.size || activeFilters.statut.has(r.statut));

    const rowHTML = r => `
        <tr class="border-t hover:bg-gray-50">
            <td class="py-3 px-4 border border-gray-200">${r.reference}</td>
            <td class="py-3 px-4 border border-gray-200">${r.type}</td>
            <td class="py-3 px-4 border border-gray-200">${r.lieux}</td>
            <td class="py-3 px-4 border border-gray-200">${r.statut}</td>
            <td class="py-3 px-4 border border-gray-200">${r.quantite}</td>
            <td class="py-3 px-4 border border-gray-200">${r.actions}</td>
        </tr>
    `;

    function renderTable() {
        body.innerHTML = données.filter(filtreOK).map(rowHTML).join('');
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
