@extends('layouts.app')

@section('title', 'Supervisor Dashboard | MixEat')

@section('content')
<section class="py-10">
    <div class="supervisor-dashboard">
        <!-- Header with Export Button -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.18em] text-[#FBAB18]">SUPERVISOR</p>
                <h1 class="mt-1 text-3xl font-black text-[#FFFFFF] md:text-4xl">Supervisor Dashboard</h1>
                <p class="mt-1 text-sm font-medium text-[#B8B8B8]">Managing Branch: <strong class="text-[#FFFFFF]">{{ $branch->name }}</strong></p>
            </div>

            <!-- Export Daily Sales Report Excel Button -->
            <div class="flex items-center gap-3 self-start sm:self-auto">
                <div class="relative" id="notif-bell-container">
                    <button id="notif-bell-btn" type="button" class="relative inline-flex items-center justify-center rounded-full bg-[#201D19] border border-[#333333] p-2.5 text-[#FFFFFF]">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        <span id="notif-count" class="absolute -top-0.5 -right-0.5 hidden min-w-[18px] h-[18px] items-center justify-center rounded-full bg-red-600 text-[10px] font-bold text-white leading-none px-1">0</span>
                    </button>
                    <div id="notif-dropdown" class="hidden absolute right-0 mt-2 w-80 max-h-96 overflow-auto rounded-2xl border border-[#333333] bg-[#201D19] shadow-lg z-50">
                        <div class="px-4 py-3 border-b border-[#333333]">
                            <p class="text-sm font-bold text-[#FFFFFF]">Notifications</p>
                        </div>
                        <div id="notif-list" class="divide-y divide-[#333333]"></div>
                        <div id="notif-empty" class="px-4 py-4 text-center text-sm text-[#B8B8B8] hidden">No new notifications</div>
                    </div>
                </div>
                <a href="{{ route('admin.supervisors.export-daily-sales') }}" class="inline-flex items-center gap-2 rounded-full bg-[#107C41] px-5 py-2.5 text-xs font-bold text-white shadow-md transition hover:bg-[#0E6C38]">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Export Daily Sales (Excel)
                </a>
            </div>
        </div>

        @if(session('admin_status'))
            <div class="mb-6 rounded-2xl bg-[#201D19] px-4 py-3 font-semibold text-[#FFFFFF]">
                {{ session('admin_status') }}
            </div>
        @endif

        <!-- Responsive Daily Inventory & Revenue Metrics Grid -->
       <section class="supervisor-stats">
            <!-- Today's Orders -->
            <div class="rounded-[20px] border border-[#333333] bg-[#1A1A1A] p-5 shadow-sm h-full w-full">
                <p class="text-xs font-bold uppercase tracking-wider text-[#777777]">
                    TODAY'S ORDERS
                </p>
                <h3 id="live-total-orders" class="mt-2 text-2xl font-black text-[#FFFFFF]">
                    {{ $dailySummary['total_orders'] }}
                </h3>
            </div>

            <!-- In Preparation -->
            <div class="rounded-[20px] border border-[#333333] bg-[#FFF3CD] p-5 shadow-sm h-full w-full">
                <p class="text-xs font-bold uppercase tracking-wider text-[#856404]">
                    IN PREPARATION
                </p>
                <h3 id="live-preparing-orders" class="mt-2 text-2xl font-black text-[#856404]">
                    {{ $dailySummary['preparing_count'] }}
                </h3>
            </div>

            <!-- Ready for Pickup -->
            <div class="rounded-[20px] border border-[#C3E6CB] bg-[#E6F4EA] p-5 shadow-sm h-full w-full">
                <p class="text-xs font-bold uppercase tracking-wider text-[#1E8E3E]">
                    READY FOR PICKUP
                </p>
                <h3 id="live-ready-orders" class="mt-2 text-2xl font-black text-[#1E8E3E]">
                    {{ $dailySummary['ready_count'] }}
                </h3>
            </div>

            <!-- Estimated Revenue -->
            <div class="rounded-[20px] border border-[#333333] bg-[#201D19] p-5 shadow-sm h-full w-full">
                <p class="text-xs font-bold uppercase tracking-wider text-[#777777]">
                    ESTIMATED REVENUE
                </p>
                <h3 id="live-revenue" class="mt-2 text-2xl font-black text-[#FFFFFF]">
                    &#8369;{{ number_format($dailySummary['total_revenue'], 2) }}
                </h3>
            </div>
        </section>

        <!-- Branch Orders Section -->
        <div class="supervisor-orders rounded-[28px] border border-[#333333] bg-[#201D19] p-6 shadow-sm">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-xl font-bold text-[#FFFFFF]">Branch Orders</h2>
                <span id="live-orders-status" role="status" class="rounded-full bg-[#FBAB18] px-3 py-1 text-xs font-bold text-[#111111]">Live updates</span>
            </div>

            <div class="overflow-x-auto">
                <table class="branch-orders-table w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-[#333333] text-xs font-bold uppercase tracking-wider text-[#C5C5C5]">
                            <th class="py-3 px-4">Order #</th>
                            <th class="py-3 px-4">Type</th>
                            <th class="py-3 px-4">Total</th>
                            <th class="py-3 px-4 text-center">Status</th>
                            <th class="py-3 px-4 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody id="live-orders-table" class="divide-y divide-[#333333] text-sm font-medium">
                        @include('admin.supervisors.partials.order-rows', ['orders' => $orders])
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Menu Availability Section -->
        <div class="supervisor-menu rounded-[28px] border border-[#333333] bg-[#201D19] p-6 shadow-sm">
            <h2 class="mb-4 text-xl font-bold text-[#FFFFFF]">Menu Availability</h2>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-[#333333] text-xs font-bold uppercase tracking-wider text-[#777777]">
                            <th class="py-3 px-4">Item Name</th>
                            <th class="py-3 px-4">Price</th>
                            <th class="py-3 px-4 text-center">Status</th>
                            <th class="py-3 px-4 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#333333]/60 text-sm font-medium">
                        @forelse($products as $product)
                            @php
                                $branchPivot = $product->branches->first()?->pivot;
                                $isAvailable = $branchPivot ? (bool) $branchPivot->available : true;
                            @endphp
                            <tr>
                                <td class="py-4 px-4 font-bold text-[#FFFFFF]">{{ $product->name }}</td>
                                <td class="py-4 px-4 text-[#FFFFFF]">&#8369;{{ number_format($product->price, 2) }}</td>
                                <td class="py-4 px-4 text-center">
                                    @if($isAvailable)
                                        <span class="inline-block rounded-full bg-[#E6F4EA] px-3 py-1 text-xs font-bold text-[#1E8E3E]">Available</span>
                                    @else
                                        <span class="inline-block rounded-full bg-[#FCE8E6] px-3 py-1 text-xs font-bold text-[#D93025]">Sold Out</span>
                                    @endif
                                </td>
                                <td class="py-4 px-4 text-right">
                                    <form action="{{ route('admin.supervisors.toggle', $product->id) }}" method="POST" class="inline-block">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="available" value="{{ $isAvailable ? '0' : '1' }}">
                                        <button type="submit" class="text-xs font-bold {{ $isAvailable ? 'text-[#DC3545] hover:underline' : 'text-[#28A745] hover:underline' }}">
                                            Set as {{ $isAvailable ? 'Sold Out' : 'Available' }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-6 text-center text-sm font-medium text-[#777777]">No menu items found in the system.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
(function(){
    const notifBellBtn = document.getElementById('notif-bell-btn');
    const notifDropdown = document.getElementById('notif-dropdown');
    const notifCount = document.getElementById('notif-count');
    const notifList = document.getElementById('notif-list');
    const notifEmpty = document.getElementById('notif-empty');
    let lastCount = 0, audioEnabled = false;
    window.addEventListener('click', function(){ audioEnabled = true; }, {once:true});
    function playBeep(){
        if(!audioEnabled) return;
        try {
            const AudioCtx = window.AudioContext || window.webkitAudioContext;
            const ctx = new AudioCtx();
            const o = ctx.createOscillator();
            const g = ctx.createGain();
            o.connect(g); g.connect(ctx.destination);
            o.frequency.value = 880; o.type = 'sine';
            g.gain.setValueAtTime(0.001, ctx.currentTime);
            g.gain.exponentialRampToValueAtTime(0.1, ctx.currentTime+0.01);
            g.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime+0.2);
            o.start(ctx.currentTime); o.stop(ctx.currentTime+0.2);
        } catch(e){}
    }
    if(notifBellBtn){
        notifBellBtn.addEventListener('click', function(e){ e.stopPropagation(); notifDropdown.classList.toggle('hidden'); });
        document.addEventListener('click', function(){ notifDropdown.classList.add('hidden'); });
        notifDropdown.addEventListener('click', function(e){ e.stopPropagation(); });
    }
    async function fetchNotifications(){
        try {
              const res = await fetch("{{ route('admin.supervisors.notifications') }}", 
{headers:{'Accept':'application/json'}, credentials:'same-origin'});
            if(!res.ok) return;
            const data = await res.json();
            const count = data.count || 0;
            if(count>0){ notifCount.textContent = count>9?'9+':count; notifCount.classList.remove('hidden'); notifCount.classList.add('flex'); }
            else { notifCount.classList.add('hidden'); notifCount.classList.remove('flex'); }
            if(count>lastCount){ playBeep(); }
            lastCount = count;
            notifList.innerHTML = '';
            if(!data.notifications || data.notifications.length===0){ notifEmpty.classList.remove('hidden'); }
            else {
                notifEmpty.classList.add('hidden');
                data.notifications.forEach(function(n){
                    const d = n.data || {};
                    const item = document.createElement('div');
                    item.className = 'px-4 py-3 hover:bg-[#2a2720]';
                    item.innerHTML = '<p class="text-sm font-bold text-[#FFFFFF]">' + (d.message||'New Order Received!') + '</p>' +
                        '<p class="text-xs text-[#B8B8B8] mt-1">Order ' + (d.order_number||'') + ' Â· ' + (d.branch_name||'') + '</p>' +
                        '<p class="text-xs text-[#B8B8B8] mt-1">Total: â‚±' + Number(d.total||0).toFixed(2) + '</p>' +
                        '<p class="text-xs text-[#777777] mt-1">' + (n.created_at||'') + '</p>' +
                        '<div class="flex gap-2 mt-2"><a href="{{ url("/admin/supervisors/notifications") }}/' + n.id + 
'/order" class="text-xs font-bold text-[#FBAB18] hover:underline">View Order</a><button data-read="' + n.id + '" 
class="text-xs text-[#B8B8B8] hover:text-[#FFFFFF]">Mark as Read</button></div>';
                    notifList.appendChild(item);
                });
                notifList.querySelectorAll('[data-read]').forEach(function(btn){
                    btn.addEventListener('click', async function(e){
                        e.preventDefault();
                        const id = btn.getAttribute('data-read');
                        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
                          await fetch("{{ url('/admin/supervisors/notifications') }}/" + id + '/read', { method: 'POST', 
headers: {'X-CSRF-TOKEN': token, 'Accept': 'application/json'}, credentials: 'same-origin' });
                        fetchNotifications();
                    });
                });
            }
        } catch(e){}
    }
    fetchNotifications(); setInterval(fetchNotifications, 5000);
})();
</script>
@endpush
@endsection

@push('scripts')
<script>
    (() => {
        const table = document.getElementById('live-orders-table');
        const indicator = document.getElementById('live-orders-status');
        if (!table || !indicator) return;

        let polling = false;
        const refreshOrders = async () => {
            if (polling || document.hidden) return;
            polling = true;

            try {
                const response = await fetch(@json(route('admin.supervisors.orders.live')), {
                    headers: { Accept: 'application/json' },
                    credentials: 'same-origin',
                    cache: 'no-store',
                });
                if (!response.ok) throw new Error(`Live order request failed (${response.status})`);
                const data = await response.json();

                table.innerHTML = data.orders_html;
                document.getElementById('live-total-orders').textContent = data.daily_summary.total_orders;
                document.getElementById('live-preparing-orders').textContent = data.daily_summary.preparing_count;
                document.getElementById('live-ready-orders').textContent = data.daily_summary.ready_count;
                document.getElementById('live-revenue').textContent =
                    '\u20B1' + Number(data.daily_summary.total_revenue).toLocaleString('en-PH', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                indicator.textContent = 'Live updates';
                indicator.className = 'rounded-full bg-[#E6F4EA] px-3 py-1 text-xs font-bold text-[#1E8E3E]';
            } catch (error) {
                console.error('Unable to refresh branch orders.', error);
                indicator.textContent = 'Live updates unavailable';
                indicator.className = 'rounded-full bg-[#FCE8E6] px-3 py-1 text-xs font-bold text-[#D93025]';
            } finally {
                polling = false;
            }
        };

        refreshOrders();
        window.setInterval(refreshOrders, 3000);
    })();
</script>
@endpush
