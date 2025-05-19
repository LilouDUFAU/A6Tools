@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto my-4 py-8 px-6 bg-white shadow-md rounded-lg">
    <h1 class="text-3xl font-extrabold text-gray-800 mb-8">Modifier une Location/Prêt</h1>
    
    <form method="POST" action="{{ route('locpret.update', $locPret->id) }}" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Partie Client -->
        <div class="border-l-4 border-green-600 pl-4">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Information Client</h2>
            
            <div class="mb-4">
                <label for="client_id" class="block text-sm font-semibold text-gray-700">* Client</label>
                <div class="relative">
                    @php
                        $selectedClient = $clients->where('id', $locPret->client_id)->first();
                        $clientName = $selectedClient ? ($selectedClient->prenom ? $selectedClient->nom . ' ' . $selectedClient->prenom : $selectedClient->nom) : 'Client inconnu';
                    @endphp
                    <input type="text" value="{{ $clientName }}" class="mt-2 block w-full border-gray-300 bg-gray-100 rounded-lg shadow-sm text-gray-700 cursor-not-allowed px-2 py-1" readonly>
                    <input type="hidden" id="client_id" name="client_id" value="{{ $locPret->client_id }}">
                </div>
                <div class="mt-1 text-gray-500 text-sm italic">
                    <span class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 15v5M8 9h8M6 19h12a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2z"/>
                        </svg>
                        Le client ne peut pas être modifié
                    </span>
                </div>
                
                <!-- Afficher les informations du client -->
                @if($selectedClient)
                <div class="mt-2 p-3 bg-blue-50 rounded-lg border border-blue-200">
                    <div class="text-sm">
                        @if($selectedClient->code_client)
                            <p><span class="font-medium">Code client:</span> {{ $selectedClient->code_client }}</p>
                        @endif
                        @if($selectedClient->numero_telephone)
                            <p><span class="font-medium">Téléphone:</span> {{ $selectedClient->numero_telephone }}</p>
                        @endif
                    </div>
                </div>
                @endif
                
                @error('client_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Partie Dates -->
        <div class="border-l-4 border-green-600 pl-4">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Informations de Période</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="mb-4">
                    <label for="date_debut" class="block text-sm font-semibold text-gray-700">* Date de début</label>
                    <input type="date" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1" id="date_debut" name="date_debut" value="{{ old('date_debut', $locPret->date_debut) }}" required>
                    @error('date_debut')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mb-4">
                    <label for="date_retour" class="block text-sm font-semibold text-gray-700">* Date de retour prévue</label>
                    <input type="date" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1" id="date_retour" name="date_retour" value="{{ old('date_retour', $locPret->date_retour) }}" required>
                    @error('date_retour')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Partie Type d'opération -->
        <div class="border-l-4 border-green-600 pl-4">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Type d'Opération</h2>
            
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">* Sélectionner le type</label>
                @php
                    $typeOperation = $locPret->pcrenouvs->first() ? ($locPret->pcrenouvs->first()->statut == 'prêté' ? 'prêt' : 'location') : 'prêt';
                @endphp
                <div class="flex items-center space-x-6">
                    <div class="flex items-center">
                        <input class="h-4 w-4 text-green-600 border-gray-300 focus:ring-green-500" type="radio" name="type_operation" id="type_pret" value="prêt" {{ old('type_operation', $typeOperation) == 'prêt' ? 'checked' : '' }}>
                        <label class="ml-2 block text-sm text-gray-700" for="type_pret">Prêt</label>
                    </div>
                    <div class="flex items-center">
                        <input class="h-4 w-4 text-green-600 border-gray-300 focus:ring-green-500" type="radio" name="type_operation" id="type_location" value="location" {{ old('type_operation', $typeOperation) == 'location' ? 'checked' : '' }}>
                        <label class="ml-2 block text-sm text-gray-700" for="type_location">Location</label>
                    </div>
                </div>
                @error('type_operation')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Partie PC disponibles -->
        <div class="border-l-4 border-green-600 pl-4">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Sélection des PC</h2>
            
            <div class="mb-4">
                <div class="bg-green-50 border border-green-200 text-green-800 rounded px-4 py-3 mb-4 text-sm">
                    Sélectionnez au moins un PC à prêter ou louer.
                </div>

                <!-- Barre de recherche pour les PCs -->
                <div class="mb-4">
                    <div class="relative">
                        <input
                            type="text"
                            id="pcrenouv_search"
                            placeholder="Rechercher par référence ou numéro de série..."
                            class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 pl-10 pr-4 py-2"
                        >
                        <div class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                        </div>
                    </div>
                </div>

                @php
                    // IDs des PC déjà associés à la commande
                    $pcAssocies = $locPret->pcrenouvs->pluck('id')->toArray();
                @endphp

                @php
                    // On affiche seulement les PC en stock ou déjà associés à la commande
                    $pcsAffiches = $pcrenouvs->filter(function($pc) use ($pcAssocies) {
                        return $pc->statut === 'en stock' || in_array($pc->id, $pcAssocies);
                    });
                @endphp

                @if($pcsAffiches->count() > 0)
                    <div class="overflow-x-auto rounded-lg shadow">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-3 text-left font-medium text-gray-500">#</th>
                                    <th class="px-3 py-3 text-left font-medium text-gray-500">Numéro de série</th>
                                    <th class="px-3 py-3 text-left font-medium text-gray-500">Référence</th>
                                    <th class="px-3 py-3 text-left font-medium text-gray-500">Caractéristiques</th>
                                    <th class="px-3 py-3 text-left font-medium text-gray-500">Type</th>
                                    <th class="px-3 py-3 text-left font-medium text-gray-500">Statut</th>
                                    <th class="px-3 py-3 text-left font-medium text-gray-500">Magasin</th>
                                </tr>
                            </thead>
                            <tbody id="pcrenouv_table_body" class="bg-white divide-y divide-gray-100">
                                @foreach($pcsAffiches as $pcrenouv)
                                    <tr class="hover:bg-gray-50 pcrenouv-row {{ in_array($pcrenouv->id, $pcAssocies) ? 'bg-blue-50' : '' }}" data-pcrenouv-id="{{ $pcrenouv->id }}" data-reference="{{ $pcrenouv->reference }}" data-numero-serie="{{ $pcrenouv->numero_serie }}">
                                        <td class="px-3 py-3">
                                            <input class="h-4 w-4 text-green-600 border-gray-300 focus:ring-green-500 pcrenouv-checkbox" type="checkbox" name="pcrenouv_ids[]" value="{{ $pcrenouv->id }}" id="pc{{ $pcrenouv->id }}"
                                                {{ (is_array(old('pcrenouv_ids')) && in_array($pcrenouv->id, old('pcrenouv_ids')))
                                                    || (!old('pcrenouv_ids') && in_array($pcrenouv->id, $pcAssocies)) ? 'checked' : '' }}>
                                            @if(in_array($pcrenouv->id, $pcAssocies))
                                                <div class="mt-1">
                                                    <span class="inline-block px-2 py-0.5 text-xs bg-blue-100 text-blue-800 rounded-full">
                                                        Déjà associé
                                                    </span>
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-3 py-3">{{ $pcrenouv->numero_serie }}</td>
                                        <td class="px-3 py-3">{{ $pcrenouv->reference }}</td>
                                        <td class="px-3 py-3">{{ Str::limit($pcrenouv->caracteristiques, 50) }}</td>
                                        <td class="px-3 py-3">{{ $pcrenouv->type }}</td>
                                        <td class="px-3 py-3">
                                            <span class="inline-block px-2 py-1 text-xs font-semibold rounded 
                                                {{ 
                                                    $pcrenouv->statut === 'en stock' ? 'bg-green-100 text-green-800' : 
                                                    ($pcrenouv->statut === 'prêté' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800') 
                                                }}">
                                                {{ $pcrenouv->statut }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-3">
                                            @php
                                                $lieux = DB::table('pcrenouv_stock')
                                                    ->join('stocks', 'pcrenouv_stock.stock_id', '=', 'stocks.id')
                                                    ->where('pcrenouv_stock.pcrenouv_id', $pcrenouv->id)
                                                    ->distinct()->pluck('stocks.lieux')->implode(', ');
                                            @endphp
                                            {{ $lieux ?: '-' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div id="no_results" class="hidden p-4 bg-gray-50 text-gray-600 text-center rounded mt-2">
                        Aucun PC ne correspond à votre recherche.
                    </div>
                    @error('pcrenouv_ids')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    @error('pcrenouv_ids.*')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                @else
                    <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 rounded px-4 py-3 text-sm">
                        Aucun PC disponible en stock pour le moment.
                    </div>
                @endif
            </div>
        </div>

        <button type="submit" class="w-full bg-green-600 text-white px-6 py-3 rounded-lg shadow hover:bg-green-700 focus:ring-2 focus:ring-green-500 flex items-center justify-center" {{ $pcsAffiches->count() > 0 ? '' : 'disabled' }}>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                <polyline points="17 21 17 13 7 13 7 21"/>
                <polyline points="7 3 7 8 15 8"/>
            </svg>
            Mettre à jour
        </button>
    </form>
    
    <div class="text-right mt-4 p-4">
        <a href="{{ route('locpret.index') }}" class="text-gray-500 hover:underline">Retour</a>
    </div>
</div>

<script>
let pcSearchTimeout;

document.addEventListener('DOMContentLoaded', function() {
    // PC Search functionality
    const pcSearchInput = document.getElementById('pcrenouv_search');
    const pcRows = document.querySelectorAll('.pcrenouv-row');
    const noResultsDiv = document.getElementById('no_results');

    pcSearchInput.addEventListener('input', function(e) {
        clearTimeout(pcSearchTimeout);
        
        pcSearchTimeout = setTimeout(() => {
            const query = e.target.value.toLowerCase().trim();
            let hasVisibleRows = false;
            
            pcRows.forEach(row => {
                const reference = row.getAttribute('data-reference').toLowerCase();
                const numeroSerie = row.getAttribute('data-numero-serie').toLowerCase();
                
                if (query === '' || reference.includes(query) || numeroSerie.includes(query)) {
                    row.classList.remove('hidden');
                    hasVisibleRows = true;
                } else {
                    row.classList.add('hidden');
                }
            });
            
            // Show or hide the "No results" message
            if (hasVisibleRows) {
                noResultsDiv.classList.add('hidden');
            } else {
                noResultsDiv.classList.remove('hidden');
            }
        }, 300);
    });

    // Clicking on row should also toggle the checkbox
    pcRows.forEach(row => {
        row.addEventListener('click', function(e) {
            // Don't toggle if clicking on the checkbox itself
            if (e.target.type !== 'checkbox') {
                const checkbox = this.querySelector('.pcrenouv-checkbox');
                checkbox.checked = !checkbox.checked;
            }
        });
    });
});
</script>
@endsection