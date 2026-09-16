@extends('layouts.app')

@section('title', 'Track Order | MixEat')

@section('content')
    <section class="py-14">
        <div class="mixeat-shell px-4">
            <div class="mb-8">
                <p class="text-sm font-bold uppercase tracking-[0.18em] text-[#FFC60A]">Order Tracking</p>
                <h1 class="mt-2 text-4xl font-black text-[#090909]">Order #{{ $order['id'] }}</h1>
                <p class="mt-2 text-lg text-[#555555]">{{ $order['branch'] }}</p>
            </div>

            <div class="rounded-[30px] border border-[#E8DFAF] bg-white p-6 shadow-[0_14px_40px_rgba(229,169,0,0.10)]">
                <div class="mb-8">
                    <p class="mb-3 text-sm font-bold uppercase tracking-[0.12em] text-[#555555]">Order Status</p>
                    <div class="grid gap-4 md:grid-cols-7">
                        @foreach ($statuses as $status)
                            <div class="flex flex-col items-center text-center">
                                <div class="{{ $status === $order['status'] ? 'bg-[#FFC60A] text-[#090909]' : ($status === 'Pending' || $status === 'Confirmed' || $status === 'Preparing' || $status === 'Ready' || $status === 'Completed' ? 'bg-[#EAF6EE] text-[#198754]' : 'bg-[#FFF9E6] text-[#999]') }} flex h-12 w-12 items-center justify-center rounded-full border border-[#E8DFAF] text-lg font-bold">
                                    @if ($status === $order['status'])
                                        &#10003;
                                    @elseif ($status === 'Cancelled')
                                        -
                                    @else
                                        -
                                    @endif
                                </div>
                                <span class="mt-2 text-[11px] font-semibold uppercase tracking-[0.08em] text-[#555555]">{{ $status }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="grid gap-6 lg:grid-cols-2">
                    <div class="rounded-[24px] bg-[#FFF9E6] p-5">
                        <h2 class="text-xl font-black text-[#090909]">Order Details</h2>
                        <div class="mt-4 space-y-3 text-sm text-[#555555]">
                            <div class="flex items-center justify-between"><span>Order Number</span><span class="font-semibold text-[#090909]">{{ $order['id'] }}</span></div>
                            <div class="flex items-center justify-between"><span>Branch</span><span class="font-semibold text-[#090909]">{{ $order['branch'] }}</span></div>
                            <div class="flex items-center justify-between"><span>Status</span><span class="font-semibold text-[#090909]">{{ $order['status'] }}</span></div>
                            <div class="flex items-center justify-between"><span>Total</span><span class="font-semibold text-[#090909]">&#8369;{{ number_format($order['total'], 2) }}</span></div>
                        </div>
                    </div>

                    <div class="rounded-[24px] bg-[#FFF9E6] p-5">
                        <h2 class="text-xl font-black text-[#090909]">Customer Notes</h2>
                        <p class="mt-4 text-sm leading-7 text-[#555555]">
                            Your order is being prepared at the selected MixEat branch. You'll receive updates as the status changes.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection


