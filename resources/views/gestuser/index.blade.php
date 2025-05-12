@extends('layouts.app')

@section('content')
<div class="min-h-screen py-10" x-data="{ 
    view: '{{ request('view', 'grid') }}',
    selectedRole: '{{ request('role', '') }}',
    selectedService: '{{ request('service', '') }}',
    filterEmployees() {
        const url = new URL(window.location);
        url.searchParams.set('role', this.selectedRole);
        url.searchParams.set('service', this.selectedService);
        url.searchParams.set('view', this.view); // On garde la vue
        window.location = url.toString();
    }
}">       
        <div class="container mx-auto py-8 px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row items-center justify-between mb-6">
        <h1 class="text-3xl font-bold mb-8 px-4 pt-10 text-gray-800 text-center sm:text-left">Liste des employés ({{ $users->count() }})</h1>
            <button 
                @click="view = view === 'grid' ? 'list' : 'grid'" 
                class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-xl transition w-full sm:w-auto"
            >
                <span x-text="view === 'grid' ? 'Afficher en liste' : 'Afficher en mosaïque'"></span>
            </button>
        </div>

        {{-- Filtrage --}}
        <div class="mb-6 flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4">
            {{-- Filtre par Rôle --}}
            <div class="w-full sm:w-1/2">
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
            <div class="w-full sm:w-1/2">
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

            {{-- Bouton Réinitialiser --}}
            <div class="w-full sm:w-auto">
                <button 
                    @click="selectedRole = ''; selectedService = ''; filterEmployees()" 
                    class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-md transition w-full sm:w-auto"
                >
                    Réinitialiser
                </button>
            </div>
        </div>

        <div class="mb-6 flex justify-end">
            <a href="{{ route('gestuser.create') }}" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-xl transition w-full sm:w-auto text-center">
                Créer un nouvel employé
            </a>
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
                    <a href="{{ route('gestuser.show', $user->id) }}" class="block">
                        @if($user->photo)
                            <img src="{{ asset('storage/' . $user->photo) }}" alt="{{ $user->nom }}" class="mx-auto w-24 h-24 rounded-full object-cover mb-4">
                        @else
                            <div class="mx-auto w-24 h-24 rounded-full bg-gray-200 flex items-center justify-center text-gray-500 mb-4">
                                <i class="fa-regular fa-circle-user text-6xl sm:text-8xl"></i>
                            </div>
                        @endif
                        <h3 class="text-lg font-semibold text-gray-900">{{ $user->nom }} {{ $user->prenom }}</h3>
                        <p class="text-sm text-gray-600">{{ $user->service->nom ?? 'Service inconnu' }} | {{ $user->role->nom ?? 'Rôle inconnu' }}</p>
                    </a>

                    <div class="mt-4 flex justify-center space-x-2">
                        <a href="{{ route('gestuser.edit', $user->id) }}" class="text-blue-600 hover:underline">Modifier</a>
                        <form action="{{ route('gestuser.destroy', $user->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="button" onclick="openModal({{ $user->id }}, '{{ $user->prenom }}', '{{ $user->nom }}')" class="text-red-600 hover:underline">Supprimer</button>
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
                        <tr class="hover:bg-gray-50 cursor-pointer" onclick="window.location='{{ route('gestuser.show', $user->id) }}';">
                            <td class="px-6 py-4">
                                {{ $user->nom }} {{ $user->prenom }}
                            </td>
                            <td class="px-6 py-4">{{ $user->service->nom ?? 'Service inconnu' }}</td>
                            <td class="px-6 py-4">{{ $user->role->nom ?? 'Rôle inconnu' }}</td>
                            <td class="px-6 py-4 flex space-x-2">
                                <a href="{{ route('gestuser.edit', $user->id) }}" class="text-blue-600 hover:underline" onclick="event.stopPropagation();">Modifier</a>
                                <form action="{{ route('gestuser.destroy', $user->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" onclick="event.stopPropagation(); openModal({{ $user->id }}, '{{ $user->prenom }}', '{{ $user->nom }}')" class="text-red-600 hover:underline">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal --}}
<div id="modal" class="fixed inset-0 z-50 hidden bg-gray-800/40 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-lg w-11/12 sm:w-1/2 lg:w-1/3">
        <div class="px-4 py-2 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-800">Confirmation de Suppression</h3>
            <button id="closeModal" class="text-gray-600 hover:text-gray-800">&times;</button>
        </div>
        <div class="p-4">
        <p class="text-gray-700">
            Êtes-vous sûr de vouloir supprimer 
            <strong id="modalUserName">cet utilisateur</strong> ? Cette action est irréversible.
        </p>
        </div>
        <div class="px-4 py-2 flex justify-end space-x-4">
            <button id="cancelModal" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">Annuler</button>
            <form id="deleteForm" method="POST" action="{{ route('gestuser.destroy', 0) }}">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700">Supprimer</button>
            </form>
        </div>
    </div>
</div>

<script>
    const modal = document.getElementById('modal');
    const closeModal = document.getElementById('closeModal');
    const cancelModal = document.getElementById('cancelModal');
    const deleteForm = document.getElementById('deleteForm');
    const modalUserName = document.getElementById('modalUserName');
    const deleteRouteTemplate = "{{ route('gestuser.destroy', ':id') }}";

    function openModal(id, prenom, nom) {
        deleteForm.action = deleteRouteTemplate.replace(':id', id);
        modalUserName.textContent = `${prenom} ${nom}`;
        modal.classList.remove('hidden');
    }

    function closeModalHandler() {
        modal.classList.add('hidden');
    }

    closeModal.addEventListener('click', closeModalHandler);
    cancelModal.addEventListener('click', closeModalHandler);
    window.addEventListener('click', (e) => {
        if (e.target === modal) closeModalHandler();
    });
</script>

@endsection
