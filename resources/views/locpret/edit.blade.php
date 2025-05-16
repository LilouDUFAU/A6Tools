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
                <select class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1" id="client_id" name="client_id" required>
                    <option value="">-- Sélectionner un client --</option>
                    @foreach($clients as $client)
                        <option value="{{ $client->id }}" {{ old('client_id', $locPret->client_id) == $client->id ? 'selected' : '' }}>
                            {{ $client->nom }} {{ $client->prenom }}
                        </option>
                    @endforeach
                </select>
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
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                @foreach($pcsAffiches as $pcrenouv)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-3 py-3">
                                            <input class="h-4 w-4 text-green-600 border-gray-300 focus:ring-green-500" type="checkbox" name="pcrenouv_ids[]" value="{{ $pcrenouv->id }}" id="pc{{ $pcrenouv->id }}"
                                                {{ (is_array(old('pcrenouv_ids')) && in_array($pcrenouv->id, old('pcrenouv_ids')))
                                                    || (!old('pcrenouv_ids') && in_array($pcrenouv->id, $pcAssocies)) ? 'checked' : '' }}>
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
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
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

        <button type="submit" class="w-full bg-green-600 text-white px-6 py-3 rounded-lg shadow hover:bg-green-700 focus:ring-2 focus:ring-green-500 flex items-center justify-center" {{ $pcrenouvs->count() > 0 ? '' : 'disabled' }}>
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
@endsection