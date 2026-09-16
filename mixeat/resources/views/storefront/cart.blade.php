@extends('layouts.app')

@section('title', 'Your Cart | MixEat')

@section('content')
    <section class="py-14">
        <div class="mixeat-shell px-4">
            <div class="mb-8 text-center md:text-left">
                <p class="text-sm font-bold uppercase tracking-[0.18em] text-[#FFC60A]">Your Cart</p>
                <h1 class="mt-2 text-4xl font-black text-[#090909]">Ready for checkout.</h1>
            </div>

            @if (session('cart_status'))
                <div class="mb-6 rounded-2xl border border-[#E8DFAF] bg-[#FFF4BF] px-4 py-3 text-sm font-semibold text-[#090909]" role="status">
                    {{ session('cart_status') }}
                </div>
            @endif

            @if (empty($items))
                <div class="rounded-[32px] border border-dashed border-[#E8DFAF] bg-white p-10 text-center shadow-[0_14px_40px_rgba(229,169,0,0.08)]">
                    <div class="mx-auto mb-5 flex h-20 w-20 items-center justify-center rounded-full bg-[#FFF4BF] text-[#FFC60A]">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 3h2l.4 2M7 13h10l3-8H6.2M7 13L5.7 5M7 13l-1 7m11-7l1 7M9 20a1 1 0 100 2 1 1 0 000-2zm8 0a1 1 0 100 2 1 1 0 000-2z" />
                        </svg>
                    </div>
                    <h2 class="text-2xl font-black text-[#090909]">Your cart is empty</h2>
                    <p class="mt-3 text-[#555555]">Add some delicious MixEat favorites to get started.</p>
                    <a href="{{ route('menu') }}" class="mt-6 inline-flex items-center justify-center rounded-full bg-[#FFC60A] px-6 py-3.5 text-base font-semibold text-[#090909] shadow-lg shadow-[#E5A900]/25 transition hover:bg-[#E5A900]">
                        Explore Menu
                    </a>
                </div>
            @else
                <div class="grid gap-8 lg:grid-cols-[minmax(0,1.5fr)_360px]">
                    <div class="space-y-5">
                        @foreach ($items as $item)
                            <div class="flex flex-col gap-5 rounded-[28px] border border-[#E8DFAF] bg-white p-4 shadow-[0_14px_40px_rgba(229,169,0,0.08)] sm:flex-row sm:items-center">
                                <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" class="h-28 w-full rounded-[20px] object-cover sm:w-28">
                                <div class="flex-1">
                                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                        <div>
                                            <h3 class="text-xl font-bold text-[#090909]">{{ $item['name'] }}</h3>
                                            <p class="mt-1 text-lg font-semibold text-[#FFC60A]">&#8369;{{ number_format($item['price'], 2) }}</p>
                                        </div>
                                        <form action="{{ route('cart.remove', ['id' => $item['id']]) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-sm font-semibold text-[#DC3545]">Remove</button>
                                        </form>
                                    </div>
                                    <div class="mt-4 flex items-center justify-between gap-4">
                                        <div class="inline-flex items-center rounded-full border border-[#E8DFAF] bg-[#FFF9E6] px-3 py-2">
                                            <form action="{{ route('cart.quantity', ['id' => $item['id']]) }}" method="POST">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="quantity" value="{{ max(0, $item['quantity'] - 1) }}">
                                                <button type="submit" aria-label="Decrease {{ $item['name'] }} quantity" class="h-8 w-8 text-xl font-bold text-[#090909]">-</button>
                                            </form>
                                            <span class="w-10 text-center text-lg font-bold text-[#090909]">{{ $item['quantity'] }}</span>
                                            <form action="{{ route('cart.quantity', ['id' => $item['id']]) }}" method="POST">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="quantity" value="{{ $item['quantity'] + 1 }}">
                                                <button type="submit" aria-label="Increase {{ $item['name'] }} quantity" class="h-8 w-8 text-xl font-bold text-[#090909]">+</button>
                                            </form>
                                        </div>
                                        <div class="text-xl font-black text-[#090909]">&#8369;{{ number_format($item['price'] * $item['quantity'], 2) }}</div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <aside class="rounded-[30px] border border-[#E8DFAF] bg-white p-6 shadow-[0_14px_40px_rgba(229,169,0,0.10)]">
                        <h2 class="text-2xl font-black text-[#090909]">Order Summary</h2>
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

                        <div class="mt-8 space-y-3">
                            <a href="{{ route('menu') }}" class="inline-flex w-full items-center justify-center rounded-full border border-[#FFC60A] bg-white px-6 py-3.5 text-base font-semibold text-[#FFC60A] transition hover:bg-[#FFF4BF]">
                                Continue Shopping
                            </a>
                            <a href="{{ route('checkout') }}" class="inline-flex w-full items-center justify-center rounded-full bg-[#FFC60A] px-6 py-3.5 text-base font-semibold text-[#090909] shadow-lg shadow-[#E5A900]/25 transition hover:bg-[#E5A900]">
                                Proceed to Checkout
                            </a>
                        </div>
                    </aside>
                </div>
            @endif
        </div>
    </section>
@endsection


