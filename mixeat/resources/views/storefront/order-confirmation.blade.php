@extends('layouts.app')

@section('title', 'Order Confirmation | MixEat')

@section('content')
    <section class="py-20">
        <div class="mixeat-shell px-4">
            <div class="mx-auto max-w-2xl rounded-[32px] border border-[#E8DFAF] bg-white p-8 text-center shadow-[0_20px_60px_rgba(229,169,0,0.12)]">
                <div class="mx-auto mb-6 flex h-20 w-20 items-center justify-center rounded-full bg-[#EAF6EE] text-3xl text-[#198754]">&#10003;</div>
                <h1 class="text-4xl font-black text-[#090909]">Order Placed Successfully!</h1>
                <p class="mt-4 text-lg text-[#555555]">Your order has been received and is being prepared.</p>

                <div class="mt-8 rounded-[24px] bg-[#FFF9E6] p-5 text-left">
                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <p class="text-sm font-bold uppercase tracking-[0.12em] text-[#555555]">Order Number</p>
                            <p class="mt-2 text-2xl font-black text-[#090909]">#{{ $order['id'] }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-bold uppercase tracking-[0.12em] text-[#555555]">Branch</p>
                            <p class="mt-2 text-lg font-semibold text-[#090909]">{{ $order['branch'] }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-bold uppercase tracking-[0.12em] text-[#555555]">Order Type</p>
                            <p class="mt-2 text-lg font-semibold text-[#090909]">{{ $order['type'] }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-bold uppercase tracking-[0.12em] text-[#555555]">Estimated preparation time</p>
                            <p class="mt-2 text-lg font-semibold text-[#090909]">{{ $order['time'] }}</p>
                        </div>
                    </div>
                    <div class="mt-5 border-t border-dashed border-[#E8DFAF] pt-4">
                        <p class="text-sm font-bold uppercase tracking-[0.12em] text-[#555555]">Total</p>
                        <p class="mt-2 text-3xl font-black text-[#FFC60A]">&#8369;{{ number_format($order['total'], 2) }}</p>
                    </div>
                </div>

                <div class="mt-8 flex flex-col justify-center gap-4 sm:flex-row">
                    <a href="{{ route('orders') }}" class="inline-flex items-center justify-center rounded-full bg-[#FFC60A] px-6 py-3.5 text-base font-semibold text-[#090909] shadow-lg shadow-[#E5A900]/25 transition hover:bg-[#E5A900]">
                        Track Order
                    </a>
                    <a href="{{ route('home') }}" class="inline-flex items-center justify-center rounded-full border border-[#FFC60A] bg-white px-6 py-3.5 text-base font-semibold text-[#FFC60A] transition hover:bg-[#FFF4BF]">
                        Back to Home
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection


