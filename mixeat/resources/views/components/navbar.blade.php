@php
    $navItems = [
        ['label' => 'Home', 'route' => 'home'],
        ['label' => 'Rice Bowl', 'route' => 'menu', 'parameters' => ['category' => 'Food Bowls']],
        ['label' => 'Featured', 'route' => 'featured'],
        ['label' => 'Branches', 'route' => 'branches'],
    ];
@endphp
@php
    $isSupervisor = auth()->check() && method_exists(auth()->user(), 'isSupervisor') && auth()->user()->isSupervisor();
    $isMarketingAdmin = auth()->check() && (
        auth()->user()->role === 'marketing_admin' ||
        (method_exists(auth()->user(), 'isMarketing') && auth()->user()->isMarketing()) ||
        (auth()->user()->is_admin && auth()->user()->role !== 'customer')
    );
    $cartCount = collect(session('cart', []))->sum('quantity');
    $customerCateringUnread = auth()->check() && auth()->user()->role === 'customer'
        ? auth()->user()->unreadNotifications()->where('type', 'App\\Notifications\\CateringBookingNotification')->count()
        : 0;
    $showCateringCta = true;
    if (auth()->check()) {
        $u = auth()->user();
        if ((method_exists($u, 'isSupervisor') && $u->isSupervisor()) || (isset($u->role) && in_array($u->role, ['marketing_admin', 'marketing'], true))) {
            $showCateringCta = false;
        }
    }
@endphp

<header class="sticky top-0 z-50 border-b border-[#333333] bg-[#111111]/95 backdrop-blur-md">
    <div class="mixeat-shell flex items-center justify-between gap-4 py-3.5">
        <!-- Logo & Brand Name -->
        <a href="{{ route('home') }}" class="flex shrink-0 items-center gap-3 no-underline" aria-label="MixEat home">
            <img src="{{ asset('images/mixeat.jpg') }}" alt="MixEat" class="h-10 w-10 rounded-xl object-contain border border-[#333333]" />
            <div class="text-xl font-heading font-black tracking-[0.14em] text-white">MIXEAT</div>
        </a>

        <!-- Main Navigation Links -->
        @unless ($isMarketingAdmin || $isSupervisor)
            <nav class="hidden items-center gap-8 lg:flex lg:ml-auto lg:mr-6">
                @foreach ($navItems as $item)
                    <a href="{{ route($item['route'], $item['parameters'] ?? []) }}" class="text-sm font-semibold transition hover:text-[#FBAB18] {{ request()->routeIs($item['route']) ? 'text-[#FBAB18]' : 'text-[#B8B8B8]' }}">
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </nav>
        @endunless

        <!-- Right Side Actions & Admin Control Pills -->
        <div class="flex items-center gap-3">

            @auth
                @if (auth()->user()->role === 'customer')
                    <div class="relative" data-catering-notifications>
                        <button type="button" class="relative flex h-10 w-10 items-center justify-center rounded-full border border-[#333333] bg-[#1A1A1A] text-white hover:border-[#FBAB18] hover:text-[#FBAB18]" data-catering-notifications-toggle aria-label="Catering notifications">
                            <span aria-hidden="true">🔔</span>
                            <span data-catering-notifications-count class="{{ $customerCateringUnread ? '' : 'hidden' }} absolute -right-1 -top-1 min-w-4 rounded-full bg-[#FBAB18] px-1 text-center text-[10px] font-bold text-[#111111]">{{ $customerCateringUnread }}</span>
                        </button>
                        <div data-catering-notifications-list class="absolute right-0 top-12 z-50 hidden w-80 rounded-xl border border-[#333333] bg-[#201D19] p-3 shadow-xl">
                            <p class="text-sm font-bold text-white">Catering notifications</p>
                            <div data-catering-notifications-items class="mt-2 space-y-2 text-xs text-[#B8B8B8]"></div>
                        </div>
                    </div>
                @endif
            @endauth

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

            {{-- Prominent BOOK CATERING button --}}
            @php
                $showCateringCta = true;
                if (auth()->check()) {
                    $u = auth()->user();
                    if ($u->isSupervisor() || $u->role === 'marketing_admin' || $u->role === 'marketing' || $u->role === 'admin') {
                        if ($u->isSupervisor() || $u->role === 'marketing_admin' || $u->role === 'marketing') {
                            $showCateringCta = false;
                        }
                    }
                }
            @endphp
            @if (
                    auth()->guest() ||
                    (
                        !auth()->user()->isSupervisor() &&
                        !in_array(auth()->user()->role, ['marketing_admin', 'marketing'], true)
                    )
                )
                <a href="{{ route('catering') }}"
                    class="hidden sm:inline-flex items-center justify-center rounded-full bg-[#FBAB18] px-4 py-2 text-xs font-heading font-extrabold text-[#111111] transition hover:bg-[#FFC247]">
                    BOOK CATERING
                </a>
            @endif

            {{-- Admin Control Pills --}}
            @auth
                @if (auth()->user()->is_admin || auth()->user()->role === 'marketing')
                    <div class="hidden items-center gap-2 md:flex">
                        <a href="{{ route('admin.promotions.index') }}" class="rounded-full border border-[#333333] bg-[#1A1A1A] px-3 py-1.5 text-xs font-bold text-white transition hover:border-[#FBAB18] hover:text-[#FBAB18]">Promotions</a>
                        <a href="{{ route('admin.branches.index') }}" class="rounded-full border border-[#333333] bg-[#1A1A1A] px-3 py-1.5 text-xs font-bold text-white transition hover:border-[#FBAB18] hover:text-[#FBAB18]">Branches</a>
                        <a href="{{ route('admin.supervisors.manage') }}" class="rounded-full border border-[#333333] bg-[#1A1A1A] px-3 py-1.5 text-xs font-bold text-white transition hover:border-[#FBAB18] hover:text-[#FBAB18]">Supervisors</a>
                        <a href="{{ route('admin.products.index') }}" class="rounded-full border border-[#333333] bg-[#1A1A1A] px-3 py-1.5 text-xs font-bold text-white transition hover:border-[#FBAB18] hover:text-[#FBAB18]">Manage Rice Bowls</a>
                        <a href="{{ route('admin.catering-menu.index') }}" class="rounded-full border border-[#FBAB18] bg-[#1A1A1A] px-3 py-1.5 text-xs font-bold text-[#FBAB18] transition hover:bg-[#FBAB18] hover:text-[#111111]">Catering Menu</a>
                    </div>
                @elseif (auth()->user()->isSupervisor())
                    <div class="hidden items-center gap-2 md:flex">
                        <a href="{{ route('admin.supervisors.index') }}" class="rounded-full border border-[#FBAB18] bg-[#1A1A1A] px-3.5 py-1.5 text-xs font-bold text-[#FBAB18] transition hover:bg-[#FBAB18] hover:text-[#111111]">Dashboard</a>
                        <a href="{{ route('admin.supervisors.foods.index') }}" class="rounded-full border border-[#333333] bg-[#1A1A1A] px-3.5 py-1.5 text-xs font-bold text-white transition hover:border-[#FBAB18] hover:text-[#FBAB18]">Manage Foods</a>
                        <a href="{{ route('admin.supervisors.catering-bookings') }}" class="rounded-full border border-[#333333] bg-[#1A1A1A] px-3.5 py-1.5 text-xs font-bold text-white transition hover:border-[#FBAB18] hover:text-[#FBAB18]">Catering Management</a>
                        <a href="{{ route('admin.supervisors.catering-reports') }}" class="rounded-full border border-[#333333] bg-[#1A1A1A] px-3.5 py-1.5 text-xs font-bold text-white transition hover:border-[#FBAB18] hover:text-[#FBAB18]">Catering Reports</a>
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
    @unless ($isMarketingAdmin)
        <div id="mobile-navigation" class="hidden border-t border-[#333333] bg-[#111111] px-4 py-3 lg:hidden">
            <div class="mixeat-shell flex flex-wrap items-center gap-2 pb-1">
                @if ($isSupervisor)
                    <a href="{{ route('admin.supervisors.index') }}" class="whitespace-nowrap rounded-full px-3 py-1.5 text-xs font-semibold no-underline {{ request()->routeIs('admin.supervisors.index') ? 'bg-[#FBAB18] text-[#111111]' : 'text-[#B8B8B8] hover:text-white' }}" data-menu-link>
                        Branch Menu Availability
                    </a>
                @else
                    @foreach ($navItems as $item)
                        <a href="{{ route($item['route'], $item['parameters'] ?? []) }}" class="whitespace-nowrap rounded-full px-3 py-1.5 text-xs font-semibold no-underline {{ request()->routeIs($item['route']) ? 'bg-[#FBAB18] text-[#111111]' : 'text-[#B8B8B8] hover:text-white' }}" data-menu-link>
                            {{ $item['label'] }}
                        </a>
                    @endforeach
                @endif
                <div class="mt-2 w-full pt-2 border-t border-[#333333] flex items-center justify-between flex-wrap gap-2">
                    @if ($isSupervisor)
                        <span class="text-xs text-[#B8B8B8]">{{ auth()->user()->name }}</span>
                    @else
            @php
                $showCateringCta = true;
                if (auth()->check()) {
                    $u = auth()->user();
                    if (method_exists($u, 'isSupervisor') && $u->isSupervisor()) {
                        $showCateringCta = false;
                    } elseif (isset($u->role) && in_array($u->role, ['marketing_admin', 'marketing'], true)) {
                        $showCateringCta = false;
                    }
                }
            @endphp
            @if (!isset($showCateringCta) || $showCateringCta)
            {{-- Prominent BOOK CATERING button --}}
            <a href="{{ route('catering') }}" class="hidden sm:inline-flex items-center justify-center rounded-full bg-[#FBAB18] px-4 py-2 text-xs font-heading font-extrabold text-[#111111] transition hover:bg-[#FFC247]">
                BOOK CATERING
            </a>
            @endif
                    @endif
                    @auth
                        @if (!$isSupervisor)
                            <a href="{{ route('profile') }}" class="rounded-full border border-[#333333] bg-[#1A1A1A] px-4 py-2 text-xs font-bold text-white">Profile</a>
                        @endif
                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="rounded-full border border-[#333333] bg-[#1A1A1A] px-4 py-2 text-xs font-bold text-white">Logout</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="rounded-full border border-[#333333] bg-[#1A1A1A] px-4 py-2 text-xs font-bold text-white">Login</a>
                    @endauth
                </div>
            </div>
        </div>
    @endunless
</header>
@auth
    @if (auth()->user()->role === 'customer')
        <script>
            (() => {
                const root = document.querySelector('[data-catering-notifications]');
                if (!root) return;
                const toggle = root.querySelector('[data-catering-notifications-toggle]');
                const list = root.querySelector('[data-catering-notifications-list]');
                const items = root.querySelector('[data-catering-notifications-items]');
                const count = root.querySelector('[data-catering-notifications-count]');
                const endpoint = @json(route('customer.notifications.catering'));
                toggle.addEventListener('click', async () => {
                    list.classList.toggle('hidden');
                    if (list.classList.contains('hidden')) return;
                    const response = await fetch(endpoint, {headers: {'Accept': 'application/json'}});
                    if (!response.ok) return;
                    const payload = await response.json();
                    count.textContent = payload.count;
                    count.classList.toggle('hidden', payload.count < 1);
                    items.innerHTML = '';
                    payload.notifications.forEach((notification) => {
                        const link = document.createElement('a');
                        link.href = notification.data.booking_url;
                        link.className = 'block rounded-lg border border-[#333333] p-2 hover:border-[#FBAB18]';
                        link.textContent = notification.data.message;
                        link.addEventListener('click', () => fetch(@json(route('customer.notifications.catering.read', ['id' => '__id__'])).replace('__id__', notification.id), {method: 'POST', headers: {'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''}}));
                        items.appendChild(link);
                    });
                    if (!payload.notifications.length) items.textContent = 'No catering notifications.';
                });
            })();
        </script>
    @endif
@endauth
