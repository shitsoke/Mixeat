@extends('layouts.app')

@section('title', 'My Orders | MixEat')

@section('content')
    <section class="py-14">
        <div class="mixeat-shell px-4">
            <div class="mb-8 text-center md:text-left">
                <p class="text-sm font-bold uppercase tracking-[0.18em] text-[#FFC60A]">My Orders</p>
                <h1 class="mt-2 text-4xl font-black text-[#090909]">Order history</h1>
            </div>

            <div class="space-y-5">
                @foreach ($orders as $order)
                    <div class="flex flex-col gap-4 rounded-[28px] border border-[#E8DFAF] bg-white p-5 shadow-[0_14px_40px_rgba(229,169,0,0.08)] md:flex-row md:items-center md:justify-between">
                        <div>
                            <p class="text-sm font-bold uppercase tracking-[0.12em] text-[#FFC60A]">Order {{ $order['id'] }}</p>
                            <div class="mt-2 flex flex-wrap items-center gap-4 text-sm text-[#555555]">
                                <span>{{ $order['date'] }}</span>
                                <span class="h-1.5 w-1.5 rounded-full bg-[#E8DFAF]"></span>
                                <span>{{ $order['branch'] }}</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="text-right">
                                <div class="text-sm text-[#555555]">Total</div>
                                <div class="text-xl font-black text-[#090909]">&#8369;{{ number_format($order['total'], 2) }}</div>
                            </div>
                            <span class="rounded-full bg-[#FFF4BF] px-3 py-1.5 text-xs font-bold uppercase tracking-[0.12em] text-[#FFC60A]">{{ $order['status'] }}</span>
                            <a href="{{ route('order-detail', ['id' => str_replace('MX-', '', $order['id'])]) }}" class="inline-flex items-center justify-center rounded-full bg-[#FFC60A] px-5 py-3 text-sm font-semibold text-[#090909] shadow-lg shadow-[#E5A900]/25 transition hover:bg-[#E5A900]">
                                View Order
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endsection


