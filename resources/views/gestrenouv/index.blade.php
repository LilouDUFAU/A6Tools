@extends('layouts.app')
@section('content')
    <div class="rounded-lg overflow-hidden">
        <div class="p-6 flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-800">Liste des PC Renouv</h1>
            <a href="{{ route('locpret.index') }}" 
                class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition-colors mr-2">
                 <i class="fas fa-list mr-1"></i> Voir les locations/prêts
            </a>
            <a href="{{ route('locpret.create') }}" 
                class="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700 transition-colors mr-2">
                 <i class="fas fa-plus mr-1"></i> Créer une location
            </a>
            <a href="{{ route('gestrenouv.create') }}" 
               class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors">
                <i class="fas fa-plus mr-1"></i> Ajouter un PC
            </a>
        </div>

        <div class="p-6">
            <div class="mb-4 flex justify-end">
                <div class="relative">
                    <input type="text" id="searchInput" 
                           class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="Rechercher...">
                    <i class="fas fa-search absolute right-3 top-3 text-gray-400"></i>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">N° Série</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Référence</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($pcrenouvs as $pcrenouv)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $pcrenouv->numero_serie }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $pcrenouv->reference }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $pcrenouv->type }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @if($pcrenouv->statut == 'en stock')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            En stock
                                        </span>
                                    @elseif($pcrenouv->statut == 'prêté')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            Prêté
                                        </span>
                                    @elseif($pcrenouv->statut == 'loué')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            Loué
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if($pcrenouv->locprets->isNotEmpty() && $pcrenouv->locprets->first()->client)
                                        {{ $pcrenouv->locprets->first()->client->nom }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 space-x-2">
                                    <a href="{{ route('gestrenouv.show', $pcrenouv) }}" class="text-blue-600 hover:text-blue-900">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('gestrenouv.edit', $pcrenouv->id) }}" class="text-yellow-600 hover:text-yellow-900">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    
                                    @if($pcrenouv->statut == 'en stock')
                                        <a href="{{ route('locpret.create', ['pcrenouv_id' => $pcrenouv->id]) }}" class="text-blue-600 hover:text-blue-900">
                                            <i class="fas fa-handshake"></i>
                                        </a>
                                    @elseif($pcrenouv->statut == 'prêté' || $pcrenouv->statut == 'loué')
                                        {{-- Formulaire invisible pour PUT retour via locpret.retourner --}}
                                        @if($pcrenouv->locprets->isNotEmpty())
                                            <form id="retourner-form-{{ $pcrenouv->id }}" action="{{ route('locpret.retourner', $pcrenouv->locprets->first()) }}" method="POST" style="display:none;">
                                                @csrf
                                                @method('PUT')
                                            </form>
                                            <a href="#" class="text-green-600 hover:text-green-900"
                                               onclick="event.preventDefault(); if(confirm('Confirmer le retour de ce PC?')) document.getElementById('retourner-form-{{ $pcrenouv->id }}').submit();">
                                                <i class="fas fa-undo"></i>
                                            </a>
                                        @endif
                                    @endif
                                    
                                    <form action="{{ route('gestrenouv.destroy', $pcrenouv) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce PC?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500">Aucun PC Renouv trouvé</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal pour Prêt/Location -->
    <div id="pretModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden flex items-center justify-center">
        <div class="bg-white rounded-lg p-8 max-w-md w-full">
            <h2 class="text-xl font-bold mb-4">Prêter/Louer ce PC</h2>
            
            <form id="pretForm" action="" method="POST">
                @csrf
                @method('POST')
                
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="client_id">
                        Client
                    </label>
                    <select name="client_id" id="client_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                        <option value="">Sélectionner un client</option>
                        @foreach(App\Models\Client::all() as $client)
                            <option value="{{ $client->id }}">{{ $client->nom }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="type_operation">
                        Type d'opération
                    </label>
                    <select name="type_operation" id="type_operation" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                        <option value="prêt">Prêt</option>
                        <option value="location">Location</option>
                    </select>
                </div>
                
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="date_debut">
                        Date de début
                    </label>
                    <input type="text" name="date_debut" id="date_debut" class="datepicker shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                </div>
                
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="date_retour">
                        Date de retour prévue
                    </label>
                    <input type="text" name="date_retour" id="date_retour" class="datepicker shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                </div>
                
                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="closeModal()" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Annuler
                    </button>
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Confirmer
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    function openPretModal(pcrenouvId) {
        // Correction du chemin ici, pour coller à ta route
        document.getElementById('pretForm').action = `/gestrenouv/${pcrenouvId}/preter-louer`;
        document.getElementById('pretModal').classList.remove('hidden');
    }
    
    function closeModal() {
        document.getElementById('pretModal').classList.add('hidden');
    }
    
    // Filtre de recherche pour la table
    document.getElementById('searchInput').addEventListener('keyup', function() {
        const searchValue = this.value.toLowerCase();
        const rows = document.querySelectorAll('tbody tr');
        
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchValue) ? '' : 'none';
        });
    });
</script>
@endsection
