@extends('layouts.app')

@section('content')
<div class="min-h-screen">
    <div class="max-w-6xl mx-auto my-4 py-8 px-6 bg-white shadow-md rounded-lg">
        <h1 class="text-3xl font-extrabold text-gray-800 mb-8">Récapitulatif de la Préparation Atelier</h1>

        <!-- Partie Informations Générales -->
        <div class="border-l-4 border-green-600 pl-4 mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Informations Générales</h2>
            <p><strong>Technicien :</strong> {{ $prepAtelier->employe->prenom ?? 'Inconnu' }} {{ $prepAtelier->employe->nom ?? 'Inconnu' }}</p>
            <p><strong>Date de création :</strong> {{ $prepAtelier->created_at->format('d/m/Y H:i') }}</p>
        </div>

        <!-- Partie Commande Associée -->
        <div class="border-l-4 border-green-600 pl-4 mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Commande Associée</h2>
            <p><strong>ID :</strong> {{ $prepAtelier->commande->id }}</p>
            <p><strong>Client :</strong> {{ $prepAtelier->commande->client->nom }} ({{ $prepAtelier->commande->client->code_client }})</p>
            <p><strong>Voir la commande :</strong>
                <a href="{{ route('commande.show', ['id' => $prepAtelier->commande->id]) }}" class="text-blue-600 hover:underline">Voir la Commande</a>
            </p>
        </div>

        <!-- Partie Notes du Technicien -->
        <div class="border-l-4 border-green-600 pl-4 mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Notes du Technicien</h2>
            <p class="bg-gray-50 p-4 rounded-lg shadow text-gray-800">{{ $prepAtelier->notes }}</p>
        </div>

        <!-- Partie Étapes -->
        <div class="border-l-4 border-green-600 pl-4 mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Étapes</h2>
            @if ($prepAtelier->etapes->count())
                <ul class="list-disc pl-5 space-y-2">
                    @foreach ($prepAtelier->etapes as $etape)
                        <li class="flex justify-between items-center">
                        <form action="{{ route('etapes.update', $etape->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <label class="flex items-center space-x-3">
                                <input 
                                    type="checkbox" 
                                    class="form-checkbox h-5 w-5 text-green-600" 
                                    name="is_done"
                                    value="1"
                                    {{ $etape->is_done ? 'checked' : '' }}
                                    onchange="this.form.submit()"
                                >
                                <span>{{ $etape->intitule }}</span>
                            </label>
                        </form>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-gray-500 italic">Aucune étape enregistrée.</p>
            @endif
        </div>
        
        <!-- Boutons -->
        <div class="flex justify-between mt-8">
            <a href="{{ route('prepatelier.edit', $prepAtelier->id) }}" class="text-green-600 font-medium hover:underline">Modifier</a>
            <a href="{{ route('prepatelier.index') }}" class="text-gray-600 hover:underline">Retour à la liste</a>
        </div>
    </div>
</div>
@endsection
