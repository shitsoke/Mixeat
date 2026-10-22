@extends('layouts.app')

@section('title', 'MixEat | Good Food. Great Moments.')

@section('content')
    <section class="relative overflow-hidden bg-[#111111] py-12 md:py-20">
        <div class="absolute -top-24 left-1/2 h-72 w-72 -translate-x-1/2 rounded-full bg-[#FBAB18]/20 blur-3xl"></div>
        <div class="mixeat-shell grid items-center gap-10 px-4 md:grid-cols-2">
            <div>
                <span class="inline-flex rounded-full border border-[#333333] bg-[#1A1A1A] px-4 py-2 text-xs font-bold uppercase tracking-[0.18em] text-[#FBAB18]">WE CATER, WE SERVE, WE CELEBRATE.</span>
                <h1 class="mt-6 text-5xl font-black leading-[1.1] text-white md:text-6xl lg:text-6xl">
                    <span class="block">Good Food.</span>
                    <span class="block">Great Moments.</span>
                    <span class="block text-[#FBAB18]">MixEat.</span>
                </h1>
                <p class="mt-5 max-w-xl text-lg leading-8 text-[#B8B8B8]">
                    Order online or book catering for your next office event or celebration!
                </p>
                <div class="mt-8 flex flex-col gap-4 sm:flex-row">
                    <a href="{{ route('menu') }}" class="btn-primary">ORDER ONLINE</a>
                    <a href="{{ route('catering') }}" class="btn-secondary">BOOK CATERING</a>
                </div>
            </div>

            <div class="relative" data-home-promotion-carousel aria-label="Current promotions">
                <div class="absolute -left-7 top-10 h-28 w-28 rounded-full bg-[#FBAB18]/20 blur-2xl"></div>
                <div class="absolute -right-4 bottom-10 h-32 w-32 rounded-full bg-[#FBAB18]/20 blur-2xl"></div>

                @php($slides = count($homePromotions) ? $homePromotions : [[...$promotion, 'details_url' => route('featured')]])
                <div class="relative">
                    @foreach ($slides as $slide)
                        <article data-promotion-slide class="{{ $loop->first ? '' : 'hidden' }} relative overflow-hidden rounded-[32px] border border-[#333333] bg-[#201D19] p-4 shadow-[0_20px_70px_rgba(229,169,0,0.18)]" aria-hidden="{{ $loop->first ? 'false' : 'true' }}">
                            <a href="{{ $slide['details_url'] }}" class="block overflow-hidden rounded-[24px] focus-visible:outline focus-visible:outline-4 focus-visible:outline-offset-2 focus-visible:outline-[#A87900]" aria-label="View details for {{ $slide['title'] }}">
                                <img src="{{ $slide['image'] }}" alt="{{ $slide['title'] }}" class="max-h-[520px] w-full rounded-[24px] object-cover transition duration-300 hover:scale-[1.02]">
                            </a>
                        </article>
                    @endforeach

                    @if (count($slides) > 1)
                        <div class="pointer-events-none absolute inset-x-4 bottom-8 flex items-center justify-between gap-4">
                            <div class="pointer-events-auto flex gap-2 rounded-full bg-black/45 px-3 py-2" role="group" aria-label="Choose promotion">
                                @foreach ($slides as $slide)
                                    <button type="button" data-promotion-dot aria-label="Show promotion {{ $loop->iteration }}" aria-current="{{ $loop->first ? 'true' : 'false' }}" class="h-2.5 w-2.5 rounded-full transition {{ $loop->first ? 'bg-white' : 'bg-white/50' }}"></button>
                                @endforeach
                            </div>
                            <div class="pointer-events-auto flex gap-2">
                                <button type="button" data-promotion-previous aria-label="Previous promotion" class="flex h-10 w-10 items-center justify-center rounded-full bg-black/45 text-lg font-bold text-white transition hover:bg-black/65">&larr;</button>
                                <button type="button" data-promotion-next aria-label="Next promotion" class="flex h-10 w-10 items-center justify-center rounded-full bg-black/45 text-lg font-bold text-white transition hover:bg-black/65">&rarr;</button>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <section class="py-12" aria-labelledby="home-order-tracker-title">
        <div class="mixeat-shell px-4">
            <div class="rounded-[30px] border border-[#333333] bg-[#201D19] p-6 shadow-[0_14px_40px_rgba(229,169,0,0.10)] md:p-8">
                <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
                    <div>
                        <p class="text-sm font-bold uppercase tracking-[0.18em] text-[#FBAB18]">Order tracker</p>
                        <h2 id="home-order-tracker-title" class="mt-2 text-3xl font-black text-[#FFFFFF]">Track your orders</h2>
                        <p class="mt-2 text-sm text-[#B8B8B8]">Order statuses refresh automatically while you’re on this page.</p>
                    </div>
                    @auth
                        <a href="{{ route('orders') }}" class="text-sm font-semibold text-[#FBAB18]">View all orders</a>
                    @else
                        <a href="{{ route('login') }}" class="rounded-full bg-[#FBAB18] px-5 py-3 text-sm font-semibold text-[#FFFFFF]">Log in to track orders</a>
                    @endauth
                </div>

                @auth
                    <div id="home-order-tracker" class="mt-6 space-y-4" data-live-url="{{ route('orders.live') }}">
                        @forelse ($trackedOrders as $order)
                            <a href="{{ route('order-detail', (int) str_replace('MX-', '', $order->order_number)) }}" class="block rounded-2xl border border-[#333333] bg-[#1A1A1A] p-5 transition hover:border-[#FBAB18]">
                                <div class="flex flex-wrap items-center justify-between gap-3">
                                    <span class="font-bold text-[#FFFFFF]">{{ $order->order_number }} · {{ $order->branch?->name ?? 'MixEat' }}</span>
                                    <span class="rounded-full bg-[#201D19] px-3 py-1 text-sm font-bold text-[#FFFFFF]">{{ $order->status }}</span>
                                </div>
                                <p class="mt-2 text-sm text-[#B8B8B8]">{{ $order->type }} · &#8369;{{ number_format($order->total, 2) }}</p>
                            </a>
                        @empty
                            <p class="rounded-2xl bg-[#1A1A1A] p-5 text-sm text-[#B8B8B8]">Your orders will appear here after checkout.</p>
                        @endforelse
                    </div>
                    <p id="home-order-tracker-status" class="mt-3 text-xs text-[#B8B8B8]" role="status">Live updates enabled.</p>
                @else
                    <p class="mt-6 rounded-2xl bg-[#1A1A1A] p-5 text-sm text-[#B8B8B8]">Log in to securely see and track your orders here.</p>
                @endauth
            </div>
        </div>
    </section>

    <section class="border-y border-[#333333] bg-[#111111] py-10">
        <div class="mixeat-shell px-4">
            <div class="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
                <div>
                    <p class="text-sm font-bold uppercase tracking-[0.18em] text-[#FBAB18]">Order from your nearest branch</p>
                    <h2 class="mt-2 text-3xl font-black text-[#FFFFFF]">Choose your branch</h2>
                </div>
                <a href="{{ route('branches') }}" class="text-sm font-semibold text-[#FBAB18]">View all branches</a>
            </div>

            <form action="{{ route('branches.select-dropdown') }}" method="POST" class="flex flex-col gap-4 rounded-[24px] border border-[#333333] bg-[#1A1A1A] p-5 sm:flex-row sm:items-end">
                @csrf
                <label class="grid flex-1 gap-2 text-sm font-bold text-[#FFFFFF]" for="home-branch">Your branch
                    <select id="home-branch" name="branch" class="min-w-0 w-full rounded-full border border-[#333333] bg-[#201D19] px-5 py-3 text-base font-semibold text-[#FFFFFF] focus:border-[#FBAB18] focus:outline-none focus:ring-2 focus:ring-[#FBAB18]/30">
                        @foreach ($branches as $index => $branch)
                            <option value="{{ $index }}" @selected($selectedBranch === $index)>{{ $branch['name'] }} - {{ $branch['address'] }}</option>
                        @endforeach
                    </select>
                </label>
                <button type="submit" class="rounded-full bg-[#FBAB18] px-6 py-3 font-semibold text-[#FFFFFF] shadow-lg shadow-[#FBAB18]/25 transition hover:bg-[#FBAB18]">Select Branch</button>
            </form>
        </div>
    </section>

    <section class="py-16 md:py-20">
        <div class="mixeat-shell px-4">
            <div class="mb-8 flex items-center justify-between gap-4">
                <div>
                    <p class="text-sm font-bold uppercase tracking-[0.18em] text-[#FBAB18]">Featured Products</p>
                    <h2 class="mt-2 text-3xl font-black text-[#FFFFFF]">Try our customers' favorites.</h2>
                </div>
                <a href="{{ route('menu') }}" class="hidden rounded-full border border-[#FBAB18] px-5 py-3 text-sm font-semibold text-[#FBAB18] transition hover:bg-[#201D19] md:inline-flex">Browse All</a>
            </div>

            <div class="mb-10 overflow-x-auto pb-2 md:overflow-visible">
                <div class="flex min-w-max gap-3 md:flex-wrap md:justify-start">
                    @foreach($categories as $category)
                        <a href="{{ route('menu', ['category' => $category]) }}" class="rounded-full border border-[#333333] bg-[#201D19] px-4 py-2 text-sm font-semibold text-white border border-[#333333] transition hover:border-[#FBAB18] hover:bg-[#1A1A1A] hover:text-[#FBAB18] {{ $loop->first ? 'border-[#FBAB18] bg-[#FBAB18] text-[#111111]' : '' }}">
                            {{ $category }}
                        </a>
                    @endforeach
                </div>
            </div>

            <div class="product-grid">
                @foreach ($featuredProducts as $product)
                    <x-product-card :product="$product" />
                @endforeach
            </div>
        </div>
    </section>
@endsection

@auth
    @push('scripts')
    <script>
        (() => {
            const tracker = document.getElementById('home-order-tracker');
            const indicator = document.getElementById('home-order-tracker-status');
            if (!tracker || !indicator) return;

            let polling = false;
            const refreshOrders = async () => {
                if (polling || document.hidden) return;
                polling = true;

                try {
                    const response = await fetch(tracker.dataset.liveUrl, {
                        headers: { Accept: 'application/json' },
                        credentials: 'same-origin',
                        cache: 'no-store',
                    });
                    if (!response.ok) throw new Error(`Order tracker request failed (${response.status})`);

                    const { orders } = await response.json();
                    tracker.replaceChildren();

                    if (!orders.length) {
                        const empty = document.createElement('p');
                        empty.className = 'rounded-2xl bg-[#1A1A1A] p-5 text-sm text-[#B8B8B8]';
                        empty.textContent = 'Your orders will appear here after checkout.';
                        tracker.append(empty);
                    } else {
                        orders.forEach((order) => {
                            const link = document.createElement('a');
                            link.href = order.url;
                            link.className = 'block rounded-2xl border border-[#333333] bg-[#1A1A1A] p-5 transition hover:border-[#FBAB18]';

                            const header = document.createElement('div');
                            header.className = 'flex flex-wrap items-center justify-between gap-3';
                            const title = document.createElement('span');
                            title.className = 'font-bold text-[#FFFFFF]';
                            title.textContent = `${order.id} · ${order.branch}`;
                            const status = document.createElement('span');
                            status.className = 'rounded-full bg-[#201D19] px-3 py-1 text-sm font-bold text-[#FFFFFF]';
                            status.textContent = order.status;
                            header.append(title, status);

                            const details = document.createElement('p');
                            details.className = 'mt-2 text-sm text-[#B8B8B8]';
                            const formattedTotal = new Intl.NumberFormat('en-PH', {
                                style: 'currency',
                                currency: 'PHP',
                            }).format(Number(order.total));
                            details.textContent = `${order.type}${order.pickup_at ? ` · Pickup ${order.pickup_at}` : ''} · ${formattedTotal}`;
                            link.append(header, details);
                            tracker.append(link);
                        });
                    }

                    indicator.textContent = 'Live updates enabled.';
                } catch (error) {
                    console.error('Unable to refresh customer orders.', error);
                    indicator.textContent = 'Live updates temporarily unavailable. Retrying automatically.';
                } finally {
                    polling = false;
                }
            };

            refreshOrders();
            window.setInterval(refreshOrders, 3000);
        })();
    </script>
    @endpush
@endauth





