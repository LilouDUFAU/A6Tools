<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <script src="https://kit.fontawesome.com/40018cf627.js" crossorigin="anonymous"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-[Poppins]">
    <main class="grid grid-cols-12">
        <!-- Burger Menu -->
        <div class="md:hidden col-span-12 bg-green-600 text-white p-4">
            <button id="burger-menu" class="text-white">
            <i class="fa-solid fa-bars"></i> <span class="ml-2">Menu</span>
            </button>
            <div id="mobile-menu" class="hidden">
                @include('layouts.partials.aside')
            </div>
        </div>

        <!-- Aside pour md et lg -->
        <aside class="hidden md:block col-start-1 md:col-span-3 lg:col-span-2 border-r border-gray-300 bg-white">
            @include('layouts.partials.aside')
        </aside>

        <section class="col-span-12 md:col-start-4 md:col-span-9 lg:col-span-3 lg:col-span-10 bg-gray-100 bg-gray-100">
            <!-- header -->
            <header class="">
                @include('layouts.partials.header')
            </header>

            <!-- contenu -->
            @yield('content')
        </section>

        <!-- footer -->
        <footer class="bg-green-600 text-white text-center py-4 col-span-12 w-full">
            @include('layouts.partials.footer')
        </footer>
    </main>

    <script>
        document.getElementById('burger-menu').addEventListener('click', function () {
            const mobileMenu = document.getElementById('mobile-menu');
            mobileMenu.classList.toggle('hidden');
        });
    </script>
</body>
</html>
