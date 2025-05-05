@extends('layouts.app')

@section('content')
<div class="min-h-screen py-10" x-data="{ 
    view: 'grid',
    selectedRole: '{{ request('role', '') }}',
    selectedService: '{{ request('service', '') }}',
    filterEmployees() {
        const url = new URL(window.location);
        url.searchParams.set('role', this.selectedRole);
        url.searchParams.set('service', this.selectedService);
        window.location = url.toString();
    }
}">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Liste des employés</h1>
            <button 
                @click="view = view === 'grid' ? 'list' : 'grid'" 
                class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-xl transition"
            >
                <span x-text="view === 'grid' ? 'Afficher en liste' : 'Afficher en mosaïque'"></span>
            </button>
        </div>

        {{-- Filtrage --}}
        <div class="mb-6 flex space-x-4">
            {{-- Filtre par Rôle --}}
            <div class="w-1/2">
                <label for="role" class="block text-sm font-medium text-gray-700">Filtrer par rôle</label>
                <select 
                    id="role" 
                    x-model="selectedRole" 
                    @change="filterEmployees()"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                >
                    <option value="">Tous les rôles</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}">{{ $role->nom }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Filtre par Service --}}
            <div class="w-1/2">
                <label for="service" class="block text-sm font-medium text-gray-700">Filtrer par service</label>
                <select 
                    id="service" 
                    x-model="selectedService" 
                    @change="filterEmployees()"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                >
                    <option value="">Tous les services</option>
                    @foreach($services as $service)
                        <option value="{{ $service->id }}">{{ $service->nom }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Vue Mosaïque --}}
        <div 
            x-show="view === 'grid'" 
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform scale-95"
            x-transition:enter-end="opacity-100 transform scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 transform scale-100"
            x-transition:leave-end="opacity-0 transform scale-95"
            class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6"
        >
            @foreach($users as $user)
                <div class="bg-white rounded-2xl shadow p-6 text-center hover:shadow-lg transition-all">
                    <a href="{{ route('employe.show', $user->id) }}" class="block">
                        @if($user->photo)
                            <img src="{{ asset('storage/' . $user->photo) }}" alt="{{ $user->nom }}" class="mx-auto w-24 h-24 rounded-full object-cover mb-4">
                        @else
                            <div class="mx-auto w-24 h-24 rounded-full bg-gray-200 flex items-center justify-center text-gray-500 mb-4">
                                <i class="fa-regular fa-circle-user text-6xl sm:text-8xl"></i>
                            </div>
                        @endif
                        <h3 class="text-lg font-semibold text-gray-900">{{ $user->nom }} {{ $user->prenom }}</h3>
                        <p class="text-sm text-gray-600">{{ $user->service->nom ?? 'Service inconnu' }} | {{ $user->role->nom ?? 'Role inconnu' }}</p>
                    </a>

                    <div class="mt-4 flex justify-center space-x-2">
                        <a href="{{ route('employe.edit', $user->id) }}" class="text-blue-600 hover:underline">Modifier</a>
                        <form action="{{ route('employe.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet employé ?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:underline">Supprimer</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Vue Liste --}}
        <div 
            x-show="view === 'list'" 
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform scale-95"
            x-transition:enter-end="opacity-100 transform scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 transform scale-100"
            x-transition:leave-end="opacity-0 transform scale-95"
            x-cloak 
            class="overflow-x-auto mt-6"
        >
            <table class="min-w-full bg-white shadow rounded-2xl overflow-hidden">
                <thead class="bg-gray-100 text-left text-sm font-semibold text-gray-700">
                    <tr>
                        <th class="px-6 py-3">Nom & Prénom</th>
                        <th class="px-6 py-3">Service</th>
                        <th class="px-6 py-3">Rôle</th>
                        <th class="px-6 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 text-sm text-gray-800">
                    @foreach($users as $user)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <a href="{{ route('employe.show', $user->id) }}" class="text-blue-600 hover:underline">
                                    {{ $user->nom }} {{ $user->prenom }}
                                </a>
                            </td>
                            <td class="px-6 py-4">{{ $user->service->nom ?? 'Service inconnu' }}</td>
                            <td class="px-6 py-4">{{ $user->role->nom ?? 'Rôle inconnu' }}</td>
                            <td class="px-6 py-4 flex space-x-2">
                                <a href="{{ route('employe.edit', $user->id) }}" class="text-blue-600 hover:underline">Modifier</a>
                                <form action="{{ route('employe.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet employé ?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection