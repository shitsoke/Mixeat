<!DOCTYPE html>
<html lang="en">
    @php($orderingStatus = app(\App\Services\OrderingHours::class)->status())
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="description" content="MixEat lets you order delicious meals from your nearest branch with quick pickup and delivery options.">
        <title>@yield('title', 'MixEat | Good Food. Great Moments.')</title>
                <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800,900|montserrat:600,700,800,900" rel="stylesheet">
        @stack('styles')
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body
        class="min-h-screen bg-[#111111] font-sans text-white antialiased selection:bg-[#FBAB18] selection:text-[#111111]"
        data-ordering-status-url="{{ route('ordering.status') }}"
        data-ordering-open="{{ $orderingStatus['open'] ? 'true' : 'false' }}"
    >
        <x-navbar />

        <section id="ordering-status" class="border-b border-[#333333] bg-[#1A1A1A]" role="status" aria-live="polite">
            <div class="mixeat-shell flex flex-col gap-1 px-4 py-2 sm:flex-row sm:items-center sm:justify-between">
                <p id="ordering-status-label" class="text-xs font-bold {{ $orderingStatus['open'] ? 'text-[#FBAB18]' : 'text-[#FFC247]' }}">
                    {{ $orderingStatus['label'] }}
                </p>
                <p id="ordering-status-message" class="text-xs text-[#B8B8B8] {{ $orderingStatus['open'] ? 'hidden' : '' }}">
                    {{ $orderingStatus['message'] }}
                </p>
            </div>
        </section>

        <main>
            @yield('content')
        </main>

        <x-footer />
        @stack('scripts')
    </body>
</html>
