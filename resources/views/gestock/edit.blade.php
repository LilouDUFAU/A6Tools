@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto py-6 px-4">
    <h1 class="text-2xl font-bold mb-6">Modifier la commande</h1>

    <form action="{{ route('commande.update', $commande->id) }}" method="POST" class="space-y-4">
        @csrf @method('PUT')

        <x-input label="Intitulé" name="intitule" value="{{ $commande->intitule }}" required />
        <x-input label="Prix total (€)" name="prix_total" type="number" value="{{ $commande->prix_total }}" step="0.01" required />
        <x-input label="État" name="etat" value="{{ $commande->etat }}" required />
        <x-input label="Remarque" name="remarque" value="{{ $commande->remarque }}" />
        <x-input label="Date de livraison fournisseur" name="date_livraison_fournisseur" type="date" value="{{ $commande->date_livraison_fournisseur }}" />
        <x-input label="Date d'installation prévue" name="date_installation_prevue" type="date" value="{{ $commande->date_installation_prevue }}" />

        <div>
            <label class="block text-sm font-medium text-gray-700">Client</label>
            <select name="client_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                @foreach($clients as $client)
                    <option value="{{ $client->id }}" @if($commande->client_id == $client->id) selected @endif>
                        {{ $client->nom }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="inline-flex items-center">
                <input type="checkbox" name="urgence" value="1" class="form-checkbox"
                    {{ $commande->urgence ? 'checked' : '' }}>
                <span class="ml-2">Urgent</span>
            </label>
        </div>

        <button type="submit" class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600">Mettre à jour</button>
    </form>
</div>
@endsection
