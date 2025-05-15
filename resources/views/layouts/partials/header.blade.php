<div class="flex items-center justify-between p-4 gap-4">
    <form onsubmit="event.preventDefault();" class="w-full max-w-md">
        <div class="relative">
            <input id="searchInput" type="text" name="query" placeholder="Rechercher..." class="w-full px-4 py-2 border border-gray-300 rounded-lg outline-none"/>
            <button type="submit" class="absolute top-0 right-0 px-4 py-2 text-white bg-green-600 rounded-r-lg hover:bg-green-700 font-semibold outline-none">
                Rechercher
            </button>
        </div>
    </form>


    @if (Route::has('login'))
        <div>
            @auth
                <a href="{{ route('gestuser.show', ['id' => Auth::user()->id]) }}" class="hidden md:flex bg-green-600 text-white rounded-lg hover:bg-green-700 transition-all duration-300 items-center gap-2 px-2 py-1">
                    <div class="flex items-center gap-4 px-2 py-1 transition-all duration-300">
                        <i class="fa-solid fa-circle-user text-2xl text-white"></i>
                        <span class="text-lg font-semibold"> {{ Auth::user()->prenom }} </span>
                    </div>
                </a>
            @else
                <a href="{{ route('login') }}" class="hidden md:flex bg-green-600 text-white rounded-lg hover:bg-green-700 transition-all duration-300 items-center gap-2 px-2 py-1">
                    <div class="flex items-center gap-4 px-2 py-1 transition-all duration-300">
                        <i class="fa-solid fa-circle-user text-2xl text-white"></i>
                        <span class="text-lg font-semibold"> Connexion</span>
                    </div>
                </a>
            @endauth
        </div>
    @endif
</div>
<script>
        // Fonction pour supprimer les accents d'une chaîne
        function removeAccents(str) {
            return str.normalize("NFD").replace(/[\u0300-\u036f]/g, "");
        }

        document.getElementById('searchInput').addEventListener('keyup', function() {
            const searchValue = removeAccents(this.value.toLowerCase());
            const rows = document.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                const text = removeAccents(row.textContent.toLowerCase());
                row.style.display = text.includes(searchValue) ? '' : 'none';
            });
        });
</script>