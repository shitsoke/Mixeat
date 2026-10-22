@php
    $navItems = [
        ['label' => 'Home', 'route' => 'home'],
        ['label' => 'Rice Bowl', 'route' => 'menu', 'parameters' => ['category' => 'Food Bowls']],
        ['label' => 'Featured', 'route' => 'featured'],
        ['label' => 'Branches', 'route' => 'branches'],
        ['label' => 'Catering', 'route' => 'catering'],
        ['label' => 'Feedback', 'route' => 'feedback.index'],
        ['label' => 'About', 'route' => 'about'],
        ['label' => 'Contact', 'route' => 'contact'],
        ['label' => 'Careers', 'route' => 'careers'],
    ];
@endphp
@php($isSupervisor = auth()->check() && auth()->user()->isSupervisor())
@php($isMarketingAdmin = auth()->check() && (auth()->user()->role === 'marketing_admin' || auth()->user()->isMarketing() || auth()->user()->is_admin))
@php($cartCount = collect(session('cart', []))->sum('quantity'))
@php($cartCount = collect(session('cart', []))->sum('quantity'))

<header class="sticky top-0 z-50 border-b border-[#333333] bg-[#111111]/95 backdrop-blur-md">
    <div class="mixeat-shell flex items-center justify-between gap-4 py-3.5">
        <!-- Logo & Brand Name -->
        <a href="{{ route('home') }}" class="flex shrink-0 items-center gap-3 no-underline" aria-label="MixEat home">
            <img src="{{ asset('images/mixeat.jpg') }}" alt="MixEat" class="h-10 w-10 rounded-xl object-contain border border-[#333333]" />
            <div class="text-xl font-heading font-black tracking-[0.14em] text-white">MIXEAT</div>
        </a>

        <!-- Main Navigation Links -->
        @unless ($isMarketingAdmin || $isSupervisor)
            <nav class="hidden items-center gap-6 lg:flex">
                @foreach ($navItems as $item)
                    <a href="{{ route($item['route'], $item['parameters'] ?? []) }}" class="text-sm font-semibold transition hover:text-[#FBAB18] {{ request()->routeIs($item['route']) ? 'text-[#FBAB18]' : 'text-[#B8B8B8]' }}">
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </nav>
        @endunless

        <!-- Right Side Actions & Admin Control Pills -->
        <div class="flex items-center gap-3">

            {{-- Cart Icon (Hidden for Marketing/Admin accounts) --}}
            @if (!auth()->check() || (!auth()->user()->is_admin && auth()->user()->role !== 'marketing' && !auth()->user()->isSupervisor()))
                <a href="{{ route('cart') }}" class="relative flex h-10 w-10 items-center justify-center rounded-full border border-[#333333] bg-[#1A1A1A] text-white transition hover:border-[#FBAB18] hover:text-[#FBAB18]" aria-label="Cart">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.25 3h1.5l2.3 9.18a1.5 1.5 0 001.46 1.2h8.98a1.5 1.5 0 001.44-1.12L19.5 6H6.12" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 19.25a1.25 1.25 0 100 2.5 1.25 1.25 0 000-2.5zm7 0a1.25 1.25 0 100 2.5 1.25 1.25 0 000-2.5z" />
                    </svg>
                    @if ($cartCount > 0)
                        <span class="absolute -right-1 -top-1 flex h-4 min-w-4 items-center justify-center rounded-full bg-[#FBAB18] px-1 text-[10px] font-bold text-[#111111]">{{ $cartCount }}</span>
                    @endif
                </a>
            @endif

            @unless ($isSupervisor)
            {{-- Prominent BOOK CATERING button --}}
            <a href="{{ route('catering') }}" class="hidden sm:inline-flex items-center justify-center rounded-full bg-[#FBAB18] px-4 py-2 text-xs font-heading font-extrabold text-[#111111] transition hover:bg-[#FFC247]">
                BOOK CATERING
            </a>
            @endunless

            {{-- Admin Control Pills --}}
            @auth
                @if (auth()->user()->role === 'marketing_admin')
                    <div class="hidden items-center gap-2 md:flex">
                        <a href="{{ route('admin.marketing.catering-bookings') }}" class="rounded-full border border-[#333333] bg-[#1A1A1A] px-3.5 py-1.5 text-xs font-bold text-white no-underline transition hover:border-[#FBAB18] hover:text-[#FBAB18]">Catering Bookings</a>
                        <a href="{{ route('admin.marketing.catering-partners') }}" class="rounded-full border border-[#333333] bg-[#1A1A1A] px-3.5 py-1.5 text-xs font-bold text-white no-underline transition hover:border-[#FBAB18] hover:text-[#FBAB18]">Catering Partners</a>
                    </div>
                @elseif (auth()->user()->is_admin || auth()->user()->role === 'marketing')
                    <div class="hidden items-center gap-2 md:flex">
                        <a href="{{ route('admin.promotions.index') }}" class="rounded-full border border-[#333333] bg-[#1A1A1A] px-3 py-1.5 text-xs font-bold text-white transition hover:border-[#FBAB18] hover:text-[#FBAB18]">Promotions</a>
                        <a href="{{ route('admin.branches.index') }}" class="rounded-full border border-[#333333] bg-[#1A1A1A] px-3 py-1.5 text-xs font-bold text-white transition hover:border-[#FBAB18] hover:text-[#FBAB18]">Branches</a>
                        <a href="{{ route('admin.catering.marketing') }}" class="rounded-full border border-[#333333] bg-[#1A1A1A] px-3 py-1.5 text-xs font-bold text-white transition hover:border-[#FBAB18] hover:text-[#FBAB18]">Catering</a>
                        <a href="{{ route('admin.supervisors.manage') }}" class="rounded-full border border-[#333333] bg-[#1A1A1A] px-3 py-1.5 text-xs font-bold text-white transition hover:border-[#FBAB18] hover:text-[#FBAB18]">Supervisors</a>
                        <a href="{{ route('admin.products.index') }}" class="rounded-full border border-[#333333] bg-[#1A1A1A] px-3 py-1.5 text-xs font-bold text-white transition hover:border-[#FBAB18] hover:text-[#FBAB18]">Admin</a>
                    </div>
                @elseif (auth()->user()->isSupervisor())
                    <div class="hidden items-center gap-2 md:flex">
                        <a href="{{ route('admin.supervisors.index') }}" class="rounded-full border border-[#FBAB18] bg-[#1A1A1A] px-3.5 py-1.5 text-xs font-bold text-[#FBAB18] transition hover:bg-[#FBAB18] hover:text-[#111111]">Dashboard</a>
                    </div>
                @endif

                <a href="{{ route('profile') }}" class="hidden items-center gap-1.5 rounded-full border border-[#333333] bg-[#1A1A1A] px-4 py-2 text-xs font-bold text-white transition hover:border-[#FBAB18] hover:text-[#FBAB18] md:inline-flex">
                    Profile
                </a>
            @else
                <a href="{{ route('login') }}" class="hidden items-center gap-1.5 rounded-full border border-[#333333] bg-[#1A1A1A] px-4 py-2 text-xs font-bold text-white transition hover:border-[#FBAB18] hover:text-[#FBAB18] md:inline-flex">
                    Login
                </a>
            @endauth

            <!-- Mobile Hamburger Menu Button -->
            <button type="button" class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-[#333333] bg-[#1A1A1A] text-white lg:hidden" aria-label="Open menu" aria-expanded="false" aria-controls="mobile-navigation" data-menu-toggle>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 7h16M4 12h16M4 17h16" />
                </svg>
            </button>
        </div>
    </div>

    <!-- Mobile Drawer / Secondary Nav -->
    @unless ($isMarketingAdmin || $isSupervisor)
        <div id="mobile-navigation" class="hidden border-t border-[#333333] bg-[#111111] px-4 py-3 lg:hidden">
            <div class="mixeat-shell flex flex-wrap items-center gap-2 pb-1">
                @foreach ($navItems as $item)
                    <a href="{{ route($item['route'], $item['parameters'] ?? []) }}" class="whitespace-nowrap rounded-full px-3 py-1.5 text-xs font-semibold no-underline {{ request()->routeIs($item['route']) ? 'bg-[#FBAB18] text-[#111111]' : 'text-[#B8B8B8] hover:text-white' }}" data-menu-link>
                        {{ $item['label'] }}
                    </a>
                @endforeach
                <div class="mt-2 w-full pt-2 border-t border-[#333333] flex items-center justify-between">
                    <a href="{{ route('catering') }}" class="rounded-full bg-[#FBAB18] px-4 py-2 text-xs font-heading font-extrabold text-[#111111]">BOOK CATERING</a>
                    @auth
                        <a href="{{ route('profile') }}" class="rounded-full border border-[#333333] bg-[#1A1A1A] px-4 py-2 text-xs font-bold text-white">Profile</a>
                    @else
                        <a href="{{ route('login') }}" class="rounded-full border border-[#333333] bg-[#1A1A1A] px-4 py-2 text-xs font-bold text-white">Login</a>
                    @endauth
                </div>
            </div>
        </div>
    @endunless
</header>






