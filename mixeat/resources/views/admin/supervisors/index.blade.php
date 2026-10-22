@extends('layouts.app')

@section('title', 'Supervisor Dashboard | MixEat')

@section('content')
<section class="py-10">
    <div class="mixeat-shell px-4">
        <!-- Header with Export Button -->
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.18em] text-[#FBAB18]">Supervisor</p>
                <h1 class="mt-1 text-3xl font-black text-[#FFFFFF] md:text-4xl">Supervisor Dashboard</h1>
                <p class="mt-1 text-sm font-medium text-[#B8B8B8]">Managing Branch: <strong class="text-[#FFFFFF]">{{ $branch->name }}</strong></p>
            </div>

            <!-- Export Daily Sales Report Excel Button -->
            <div>
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
        <div class="mb-8 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <!-- Today's Orders -->
            <div class="rounded-[20px] border border-[#333333] bg-[#1A1A1A] p-5 shadow-sm">
                <p class="text-xs font-bold uppercase tracking-wider text-[#777777]">Today's Orders</p>
                <h3 id="live-total-orders" class="mt-2 text-2xl font-black text-[#FFFFFF]">{{ $dailySummary['total_orders'] }}</h3>
            </div>

            <!-- In Preparation -->
            <div class="rounded-[20px] border border-[#333333] bg-[#FFF3CD] p-5 shadow-sm">
                <p class="text-xs font-bold uppercase tracking-wider text-[#856404]">In Preparation</p>
                <h3 id="live-preparing-orders" class="mt-2 text-2xl font-black text-[#856404]">{{ $dailySummary['preparing_count'] }}</h3>
            </div>

            <!-- Ready for Pickup -->
            <div class="rounded-[20px] border border-[#C3E6CB] bg-[#E6F4EA] p-5 shadow-sm">
                <p class="text-xs font-bold uppercase tracking-wider text-[#1E8E3E]">Ready for Pickup</p>
                <h3 id="live-ready-orders" class="mt-2 text-2xl font-black text-[#1E8E3E]">{{ $dailySummary['ready_count'] }}</h3>
            </div>

            <!-- Estimated Revenue -->
            <div class="rounded-[20px] border border-[#333333] bg-[#201D19] p-5 shadow-sm">
                <p class="text-xs font-bold uppercase tracking-wider text-[#777777]">Estimated Revenue</p>
                <h3 id="live-revenue" class="mt-2 text-2xl font-black text-[#FFFFFF]">&#8369;{{ number_format($dailySummary['total_revenue'], 2) }}</h3>
            </div>
        </div>

        <!-- Branch Orders Section -->
        <div class="mb-10 rounded-[28px] border border-[#333333] bg-[#201D19] p-6 shadow-sm">
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
        <div class="rounded-[28px] border border-[#333333] bg-[#201D19] p-6 shadow-sm">
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
                document.getElementById('live-revenue').textContent = "₱" + Number(data.daily_summary.total_revenue).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
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
