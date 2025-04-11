<div class="flex items-center justify-between p-4">
    <form action="/search" method="GET" class="w-full max-w-md">
        <div class="relative">
            <input type="text" name="query" placeholder="Rechercher..." class="w-full px-4 py-2 border border-gray-300 rounded-lg outline-none"/>
            <button type="submit" class="absolute top-0 right-0 px-4 py-2 text-white bg-green-600 rounded-r-lg hover:bg-green-700 outline-none">
                Rechercher
            </button>
        </div>
    </form>

    <a href="/login" class="text-gray-700 hover:text-green-600">
        <div class="flex items-center gap-4 p-4">
            <p class="hidden md:block">Connexion</p>
            <i class="fa-solid fa-circle-user text-2xl"></i>
        </div>
    </a>
</div>