@extends('layouts.app')

@section('title', 'My Orders | MixEat')

@section('content')
<section class="customer-orders py-10 md:py-16">
    <div class="mixeat-shell px-4">
        <!-- Header -->
        <div class="mb-8 text-center md:text-left">
            <p class="text-xs font-bold uppercase tracking-[0.18em] text-[#FBAB18]">Customer Dashboard</p>
            <h1 class="mt-1 text-3xl font-black text-[#FFFFFF] md:text-4xl">My Orders & Activity</h1>
            <p class="mt-1 text-sm font-medium text-[#B8B8B8]">Track active meal preparation and view past purchase history.</p>
        </div>

        <!-- 1. ACTIVE ORDER ACTIVITY -->
        <div class="mb-12">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-xl font-bold text-[#FFFFFF]">Active Order Activity</h2>
                <span class="rounded-full bg-[#FBAB18] px-3 py-1 text-xs font-bold text-[#111111]">Live Updates</span>
            </div>

            <div id="customer-active-orders" data-live-url="{{ route('orders.dashboard.live') }}">
            @if($activeOrders->count() > 0)
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    @foreach($activeOrders as $order)
                        <div class="rounded-[28px] border border-[#333333] bg-[#201D19] p-6 shadow-sm">
                            <div class="flex items-center justify-between gap-3 border-b border-[#333333] pb-4">
                                <div class="min-w-0">
                                    <span class="block text-xs font-bold text-[#B8B8B8]">{{ $order->created_at ? $order->created_at->format('M d, Y • h:i A') : 'Just Now' }}</span>
                                    <h3 class="text-lg font-black break-words text-[#FFFFFF]">{{ $order->order_number }}</h3>
                                </div>
                                <div class="shrink-0">
                                    @if($order->status === 'Pending')
                                        <span class="inline-block rounded-full bg-[#FFF3CD] px-3 py-1 text-xs font-bold text-[#856404]">Pending Acceptance</span>
                                    @elseif($order->status === 'Preparing')
                                        <span class="inline-block rounded-full bg-[#E6F4EA] px-3 py-1 text-xs font-bold text-[#1E8E3E]">Kitchen is Preparing</span>
                                    @elseif($order->status === 'Ready for Pickup')
                                        <span class="inline-block rounded-full bg-[#FBAB18] px-3 py-1 text-xs font-bold text-[#111111]">Ready for Pickup</span>
                                    @endif
                                </div>
                            </div>

                            <div class="py-4">
                                <p class="text-xs font-bold uppercase text-[#B8B8B8]">Branch Location</p>
                                <p class="text-sm font-semibold break-words text-[#FFFFFF]">{{ $order->branch?->name ?? 'MixEat Branch' }}</p>
                                <p class="mt-2 text-sm font-semibold text-[#E5E5E5]">{{ $order->type }}</p>
                                @if ($order->pickup_at)
                                    <p class="mt-1 text-sm text-[#B8B8B8]">Pickup: {{ $order->pickup_at->format('M j, Y g:i A') }}</p>
                                @endif
                                @if ($order->delivery_method)
                                    <p class="mt-1 text-sm text-[#B8B8B8]">Delivery: {{ $order->delivery_method }}</p>
                                    <p class="mt-1 text-sm break-words text-[#E5E5E5]">{{ $order->delivery_address }}</p>
                                @endif
                                @if ($order->payment_method)
                                    <p class="mt-1 text-sm break-words text-[#B8B8B8]">Payment: {{ $order->payment_method }}</p>
                                @endif
                                @if ($order->gcash_reference_number)
                                    <p class="mt-1 text-sm break-all text-[#B8B8B8]">GCash reference: {{ $order->gcash_reference_number }}</p>
                                @endif

                                <div class="mt-4 flex items-center justify-between gap-3">
                                    <span class="text-xs font-bold uppercase text-[#B8B8B8]">Total Amount</span>
                                    <span class="text-lg font-bold text-[#FBAB18]">&#8369;{{ number_format($order->total, 2) }}</span>
                                </div>
                            </div>

                            <!-- Progress Indicator -->
                            <div class="mt-2 rounded-2xl bg-[#1A1A1A] p-4">
                                <p class="mb-2 text-xs font-bold text-[#FFFFFF]">Order Progress</p>
                                <div class="flex gap-2">
                                    <div class="h-2 flex-1 rounded-full {{ in_array($order->status, ['Pending', 'Preparing', 'Ready for Pickup']) ? 'bg-[#FBAB18]' : 'bg-[#333333]' }}"></div>
                                    <div class="h-2 flex-1 rounded-full {{ in_array($order->status, ['Preparing', 'Ready for Pickup']) ? 'bg-[#FBAB18]' : 'bg-[#333333]' }}"></div>
                                    <div class="h-2 flex-1 rounded-full {{ $order->status === 'Ready for Pickup' ? 'bg-[#FBAB18]' : 'bg-[#333333]' }}"></div>
                                </div>
                                <div class="mt-2 text-center text-xs font-bold text-[#E5E5E5]">
                                    @if($order->status === 'Pending')
                                        Waiting for branch supervisor to confirm order...
                                    @elseif($order->status === 'Preparing')
                                        Your meal is currently being cooked!
                                    @elseif($order->status === 'Ready for Pickup')
                                        Your order is ready! Please proceed to the counter for pickup.
                                    @endif
                                </div>
                            </div>

                            <div class="mt-4 text-right">
                                <a href="{{ route('order-detail', ['id' => (int) str_replace('MX-', '', $order->order_number)]) }}" class="inline-flex items-center justify-center rounded-full bg-[#FBAB18] px-5 py-2.5 text-xs font-bold text-[#111111] transition hover:bg-[#FFC247]">
                                    View Details
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="rounded-[28px] border border-[#333333] bg-[#1A1A1A] p-8 text-center">
                    <p class="text-sm font-bold text-[#B8B8B8]">You have no active orders right now.</p>
                    <a href="{{ route('menu') }}" class="mt-3 inline-block rounded-full bg-[#FBAB18] px-6 py-2.5 text-xs font-bold text-[#111111] transition hover:bg-[#FFC247]">Order Fresh Food</a>
                </div>
            @endif
            </div>
            <p id="customer-orders-live-status" class="mt-3 text-xs text-[#B8B8B8]" role="status">Live updates enabled.</p>
        </div>

        <!-- 2. PAST ORDER HISTORY -->
        <div>
            <h2 class="mb-4 text-xl font-bold text-[#FFFFFF]">Order History</h2>

            <div id="customer-order-history" class="space-y-4">
                @forelse ($orderHistory as $order)
                    <div class="flex flex-col gap-4 rounded-[28px] border border-[#333333] bg-[#201D19] p-5 shadow-sm md:flex-row md:items-center md:justify-between">
                        <div class="min-w-0">
                            <p class="text-sm font-bold uppercase tracking-[0.12em] text-[#FBAB18]">{{ $order->order_number }}</p>
                            <div class="mt-2 flex flex-wrap items-center gap-4 text-sm text-[#B8B8B8]">
                                <span>{{ $order->created_at ? $order->created_at->format('M d, Y') : 'N/A' }}</span>
                                <span class="h-1.5 w-1.5 rounded-full bg-[#333333]"></span>
                                <span>{{ $order->branch?->name ?? 'MixEat' }}</span>
                                @if ($order->pickup_at)
                                    <span>Pickup: {{ $order->pickup_at->format('M j, Y g:i A') }}</span>
                                @elseif ($order->delivery_method)
                                    <span>{{ $order->delivery_method }} delivery</span>
                                @endif
                            </div>
                        </div>
                        <div class="flex flex-wrap items-center gap-4">
                            <div class="text-right">
                                <div class="text-xs text-[#B8B8B8]">Total</div>
                                <div class="text-xl font-bold text-[#FBAB18]">&#8369;{{ number_format($order->total, 2) }}</div>
                            </div>
                            <span class="rounded-full px-3 py-1.5 text-xs font-bold uppercase tracking-[0.12em] {{ $order->status === 'Completed' ? 'bg-[#22C55E] text-[#111111]' : 'bg-[#EF4444] text-[#FFFFFF]' }}">
                                {{ $order->status }}
                            </span>
                            <a href="{{ route('order-detail', ['id' => (int) str_replace('MX-', '', $order->order_number)]) }}" class="inline-flex items-center justify-center rounded-full bg-[#FBAB18] px-5 py-3 text-sm font-semibold text-[#111111] transition hover:bg-[#FFC247]">
                                View Order
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="rounded-[28px] border border-[#333333] bg-[#1A1A1A] p-8 text-center text-sm font-medium text-[#B8B8B8]">
                        No past order history found.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</section>
@endsection

@auth
    @push('scripts')
    <script>
        (() => {
            const activeOrdersContainer = document.getElementById('customer-active-orders');
            const historyContainer = document.getElementById('customer-order-history');
            const liveStatus = document.getElementById('customer-orders-live-status');
            if (!activeOrdersContainer || !historyContainer || !liveStatus) return;

            let polling = false;
            const createText = (tag, className, text) => {
                const element = document.createElement(tag);
                element.className = className;
                element.textContent = text;
                return element;
            };

            const renderOrders = (orders) => {
                const active = orders.filter((order) => ['Pending', 'Preparing', 'Ready for Pickup'].includes(order.status));
                const history = orders.filter((order) => ['Completed', 'Declined', 'Cancelled'].includes(order.status));
                activeOrdersContainer.replaceChildren();

                if (!active.length) {
                    const empty = document.createElement('div');
                    empty.className = 'rounded-[28px] border border-[#333333] bg-[#1A1A1A] p-8 text-center';
                    empty.append(createText('p', 'text-sm font-bold text-[#B8B8B8]', 'You have no active orders right now.'));
                    const menuLink = createText('a', 'mt-3 inline-block rounded-full bg-[#FBAB18] px-6 py-2.5 text-xs font-bold text-[#111111] transition hover:bg-[#FFC247]', 'Order Fresh Food');
                    menuLink.href = @json(route('menu'));
                    empty.append(menuLink);
                    activeOrdersContainer.append(empty);
                } else {
                    const grid = document.createElement('div');
                    grid.className = 'grid grid-cols-1 gap-6 md:grid-cols-2';
                    active.forEach((order) => {
                        const card = document.createElement('article');
                        card.className = 'rounded-[28px] border border-[#333333] bg-[#201D19] p-6 shadow-sm';
                        const header = document.createElement('div');
                        header.className = 'flex items-center justify-between gap-3 border-b border-[#333333] pb-4';
                        const heading = document.createElement('div');
                        heading.className = 'min-w-0';
                        heading.append(
                            createText('span', 'block text-xs font-bold text-[#B8B8B8]', order.created_at || 'Just Now'),
                            createText('h3', 'break-words text-lg font-black text-[#FFFFFF]', order.id)
                        );
                        const statusLabels = {
                            Pending: 'Pending Acceptance',
                            Preparing: 'Kitchen is Preparing',
                            'Ready for Pickup': 'Ready for Pickup',
                        };
                        const statusClasses = {
                            Pending: 'bg-[#FFF3CD] text-[#856404]',
                            Preparing: 'bg-[#E6F4EA] text-[#1E8E3E]',
                            'Ready for Pickup': 'bg-[#FBAB18] text-[#111111]',
                        };
                        const badge = createText('span', `inline-block rounded-full px-3 py-1 text-xs font-bold ${statusClasses[order.status]}`, statusLabels[order.status]);
                        header.append(heading, badge);

                        const details = document.createElement('div');
                        details.className = 'py-4';
                        details.append(
                            createText('p', 'text-xs font-bold uppercase text-[#B8B8B8]', 'Branch Location'),
                            createText('p', 'break-words text-sm font-semibold text-[#FFFFFF]', order.branch),
                            createText('p', 'mt-2 text-sm font-semibold text-[#E5E5E5]', order.type)
                        );
                        if (order.pickup_at) details.append(createText('p', 'mt-1 text-sm text-[#B8B8B8]', `Pickup: ${order.pickup_at}`));
                        if (order.delivery_method) {
                            details.append(
                                createText('p', 'mt-1 text-sm text-[#B8B8B8]', `Delivery: ${order.delivery_method}`),
                                createText('p', 'mt-1 break-words text-sm text-[#E5E5E5]', order.delivery_address || '')
                            );
                        }
                        if (order.payment_method) details.append(createText('p', 'mt-1 break-words text-sm text-[#B8B8B8]', `Payment: ${order.payment_method}`));
                        if (order.gcash_reference_number) details.append(createText('p', 'mt-1 break-all text-sm text-[#B8B8B8]', `GCash reference: ${order.gcash_reference_number}`));
                        details.append(createText('p', 'mt-4 text-right text-lg font-bold text-[#FBAB18]', `Total Amount: ₱${Number(order.total).toFixed(2)}`));

                        const progress = document.createElement('div');
                        progress.className = 'mt-2 rounded-2xl bg-[#1A1A1A] p-4';
                        progress.append(createText('p', 'mb-2 text-xs font-bold text-[#FFFFFF]', 'Order Progress'));
                        const bars = document.createElement('div');
                        bars.className = 'flex gap-2';
                        const step = { Pending: 1, Preparing: 2, 'Ready for Pickup': 3 }[order.status];
                        for (let index = 0; index < 3; index++) {
                            const bar = document.createElement('div');
                            bar.className = `h-2 flex-1 rounded-full ${index < step ? 'bg-[#FBAB18]' : 'bg-[#333333]'}`;
                            bars.append(bar);
                        }
                        const progressText = {
                            Pending: 'Waiting for branch supervisor to confirm order...',
                            Preparing: 'Your meal is currently being cooked!',
                            'Ready for Pickup': 'Your order is ready! Please proceed to the counter for pickup.',
                        };
                        progress.append(bars, createText('div', 'mt-2 text-center text-xs font-bold text-[#E5E5E5]', progressText[order.status]));
                        const actions = document.createElement('div');
                        actions.className = 'mt-4 text-right';
                        const viewLink = createText('a', 'inline-flex items-center justify-center rounded-full bg-[#FBAB18] px-5 py-2.5 text-xs font-bold text-[#111111] transition hover:bg-[#FFC247]', 'View Details');
                        viewLink.href = order.url;
                        actions.append(viewLink);
                        card.append(header, details, progress, actions);
                        grid.append(card);
                    });
                    activeOrdersContainer.append(grid);
                }

                historyContainer.replaceChildren();
                if (!history.length) {
                    const empty = document.createElement('div');
                    empty.className = 'rounded-[28px] border border-[#333333] bg-[#1A1A1A] p-8 text-center text-sm font-medium text-[#B8B8B8]';
                    empty.textContent = 'No past order history found.';
                    historyContainer.append(empty);
                    return;
                }

                history.forEach((order) => {
                    const row = document.createElement('div');
                    row.className = 'flex flex-col gap-4 rounded-[28px] border border-[#333333] bg-[#201D19] p-5 shadow-sm md:flex-row md:items-center md:justify-between';
                    const info = document.createElement('div');
                    info.className = 'min-w-0';
                    info.append(
                        createText('p', 'text-sm font-bold uppercase tracking-[0.12em] text-[#FBAB18]', order.id),
                        createText('p', 'mt-2 text-sm text-[#B8B8B8]', `${order.created_at || 'N/A'} · ${order.branch}`)
                    );
                    const actions = document.createElement('div');
                    actions.className = 'flex flex-wrap items-center gap-4';
                    actions.append(
                        createText('span', 'text-xl font-bold text-[#FBAB18]', `₱${Number(order.total).toFixed(2)}`),
                        createText('span', `rounded-full px-3 py-1.5 text-xs font-bold uppercase tracking-[0.12em] ${order.status === 'Completed' ? 'bg-[#22C55E] text-[#111111]' : 'bg-[#EF4444] text-[#FFFFFF]'}`, order.status)
                    );
                    const viewLink = createText('a', 'inline-flex items-center justify-center rounded-full bg-[#FBAB18] px-5 py-3 text-sm font-semibold text-[#111111] transition hover:bg-[#FFC247]', 'View Order');
                    viewLink.href = order.url;
                    actions.append(viewLink);
                    row.append(info, actions);
                    historyContainer.append(row);
                });
            };

            const refreshOrders = async () => {
                if (polling) return;
                polling = true;
                try {
                    const response = await fetch(activeOrdersContainer.dataset.liveUrl, {
                        headers: { Accept: 'application/json' },
                        credentials: 'same-origin',
                        cache: 'no-store',
                    });
                    if (!response.ok) throw new Error(`Order update request failed (${response.status})`);
                    const data = await response.json();
                    renderOrders(Array.isArray(data.orders) ? data.orders : []);
                    liveStatus.textContent = 'Live updates enabled.';
                } catch (error) {
                    console.error('Unable to refresh customer orders.', error);
                    liveStatus.textContent = 'Live updates temporarily unavailable. Retrying automatically.';
                } finally {
                    polling = false;
                }
            };

            refreshOrders();
            const interval = window.setInterval(refreshOrders, 3000);
            document.addEventListener('visibilitychange', function(){
                if (!document.hidden) refreshOrders();
            });
            window.addEventListener('beforeunload', function(){ window.clearInterval(interval); });
        })();
    </script>
    @endpush
@endauth
