@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <h1 class="text-3xl font-bold mb-8 px-4 pt-10 text-gray-800">Tableau de Bord des Commandes</h1>

    {{-- Alertes de délai de livraison --}}
    @if(!empty($alerteCommandes))
    <div class="">
        <h2 class="text-xl font-bold mb-2">⚠️ Alerte Délai de Livraison</h2>
        <div class="space-y-2">
            @foreach($alerteCommandes as $id => $alerte)
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-md">
            <p><i class="fa-solid fa-circle-exclamation" style="color: #C53030;"></i> <strong> Attention</strong>, il semble que la <a href="{{ route('gestcommande.show', $id) }}" class="underline hover:text-red-300 font-semibold">Commande n°{{ $id }}</a> (Client : {{ $alerte['commande']->client?->code_client ?? 'Non défini' }}) présente un risque potentiel de retard. Nous vous prions de bien vouloir contacter le founisseur afin de clarifier la situation et, si nécessaire, informer le client.</p>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Filtres et Statistiques --}}
    <h2 class="text-2xl font-semibold px-4 py-2 text-gray-700">Commandes par Site</h2>
    @php
        use Illuminate\Support\Facades\DB;
        $commandesMontDeMarsan = DB::table('produit_stock')
            ->join('stocks', 'produit_stock.stock_id', '=', 'stocks.id')
            ->where('stocks.lieux', 'like', 'mont de marsan')
            ->distinct()->pluck('commande_id');
        $commandesAireSurAdour = DB::table('produit_stock')
            ->join('stocks', 'produit_stock.stock_id', '=', 'stocks.id')
            ->where('stocks.lieux', 'like', 'aire sur adour')
            ->distinct()->pluck('commande_id');
    @endphp
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-8 px-4">
        <div class="filter-btn bg-green-600 text-white text-center py-6 rounded-lg shadow-md hover:bg-green-700 cursor-pointer" data-filter="mont de marsan" data-type="lieu">
            <div class="text-3xl font-bold count-display">{{ $commandesMontDeMarsan->count() }}</div>
            <div class="text-lg">Mont de Marsan</div>
        </div>
        <div class="filter-btn bg-red-600 text-white text-center py-6 rounded-lg shadow-md hover:bg-red-700 cursor-pointer" data-filter="aire sur adour" data-type="lieu">
            <div class="text-3xl font-bold count-display">{{ $commandesAireSurAdour->count() }}</div>
            <div class="text-lg">Aire sur Adour</div>
        </div>
    </div>

    <h2 class="text-2xl font-semibold px-4 py-2 text-gray-700">Nombre de commandes par état</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6 mb-8 px-4">
        @php
            $etatCouleurs = [
                'A faire' => 'bg-green-600 hover:bg-green-700',
                'Commandé' => 'bg-yellow-600 hover:bg-yellow-700',
                'Reçu' => 'bg-amber-600 hover:bg-amber-700',
                'Prévenu' => 'bg-orange-600 hover:bg-orange-700',
                'Délais' => 'bg-red-600 hover:bg-red-700'
            ];
        @endphp
        @foreach($etatCouleurs as $etat => $classes)
        <div class="filter-btn {{ $classes }} text-white text-center py-6 rounded-lg shadow-md cursor-pointer" data-filter="{{ strtolower($etat) }}" data-type="etat">
            <div class="text-3xl font-bold count-display">{{ $commandes->where('etat', $etat)->count() }}</div>
            <div class="text-lg">{{ $etat }}</div>
        </div>
        @endforeach
    </div>

    <h2 class="text-2xl font-semibold px-4 py-2 text-gray-700">Nombre de commandes par urgence</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-8 px-4">
        @foreach(['pas urgent'=>'green','urgent'=>'red'] as $urgence => $color)
        <div class="filter-btn bg-{{$color}}-600 text-white text-center py-6 rounded-lg shadow-md hover:bg-{{$color}}-700 cursor-pointer" data-filter="{{ $urgence }}" data-type="urgence">
            <div class="text-3xl font-bold count-display">{{ $commandes->where('urgence', $urgence)->count() }}</div>
            <div class="text-lg">{{ ucfirst($urgence) }}</div>
        </div>
        @endforeach
    </div>

    <div class="flex flex-wrap justify-between items-center mb-4 px-4 gap-4">
        <div class="flex flex-wrap gap-4 w-full sm:w-auto">
            <button id="resetFilters" class="bg-gray-600 text-white px-6 py-3 rounded-lg shadow-md hover:bg-gray-700 w-full sm:w-auto">Réinitialiser les filtres</button>
            <button id="groupDefault" class="bg-indigo-600 text-white px-6 py-3 rounded-lg shadow-md hover:bg-indigo-700 w-full sm:w-auto">Vue par Défaut</button>
            <button id="groupByArticle" class="bg-blue-600 text-white px-6 py-3 rounded-lg shadow-md hover:bg-blue-700 w-full sm:w-auto">Grouper par Article</button>
            <button id="groupByFournisseur" class="bg-purple-600 text-white px-6 py-3 rounded-lg shadow-md hover:bg-purple-700 w-full sm:w-auto">Grouper par Fournisseur</button>
        </div>
        <a href="{{ route('gestcommande.create') }}" class="bg-green-600 text-white px-6 py-3 rounded-lg shadow-md hover:bg-green-700 text-center w-full sm:w-auto">Ajouter une Commande</a>
    </div>

    <div class="bg-white shadow rounded-lg p-4 sm:p-6">
        <h2 id="table-title" class="text-xl sm:text-2xl font-semibold text-gray-800 mb-4">Liste des Commandes</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white shadow-md rounded-lg text-sm">
                <thead>
                    <tr id="table-headers" class="bg-gray-100 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                        <th class="py-2 px-2 border border-gray-200 min-w-32">N° cmde fournisseur</th>
                        <th class="py-2 px-2 border border-gray-200 min-w-28">Doc client</th>
                        <th class="py-2 px-2 border border-gray-200 min-w-20">Client</th>
                        <th class="py-2 px-2 border border-gray-200 min-w-32">Fournisseur</th>
                        <th class="py-2 px-2 border border-gray-200 min-w-40">Produit</th>
                        <th class="py-2 px-2 border border-gray-200 min-w-24">Site</th>
                        <th class="py-2 px-2 border border-gray-200 min-w-24">État</th>
                        <th class="py-2 px-2 border border-gray-200 min-w-20">Urgence</th>
                        <th class="py-2 px-2 border border-gray-200 min-w-20 text-center">Der-min?</th>
                        <th class="py-2 px-2 border border-gray-200 min-w-32">Actions</th>
                    </tr>
                </thead>
                <tbody id="commandes-body">
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
$commandesData = $commandes->map(function($c) use($csrf) {
    $lieux = DB::table('produit_stock')
        ->join('stocks','produit_stock.stock_id','=','stocks.id')
        ->where('produit_stock.commande_id',$c->id)
        ->distinct()->pluck('stocks.lieux')->implode(', ');
    $produits = DB::table('commande_produit')
        ->join('produits','commande_produit.produit_id','=','produits.id')
        ->where('commande_produit.commande_id',$c->id)
        ->select('produits.nom as nom', 'produits.lien_produit_fournisseur as lien', 'commande_produit.quantite_totale as quantite')
        ->get()->map(fn($p)=>['nom'=>$p->nom, 'lien'=>$p->lien, 'quantite'=>$p->quantite]);
    $fourn = $produits->isNotEmpty()
        ? DB::table('fournisseur_produit')
            ->join('fournisseurs','fournisseur_produit.fournisseur_id','=','fournisseurs.id')
            ->where('produit_id',DB::table('produits')->where('nom',$produits[0]['nom'])->value('id'))
            ->where('commande_id',$c->id)->value('fournisseurs.nom')
        : '/';
    $actions = 
        "<form action='".route('gestcommande.destroy',$c->id)."' method='POST' class='inline'>".
        "<input type='hidden' name='_token' value='{$csrf}'>".
        "<input type='hidden' name='_method' value='DELETE'>".
        "<button type='button' class='text-green-600 hover:text-green-700 font-semibold mr-1 text-xs' onclick=\"window.location.href='".route('gestcommande.show',$c->id)."'\">Détails</button>".
        "<button type='button' class='text-yellow-600 hover:text-yellow-700 font-semibold mr-1 text-xs' onclick=\"window.location.href='".route('gestcommande.edit',$c->id)."'\">Modifier</button>".
        "<button type='button' onclick=\"openModal({$c->id})\" class='text-red-600 hover:text-red-700 font-semibold text-xs'>Supprimer</button>".
        "</form>";
    // Check if this command has an alert
    $hasAlert = isset($alerteCommandes[$c->id]);
    return [
        'id'=>$c->id,
        'numero_commande_fournisseur'=>$c->numero_commande_fournisseur ?? 'Non défini',
        'doc_client'=>$c->doc_client ?? 'Non défini',
        'client'=>$c->client?->code_client?:'<p class="text-red-500">Pas de client</p>',
        'fournisseur'=>$fourn,
        'lieux'=>$lieux?:'Non défini',
        'produits'=>$produits,
        'etat'=>strtolower($c->etat),
        'urgence'=>strtolower($c->urgence),
        'is_derMinute'=>$c->is_derMinute ?? false, // Ajout de la valeur is_derMinute
        'hasAlert'=>$hasAlert,
        'actions'=>$actions];
});
@endphp

<div id="modal" class="fixed inset-0 z-50 hidden bg-gray-800/40 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-lg w-11/12 sm:w-1/2 lg:w-1/3">
        <div class="px-4 py-2 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-800">Confirmation de Suppression</h3>
            <button id="closeModal" class="text-gray-600 hover:text-gray-800">&times;</button>
        </div>
        <div class="p-4">
            <p class="text-gray-700">Êtes-vous sûr de vouloir supprimer cette commande ? Cette action est irréversible.</p>
        </div>
        <div class="px-4 py-2 flex justify-end space-x-4">
            <button id="cancelModal" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">Annuler</button>
            <form id="deleteForm" method="POST" action="{{ route('gestcommande.destroy', 0) }}">
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
    const deleteRouteTemplate = "{{ route('gestcommande.destroy', ':id') }}";

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
const données = @json($commandesData);
let activeFilters = {lieu:new Set(),etat:new Set(),urgence:new Set()};
const body = document.getElementById('commandes-body');
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
    const filteredData = données.filter(cmd => {
        const lieuMatch = !activeFilters.lieu.size || [...activeFilters.lieu].some(f => cmd.lieux.toLowerCase().includes(f));
        const etatMatch = !activeFilters.etat.size || activeFilters.etat.has(cmd.etat);
        const urgenceMatch = !activeFilters.urgence.size || activeFilters.urgence.has(cmd.urgence);
        return lieuMatch && etatMatch && urgenceMatch;
    });

    return filteredData.filter(cmd => {
        if (type === 'lieu') return cmd.lieux.toLowerCase().includes(value.toLowerCase());
        if (type === 'etat') return cmd.etat === value.toLowerCase();
        return cmd.urgence === value.toLowerCase();
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
    <th class="py-2 px-2 border border-gray-200 min-w-32">N° cmde fournisseur</th>
    <th class="py-2 px-2 border border-gray-200 min-w-28">Doc client</th>
    <th class="py-2 px-2 border border-gray-200 min-w-20">Client</th>
    <th class="py-2 px-2 border border-gray-200 min-w-32">Fournisseur</th>
    <th class="py-2 px-2 border border-gray-200 min-w-40">Produit</th>
    <th class="py-2 px-2 border border-gray-200 min-w-24">Site</th>
    <th class="py-2 px-2 border border-gray-200 min-w-24">État</th>
    <th class="py-2 px-2 border border-gray-200 min-w-20">Urgence</th>
    <th class="py-2 px-2 border border-gray-200 min-w-20 text-center">Der-min?</th>
    <th class="py-2 px-2 border border-gray-200 min-w-32">Actions</th>
`;

const filtreOK = cmd =>
    (!activeFilters.lieu.size || [...activeFilters.lieu].some(f => cmd.lieux.toLowerCase().includes(f))) &&
    (!activeFilters.etat.size || activeFilters.etat.has(cmd.etat)) &&
    (!activeFilters.urgence.size || activeFilters.urgence.has(cmd.urgence));

const rowHTML = cmd => `
    <tr class="border-t hover:bg-gray-50">
        <td class="py-2 px-2 border border-gray-200 text-xs break-words">
            ${cmd.numero_commande_fournisseur}
        </td>
        <td class="py-2 px-2 border border-gray-200 text-xs break-words">
            ${cmd.doc_client ?? 'N/A'}
        </td>
        <td class="py-2 px-2 border border-gray-200 text-xs">${cmd.client}</td>
        <td class="py-2 px-2 border border-gray-200 text-xs break-words">${cmd.fournisseur}</td>
        <td class="py-2 px-2 border border-gray-200 text-xs">
            ${cmd.produits.map(p => {
                // Vérifie si le lien produit existe
                if (p.lien) {
                    return `<div class="mb-1">
                        <a href="${p.lien}" 
                           target="_blank" 
                           class="text-blue-600 hover:underline break-words" 
                           title="Voir le produit chez le fournisseur">
                            ${p.nom}
                        </a>
                    </div>`;
                } else {
                    // Si pas de lien, affichage normal
                    return `<div class="mb-1 break-words">${p.nom}</div>`;
                }
            }).join('')}
        </td>
        <td class="py-2 px-2 border border-gray-200 text-xs break-words">${cmd.lieux}</td>
        <td class="py-2 px-2 border border-gray-200">
            <div class="relative">
                <select
                    data-commande-id="${cmd.id}"
                    class="state-select appearance-none w-full pl-2 pr-6 py-1 rounded-md border text-xs focus:outline-none ${
                        {
                            'a faire': 'border-green-600',
                            'commandé': 'border-yellow-600',
                            'reçu': 'border-amber-600',
                            'prévenu': 'border-orange-600',
                            'délais': 'border-red-600'
                        }[cmd.etat] || ''
                    }"
                >
                    ${['A faire', 'Commandé', 'Reçu', 'Prévenu', 'Délais']
                        .map(etat => `<option value="${etat.toLowerCase()}" ${
                            cmd.etat === etat.toLowerCase() ? 'selected' : ''
                        }>${etat}</option>`).join('')}
                </select>
                <div class="absolute inset-y-0 right-0 flex items-center pr-1 pointer-events-none loading-indicator hidden">
                    <div class="animate-spin rounded-full h-3 w-3 border-b-2 border-gray-900"></div>
                </div>
            </div>
        </td>
        <td class="py-2 px-2 border border-gray-200">
            <span class="${cmd.urgence === 'urgent' ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700'} px-1 py-0.5 rounded text-xs">
                ${cmd.urgence}
            </span>
        </td>
<td class="py-2 px-2 border border-gray-200 text-center">
    ${
        cmd.produits.some(p => p.is_derMinute)
            ? '<span class="bg-red-100 text-red-700 px-2 py-1 rounded text-xs font-semibold">Oui</span>'
            : '<span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs font-semibold">Non</span>'
    }
</td>

        <td class="py-2 px-2 border border-gray-200 text-xs">${cmd.actions}</td>
    </tr>
`;

function renderDefault() {
    titre.textContent = 'Liste des Commandes';
    headers.innerHTML = defaultHeaders;
    body.innerHTML = données.filter(filtreOK).map(cmd => {
        const rowHtml = rowHTML(cmd);
        // Modifier le HTML pour ajouter la classe et le data attribute
        return rowHtml.replace(
            `<td class="py-2 px-2 border border-gray-200 text-xs break-words">${cmd.fournisseur}</td>`, 
            `<td class="py-2 px-2 border border-gray-200 text-xs break-words supplier-cell cursor-pointer hover:bg-gray-200" data-commande-id="${cmd.id}">${cmd.fournisseur}</td>`
        );
    }).join('');
    updateFilterCounts();
    makeSupplierEditable();
    reactivateStateSelectors();
}

function renderByArticle() {
    titre.textContent = 'Grouper par Article';
    headers.innerHTML = `
        <th class="py-2 px-2 border border-gray-200">Produit</th>
        <th class="py-2 px-2 border border-gray-200">Quantité</th>
    `;
    const agg = {};
    données.forEach(cmd => {
        if (filtreOK(cmd)) {
            cmd.produits.forEach(p => {
                agg[p.nom] = (agg[p.nom] || 0) + p.quantite;
            });
        }
    });
    const rows = Object.entries(agg).map(([nom, total]) => `
        <tr class="border-t hover:bg-gray-50">
            <td class="py-2 px-2 border border-gray-200 text-xs break-words">${nom}</td>
            <td class="py-2 px-2 border border-gray-200 text-xs">${total}</td>
        </tr>
    `);
    body.innerHTML = rows.join('');
    updateFilterCounts();
}

function renderByFournisseur() {
    titre.textContent = 'Grouper par Fournisseur';
    headers.innerHTML = `
        <th class="py-2 px-2 border border-gray-200">Fournisseur</th>
        <th class="py-2 px-2 border border-gray-200">Produit</th>
        <th class="py-2 px-2 border border-gray-200">Quantité</th>
    `;
    const agg = {};
    données.forEach(cmd => {
        if (filtreOK(cmd)) {
            cmd.produits.forEach(p => {
                const fournisseur = cmd.fournisseur || 'Non défini';
                const key = fournisseur + '|' + p.nom;
                if (!agg[key]) agg[key] = {fournisseur: fournisseur, produit: p.nom, quantite: 0};
                agg[key].quantite += p.quantite;
            });
        }
    });
    const rows = Object.values(agg)
        .sort((a, b) => a.fournisseur.localeCompare(b.fournisseur) || a.produit.localeCompare(b.produit))
        .map(item => `
        <tr class="border-t hover:bg-gray-50">
            <td class="py-2 px-2 border border-gray-200 text-xs break-words">${item.fournisseur}</td>
            <td class="py-2 px-2 border border-gray-200 text-xs break-words">${item.produit}</td>
            <td class="py-2 px-2 border border-gray-200 text-xs">${item.quantite}</td>
        </tr>
    `);
    body.innerHTML = rows.join('');
    updateFilterCounts();
}

document.querySelectorAll('.filter-btn').forEach(b => b.addEventListener('click', () => {
    const t = b.dataset.type, v = b.dataset.filter.toLowerCase();
    if (activeFilters[t].has(v)) activeFilters[t].delete(v);
    else activeFilters[t].add(v);
    b.classList.toggle('ring-4'); b.classList.toggle('ring-blue-500');
    const view = titre.textContent;
    if (view.includes('Article')) renderByArticle();
    else if (view.includes('Fournisseur')) renderByFournisseur();
    else renderDefault();
}));

document.getElementById('resetFilters').addEventListener('click', () => {
    activeFilters = {lieu:new Set(), etat:new Set(), urgence:new Set()};
    document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('ring-4','ring-blue-500'));
    // Si l'utilisateur est associé à un stock, le réappliquer après la réinitialisation
    applyUserStockFilter();
    renderDefault();
});
document.getElementById('groupDefault').addEventListener('click', renderDefault);
document.getElementById('groupByArticle').addEventListener('click', renderByArticle);
document.getElementById('groupByFournisseur').addEventListener('click', renderByFournisseur);

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

// State change handling
function reactivateStateSelectors() {
    document.querySelectorAll('.state-select').forEach(select => {
        select.addEventListener('change', async function() {
            const commandeId = this.dataset.commandeId;
            const newState = this.value;
            const loadingIndicator = this.parentElement.querySelector('.loading-indicator');
            
            // Show loading indicator
            loadingIndicator.classList.remove('hidden');
            
            try {
                const response = await fetch(`/commandes/${commandeId}/etat`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({ etat: newState })
                });
                
                const data = await response.json();
                
                if (response.ok) {
                    // Update border color based on new state
                    const borderColors = {
                        'a faire': 'border-green-600',
                        'commandé': 'border-yellow-600',
                        'reçu': 'border-amber-600',
                        'prévenu': 'border-orange-600',
                        'délais': 'border-red-600'
                    };
                    
                    // Remove all border colors and add new one
                    Object.values(borderColors).forEach(color => this.classList.remove(color));
                    this.classList.add(borderColors[newState]);
                    
                    // Mettre à jour les données du client
                    const cmdIndex = données.findIndex(cmd => cmd.id == commandeId);
                    if (cmdIndex !== -1) {
                        données[cmdIndex].etat = newState;
                    }
                    
                    showToast('État mis à jour avec succès', 'success');
                    
                    // Update the count in state filters
                    updateFilterCounts();
                } else {
                    throw new Error(data.message || 'Une erreur est survenue');
                }
            } catch (error) {
                showToast(error.message, 'error');
                // Revert select to previous value
                this.value = this.querySelector('[selected]').value;
            } finally {
                loadingIndicator.classList.add('hidden');
            }
        });
    });
}

// Fonction pour rendre les cellules de fournisseur modifiables
function makeSupplierEditable() {
    const supplierCells = document.querySelectorAll('.supplier-cell');
    supplierCells.forEach(cell => {
        cell.addEventListener('click', function() {
            // Ne pas réappliquer si déjà en mode édition
            if (this.querySelector('input')) return;

            const originalText = this.textContent.trim();
            const commandeId = this.dataset.commandeId;
            
            // Créer un input pour éditer
            const input = document.createElement('input');
            input.type = 'text';
            input.value = originalText;
            input.classList.add('w-full', 'px-2', 'py-1', 'border', 'rounded', 'text-xs');
            
            // Sauvegarder les anciens contenus
            const oldContent = this.innerHTML;
            this.innerHTML = '';
            this.appendChild(input);
            input.focus();

            // Gestion de la perte de focus (annulation ou sauvegarde)
            input.addEventListener('blur', async function() {
                const newValue = this.value.trim();
                
                // Si la valeur est différente, envoyer une requête AJAX
                if (newValue !== originalText) {
                    try {
                        const response = await fetch(`/commandes/${commandeId}/fournisseur`, {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            },
                            body: JSON.stringify({ 
                                fournisseur: newValue,
                                commande_id: commandeId 
                            })
                        });
                        
                        const data = await response.json();
                        
                        if (response.ok) {
                            cell.innerHTML = newValue;
                            // Mettre à jour les données du client
                            const cmdIndex = données.findIndex(cmd => cmd.id == commandeId);
                            if (cmdIndex !== -1) {
                                données[cmdIndex].fournisseur = newValue;
                            }
                            showToast('Fournisseur mis à jour avec succès', 'success');
                        } else {
                            // Restaurer la valeur originale en cas d'erreur
                            cell.innerHTML = originalText;
                            showToast(data.message || 'Une erreur est survenue', 'error');
                        }
                    } catch (error) {
                        cell.innerHTML = originalText;
                        showToast('Erreur de communication', 'error');
                    }
                } else {
                    // Si pas de changement, restaurer le texte original
                    cell.innerHTML = originalText;
                }
            });

            // Permettre la sauvegarde avec la touche Entrée
            input.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    this.blur(); // Déclenche l'événement blur qui gère la sauvegarde
                } else if (e.key === 'Escape') {
                    cell.innerHTML = originalText;
                }
            });
        });
    });
}

// Appliquer le filtre de stock utilisateur au chargement initial
applyUserStockFilter();

// Initialiser l'affichage
renderDefault();
</script>
@endsection