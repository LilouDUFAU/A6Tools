@extends('layouts.app')

@section('content')
<div class="text-black flex items-center justify-center min-h-screen flex-col">
    <div class="w-full max-w-4xl text-center mb-6 bg-white shadow-lg p-6 rounded-lg">
        {{-- Welcome Message --}}
        <h1 class="text-4xl font-bold text-gray-800 mb-4">Bienvenue sur A6Tools</h1>
        <p class="text-lg text-gray-600">Votre plateforme pour des outils modernes et efficaces.</p>
    </div>

    {{-- Navigation --}}
    <div class="w-full max-w-4xl text-center">
        @if (Route::has('login'))
            <div class="flex items-center justify-center gap-6">
                @auth
                    <a
                        href="{{ url('/dashboard') }}"
                        class="bg-green-600 text-white hover:bg-green-700 font-semibold py-2 px-4 rounded-lg shadow-md transition"
                    >
                        Accéder au tableau de bord
                    </a>
                @else
                    <a
                        href="{{ route('login') }}"
                        class="bg-green-600 text-white hover:bg-green-700 font-semibold py-2 px-4 rounded-lg shadow-md transition"
                    >
                        Connexion
                    </a>

                    @if (Route::has('register'))
                        <a
                            href="{{ route('register') }}"
                            class="bg-green-600 text-white hover:bg-green-700 font-semibold py-2 px-4 rounded-lg shadow-md transition"
                        >
                            Inscription
                        </a>
                    @endif
                @endauth
            </div>
        @endif
    </div>
</div>
@endsection
