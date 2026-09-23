@extends('layouts.app')

@section('title', 'My Orders | MixEat')

@section('content')
<section class="py-10 md:py-16">
    <div class="mixeat-shell px-4">
        <!-- Header -->
        <div class="mb-8 text-center md:text-left">
            <p class="text-xs font-bold uppercase tracking-[0.18em] text-[#FFC60A]">Customer Dashboard</p>
            <h1 class="mt-1 text-3xl font-black text-[#090909] md:text-4xl">My Orders & Activity</h1>
            <p class="mt-1 text-sm font-medium text-[#555555]">Track active meal preparation and view past purchase history.</p>
        </div>

        <!-- 1. ACTIVE ORDER ACTIVITY -->
        <div class="mb-12">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-xl font-bold text-[#090909]">Active Order Activity</h2>
                <span class="rounded-full bg-[#FFC60A] px-3 py-1 text-xs font-bold text-[#090909]">Live Updates</span>
            </div>

            @if($activeOrders->count() > 0)
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    @foreach($activeOrders as $order)
                        <div class="rounded-[28px] border border-[#E8DFAF] bg-white p-6 shadow-sm">
                            <div class="flex items-center justify-between border-b border-[#E8DFAF]/60 pb-4">
                                <div>
                                    <span class="text-xs font-bold text-[#777777]">{{ $order->created_at ? $order->created_at->format('M d, Y • h:i A') : 'Just Now' }}</span>
                                    <h3 class="text-lg font-black text-[#090909]">{{ $order->order_number }}</h3>
                                </div>
                                <div>
                                    @if($order->status === 'Pending')
                                        <span class="inline-block rounded-full bg-[#FFF3CD] px-3 py-1 text-xs font-bold text-[#856404]">Pending Acceptance</span>
                                    @elseif($order->status === 'Preparing')
                                        <span class="inline-block rounded-full bg-[#E6F4EA] px-3 py-1 text-xs font-bold text-[#1E8E3E]">Kitchen is Preparing</span>
                                    @elseif($order->status === 'Ready for Pickup')
                                        <span class="inline-block rounded-full bg-[#CCE5FF] px-3 py-1 text-xs font-bold text-[#004085]">Ready for Pickup</span>
                                    @endif
                                </div>
                            </div>

                            <div class="py-4">
                                <p class="text-xs font-bold uppercase text-[#777777]">Branch Location</p>
                                <p class="text-sm font-bold text-[#090909]">{{ $order->branch?->name ?? 'MixEat Branch' }}</p>

                                <div class="mt-4 flex items-center justify-between">
                                    <span class="text-xs font-bold uppercase text-[#777777]">Total Amount</span>
                                    <span class="text-lg font-black text-[#FFC60A]">&#8369;{{ number_format($order->total, 2) }}</span>
                                </div>
                            </div>

                            <!-- Progress Indicator -->
                            <div class="mt-2 rounded-2xl bg-[#FFF9E6] p-4">
                                <p class="mb-2 text-xs font-bold text-[#555555]">Order Progress</p>
                                <div class="flex gap-2">
                                    <div class="h-2 flex-1 rounded-full {{ in_array($order->status, ['Pending', 'Preparing', 'Ready for Pickup']) ? 'bg-[#FFC60A]' : 'bg-gray-200' }}"></div>
                                    <div class="h-2 flex-1 rounded-full {{ in_array($order->status, ['Preparing', 'Ready for Pickup']) ? 'bg-[#FFC60A]' : 'bg-gray-200' }}"></div>
                                    <div class="h-2 flex-1 rounded-full {{ $order->status === 'Ready for Pickup' ? 'bg-[#28A745]' : 'bg-gray-200' }}"></div>
                                </div>
                                <div class="mt-2 text-center text-xs font-bold text-[#090909]">
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
                                <a href="{{ route('order-detail', ['id' => (int) str_replace('MX-', '', $order->order_number)]) }}" class="inline-flex items-center justify-center rounded-full bg-[#FFC60A] px-5 py-2.5 text-xs font-bold text-[#090909] transition hover:bg-[#E5A900]">
                                    View Details
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="rounded-[28px] border border-[#E8DFAF] bg-[#FFF9E6] p-8 text-center">
                    <p class="text-sm font-bold text-[#555555]">You have no active orders right now.</p>
                    <a href="{{ route('menu') }}" class="mt-3 inline-block rounded-full bg-[#FFC60A] px-6 py-2.5 text-xs font-bold text-[#090909] transition hover:bg-[#E5A900]">Order Fresh Food</a>
                </div>
            @endif
        </div>

        <!-- 2. PAST ORDER HISTORY -->
        <div>
            <h2 class="mb-4 text-xl font-bold text-[#090909]">Order History</h2>

            <div class="space-y-4">
                @forelse ($orderHistory as $order)
                    <div class="flex flex-col gap-4 rounded-[28px] border border-[#E8DFAF] bg-white p-5 shadow-sm md:flex-row md:items-center md:justify-between">
                        <div>
                            <p class="text-sm font-bold uppercase tracking-[0.12em] text-[#FFC60A]">{{ $order->order_number }}</p>
                            <div class="mt-2 flex flex-wrap items-center gap-4 text-sm text-[#555555]">
                                <span>{{ $order->created_at ? $order->created_at->format('M d, Y') : 'N/A' }}</span>
                                <span class="h-1.5 w-1.5 rounded-full bg-[#E8DFAF]"></span>
                                <span>{{ $order->branch?->name ?? 'MixEat' }}</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="text-right">
                                <div class="text-xs text-[#555555]">Total</div>
                                <div class="text-xl font-black text-[#090909]">&#8369;{{ number_format($order->total, 2) }}</div>
                            </div>
                            <span class="rounded-full px-3 py-1.5 text-xs font-bold uppercase tracking-[0.12em] {{ $order->status === 'Completed' ? 'bg-[#D4EDDA] text-[#155724]' : 'bg-[#FCE8E6] text-[#D93025]' }}">
                                {{ $order->status }}
                            </span>
                            <a href="{{ route('order-detail', ['id' => (int) str_replace('MX-', '', $order->order_number)]) }}" class="inline-flex items-center justify-center rounded-full bg-[#FFC60A] px-5 py-3 text-sm font-semibold text-[#090909] transition hover:bg-[#E5A900]">
                                View Order
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="rounded-[28px] border border-[#E8DFAF] bg-white p-8 text-center text-sm font-medium text-[#777777]">
                        No past order history found.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</section>
@endsection