@extends('layouts.app')

@section('content')
<form action="{{ route('locpret.update', $locPret->id) }}" method="POST" class="space-y-6">
    @csrf
    @method('PUT')

    <!-- Sélection du client -->
    <div>
        <label for="client_id" class="block text-sm font-medium text-gray-700">Client</label>
        <select id="client_id" name="client_id" required
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('client_id') border-red-500 @enderror">
            <option value="">Sélectionner un client</option>
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

    <!-- Dates -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label for="date_debut" class="block text-sm font-medium text-gray-700">Date de début</label>
            <input type="date" id="date_debut" name="date_debut" value="{{ old('date_debut', $locPret->date_debut) }}" required
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('date_debut') border-red-500 @enderror">
            @error('date_debut')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="date_retour" class="block text-sm font-medium text-gray-700">Date de retour prévue</label>
            <input type="date" id="date_retour" name="date_retour" value="{{ old('date_retour', $locPret->date_retour) }}" required
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('date_retour') border-red-500 @enderror">
            @error('date_retour')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <!-- Type d'opération -->
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Type d'opération</label>
        @php
            $typeOperation = $locPret->pcrenouvs->first() ? ($locPret->pcrenouvs->first()->statut == 'prêté' ? 'prêt' : 'location') : 'prêt';
        @endphp
        <div class="flex items-center space-x-6">
            <label class="flex items-center">
                <input type="radio" name="type_operation" value="prêt" id="type_pret"
                    class="text-indigo-600 focus:ring-indigo-500 border-gray-300"
                    {{ old('type_operation', $typeOperation) == 'prêt' ? 'checked' : '' }}>
                <span class="ml-2 text-sm text-gray-700">Prêt</span>
            </label>

            <label class="flex items-center">
                <input type="radio" name="type_operation" value="location" id="type_location"
                    class="text-indigo-600 focus:ring-indigo-500 border-gray-300"
                    {{ old('type_operation', $typeOperation) == 'location' ? 'checked' : '' }}>
                <span class="ml-2 text-sm text-gray-700">Location</span>
            </label>
        </div>
        @error('type_operation')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Liste des PC -->
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">PC disponibles</label>
        <div class="p-4 mb-4 text-sm text-blue-800 bg-blue-100 rounded-lg">
            Sélectionnez au moins un PC à prêter ou louer.
        </div>

        @if($pcrenouvs->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm text-left">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2">#</th>
                            <th class="px-4 py-2">Numéro de série</th>
                            <th class="px-4 py-2">Référence</th>
                            <th class="px-4 py-2">Caractéristiques</th>
                            <th class="px-4 py-2">Type</th>
                            <th class="px-4 py-2">Statut</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @foreach($pcrenouvs as $pcrenouv)
                            <tr>
                                <td class="px-4 py-2">
                                    <input type="checkbox" name="pcrenouv_ids[]" value="{{ $pcrenouv->id }}" id="pc{{ $pcrenouv->id }}"
                                        class="rounded text-indigo-600 border-gray-300 focus:ring-indigo-500"
                                        {{ (is_array(old('pcrenouv_ids')) && in_array($pcrenouv->id, old('pcrenouv_ids'))) || 
                                           (!old('pcrenouv_ids') && $pcrenouv->locPret_id == $locPret->id) ? 'checked' : '' }}>
                                </td>
                                <td class="px-4 py-2">{{ $pcrenouv->numero_serie }}</td>
                                <td class="px-4 py-2">{{ $pcrenouv->reference }}</td>
                                <td class="px-4 py-2">{{ Str::limit($pcrenouv->caracteristiques, 50) }}</td>
                                <td class="px-4 py-2">{{ $pcrenouv->type }}</td>
                                <td class="px-4 py-2">
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
            <div class="p-4 text-sm text-yellow-800 bg-yellow-100 rounded-lg">
                Aucun PC disponible en stock pour le moment.
            </div>
        @endif
    </div>

    <!-- Bouton de soumission -->
    <div class="flex justify-end">
        <button type="submit"
            class="inline-flex items-center px-5 py-2 bg-indigo-600 text-white text-sm font-medium rounded hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            Mettre à jour
        </button>
    </div>
</form>
@endsection
