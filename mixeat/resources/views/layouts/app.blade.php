<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="MixEat lets you order delicious meals from your nearest branch with quick pickup and delivery options.">
        <title>@yield('title', 'MixEat | Good Food. Great Moments.')</title>
                <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800,900|montserrat:600,700,800,900" rel="stylesheet">
        @stack('styles')
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-[#111111] font-sans text-white antialiased selection:bg-[#FBAB18] selection:text-[#111111]">
        <x-navbar />

        <main>
            @yield('content')
        </main>

        <x-footer />
        @stack('scripts')
    </body>
</html>
