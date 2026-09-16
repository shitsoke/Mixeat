@extends('layouts.app')

@section('title', 'Checkout | MixEat')

@section('content')
    <section class="py-14">
        <div class="mixeat-shell px-4">
            <div class="mb-8 text-center md:text-left">
                <img src="{{ asset('images/mixeat.jpg') }}" alt="MixEat" class="mx-auto mb-4 h-16 w-16 rounded-2xl object-contain md:mx-0" />
                <p class="text-sm font-bold uppercase tracking-[0.18em] text-[#FFC60A]">Checkout</p>
                <h1 class="mt-2 text-4xl font-black text-[#090909]">Complete your order.</h1>
            </div>

            <div class="grid gap-8 lg:grid-cols-[minmax(0,1.35fr)_380px]">
                <div class="space-y-6">
                    <div class="rounded-[30px] border border-[#E8DFAF] bg-white p-6 shadow-[0_14px_40px_rgba(229,169,0,0.10)]">
                        <h2 class="mb-5 text-2xl font-black text-[#090909]">1. Customer Information</h2>
                        <div class="grid gap-5 md:grid-cols-2">
                            <div>
                                <label class="mb-2 block text-sm font-semibold text-[#555555]">Full Name</label>
                                <input type="text" value="{{ auth()->user()?->name ?? '' }}" class="w-full rounded-2xl border border-[#E8DFAF] bg-[#FFF9E6] px-4 py-3 text-sm text-[#090909] focus:outline-none" />
                            </div>
                            <div>
                                <label class="mb-2 block text-sm font-semibold text-[#555555]">Email</label>
                                <input type="email" value="{{ auth()->user()?->email ?? '' }}" class="w-full rounded-2xl border border-[#E8DFAF] bg-[#FFF9E6] px-4 py-3 text-sm text-[#090909] focus:outline-none" />
                            </div>
                            <div class="md:col-span-2">
                                <label class="mb-2 block text-sm font-semibold text-[#555555]">Phone Number</label>
                                <input type="tel" value="{{ auth()->user()?->phone ?? '' }}" class="w-full rounded-2xl border border-[#E8DFAF] bg-[#FFF9E6] px-4 py-3 text-sm text-[#090909] focus:outline-none" />
                            </div>
                        </div>
                    </div>

                    <div class="rounded-[30px] border border-[#E8DFAF] bg-white p-6 shadow-[0_14px_40px_rgba(229,169,0,0.10)]">
                        <h2 class="mb-5 text-2xl font-black text-[#090909]">2. Order Type</h2>
                        <div class="flex flex-col gap-4 md:flex-row">
                            <label class="flex items-center gap-3 rounded-2xl border border-[#E8DFAF] bg-[#FFF9E6] px-4 py-3 text-sm font-semibold text-[#090909]">
                                <input type="radio" name="order_type" checked class="h-4 w-4 accent-[#FFC60A]" />
                                Pickup
                            </label>
                            <label class="flex items-center gap-3 rounded-2xl border border-[#E8DFAF] bg-[#FFF9E6] px-4 py-3 text-sm font-semibold text-[#090909]">
                                <input type="radio" name="order_type" class="h-4 w-4 accent-[#FFC60A]" />
                                Delivery
                            </label>
                        </div>
                    </div>

                    <div class="rounded-[30px] border border-[#E8DFAF] bg-white p-6 shadow-[0_14px_40px_rgba(229,169,0,0.10)]">
                        <div class="flex items-center justify-between gap-4">
                            <h2 class="text-2xl font-black text-[#090909]">3. Branch</h2>
                            <a href="{{ route('branches') }}" class="text-sm font-semibold text-[#FFC60A]">Change Branch</a>
                        </div>
                        <div class="mt-5 rounded-[24px] bg-[#FFF9E6] p-5">
                            <h3 class="text-xl font-black text-[#090909]">{{ $selectedBranch['name'] }}</h3>
                            <p class="mt-2 text-sm text-[#555555]">{{ $selectedBranch['address'] }}</p>
                            <p class="mt-1 text-sm text-[#555555]">Distance: {{ $selectedBranch['distance'] }}</p>
                        </div>
                    </div>

                    <div class="rounded-[30px] border border-[#E8DFAF] bg-white p-6 shadow-[0_14px_40px_rgba(229,169,0,0.10)]">
                        <h2 class="mb-5 text-2xl font-black text-[#090909]">4. Delivery Address</h2>
                        <div class="grid gap-5 md:grid-cols-2">
                            <div>
                                <label class="mb-2 block text-sm font-semibold text-[#555555]">Street</label>
                                <input type="text" class="w-full rounded-2xl border border-[#E8DFAF] bg-[#FFF9E6] px-4 py-3 text-sm text-[#090909] focus:outline-none" />
                            </div>
                            <div>
                                <label class="mb-2 block text-sm font-semibold text-[#555555]">Barangay</label>
                                <input type="text" class="w-full rounded-2xl border border-[#E8DFAF] bg-[#FFF9E6] px-4 py-3 text-sm text-[#090909] focus:outline-none" />
                            </div>
                            <div>
                                <label class="mb-2 block text-sm font-semibold text-[#555555]">City</label>
                                <input type="text" class="w-full rounded-2xl border border-[#E8DFAF] bg-[#FFF9E6] px-4 py-3 text-sm text-[#090909] focus:outline-none" />
                            </div>
                            <div>
                                <label class="mb-2 block text-sm font-semibold text-[#555555]">Province</label>
                                <input type="text" class="w-full rounded-2xl border border-[#E8DFAF] bg-[#FFF9E6] px-4 py-3 text-sm text-[#090909] focus:outline-none" />
                            </div>
                            <div class="md:col-span-2">
                                <label class="mb-2 block text-sm font-semibold text-[#555555]">Additional Instructions</label>
                                <textarea rows="4" class="w-full rounded-2xl border border-[#E8DFAF] bg-[#FFF9E6] px-4 py-3 text-sm text-[#090909] focus:outline-none"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-[30px] border border-[#E8DFAF] bg-white p-6 shadow-[0_14px_40px_rgba(229,169,0,0.10)]">
                        <h2 class="mb-5 text-2xl font-black text-[#090909]">5. Payment Method</h2>
                        <div class="flex flex-col gap-4 md:flex-row">
                            <label class="flex items-center gap-3 rounded-2xl border border-[#E8DFAF] bg-[#FFF9E6] px-4 py-3 text-sm font-semibold text-[#090909]">
                                <input type="radio" name="payment_method" checked class="h-4 w-4 accent-[#FFC60A]" />
                                Cash
                            </label>
                            <label class="flex items-center gap-3 rounded-2xl border border-[#E8DFAF] bg-[#FFF9E6] px-4 py-3 text-sm font-semibold text-[#090909]">
                                <input type="radio" name="payment_method" class="h-4 w-4 accent-[#FFC60A]" />
                                Online Payment
                            </label>
                        </div>
                    </div>
                </div>

                <aside class="rounded-[30px] border border-[#E8DFAF] bg-white p-6 shadow-[0_14px_40px_rgba(229,169,0,0.10)]">
                    <h2 class="text-2xl font-black text-[#090909]">6. Order Summary</h2>
                    <div class="mt-6 space-y-4">
                        @foreach ($items as $item)
                            <div class="flex items-center gap-4 border-b border-dashed border-[#E8DFAF] pb-4">
                                <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" class="h-16 w-16 rounded-2xl object-cover">
                                <div class="flex-1">
                                    <p class="font-bold text-[#090909]">{{ $item['name'] }}</p>
                                    <p class="text-sm text-[#555555]">Qty: {{ $item['quantity'] }}</p>
                                </div>
                                <div class="text-sm font-bold text-[#090909]">&#8369;{{ number_format($item['price'] * $item['quantity'], 2) }}</div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-6 space-y-4 text-[#555555]">
                        <div class="flex items-center justify-between">
                            <span>Subtotal</span>
                            <span class="font-semibold text-[#090909]">&#8369;{{ number_format($subtotal, 2) }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span>Delivery Fee</span>
                            <span class="font-semibold text-[#090909]">&#8369;{{ number_format($deliveryFee, 2) }}</span>
                        </div>
                        <div class="flex items-center justify-between border-t border-dashed border-[#E8DFAF] pt-4">
                            <span class="text-lg font-bold text-[#090909]">Total</span>
                            <span class="text-2xl font-black text-[#090909]">&#8369;{{ number_format($total, 2) }}</span>
                        </div>
                    </div>

                    <a href="{{ route('order-confirmation') }}" class="mt-8 inline-flex w-full items-center justify-center rounded-full bg-[#FFC60A] px-6 py-3.5 text-base font-semibold text-[#090909] shadow-lg shadow-[#E5A900]/25 transition hover:bg-[#E5A900]">
                        Place Order
                    </a>
                </aside>
            </div>
        </div>
    </section>
@endsection


