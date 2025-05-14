@extends('layouts.app')

@section('content')
<div class="min-h-screen py-10 flex items-center justify-center bg-gray-100">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white shadow rounded-2xl py-8 px-16">
            @if(auth()->user()->id === $user->id)
                <h2 class="text-2xl font-bold mb-6 text-gray-800">Mon compte</h2>
            @else
                <h2 class="text-2xl font-bold mb-6 text-gray-800">Détails de l'utilisateur</h2>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p class="text-sm text-gray-500">Nom :</p>
                    <p class="text-lg text-gray-900 font-semibold">{{ $user->nom }}</p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">Prénom :</p>
                    <p class="text-lg text-gray-900 font-semibold">{{ $user->prenom }}</p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">Téléphone :</p>
                    <p class="text-lg text-gray-900 font-semibold">{{ $user->telephone }}</p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">Email :</p>
                    <p class="text-lg text-gray-900 font-semibold">{{ $user->email }}</p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">Service :</p>
                    <p class="text-lg text-gray-900 font-semibold">{{ $user->service->nom ?? 'N/A' }}</p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">Rôle :</p>
                    <p class="text-lg text-gray-900 font-semibold">{{ $user->role->nom ?? 'N/A' }}</p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">Magasin associé :</p>
                    <p class="text-lg text-gray-900 font-semibold">{{ $user->stock->lieux ?? 'N/A' }}</p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">Photo :</p>
                    @if($user->photo)
                        <img src="{{ asset('storage/' . $user->photo) }}" alt="Photo de {{ $user->nom }}" class="mt-2 w-32 h-32 object-cover rounded-full shadow cursor-pointer hover:opacity-75 transition" onclick="openPopup('{{ asset('storage/' . $user->photo) }}')">
                    @else
                        <p class="text-gray-600 italic">Pas de photo</p>
                    @endif
                </div>

                <!-- Popup Modal -->
                <div id="photoPopup" class="fixed inset-0 bg-gray-800/40 flex items-center justify-center hidden" onclick="closePopup(event)">
                    <div class="bg-white p-4 rounded shadow-lg relative" onclick="event.stopPropagation()">
                        <button onclick="closePopup()" class="absolute top-2 right-2 text-gray-500 hover:text-gray-700 text-2xl">&times;</button>
                        <img id="popupImage" src="" alt="Photo agrandie" class="max-w-full max-h-screen">
                    </div>
                </div>

                <script>
                    function openPopup(imageUrl) {
                        const popup = document.getElementById('photoPopup');
                        const popupImage = document.getElementById('popupImage');
                        popupImage.src = imageUrl;
                        popupImage.style.maxHeight = '60vh';
                        popupImage.style.minHeight = '60vh';
                        popupImage.style.width = 'auto';
                        popup.classList.remove('hidden');
                    }

                    function closePopup(event) {
                        if (!event || event.target.id === 'photoPopup') {
                            const popup = document.getElementById('photoPopup');
                            popup.classList.add('hidden');
                        }
                    }
                </script>
            </div>

            <div class="mt-8 flex justify-between items-center w-full">
                @if(auth()->user()->role->nom === 'admin')
                    <a href="{{ route('gestuser.edit', ['id' => $user->id]) }}" class="text-blue-500 hover:underline transition">
                        Modifier
                    </a>
                    <a href="{{ route('gestuser.index') }}" class="text-red-500 hover:underline transition">
                        Retour à la liste des employés
                    </a>
                    <button class="ml-4 bg-red-600 text-white py-2 px-4 rounded hover:bg-red-700 transition" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        Déconnexion
                    </button>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                        @csrf
                    </form>
                @endif
                @if(auth()->check() && auth()->user()->role->nom === 'user')
                    <div class="ml-auto">
                        <a href="{{ route('home') }}" class="text-gray-500 hover:underline transition">
                            Retour à l'accueil
                        </a>
                        <button class="ml-4 bg-red-600 text-white py-2 px-4 rounded hover:bg-red-700 transition" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            Déconnexion
                        </button>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                            @csrf
                        </form>
                    </div>
                @endif
            </div>

        </div>
    </div>
</div>
@endsection
