<nav>
    <ul class="flex flex-col p-4 gap-4">
        <a href="{{ route('welcome') }}" class="hidden md:flex justify-center"><img src="{{ asset('/images/logo_a6tools.png') }}" alt="Logo" class="w-64 h-auto mb-2 md:mb-4"></a>
        <a href="{{ route('welcome') }}" class="flex md:hidden hover:bg-green-700 rounded-lg hover:px-2 hover:py-1 hover:text-white transition-all duration-300 hover:font-semibold"><i class="fa-solid fa-house"></i> <span class="ml-2">Accueil</span></a>
        <a href="" class="hover:bg-green-700 rounded-lg hover:px-2 hover:py-1 hover:text-white transition-all duration-300 hover:font-semibold"><i class="fa-solid fa-table"></i> <span class="ml-2">Dashboard</span></a>
        <a href="" class="hover:bg-green-700 rounded-lg hover:px-2 hover:py-1 hover:text-white transition-all duration-300 hover:font-semibold"><i class="fa-solid fa-box-open"></i> <span class="ml-2">Gestock</span></a>
        <a href="" class="hover:bg-green-700 rounded-lg hover:px-2 hover:py-1 hover:text-white transition-all duration-300 hover:font-semibold"><i class="fa-solid fa-desktop"></i> <span class="ml-2">GestRenouv</span></a>
        <a href="" class="hover:bg-green-700 rounded-lg hover:px-2 hover:py-1 hover:text-white transition-all duration-300 hover:font-semibold"><i class="fa-solid fa-wrench"></i> <span class="ml-2">GestAtelier</span></a>
        <a href="" class="hover:bg-green-700 rounded-lg hover:px-2 hover:py-1 hover:text-white transition-all duration-300 hover:font-semibold"><i class="fa-solid fa-bolt"></i> <span class="ml-2">GestSAV</span></a>

        @if (Route::has('login'))
            @auth
            <a href="{{ route('account') }}" class="flex md:hidden hover:bg-green-700 rounded-lg hover:px-2 hover:py-1 hover:text-white transition-all duration-300 hover:font-semibold">
                <i class="fa-solid fa-circle-user"></i> <span class="ml-2">Lilou</span> <!-- {{ Auth::user()->prenom }} -->
            </a>
            @else
            <a href="{{ route('login') }}" class="flex md:hidden  hover:bg-green-700 rounded-lg hover:px-2 hover:py-1 hover:text-white transition-all duration-300 hover:font-semibold flex items-center gap-2">
            <i class="fa-solid fa-circle-user"></i> <span class="ml-2">Connexion</span>
            </a>
            @endauth
        @endif
    </ul>
</nav>