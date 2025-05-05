<nav>
    <ul class="flex flex-col p-4 gap-4">
        <a href="{{ route('welcome') }}" class="hidden md:flex justify-center"><img src="{{ asset('/images/logo_a6tools.png') }}" alt="Logo" class="w-64 h-auto mb-2 md:mb-4"></a>
        <a href="{{ route('welcome') }}" class="flex md:hidden {{ Route::is('welcome') ? 'bg-green-700 px-2 py-1 text-white font-semibold' : 'hover:bg-green-700 hover:px-2 hover:py-1 hover:text-white transition-all duration-300 hover:font-semibold' }} rounded-lg">
            <i class="fa-solid fa-house"></i> <span class="ml-2">Accueil</span>
        </a>
        <a href="{{ route('commande.index') }}" class="{{ Route::is('commande.index') ? 'bg-green-700 px-2 py-1 text-white font-semibold' : 'hover:bg-green-700 hover:px-2 hover:py-1 hover:text-white transition-all duration-300 hover:font-semibold' }} rounded-lg">
            <i class="fa-solid fa-box-open"></i> <span class="ml-2">Gestock</span>
        </a>
        <a href="{{ route('gestrenouv.index') }}" class="{{ Route::is('gestrenouv.index') ? 'bg-green-700 px-2 py-1 text-white font-semibold' : 'hover:bg-green-700 hover:px-2 hover:py-1 hover:text-white transition-all duration-300 hover:font-semibold' }} rounded-lg">
            <i class="fa-solid fa-desktop"></i> <span class="ml-2">GestRenouv</span>
        </a>
        <a href="{{ route('prepatelier.index') }}" class="{{ Route::is('prepatelier.index') ? 'bg-green-700 px-2 py-1 text-white font-semibold' : 'hover:bg-green-700 hover:px-2 hover:py-1 hover:text-white transition-all duration-300 hover:font-semibold' }} rounded-lg">
            <i class="fa-solid fa-wrench"></i> <span class="ml-2">GestAtelier</span>
        </a>
        <a href="{{ route('panne.index') }}" class="{{ Route::is('panne.index') ? 'bg-green-700 px-2 py-1 text-white font-semibold' : 'hover:bg-green-700 hover:px-2 hover:py-1 hover:text-white transition-all duration-300 hover:font-semibold' }} rounded-lg">
            <i class="fa-solid fa-bolt"></i> <span class="ml-2">GestSAV</span>
        </a>

        @if(auth()->user()->role->nom === 'admin')
        <a href="{{ route('employe.index') }}" class="{{ Route::is('employe.index') ? 'bg-green-700 px-2 py-1 text-white font-semibold' : 'hover:bg-green-700 hover:px-2 hover:py-1 hover:text-white transition-all duration-300 hover:font-semibold' }} rounded-lg">
            <i class="fa-solid fa-users-cog"></i> <span class="ml-2">Gestion utilisateur</span>
        </a>
        @endif

        @if (Route::has('login'))
            @auth
            <a href="{{ route('employe.show', ['id' => Auth::user()->id]) }}" class="flex md:hidden {{ Request::is('profile') ? 'bg-green-700 px-2 py-1 text-white font-semibold' : 'hover:bg-green-700 hover:px-2 hover:py-1 hover:text-white transition-all duration-300 hover:font-semibold' }} rounded-lg">
                <i class="fa-solid fa-circle-user"></i> <span class="ml-2">{{ Auth::user()->prenom }}</span> 
            </a>
            @else
            <a href="{{ route('login') }}" class="flex md:hidden {{ Route::is('login') ? 'bg-green-700 px-2 py-1 text-white font-semibold' : 'hover:bg-green-700 hover:px-2 hover:py-1 hover:text-white transition-all duration-300 hover:font-semibold' }} flex items-center gap-2 rounded-lg">
                <i class="fa-solid fa-circle-user"></i> <span class="ml-2">Connexion</span>
            </a>
            @endauth
        @endif
    </ul>
</nav>