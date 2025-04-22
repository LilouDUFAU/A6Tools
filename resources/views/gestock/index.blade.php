@extends('layouts.app')

@section('content')

<div class="container mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <h1 class="text-3xl font-bold mb-8 px-4 pt-10 text-gray-800">Tableau de Bord des Commandes</h1>

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
            <div class="text-3xl font-bold">{{ $commandesMontDeMarsan->count() }}</div>
            <div class="text-lg">Mont de Marsan</div>
        </div>
        <div class="filter-btn bg-red-600 text-white text-center py-6 rounded-lg shadow-md hover:bg-red-700 cursor-pointer" data-filter="aire sur adour" data-type="lieu">
            <div class="text-3xl font-bold">{{ $commandesAireSurAdour->count() }}</div>
            <div class="text-lg">Aire sur Adour</div>
        </div>
    </div>

    <h2 class="text-2xl font-semibold px-4 py-2 text-gray-700">Nombre de commandes par état</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6 mb-8 px-4">
        @foreach(['A faire'=>'green','Commandé'=>'yellow','Reçu'=>'amber','Prévenu'=>'orange','Délais'=>'red'] as $etat => $color)
        <div class="filter-btn bg-{{$color}}-600 text-white text-center py-6 rounded-lg shadow-md hover:bg-{{$color}}-700 cursor-pointer" data-filter="{{ strtolower($etat) }}" data-type="etat">
            <div class="text-3xl font-bold">{{ $commandes->where('etat', $etat)->count() }}</div>
            <div class="text-lg">{{ $etat }}</div>
        </div>
        @endforeach
    </div>

    <h2 class="text-2xl font-semibold px-4 py-2 text-gray-700">Nombre de commandes par urgence</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-8 px-4">
        @foreach(['pas urgent'=>'green','urgent'=>'red'] as $urgence => $color)
        <div class="filter-btn bg-{{$color}}-600 text-white text-center py-6 rounded-lg shadow-md hover:bg-{{$color}}-700 cursor-pointer" data-filter="{{ $urgence }}" data-type="urgence">
            <div class="text-3xl font-bold">{{ $commandes->where('urgence', $urgence)->count() }}</div>
            <div class="text-lg">{{ ucfirst($urgence) }}</div>
        </div>
        @endforeach
    </div>

    <div class="flex justify-between items-center mb-4 px-4">
        <div class="flex flex-wrap gap-4">
            <button id="resetFilters" class="bg-gray-600 text-white px-6 py-3 rounded-lg shadow-md hover:bg-gray-700">Réinitialiser les filtres</button>
            <button id="groupDefault" class="bg-indigo-600 text-white px-6 py-3 rounded-lg shadow-md hover:bg-indigo-700">Vue par Défaut</button>
            <button id="groupByArticle" class="bg-blue-600 text-white px-6 py-3 rounded-lg shadow-md hover:bg-blue-700">Grouper par Article</button>
            <button id="groupByFournisseur" class="bg-purple-600 text-white px-6 py-3 rounded-lg shadow-md hover:bg-purple-700">Grouper par Fournisseur</button>
        </div>
        <a href="{{ route('commande.create') }}" class="bg-green-600 text-white px-6 py-3 rounded-lg shadow-md hover:bg-green-700">Ajouter une Commande</a>
    </div>
        

    <div class="bg-white shadow rounded-lg p-4 sm:p-6">
        <h2 id="table-title" class="text-xl sm:text-2xl font-semibold text-gray-800 mb-4">Liste des Commandes</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white shadow-md rounded-lg">
                <thead>
                    <tr id="table-headers" class="bg-gray-100 text-left text-sm font-semibold text-gray-700">
                        <th class="py-3 px-4 border border-gray-200">#</th>
                        <th class="py-3 px-4 border border-gray-200"">Client</th>
                        <th class="py-3 px-4 border border-gray-200">Fournisseur</th>
                        <th class="py-3 px-4 border border-gray-200">Site</th>
                        <th class="py-3 px-4 border border-gray-200">État</th>
                        <th class="py-3 px-4 border border-gray-200">Urgence</th>
                        <th class="py-3 px-4 border border-gray-200">Actions</th>
                    </tr>
                </thead>
                <tbody id="commandes-body">
                    {{-- Dynamique --}}
                </tbody>
            </table>
        </div>
    </div>
</div>

@php
    $csrf = csrf_token();
    $commandesData = $commandes->map(function($c) use($csrf) {
        $lieux = DB::table('produit_stock')
            ->join('stocks','produit_stock.stock_id','=','stocks.id')
            ->where('produit_stock.commande_id',$c->id)
            ->distinct()->pluck('stocks.lieux')->implode(', ');
        $produits = DB::table('produit_stock')
            ->join('produits','produit_stock.produit_id','=','produits.id')
            ->where('produit_stock.commande_id',$c->id)
            ->select('produits.nom as nom','produit_stock.quantite as quantite')
            ->get()->map(fn($p)=>['nom'=>$p->nom,'quantite'=>$p->quantite]);
        $fourn = $produits->isNotEmpty()
            ? DB::table('fournisseur_produit')
                ->join('fournisseurs','fournisseur_produit.fournisseur_id','=','fournisseurs.id')
                ->where('produit_id',DB::table('produits')->where('nom',$produits[0]['nom'])->value('id'))
                ->where('commande_id',$c->id)->value('fournisseurs.nom')
            : '/';
        $actions = 
            "<form action='".route('commande.destroy',$c->id)."' method='POST' class='inline'>".
            "<input type='hidden' name='_token' value='{$csrf}'>".
            "<input type='hidden' name='_method' value='DELETE'>".
            "<button type='button' class='text-green-600 hover:text-green-700 font-semibold mr-2' onclick=\"window.location.href='".route('commande.show',$c->id)."'\">Détails</button>".
            "<button type='button' class='text-yellow-600 hover:text-yellow-700 font-semibold mr-2' onclick=\"window.location.href='".route('commande.edit',$c->id)."'\">Modifier</button>".
            "<button type='submit' onclick=\"return confirm('Confirmer la suppression ?')\" class='text-red-600 hover:text-red-700 font-semibold'>Supprimer</button>".
            "</form>";
        return [
            'id'=>$c->id,
            'client'=>$c->client?->nom?:'<p class="text-red-500">Pas de client</p>',
            'fournisseur'=>$fourn,
            'lieux'=>$lieux?:'Non défini',
            'produits'=>$produits,
            'etat'=>strtolower($c->etat),
            'urgence'=>strtolower($c->urgence),
            'actions'=>$actions];
    });
@endphp

<script>
    const données = @json($commandesData);
    let activeFilters = {lieu:new Set(),etat:new Set(),urgence:new Set()};
    const body = document.getElementById('commandes-body');
    const titre = document.getElementById('table-title');
    const headers = document.getElementById('table-headers');

    const defaultHeaders = `
        <th class="py-3 px-4 border border-gray-200">Client</th>
        <th class="py-3 px-4 border border-gray-200">Fournisseur</th>
        <th class="py-3 px-4 border border-gray-200">Site</th>
        <th class="py-3 px-4 border border-gray-200">État</th>
        <th class="py-3 px-4 border border-gray-200">Urgence</th>
        <th class="py-3 px-4 border border-gray-200">Actions</th>
    `;

    const filtreOK = cmd =>
        (!activeFilters.lieu.size || [...activeFilters.lieu].some(f => cmd.lieux.toLowerCase().includes(f))) &&
        (!activeFilters.etat.size || activeFilters.etat.has(cmd.etat)) &&
        (!activeFilters.urgence.size || activeFilters.urgence.has(cmd.urgence));

    const rowHTML = cmd => `
        <tr class="border-t hover:bg-gray-50" data-lieux="${cmd.lieux}" data-etat="${cmd.etat}" data-urgence="${cmd.urgence}">
            <td class="py-3 px-4 border border-gray-200">${cmd.client}</td>
            <td class="py-3 px-4 border border-gray-200">${cmd.fournisseur}</td>
            <td class="py-3 px-4 border border-gray-200">${cmd.lieux}</td>
            <td class="py-3 px-4 border border-gray-200">${cmd.etat}</td>
            <td class="py-3 px-4 border border-gray-200">${cmd.urgence}</td>
            <td class="py-3 px-4 border border-gray-200">${cmd.actions}</td>
        </tr>
    `;

    function renderDefault(){
        titre.textContent = 'Liste des Commandes';
        headers.innerHTML = defaultHeaders;
        body.innerHTML = données.filter(filtreOK).map(rowHTML).join('');
    }

    function renderByArticle(){
        titre.textContent = 'Grouper par Article';
        headers.innerHTML = `
            <th class="py-3 px-4 border border-gray-200">Produit</th>
            <th class="py-3 px-4 border border-gray-200">Quantité</th>
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
                <td class="py-3 px-4 border border-gray-200">${nom}</td>
                <td class="py-3 px-4 border border-gray-200">${total}</td>
            </tr>
        `);
        body.innerHTML = rows.join('');
    }

    function renderByFournisseur(){
        titre.textContent = 'Grouper par Fournisseur';
        headers.innerHTML = `
            <th class="py-3 px-4 border border-gray-200">Fournisseur</th>
            <th class="py-3 px-4 border border-gray-200">Produit</th>
            <th class="py-3 px-4 border border-gray-200">Quantité</th>
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
                <td class="py-3 px-4 border border-gray-200">${item.fournisseur}</td>
                <td class="py-3 px-4 border border-gray-200">${item.produit}</td>
                <td class="py-3 px-4 border border-gray-200">${item.quantite}</td>
            </tr>
        `);
        body.innerHTML = rows.join('');
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
        renderDefault();
    });
    document.getElementById('groupDefault').addEventListener('click', renderDefault);
    document.getElementById('groupByArticle').addEventListener('click', renderByArticle);
    document.getElementById('groupByFournisseur').addEventListener('click', renderByFournisseur);

    renderDefault();
</script>

@endsection