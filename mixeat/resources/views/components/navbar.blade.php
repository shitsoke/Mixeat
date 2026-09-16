@php
    $navItems = [
        ['label' => 'Home', 'route' => 'home'],
        ['label' => 'Menu', 'route' => 'menu'],
        ['label' => 'Featured', 'route' => 'featured'],
        ['label' => 'Branches', 'route' => 'branches'],
        ['label' => 'About', 'route' => 'about'],
        ['label' => 'Contact', 'route' => 'contact'],
    ];
@endphp
@php($cartCount = collect(session('cart', []))->sum('quantity'))

<header class="sticky top-0 z-50 border-b border-[#E8DFAF] bg-white/90 backdrop-blur-md">
    <div class="mixeat-shell flex items-center justify-between gap-4 py-3">
        <a href="{{ route('home') }}" class="flex items-center gap-3" aria-label="MixEat home">
            <img src="{{ asset('images/mixeat.jpg') }}" alt="MixEat" class="h-12 w-12 rounded-xl object-contain" />
            <div class="text-xl font-extrabold tracking-[0.16em] text-[#090909]">MIXEAT</div>
        </a>

        <nav class="hidden items-center gap-7 lg:flex">
            @foreach ($navItems as $item)
                <a href="{{ route($item['route']) }}" class="text-sm font-semibold text-[#555555] transition hover:text-[#FFC60A] {{ request()->routeIs($item['route']) ? 'text-[#FFC60A]' : '' }}">
                    {{ $item['label'] }}
                </a>
            @endforeach
        </nav>

        <div class="flex items-center gap-3">
            <button type="button" class="hidden h-11 w-11 items-center justify-center rounded-full border border-[#E8DFAF] bg-[#FFF9E6] text-[#090909] transition hover:border-[#FFC60A] hover:text-[#FFC60A] sm:flex" aria-label="Search">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 21l-4.35-4.35m1.85-5.15a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </button>

            <a href="{{ route('cart') }}" class="relative flex h-11 w-11 items-center justify-center rounded-full border border-[#E8DFAF] bg-[#FFF9E6] text-[#090909] transition hover:border-[#FFC60A] hover:text-[#FFC60A]" aria-label="Cart">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.25 3h1.5l2.3 9.18a1.5 1.5 0 001.46 1.2h8.98a1.5 1.5 0 001.44-1.12L19.5 6H6.12" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 19.25a1.25 1.25 0 100 2.5 1.25 1.25 0 000-2.5zm7 0a1.25 1.25 0 100 2.5 1.25 1.25 0 000-2.5z" />
                </svg>
                @if ($cartCount > 0)
                    <span class="absolute -right-1 -top-1 flex h-5 min-w-5 items-center justify-center rounded-full bg-[#FFC60A] px-1 text-[10px] font-bold text-[#090909]">{{ $cartCount }}</span>
                @endif
            </a>

            @auth
                @if (auth()->user()->is_admin)
                    <a href="{{ route('admin.promotion') }}" class="hidden items-center gap-2 rounded-full border border-[#FFC60A] px-4 py-2.5 text-sm font-semibold text-[#090909] transition hover:bg-[#FFF4BF] md:inline-flex">
                        Promotion
                    </a>
                    <a href="{{ route('admin.branches.index') }}" class="hidden items-center gap-2 rounded-full border border-[#FFC60A] px-4 py-2.5 text-sm font-semibold text-[#090909] transition hover:bg-[#FFF4BF] md:inline-flex">
                        Branches
                    </a>
                    <a href="{{ route('admin.products.index') }}" class="hidden items-center gap-2 rounded-full border border-[#FFC60A] px-4 py-2.5 text-sm font-semibold text-[#090909] transition hover:bg-[#FFF4BF] md:inline-flex">
                        Admin
                    </a>
                @endif
                <a href="{{ route('profile') }}" class="hidden items-center gap-2 rounded-full bg-[#FFC60A] px-4 py-2.5 text-sm font-semibold text-[#090909] shadow-lg shadow-[#E5A900]/25 transition hover:bg-[#E5A900] md:inline-flex">
                    My Profile
                </a>
            @else
                <a href="{{ route('login') }}" class="hidden items-center gap-2 rounded-full bg-[#FFC60A] px-4 py-2.5 text-sm font-semibold text-[#090909] shadow-lg shadow-[#E5A900]/25 transition hover:bg-[#E5A900] md:inline-flex">
                    Login
                </a>
            @endauth

            <button type="button" class="inline-flex h-11 w-11 items-center justify-center rounded-full border border-[#E8DFAF] bg-white text-[#090909] lg:hidden" aria-label="Open menu" aria-expanded="false" aria-controls="mobile-navigation" data-menu-toggle>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 7h16M4 12h16M4 17h16" />
                </svg>
            </button>
        </div>
    </div>

    <div id="mobile-navigation" class="hidden border-t border-[#E8DFAF] bg-white px-4 py-3 lg:hidden">
        <div class="mixeat-shell flex items-center justify-between gap-2 overflow-x-auto pb-1">
            @foreach ($navItems as $item)
                <a href="{{ route($item['route']) }}" class="whitespace-nowrap rounded-full px-3 py-2 text-sm font-semibold {{ request()->routeIs($item['route']) ? 'bg-[#FFF4BF] text-[#FFC60A]' : 'text-[#555555]' }}" data-menu-link>
                    {{ $item['label'] }}
                </a>
            @endforeach
        </div>
    </div>
</header>


