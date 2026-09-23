@extends('layouts.app')

@section('title', 'Supervisor Dashboard | MixEat')

@section('content')
<section class="py-10">
    <div class="mixeat-shell px-4">
        <!-- Header with Export Button -->
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.18em] text-[#FFC60A]">Supervisor</p>
                <h1 class="mt-1 text-3xl font-black text-[#090909] md:text-4xl">Supervisor Dashboard</h1>
                <p class="mt-1 text-sm font-medium text-[#555555]">Managing Branch: <strong class="text-[#090909]">{{ $branch->name }}</strong></p>
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
            <div class="mb-6 rounded-2xl bg-[#FFF4BF] px-4 py-3 font-semibold text-[#090909]">
                {{ session('admin_status') }}
            </div>
        @endif

        <!-- Responsive Daily Inventory & Revenue Metrics Grid -->
        <div class="mb-8 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <!-- Today's Orders -->
            <div class="rounded-[20px] border border-[#E8DFAF] bg-[#FFF9E6] p-5 shadow-sm">
                <p class="text-xs font-bold uppercase tracking-wider text-[#777777]">Today's Orders</p>
                <h3 class="mt-2 text-2xl font-black text-[#090909]">{{ $dailySummary['total_orders'] }}</h3>
            </div>

            <!-- In Preparation -->
            <div class="rounded-[20px] border border-[#E8DFAF] bg-[#FFF3CD] p-5 shadow-sm">
                <p class="text-xs font-bold uppercase tracking-wider text-[#856404]">In Preparation</p>
                <h3 class="mt-2 text-2xl font-black text-[#856404]">{{ $dailySummary['preparing_count'] }}</h3>
            </div>

            <!-- Ready for Pickup -->
            <div class="rounded-[20px] border border-[#C3E6CB] bg-[#E6F4EA] p-5 shadow-sm">
                <p class="text-xs font-bold uppercase tracking-wider text-[#1E8E3E]">Ready for Pickup</p>
                <h3 class="mt-2 text-2xl font-black text-[#1E8E3E]">{{ $dailySummary['ready_count'] }}</h3>
            </div>

            <!-- Estimated Revenue -->
            <div class="rounded-[20px] border border-[#E8DFAF] bg-white p-5 shadow-sm">
                <p class="text-xs font-bold uppercase tracking-wider text-[#777777]">Estimated Revenue</p>
                <h3 class="mt-2 text-2xl font-black text-[#090909]">&#8369;{{ number_format($dailySummary['total_revenue'], 2) }}</h3>
            </div>
        </div>

        <!-- Branch Orders Section -->
        <div class="mb-10 rounded-[28px] border border-[#E8DFAF] bg-white p-6 shadow-sm">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-xl font-bold text-[#090909]">Branch Orders</h2>
                <span class="rounded-full bg-[#FFC60A] px-3 py-1 text-xs font-bold text-[#090909]">Real-time</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-[#E8DFAF] text-xs font-bold uppercase tracking-wider text-[#777777]">
                            <th class="py-3 px-4">Order #</th>
                            <th class="py-3 px-4">Type</th>
                            <th class="py-3 px-4">Total</th>
                            <th class="py-3 px-4 text-center">Status</th>
                            <th class="py-3 px-4 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E8DFAF]/60 text-sm font-medium">
                        @forelse($orders as $order)
                            <tr>
                                <td class="py-4 px-4 font-bold text-[#090909]">{{ $order->order_number }}</td>
                                <td class="py-4 px-4 text-[#555555]">{{ $order->type }}</td>
                                <td class="py-4 px-4 font-bold text-[#090909]">&#8369;{{ number_format($order->total, 2) }}</td>
                                <td class="py-4 px-4 text-center">
                                    @if($order->status === 'Pending')
                                        <span class="inline-block rounded-full bg-[#FFF3CD] px-3 py-1 text-xs font-bold text-[#856404]">Pending</span>
                                    @elseif($order->status === 'Preparing')
                                        <span class="inline-block rounded-full bg-[#E6F4EA] px-3 py-1 text-xs font-bold text-[#1E8E3E]">Preparing</span>
                                    @elseif($order->status === 'Ready for Pickup')
                                        <span class="inline-block rounded-full bg-[#CCE5FF] px-3 py-1 text-xs font-bold text-[#004085]">Ready for Pickup</span>
                                    @elseif($order->status === 'Completed')
                                        <span class="inline-block rounded-full bg-[#D4EDDA] px-3 py-1 text-xs font-bold text-[#155724]">Completed</span>
                                    @elseif($order->status === 'Declined')
                                        <span class="inline-block rounded-full bg-[#FCE8E6] px-3 py-1 text-xs font-bold text-[#D93025]">Declined</span>
                                    @else
                                        <span class="inline-block rounded-full bg-gray-100 px-3 py-1 text-xs font-bold text-gray-700">{{ $order->status }}</span>
                                    @endif
                                </td>
                                <td class="py-4 px-4 text-right">
                                    @if($order->status === 'Pending')
                                        <form action="{{ route('admin.supervisors.orders.accept', $order->id) }}" method="POST" class="inline-block me-1">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="rounded-full bg-[#28A745] px-4 py-1.5 text-xs font-bold text-white transition hover:bg-[#218838]">Accept</button>
                                        </form>
                                        <form action="{{ route('admin.supervisors.orders.decline', $order->id) }}" method="POST" class="inline-block">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="rounded-full border border-[#DC3545] px-4 py-1.5 text-xs font-bold text-[#DC3545] transition hover:bg-red-50">Decline</button>
                                        </form>
                                    @elseif($order->status === 'Preparing')
                                        <form action="{{ route('admin.supervisors.orders.ready', $order->id) }}" method="POST" class="inline-block">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="rounded-full bg-[#FFC60A] px-4 py-1.5 text-xs font-bold text-[#090909] shadow-sm transition hover:bg-[#E5A900]">Out for Pickup</button>
                                        </form>
                                    @elseif($order->status === 'Ready for Pickup')
                                        <form action="{{ route('admin.supervisors.orders.complete', $order->id) }}" method="POST" class="inline-block">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="rounded-full border border-[#28A745] px-4 py-1.5 text-xs font-bold text-[#28A745] transition hover:bg-green-50">Complete Order</button>
                                        </form>
                                    @else
                                        <span class="text-xs text-[#777777]">No actions</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-6 text-center text-sm font-medium text-[#777777]">No orders logged for this branch.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Menu Availability Section -->
        <div class="rounded-[28px] border border-[#E8DFAF] bg-white p-6 shadow-sm">
            <h2 class="mb-4 text-xl font-bold text-[#090909]">Menu Availability</h2>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-[#E8DFAF] text-xs font-bold uppercase tracking-wider text-[#777777]">
                            <th class="py-3 px-4">Item Name</th>
                            <th class="py-3 px-4">Price</th>
                            <th class="py-3 px-4 text-center">Status</th>
                            <th class="py-3 px-4 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E8DFAF]/60 text-sm font-medium">
                        @forelse($products as $product)
                            @php
                                $branchPivot = $product->branches->first()?->pivot;
                                $isAvailable = $branchPivot ? (bool) $branchPivot->available : true;
                            @endphp
                            <tr>
                                <td class="py-4 px-4 font-bold text-[#090909]">{{ $product->name }}</td>
                                <td class="py-4 px-4 text-[#090909]">&#8369;{{ number_format($product->price, 2) }}</td>
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