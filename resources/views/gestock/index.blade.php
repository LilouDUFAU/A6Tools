@extends('layouts.app')

@section('content')

<div class="min-h-screen bg-gray-50">
    <h1 class="text-3xl font-bold mb-8 px-4 pt-10 text-gray-800">Tableau de Bord des Commandes</h1>

    <h2 class="text-2xl font-semibold px-4 py-2 text-gray-700">Commandes par Site</h2>
    @php
        use Illuminate\Support\Facades\DB;

        $commandesMontDeMarsan = DB::table('produit_stock')
            ->join('stocks', 'produit_stock.stock_id', '=', 'stocks.id')
            ->where('stocks.lieux', 'like', 'mont de marsan')
            ->select('produit_stock.commande_id')
            ->distinct()
            ->pluck('commande_id');

        $commandesAireSurAdour = DB::table('produit_stock')
            ->join('stocks', 'produit_stock.stock_id', '=', 'stocks.id')
            ->where('stocks.lieux', 'like', 'aire sur adour')
            ->select('produit_stock.commande_id')
            ->distinct()
            ->pluck('commande_id');
    @endphp

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-8 px-4">
        <div class="filter-btn bg-gradient-to-r from-green-700 to-yellow-600 text-white text-center py-6 rounded-lg shadow-md hover:from-green-700 hover:to-yellow-700" data-filter="mont de marsan" data-type="lieu">
            <div class="text-3xl font-bold">{{ $commandesMontDeMarsan->count() }}</div>
            <div class="text-lg">Mont de Marsan</div>
        </div>
        <div class="filter-btn bg-gradient-to-r from-orange-600 to-red-600 text-white text-center py-6 rounded-lg shadow-md hover:from-orange-700 hover:to-red-700" data-filter="aire sur adour" data-type="lieu">
            <div class="text-3xl font-bold">{{ $commandesAireSurAdour->count() }}</div>
            <div class="text-lg">Aire sur Adour</div>
        </div>
    </div>

    <h2 class="text-2xl font-semibold px-4 py-2 text-gray-700">Nombre de commandes par état</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6 mb-8 px-4">
        <div class="filter-btn bg-green-600 text-white text-center py-6 rounded-lg shadow-md hover:bg-green-700" data-filter="A faire" data-type="etat">
            <div class="text-3xl font-bold">{{ $commandes->where('etat', 'A faire')->count() }}</div>
            <div class="text-lg">A faire</div>
        </div>
        <div class="filter-btn bg-yellow-600 text-white text-center py-6 rounded-lg shadow-md hover:bg-yellow-700" data-filter="Commandé" data-type="etat">
            <div class="text-3xl font-bold">{{ $commandes->where('etat', 'Commandé')->count() }}</div>
            <div class="text-lg">Commandé</div>
        </div>
        <div class="filter-btn bg-amber-600 text-white text-center py-6 rounded-lg shadow-md hover:bg-amber-700" data-filter="Reçu" data-type="etat">
            <div class="text-3xl font-bold">{{ $commandes->where('etat', 'Reçu')->count() }}</div>
            <div class="text-lg">Reçu</div>
        </div>
        <div class="filter-btn bg-orange-600 text-white text-center py-6 rounded-lg shadow-md hover:bg-orange-700" data-filter="Prévenu" data-type="etat">
            <div class="text-3xl font-bold">{{ $commandes->where('etat', 'Prévenu')->count() }}</div>
            <div class="text-lg">Prévenu</div>
        </div>
        <div class="filter-btn bg-red-600 text-white text-center py-6 rounded-lg shadow-md hover:bg-red-700" data-filter="Délais" data-type="etat">
            <div class="text-3xl font-bold">{{ $commandes->where('etat', 'Délais')->count() }}</div>
            <div class="text-lg">Délais</div>
        </div>
    </div>

    <h2 class="text-2xl font-semibold px-4 py-2 text-gray-700">Nombre de commandes par urgence</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6 mb-8 px-4">
        <div class="filter-btn bg-green-600 text-white text-center py-6 rounded-lg shadow-md hover:bg-green-700" data-filter="pas urgent" data-type="urgence">
            <div class="text-3xl font-bold">{{ $commandes->where('urgence', 'pas urgent')->count() }}</div>
            <div class="text-lg">Pas urgent</div>
        </div>
        <div class="filter-btn bg-yellow-600 text-white text-center py-6 rounded-lg shadow-md hover:bg-yellow-700" data-filter="peu urgent" data-type="urgence">
            <div class="text-3xl font-bold">{{ $commandes->where('urgence', 'peu urgent')->count() }}</div>
            <div class="text-lg">Peu urgent</div>
        </div>
        <div class="filter-btn bg-amber-600 text-white text-center py-6 rounded-lg shadow-md hover:bg-amber-700" data-filter="moyennement urgent" data-type="urgence">
            <div class="text-3xl font-bold">{{ $commandes->where('urgence', 'moyennement urgent')->count() }}</div>
            <div class="text-lg">moyennement urgent</div>
        </div>
        <div class="filter-btn bg-orange-600 text-white text-center py-6 rounded-lg shadow-md hover:bg-orange-700" data-filter="urgent" data-type="urgence">
            <div class="text-3xl font-bold">{{ $commandes->where('urgence', 'urgent')->count() }}</div>
            <div class="text-lg">Urgent</div>
        </div>
        <div class="filter-btn bg-red-600 text-white text-center py-6 rounded-lg shadow-md hover:bg-red-700" data-filter="très urgent" data-type="urgence">
            <div class="text-3xl font-bold">{{ $commandes->where('urgence', 'très urgent')->count() }}</div>
            <div class="text-lg">Très urgent</div>
        </div>
    </div>

    <button id="resetFilters" class="bg-gray-600 text-white px-6 py-3 rounded-lg shadow-md hover:bg-gray-700 mb-8 mx-4">Réinitialiser les filtres</button>
    <button id="groupByArticle" class="bg-blue-600 text-white px-6 py-3 rounded-lg shadow-md hover:bg-blue-700 mb-8 mx-4">Grouper par Article</button>
    <button id="groupByFournisseur" class="bg-purple-600 text-white px-6 py-3 rounded-lg shadow-md hover:bg-purple-700 mb-8 mx-4">Grouper par Fournisseur</button>

    <div id="grouped-fournisseurs" class="hidden mx-auto px-4 sm:px-6 md:px-8 py-6">
        <h2 class="text-2xl font-semibold text-gray-800 mb-4">Commandes et Articles par Fournisseur</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white shadow-md rounded-lg">
                <thead>
                    <tr class="bg-gray-100 text-left text-sm font-semibold text-gray-700">
                        <th class="py-3 px-4">Fournisseur</th>
                        <th class="py-3 px-4">Article</th>
                        <th class="py-3 px-4">Quantité</th>
                    </tr>
                </thead>
                <tbody id="grouped-fournisseurs-table">
                    @php
                        $fournisseursData = DB::table('produit_stock')
                            ->join('produits', 'produit_stock.produit_id', '=', 'produits.id')
                            ->join('fournisseur_produit', function ($join) {
                                $join->on('produits.id', '=', 'fournisseur_produit.produit_id')
                                     ->on('produit_stock.commande_id', '=', 'fournisseur_produit.commande_id');
                            })
                            ->join('fournisseurs', 'fournisseur_produit.fournisseur_id', '=', 'fournisseurs.id')
                            ->select(
                                'fournisseurs.nom as fournisseur',
                                'produits.nom as article',
                                'produit_stock.quantite as quantite'
                            )
                            ->orderBy('fournisseurs.nom')
                            ->get();
                    @endphp
                    @foreach($fournisseursData as $data)
                    <tr class="border-t hover:bg-gray-50">
                        <td class="py-3 px-4">{{ $data->fournisseur }}</td>
                        <td class="py-3 px-4">{{ $data->article }}</td>
                        <td class="py-3 px-4">{{ $data->quantite }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div id="grouped-fournisseurs" class="hidden mx-auto px-4 sm:px-6 md:px-8 py-6">
        <h2 class="text-2xl font-semibold text-gray-800 mb-4">Commandes et Articles par Fournisseur</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white shadow-md rounded-lg">
                <thead>
                    <tr class="bg-gray-100 text-left text-sm font-semibold text-gray-700">
                        <th class="py-3 px-4">Fournisseur</th>
                        <th class="py-3 px-4">Commande</th>
                        <th class="py-3 px-4">Article</th>
                        <th class="py-3 px-4">Quantité</th>
                    </tr>
                </thead>
                <tbody id="grouped-fournisseurs-table">
                    @php
                        $fournisseursData = DB::table('produit_stock')
                            ->join('produits', 'produit_stock.produit_id', '=', 'produits.id')
                            ->join('fournisseur_produit', 'produits.id', '=', 'fournisseur_produit.produit_id')
                            ->join('fournisseurs', 'fournisseur_produit.fournisseur_id', '=', 'fournisseurs.id')
                            ->select('fournisseurs.nom as fournisseur', 'produit_stock.commande_id as commande', 'produits.nom as article', 'produit_stock.quantite as quantite')
                            ->orderBy('fournisseurs.nom')
                            ->get();
                    @endphp
                    @foreach($fournisseursData as $data)
                    <tr class="border-t hover:bg-gray-50">
                        <td class="py-3 px-4">{{ $data->fournisseur }}</td>
                        <td class="py-3 px-4">{{ $data->commande }}</td>
                        <td class="py-3 px-4">{{ $data->article }}</td>
                        <td class="py-3 px-4">{{ $data->quantite }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <script>
        document.getElementById('groupByFournisseur').addEventListener('click', function() {
            const groupedFournisseursSection = document.getElementById('grouped-fournisseurs');
            groupedFournisseursSection.classList.toggle('hidden');
        });
    </script>

    <div id="grouped-articles" class="hidden mx-auto px-4 sm:px-6 md:px-8 py-6">
        <h2 class="text-2xl font-semibold text-gray-800 mb-4">Quantité Totale par Article</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white shadow-md rounded-lg">
                <thead>
                    <tr class="bg-gray-100 text-left text-sm font-semibold text-gray-700">
                        <th class="py-3 px-4">Article</th>
                        <th class="py-3 px-4">Quantité Totale</th>
                    </tr>
                </thead>
                <tbody id="grouped-articles-table">
                    @php
                        $articlesQuantities = DB::table('produit_stock')
                            ->join('produits', 'produit_stock.produit_id', '=', 'produits.id')
                            ->select('produits.nom as article', DB::raw('SUM(produit_stock.quantite) as quantite_totale'))
                            ->groupBy('produits.nom')
                            ->get();
                    @endphp
                    @foreach($articlesQuantities as $article)
                    <tr class="border-t hover:bg-gray-50">
                        <td class="py-3 px-4">{{ $article->article }}</td>
                        <td class="py-3 px-4">{{ $article->quantite_totale }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <script>
        document.getElementById('groupByArticle').addEventListener('click', function() {
            const groupedArticlesSection = document.getElementById('grouped-articles');
            groupedArticlesSection.classList.toggle('hidden');
        });
    </script>
    <div class="mx-auto px-4 sm:px-6 md:px-8 py-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
            <h2 class="text-2xl font-semibold text-gray-800">Liste des Commandes</h2>
            <a href="{{ route('commande.create') }}" class="mt-4 md:mt-0 bg-green-600 text-white px-6 py-3 rounded-lg shadow-md hover:bg-green-700 font-semibold">Nouvelle commande</a>
        </div>

        @if (session('success'))
            <div class="mb-4 text-green-700 font-semibold">{{ session('success') }}</div>
        @endif

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white shadow-md rounded-lg">
                <thead>
                    <tr class="bg-gray-100 text-left text-sm font-semibold text-gray-700">
                        <th class="py-3 px-4">#</th>
                        <th class="py-3 px-4">Client</th>
                        <th class="py-3 px-4">Fournisseur</th>
                        <th class="py-3 px-4">Site</th>
                        <th class="py-3 px-4">État</th>
                        <th class="py-3 px-4">Urgence</th>
                        <th class="py-3 px-4">Actions</th>
                    </tr>
                </thead>
                <tbody id="commandes-table">
                    @foreach($commandes as $commande)
                    @php
                        $lieuxStockCommande = DB::table('produit_stock')
                            ->join('stocks', 'produit_stock.stock_id', '=', 'stocks.id')
                            ->where('produit_stock.commande_id', $commande->id)
                            ->distinct()
                            ->pluck('stocks.lieux');
                    @endphp
                    <tr class="commandes-row border-t hover:bg-gray-50" data-lieux="{{ $lieuxStockCommande->implode(', ') }}" data-etat="{{ $commande->etat }}" data-urgence="{{ strtolower($commande->urgence) }}">
                        <td class="py-3 px-4">{{ $commande->id }}</td>
                        <td class="py-3 px-4">{{ $commande->client ? $commande->client->nom : '/' }}</td>
                        @php
                            // Supposons que vous avez une relation entre commande et produit
                            $produits = $commande->produits; // Remplacez ceci par la bonne relation ou logique pour récupérer les produits
                            $fournisseurProduitCommande = null;

                            foreach ($produits as $produit) {
                                // Utilisez les bonnes relations ou identifiants pour accéder au fournisseur
                                $fournisseurProduitCommande = DB::table('fournisseur_produit')
                                    ->join('fournisseurs', 'fournisseur_produit.fournisseur_id', '=', 'fournisseurs.id')
                                    ->where('fournisseur_produit.produit_id', $produit->id)  // Assurez-vous que le produit a bien un ID
                                    ->where('fournisseur_produit.commande_id', $commande->id)
                                    ->select('fournisseurs.nom')
                                    ->first();
                                break;  // Si vous ne voulez que le premier fournisseur, sinon continuez avec la logique adaptée
                            }
                        @endphp
                        <td class="py-3 px-4">{{ $fournisseurProduitCommande ? $fournisseurProduitCommande->nom : '/' }}</td>
                        <td class="py-3 px-4">
                            {{ $lieuxStockCommande->implode(', ') ?: 'Non défini' }}
                        </td>
                        <td class="py-3 px-4">{{ $commande->etat }}</td>
                        <td class="py-3 px-4">
                            <span class="
                                @if($commande->urgence === 'pas urgent') text-green-800 
                                @elseif($commande->urgence === 'peu urgent') text-green-600 
                                @elseif($commande->urgence === 'moyennement urgent') text-yellow-600 
                                @elseif($commande->urgence === 'urgent') text-orange-600 
                                @elseif($commande->urgence === 'très urgent') text-red-600 
                                @endif
                                font-semibold">
                                {{ $commande->urgence }}
                            </span>
                        </td>
                        <td class="py-3 px-4 space-x-2">
                            <form action="{{ route('commande.destroy', $commande->id) }}" method="POST" class="inline">
                                @csrf @method('DELETE')
                                <button type="button" class="text-green-700 hover:underline font-semibold" onclick="window.location.href='{{ route('commande.show', $commande->id) }}'">Détails</button>
                                <button type="button" class="text-yellow-700 hover:underline font-semibold" onclick="window.location.href='{{ route('commande.edit', $commande->id) }}'">Modifier</button>
                                <button type="submit" onclick="return confirm('Confirmer la suppression ?')" class="text-red-700 hover:underline font-semibold">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    // Objet pour stocker l'état des filtres actifs
    let activeFilters = {
        lieu: new Set(),
        etat: new Set(),
        urgence: new Set()
    };

    document.querySelectorAll('.filter-btn').forEach(button => {
        button.addEventListener('click', function() {
            const filterValue = this.getAttribute('data-filter').toLowerCase();
            const filterType = this.getAttribute('data-type');
            
            // Toggle le filtre (ajouter/retirer)
            if (activeFilters[filterType].has(filterValue)) {
                activeFilters[filterType].delete(filterValue);
                this.classList.remove('ring-4', 'ring-blue-500');
            } else {
                activeFilters[filterType].add(filterValue);
                this.classList.add('ring-4', 'ring-blue-500');
            }
            
            // Pour le débogage
            console.log('Active filters:', activeFilters);
            applyFilters();
        });
    });

    function applyFilters() {
        const rows = document.querySelectorAll('.commandes-row');
        
        rows.forEach(row => {
            const lieux = row.getAttribute('data-lieux').toLowerCase();
            const etat = row.getAttribute('data-etat').toLowerCase();
            const urgence = row.getAttribute('data-urgence').toLowerCase();
            
            // Pour le débogage
            console.log('Row data:', { lieux, etat, urgence });
            
            let shouldShow = true;

            // Vérifier les filtres de lieu
            if (activeFilters.lieu.size > 0) {
                const lieuMatch = Array.from(activeFilters.lieu).some(lieu => 
                    lieux.includes(lieu)
                );
                if (!lieuMatch) shouldShow = false;
            }

            // Vérifier les filtres d'état
            if (activeFilters.etat.size > 0) {
                const etatMatch = Array.from(activeFilters.etat).some(etatFilter => 
                    etat === etatFilter // Changement ici : utilisation de l'égalité stricte
                );
                if (!etatMatch) shouldShow = false;
            }

            // Vérifier les filtres d'urgence
            if (activeFilters.urgence.size > 0) {
                const urgenceMatch = Array.from(activeFilters.urgence).some(urgenceFilter => 
                    urgence === urgenceFilter // Changement ici : utilisation de l'égalité stricte
                );
                if (!urgenceMatch) shouldShow = false;
            }

            // Appliquer la visibilité
            row.style.display = shouldShow ? '' : 'none';
        });
    }

    document.getElementById('resetFilters').addEventListener('click', function() {
        // Réinitialiser tous les filtres actifs
        activeFilters = {
            lieu: new Set(),
            etat: new Set(),
            urgence: new Set()
        };
        
        // Retirer les indications visuelles des boutons
        document.querySelectorAll('.filter-btn').forEach(button => {
            button.classList.remove('ring-4', 'ring-blue-500');
        });
        
        // Afficher toutes les lignes
        const rows = document.querySelectorAll('.commandes-row');
        rows.forEach(row => {
            row.style.display = '';
        });
    });
    </script>
</div>

@endsection
