@extends('layouts.app')
@section('content')
<div class="container mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <h1 class="text-3xl font-bold mb-8 px-4 pt-10 text-gray-800">Tableau de Bord des PC Renouv</h1>

    {{-- Filtres et Statistiques --}}
    <h2 class="text-2xl font-semibold px-4 py-2 text-gray-700">PC Renouv par Site</h2>
    @php
        use Illuminate\Support\Facades\DB;
        $pcMontDeMarsan = DB::table('pcrenouv_stock')
            ->join('stocks', 'pcrenouv_stock.stock_id', '=', 'stocks.id')
            ->where('stocks.lieux', 'like', 'mont de marsan')
            ->distinct()->pluck('pcrenouv_id');
        $pcAireSurAdour = DB::table('pcrenouv_stock')
            ->join('stocks', 'pcrenouv_stock.stock_id', '=', 'stocks.id')
            ->where('stocks.lieux', 'like', 'aire sur adour')
            ->distinct()->pluck('pcrenouv_id');
    @endphp
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-8 px-4">
        <div class="filter-btn bg-green-600 text-white text-center py-6 rounded-lg shadow-md hover:bg-green-700 cursor-pointer" data-filter="mont de marsan" data-type="lieu">
            <div class="text-3xl font-bold count-display">{{ $pcMontDeMarsan->count() }}</div>
            <div class="text-lg">Mont de Marsan</div>
        </div>
        <div class="filter-btn bg-red-600 text-white text-center py-6 rounded-lg shadow-md hover:bg-red-700 cursor-pointer" data-filter="aire sur adour" data-type="lieu">
            <div class="text-3xl font-bold count-display">{{ $pcAireSurAdour->count() }}</div>
            <div class="text-lg">Aire sur Adour</div>
        </div>
    </div>

    <h2 class="text-2xl font-semibold px-4 py-2 text-gray-700">PC Renouv par statut</h2>
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8 px-4">
        @php
            $statutCouleurs = [
                'en stock' => 'bg-green-600 hover:bg-green-700',
                'prêté' => 'bg-yellow-600 hover:bg-yellow-700',
                'loué' => 'bg-red-600 hover:bg-red-700'
            ];
        @endphp
        @foreach($statutCouleurs as $statut => $classes)
        <div class="filter-btn {{ $classes }} text-white text-center py-6 rounded-lg shadow-md cursor-pointer" data-filter="{{ $statut }}" data-type="statut">
            <div class="text-3xl font-bold count-display">{{ $pcrenouvs->where('statut', $statut)->count() }}</div>
            <div class="text-lg">{{ ucfirst($statut) }}</div>
        </div>
        @endforeach
    </div>

    <h2 class="text-2xl font-semibold px-4 py-2 text-gray-700">PC Renouv par type</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-8 px-4">
        @php
            $allTypes = ['fixe', 'portable'];
            $typeCouleurs = [
                'fixe' => 'bg-green-600 hover:bg-green-700',
                'portable' => 'bg-red-600 hover:bg-red-700',
            ];
        @endphp
        @foreach($allTypes as $type)
        <div class="filter-btn {{ $typeCouleurs[$type] ?? 'bg-gray-600 hover:bg-gray-700' }} text-white text-center py-6 rounded-lg shadow-md cursor-pointer" data-filter="{{ strtolower($type) }}" data-type="type">
            <div class="text-3xl font-bold count-display">{{ $pcrenouvs->where('type', $type)->count() }}</div>
            <div class="text-lg">{{ ucfirst($type) }}</div>
        </div>
        @endforeach
    </div>

    <div class="flex flex-wrap justify-between items-center mb-4 px-4 gap-4">
        <div class="flex flex-wrap gap-4 w-full sm:w-auto">
            <button id="resetFilters" class="bg-gray-600 text-white px-6 py-3 rounded-lg shadow-md hover:bg-gray-700 w-full sm:w-auto">Réinitialiser les filtres</button>
            <a href="{{ route('locpret.index') }}" class="bg-indigo-600 text-white px-6 py-3 rounded-lg shadow-md hover:bg-indigo-700 w-full sm:w-auto text-center">Liste des Loc/Prêts</a>
            <a href="{{ route('locpret.create') }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg shadow-md hover:bg-blue-700 w-full sm:w-auto text-center">Nouveau Loc/Prêt</a>
        </div>
        <a href="{{ route('gestrenouv.create') }}" class="bg-green-600 text-white px-6 py-3 rounded-lg shadow-md hover:bg-green-700 text-center w-full sm:w-auto">Ajouter un PC</a>
    </div>

    <div class="bg-white shadow rounded-lg p-4 sm:p-6">
        <h2 id="table-title" class="text-xl sm:text-2xl font-semibold text-gray-800 mb-4">Liste des PC Renouv</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white shadow-md rounded-lg">
                <thead>
                    <tr id="table-headers" class="bg-gray-100 text-left text-sm font-semibold text-gray-700">
                        <th class="py-3 px-4 border border-gray-200">Référence</th>
                        <th class="py-3 px-4 border border-gray-200">N° Série</th>
                        <th class="py-3 px-4 border border-gray-200">Type</th>
                        <th class="py-3 px-4 border border-gray-200">Statut</th>
                        <th class="py-3 px-4 border border-gray-200">Client/Magasin</th>
                        <th class="py-3 px-4 border border-gray-200">Actions</th>
                    </tr>
                </thead>
                <tbody id="pcrenouv-body">
                    {{-- Dynamique --}}
                </tbody>
            </table>
        </div>
    </div>
    <div id="toast" class="fixed bottom-4 right-4 hidden">
        <div class="px-4 py-3 rounded-lg shadow-lg flex items-center space-x-2">
            <span class="message"></span>
        </div>
    </div>

@php
    $csrf = csrf_token();
    $pcData = $pcrenouvs->map(function($pc) use($csrf) {
        $lieux = DB::table('pcrenouv_stock')
            ->join('stocks','pcrenouv_stock.stock_id','=','stocks.id')
            ->where('pcrenouv_stock.pcrenouv_id',$pc->id)
            ->distinct()->pluck('stocks.lieux')->implode(', ');
        
        $locpret = $pc->locprets->first();
        $client = $locpret ? $locpret->clients : null;
        
        $clientInfo = '';
        if($pc->statut == 'loué' || $pc->statut == 'prêté') {
            if($client) {
                $clientInfo = "<strong>{$client->code_client}</strong> ({$client->nom})";
            } else {
                $clientInfo = 'Indéfini';
            }
        } else {
            $clientInfo = $lieux ?: '-';
        }
        
        $statutClass = '';
        $statutDisplay = '';
        if($pc->statut == 'en stock') {
            $statutClass = 'bg-green-100 text-green-800';
            $statutDisplay = 'En stock';
        } elseif($pc->statut == 'prêté') {
            $statutClass = 'bg-blue-100 text-blue-800';
            $statutDisplay = 'Prêté';
        } elseif($pc->statut == 'loué') {
            $statutClass = 'bg-yellow-100 text-yellow-800';
            $statutDisplay = 'Loué';
        }
        
        $actions = 
            "<a href='".route('gestrenouv.show', $pc)."' class='font-semibold text-green-600 hover:text-green-700 mr-2'>".
            "Détails".
            "</a>".
            "<a href='".route('gestrenouv.edit', $pc->id)."' class='font-semibold text-yellow-600 hover:text-yellow-700 mr-2'>".
            "Modifier".
            "</a>".
            "<form action='".route('gestrenouv.destroy', $pc)."' method='POST' class='inline'>".
            "<input type='hidden' name='_token' value='{$csrf}'>".
            "<input type='hidden' name='_method' value='DELETE'>".
            "<button type='button' onclick=\"openModal({$pc->id})\" class='font-semibold text-red-600 hover:text-red-700 mr-2'>".
            "Supprimer".
            "</button>".
            "</form>";
            
        return [
            'id' => $pc->id,
            'reference' => $pc->reference,
            'numero_serie' => $pc->numero_serie,
            'type' => $pc->type,
            'type_lower' => strtolower($pc->type),
            'statut' => $pc->statut,
            'statut_display' => $statutDisplay,
            'statut_class' => $statutClass,
            'client_info' => $clientInfo,
            'lieux' => $lieux ?: '',
            'actions' => $actions
        ];
    });
@endphp

<div id="modal" class="fixed inset-0 z-50 hidden bg-gray-800/40 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-lg w-11/12 sm:w-1/2 lg:w-1/3">
        <div class="px-4 py-2 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-800">Confirmation de Suppression</h3>
            <button id="closeModal" class="text-gray-600 hover:text-gray-800">&times;</button>
        </div>
        <div class="p-4">
            <p class="text-gray-700">Êtes-vous sûr de vouloir supprimer ce PC? Cette action est irréversible.</p>
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
</div>

<script>
    const modal = document.getElementById('modal');
    const closeModal = document.getElementById('closeModal');
    const cancelModal = document.getElementById('cancelModal');
    const deleteForm = document.getElementById('deleteForm');
    const deleteRouteTemplate = "{{ route('gestrenouv.destroy', ':id') }}";

    function openModal(id) {
        deleteForm.action = deleteRouteTemplate.replace(':id', id);
        modal.classList.remove('hidden');
    }

    function closeModalHandler() {
        modal.classList.add('hidden');
    }

    closeModal.addEventListener('click', closeModalHandler);
    cancelModal.addEventListener('click', closeModalHandler);
    window.addEventListener('click', (e) => {
        if (e.target === modal) closeModalHandler();
    });
</script>
<script>
// Ajout d'une variable pour stocker le stock de l'utilisateur connecté
// Cette donnée devra être fournie par le backend
const données = @json($pcData);
let activeFilters = {lieu:new Set(),statut:new Set(),type:new Set()};
const body = document.getElementById('pcrenouv-body');
const titre = document.getElementById('table-title');
const headers = document.getElementById('table-headers');

// Récupérer le stock lié à l'utilisateur connecté depuis une variable PHP
// Ajoutez cette ligne dans votre backend juste avant ce script:
// $userStock = Auth::user()->stock ? Auth::user()->stock->lieux : null;
const userStock = "{{ $userStock ?? '' }}".toLowerCase();

// Appliquer automatiquement le filtre basé sur le stock de l'utilisateur
function applyUserStockFilter() {
    if (userStock) {
        // Trouver le bouton de filtre correspondant au stock de l'utilisateur
        const stockButtons = document.querySelectorAll('.filter-btn[data-type="lieu"]');
        stockButtons.forEach(btn => {
            const stockName = btn.dataset.filter.toLowerCase();
            if (stockName === userStock) {
                // Appliquer le filtre
                activeFilters.lieu.add(stockName);
                btn.classList.add('ring-4', 'ring-blue-500');
            }
        });
    }
}

const calculateCount = (type, value) => {
    const filteredData = données.filter(pc => {
        const lieuMatch = !activeFilters.lieu.size || [...activeFilters.lieu].some(f => pc.lieux.toLowerCase().includes(f));
        const statutMatch = !activeFilters.statut.size || activeFilters.statut.has(pc.statut);
        const typeMatch = !activeFilters.type.size || activeFilters.type.has(pc.type_lower);
        return lieuMatch && statutMatch && typeMatch;
    });

    return filteredData.filter(pc => {
        if (type === 'lieu') return pc.lieux.toLowerCase().includes(value.toLowerCase());
        if (type === 'statut') return pc.statut === value.toLowerCase();
        return pc.type_lower === value.toLowerCase();
    }).length;
};

const updateFilterCounts = () => {
    document.querySelectorAll('.filter-btn').forEach(btn => {
        const type = btn.dataset.type;
        const value = btn.dataset.filter;
        const count = calculateCount(type, value);
        btn.querySelector('.count-display').textContent = count;
    });
};

const defaultHeaders = `
    <th class="py-3 px-4 border border-gray-200">Référence</th>
    <th class="py-3 px-4 border border-gray-200">N° Série</th>
    <th class="py-3 px-4 border border-gray-200">Type</th>
    <th class="py-3 px-4 border border-gray-200">Statut</th>
    <th class="py-3 px-4 border border-gray-200">Client/Magasin</th>
    <th class="py-3 px-4 border border-gray-200">Actions</th>
`;

const filtreOK = pc =>
    (!activeFilters.lieu.size || [...activeFilters.lieu].some(f => pc.lieux.toLowerCase().includes(f))) &&
    (!activeFilters.statut.size || activeFilters.statut.has(pc.statut)) &&
    (!activeFilters.type.size || activeFilters.type.has(pc.type_lower));

const rowHTML = pc => `
    <tr class="border-t hover:bg-gray-50">
        <td class="py-3 px-4 border border-gray-200">${pc.reference}</td>
        <td class="py-3 px-4 border border-gray-200">${pc.numero_serie}</td>
        <td class="py-3 px-4 border border-gray-200">${pc.type}</td>
        <td class="py-3 px-4 border border-gray-200">
            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${pc.statut_class}">
                ${pc.statut_display}
            </span>
        </td>
        <td class="py-3 px-4 border border-gray-200">${pc.client_info}</td>
        <td class="py-3 px-4 border border-gray-200">${pc.actions}</td>
    </tr>
`;

function renderDefault() {
    titre.textContent = 'Liste des PC Renouv';
    headers.innerHTML = defaultHeaders;
    body.innerHTML = données.filter(filtreOK).map(pc => rowHTML(pc)).join('');
    updateFilterCounts();
}

document.querySelectorAll('.filter-btn').forEach(b => b.addEventListener('click', () => {
    const t = b.dataset.type, v = b.dataset.filter.toLowerCase();
    if (activeFilters[t].has(v)) activeFilters[t].delete(v);
    else activeFilters[t].add(v);
    b.classList.toggle('ring-4'); b.classList.toggle('ring-blue-500');
    renderDefault();
}));

document.getElementById('resetFilters').addEventListener('click', () => {
    activeFilters = {lieu:new Set(), statut:new Set(), type:new Set()};
    document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('ring-4','ring-blue-500'));
    // Si l'utilisateur est associé à un stock, le réappliquer après la réinitialisation
    applyUserStockFilter();
    renderDefault();
});

// Toast notification handling
const toast = document.getElementById('toast');
const toastContent = toast.querySelector('.message');

function showToast(message, type) {
    const bgColor = type === 'success' ? 'bg-green-600' : 'bg-red-600';
    toast.firstElementChild.className = `px-4 py-3 rounded-lg shadow-lg flex items-center space-x-2 text-white ${bgColor}`;
    toastContent.textContent = message;
    toast.classList.remove('hidden');
    setTimeout(() => toast.classList.add('hidden'), 3000);
}

// Appliquer le filtre de stock utilisateur au chargement initial
applyUserStockFilter();

// Initialiser l'affichage
renderDefault();
</script>
@endsection