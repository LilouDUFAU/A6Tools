@extends('layouts.app')

@section('content')
<form method="POST" action="{{ route('locpret.store') }}">
    @csrf

    <div class="mb-4">
        <label for="client_id" class="block text-sm font-medium text-gray-700 mb-1">Client</label>
        <select class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('client_id') border-red-500 @enderror" id="client_id" name="client_id" required>
            <option value="">Sélectionner un client</option>
            @foreach($clients as $client)
                <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                    {{ $client->nom }} {{ $client->prenom }}
                </option>
            @endforeach
        </select>
        @error('client_id')
            <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
        @enderror
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
        <div>
            <label for="date_debut" class="block text-sm font-medium text-gray-700 mb-1">Date de début</label>
            <input type="date" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('date_debut') border-red-500 @enderror" id="date_debut" name="date_debut" value="{{ old('date_debut', date('Y-m-d')) }}" required>
            @error('date_debut')
                <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
            @enderror
        </div>
        <div>
            <label for="date_retour" class="block text-sm font-medium text-gray-700 mb-1">Date de retour prévue</label>
            <input type="date" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('date_retour') border-red-500 @enderror" id="date_retour" name="date_retour" value="{{ old('date_retour', date('Y-m-d', strtotime('+30 days'))) }}" required>
            @error('date_retour')
                <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-1">Type d'opération</label>
        <div class="flex items-center space-x-6">
            <div class="flex items-center">
                <input class="h-4 w-4 text-indigo-600 border-gray-300 focus:ring-indigo-500 @error('type_operation') border-red-500 @enderror" type="radio" name="type_operation" id="type_pret" value="prêt" {{ old('type_operation', 'prêt') == 'prêt' ? 'checked' : '' }}>
                <label class="ml-2 block text-sm text-gray-700" for="type_pret">Prêt</label>
            </div>
            <div class="flex items-center">
                <input class="h-4 w-4 text-indigo-600 border-gray-300 focus:ring-indigo-500 @error('type_operation') border-red-500 @enderror" type="radio" name="type_operation" id="type_location" value="location" {{ old('type_operation') == 'location' ? 'checked' : '' }}>
                <label class="ml-2 block text-sm text-gray-700" for="type_location">Location</label>
            </div>
        </div>
        @error('type_operation')
            <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-1">PC disponibles</label>
        <div class="bg-blue-50 border border-blue-200 text-blue-800 rounded px-4 py-2 mb-2 text-sm">
            Sélectionnez au moins un PC à prêter ou louer.
        </div>

        @if($pcrenouvs->count() > 0)
            <div class="overflow-x-auto rounded shadow">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-2 py-2 text-left font-medium text-gray-500">#</th>
                            <th class="px-2 py-2 text-left font-medium text-gray-500">Numéro de série</th>
                            <th class="px-2 py-2 text-left font-medium text-gray-500">Référence</th>
                            <th class="px-2 py-2 text-left font-medium text-gray-500">Caractéristiques</th>
                            <th class="px-2 py-2 text-left font-medium text-gray-500">Type</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @foreach($pcrenouvs as $pcrenouv)
                            <tr>
                                <td class="px-2 py-2">
                                    <input class="h-4 w-4 text-indigo-600 border-gray-300 focus:ring-indigo-500" type="checkbox" name="pcrenouv_ids[]" value="{{ $pcrenouv->id }}" id="pc{{ $pcrenouv->id }}" {{ (is_array(old('pcrenouv_ids')) && in_array($pcrenouv->id, old('pcrenouv_ids'))) ? 'checked' : '' }}>
                                </td>
                                <td class="px-2 py-2">{{ $pcrenouv->numero_serie }}</td>
                                <td class="px-2 py-2">{{ $pcrenouv->reference }}</td>
                                <td class="px-2 py-2">{{ Str::limit($pcrenouv->caracteristiques, 50) }}</td>
                                <td class="px-2 py-2">{{ $pcrenouv->type }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @error('pcrenouv_ids')
                <div class="text-red-600 text-sm mt-2">{{ $message }}</div>
            @enderror
            @error('pcrenouv_ids.*')
                <div class="text-red-600 text-sm mt-2">{{ $message }}</div>
            @enderror
        @else
            <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 rounded px-4 py-2 text-sm">
                Aucun PC disponible en stock pour le moment.
            </div>
        @endif
    </div>

    <div class="flex justify-end">
        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition" {{ $pcrenouvs->count() > 0 ? '' : 'disabled' }}>
            Enregistrer
        </button>
    </div>
</form>
@endsection
