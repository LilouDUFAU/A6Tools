<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('images/logo.ico') }}" type="image/png">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <script src="https://kit.fontawesome.com/40018cf627.js" crossorigin="anonymous"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-[Poppins] h-screen overflow-hidden">
    <main class="grid grid-cols-12 h-full">
        <!-- Burger Menu (mobile) -->
        <div class="md:hidden col-span-12 bg-green-600 text-white p-4">
            <button id="burger-menu" class="text-white">
                <i class="fa-solid fa-bars"></i> <span class="ml-2">Menu</span>
            </button>
            <div id="mobile-menu" class="hidden">
                @include('layouts.partials.aside')
            </div>
        </div>

        <!-- Aside pour md et lg -->
        <aside class="hidden md:block col-start-1 md:col-span-3 lg:col-span-2 bg-white h-full sticky top-0 overflow-y-auto border-r border-gray-300">
            @include('layouts.partials.aside')
        </aside>

        <!-- Section principale -->
        <section class="col-span-12 md:col-span-9 lg:col-span-10 bg-gray-100 h-full overflow-y-auto flex flex-col min-h-screen">
            <!-- Header -->
            <header>
                @include('layouts.partials.header')
            </header>

            <!-- Contenu principal -->
            <div class="flex-1">
                @yield('content')
            </div>


        </section>
    </main>

    <script>
        document.getElementById('burger-menu').addEventListener('click', function () {
            const mobileMenu = document.getElementById('mobile-menu');
            mobileMenu.classList.toggle('hidden');
        });
    </script>

    @if(Session::has('download.in.the.next.request'))
        <script>
            var link = document.createElement('a');
            link.href = "data:application/pdf;base64,{{ Session::get('download.in.the.next.request')['url'] }}";
            link.download = "{{ Session::get('download.in.the.next.request')['name'] }}";
            link.dispatchEvent(new MouseEvent('click'));
        </script>
    @endif
</body>
</html>
