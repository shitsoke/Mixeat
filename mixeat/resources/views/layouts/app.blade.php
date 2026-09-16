<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="MixEat lets you order delicious meals from your nearest branch with quick pickup and delivery options.">
        <title>@yield('title', 'MixEat | Good Food. Great Moments.')</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-[#FFF9E6] text-[#090909] antialiased">
        <x-navbar />

        <main>
            @yield('content')
        </main>

        <x-footer />
    </body>
</html>


