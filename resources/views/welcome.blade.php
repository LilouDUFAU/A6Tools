@extends('layouts.app')

@section('content')
<div class="text-black flex items-center justify-center flex-col bg-gray-100 pt-10 lg:pt-30">
    <div class="w-full max-w-4xl text-center mb-6 bg-white shadow-lg p-6 rounded-lg">
        {{-- Welcome Message --}}
        <h1 class="text-4xl md:text-5xl font-bold text-gray-800 mb-4">👋🏻 Bienvenue sur <span class="text-green-600">A6Tools</span></h1>
        <p class="text-lg md:text-xl text-gray-600">Votre outil de gestion web collaboratif !</p>
    </div>

    <div class="w-full max-w-4xl text-center">
        @if (Route::has('login'))
            <div class="flex flex-col items-center justify-center gap-6">
                @auth
                <div class="flex flex-wrap items-center justify-center gap-4">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="bg-red-600 text-white hover:bg-red-700 font-semibold py-2 px-4 rounded-lg shadow-md transition">
                            Déconnexion
                        </button>
                    </form>
                </div>

                <!-- Applications Section -->
                <div class="mt-12">
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-6 lg:mb-20">Applications</h2>
                    <div class="flex flex-col gap-10 lg:flex-row justify-between items-center lg:gap-30">
                        <a href="{{ route('gestcommande.index') }}" class="flex flex-col items-center text-gray-800 hover:text-green-600 transition">
                            <i class="nav-icon fas fa-box-open text-6xl mb-4"></i>
                            <span class="text-lg md:text-xl">GestCommande</span>
                        </a>
                        <a href="{{ route('gestrenouv.index') }}" class="flex flex-col items-center text-gray-800 hover:text-green-600 transition">
                            <i class="nav-icon fa-solid fa-desktop text-6xl mb-4"></i>
                            <span class="text-lg md:text-xl">GestRenouv</span>
                        </a>
                        @if (auth()->check() && auth()->user()->stock_id === 1)
                        <a href="{{ route('gestatelier.index') }}" class="flex flex-col items-center text-gray-800 hover:text-green-600 transition">
                            <i class="nav-icon fa-solid fa-wrench text-6xl mb-4"></i>
                            <span class="text-lg md:text-xl">GestAtelier</span>
                        </a>
                        @endif
                        <a href="{{ route('gestsav.index') }}" class="flex flex-col items-center text-gray-800 hover:text-green-600 transition">
                            <i class="nav-icon fa-solid fa-bolt text-6xl mb-4"></i>
                            <span class="text-lg md:text-xl">GestSAV</span>
                        </a>
                    </div>
                </div>
                @else
                <div class="flex flex-wrap items-center justify-center gap-4">
                    <a href="{{ route('login') }}" class="bg-green-600 text-white hover:bg-green-700 font-semibold py-2 px-4 rounded-lg shadow-md transition">
                        Connexion
                    </a>
                </div>
                @endauth
            </div>
        @endif
    </div>
</div>
@endsection
