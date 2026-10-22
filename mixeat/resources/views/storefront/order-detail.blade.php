@extends('layouts.app')

@section('title', 'Track Order | MixEat')

@section('content')
    @php
        $trackingFlow = ['Pending', 'Preparing', 'Ready for Pickup', 'Completed'];
        $trackingCurrent = $order['status'];
        $trackingCurrentIndex = array_search($trackingCurrent, $trackingFlow, true);
        $trackingIsCancelled = in_array($trackingCurrent, ['Cancelled', 'Declined'], true);
    @endphp

    <section class="order-tracking py-14">
        <div class="mixeat-shell px-4">
            <div class="mb-8">
                <p class="text-sm font-bold uppercase tracking-[0.18em] text-[#FBAB18]">Order Tracking</p>
                <h1 class="mt-2 text-4xl font-black text-[#FFFFFF]">Order #{{ $order['id'] }}</h1>
                <p class="mt-2 text-lg text-[#B8B8B8]">{{ $order['branch'] }}</p>
            </div>

            <div class="tracking-status-container rounded-[30px] border border-[#333333] bg-[#201D19] p-6 shadow-[0_14px_40px_rgba(0,0,0,0.35)]">
                <div class="mb-8">
                    <p class="mb-3 text-sm font-bold uppercase tracking-[0.12em] text-[#B8B8B8]">Order Status</p>
                    <div class="grid gap-4 md:grid-cols-6">
                        @foreach ($statuses as $status)
                            @php
                                $isCancelledStep = in_array($status, ['Cancelled', 'Declined'], true);
                                $statusIndex = array_search($status, $trackingFlow, true);

                                if ($status === $trackingCurrent) {
                                    $stepState = $isCancelledStep ? 'cancelled' : 'current';
                                } elseif (! $trackingIsCancelled && $trackingCurrentIndex !== false && $statusIndex !== false && $statusIndex < $trackingCurrentIndex) {
                                    $stepState = 'done';
                                } else {
                                    $stepState = 'upcoming';
                                }
                            @endphp
                            <div class="flex flex-col items-center text-center">
                                <div class="tracking-step tracking-step-{{ $stepState }} flex h-12 w-12 items-center justify-center rounded-full border text-lg font-bold">
                                    @if ($stepState === 'done')
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M5 13l4 4L19 7" /></svg>
                                    @elseif ($stepState === 'current')
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="5" /></svg>
                                    @elseif ($stepState === 'cancelled')
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M6 6l12 12M18 6L6 18" /></svg>
                                    @else
                                        <span class="text-[#B8B8B8]">&#8211;</span>
                                    @endif
                                </div>
                                <span class="mt-2 text-[11px] font-semibold uppercase tracking-[0.08em] {{ $stepState === 'current' || $stepState === 'cancelled' ? 'text-[#FFFFFF]' : 'text-[#B8B8B8]' }}">{{ $status }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="grid gap-6 lg:grid-cols-2">
                    <div class="tracking-inner-card rounded-[24px] border border-[#333333] bg-[#1A1A1A] p-5">
                        <h2 class="text-xl font-black text-[#FFFFFF]">Order Details</h2>
                        <div class="mt-4 space-y-3 text-sm text-[#B8B8B8]">
                            <div class="flex items-center justify-between"><span>Order Number</span><span class="font-semibold text-[#FFFFFF]">{{ $order['id'] }}</span></div>
                            <div class="flex items-center justify-between"><span>Branch</span><span class="font-semibold text-[#FFFFFF]">{{ $order['branch'] }}</span></div>
                            <div class="flex items-center justify-between"><span>Order Type</span><span class="font-semibold text-[#FFFFFF]">{{ $order['type'] }}</span></div>
                            @if (!empty($order['pickup_at']))
                                <div class="flex items-center justify-between"><span>Pickup</span><span class="font-semibold text-[#FFFFFF]">{{ $order['pickup_at'] }}</span></div>
                            @endif
                            @if (!empty($order['delivery_method']))
                                <div class="flex items-center justify-between"><span>Delivery Method</span><span class="font-semibold text-[#FFFFFF]">{{ $order['delivery_method'] }}</span></div>
                                <div class="flex items-center justify-between gap-4"><span>Delivery Address</span><span class="text-right font-semibold text-[#FFFFFF]">{{ $order['delivery_address'] }}</span></div>
                            @endif
                            @if (!empty($order['payment_method']))
                                <div class="flex items-center justify-between"><span>Payment</span><span class="font-semibold text-[#FFFFFF]">{{ $order['payment_method'] }}</span></div>
                            @endif
                            <div class="flex items-center justify-between"><span>Status</span><span class="font-semibold text-[#FFFFFF]">{{ $order['status'] }}</span></div>
                            <div class="flex items-center justify-between"><span>Total</span><span class="font-bold text-[#FBAB18]">&#8369;{{ number_format($order['total'], 2) }}</span></div>
                        </div>
                    </div>

                    <div class="tracking-inner-card rounded-[24px] border border-[#333333] bg-[#1A1A1A] p-5">
                        <h2 class="text-xl font-black text-[#FFFFFF]">Customer Notes</h2>
                        <p class="mt-4 text-sm leading-7 text-[#E5E5E5]">
                            Your order is being prepared at the selected MixEat branch. You'll receive updates as the status changes.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
