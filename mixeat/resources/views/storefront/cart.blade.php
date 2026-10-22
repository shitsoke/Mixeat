@extends('layouts.app')

@section('title', 'Your Cart | MixEat')

@section('content')
    <section class="py-14">
        <div class="mixeat-shell px-4">
            <div class="mb-8 text-center md:text-left">
                <p class="text-sm font-bold uppercase tracking-[0.18em] text-[#FBAB18]">Your Cart</p>
                <h1 class="mt-2 text-4xl font-black text-[#FFFFFF]">Ready for checkout.</h1>
            </div>

            @if (session('cart_status'))
                <div class="mb-6 rounded-2xl border border-[#333333] bg-[#201D19] px-4 py-3 text-sm font-semibold text-[#FFFFFF]" role="status">
                    {{ session('cart_status') }}
                </div>
            @endif

            @if (empty($items))
                <div class="rounded-[32px] border border-dashed border-[#333333] bg-[#201D19] p-10 text-center shadow-[0_14px_40px_rgba(229,169,0,0.08)]">
                    <div class="mx-auto mb-5 flex h-20 w-20 items-center justify-center rounded-full bg-[#201D19] text-[#FBAB18]">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 3h2l.4 2M7 13h10l3-8H6.2M7 13L5.7 5M7 13l-1 7m11-7l1 7M9 20a1 1 0 100 2 1 1 0 000-2zm8 0a1 1 0 100 2 1 1 0 000-2z" />
                        </svg>
                    </div>
                    <h2 class="text-2xl font-black text-[#FFFFFF]">Your cart is empty</h2>
                    <p class="mt-3 text-[#B8B8B8]">Add some delicious MixEat favorites to get started.</p>
                    <a href="{{ route('menu') }}" class="mt-6 inline-flex items-center justify-center rounded-full bg-[#FBAB18] px-6 py-3.5 text-base font-semibold text-[#FFFFFF] shadow-lg shadow-[#FBAB18]/25 transition hover:bg-[#FBAB18]">
                        Explore Menu
                    </a>
                </div>
            @else
                <div class="grid gap-8 lg:grid-cols-[minmax(0,1.5fr)_360px]">
                    <div class="space-y-5">
                        @foreach ($items as $key => $item)
                            <div class="flex flex-col gap-5 rounded-[28px] border border-[#333333] bg-[#201D19] p-4 shadow-[0_14px_40px_rgba(229,169,0,0.08)] sm:flex-row sm:items-center">
                                <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" class="h-28 w-full rounded-[20px] object-cover sm:w-28">
                                <div class="flex-1">
                                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                        <div>
                                            <h3 class="text-xl font-bold text-[#FFFFFF]">{{ $item['name'] }}</h3>
                                            
                                            {{-- Selected Add-ons Badges --}}
                                            @if (!empty($item['addons']))
                                                <div class="mt-1 flex flex-wrap gap-1.5">
                                                    @foreach ($item['addons'] as $addon)
                                                        <span class="inline-block rounded-full bg-[#1A1A1A] border border-[#333333] px-2.5 py-0.5 text-[11px] font-bold text-[#FFFFFF]">
                                                            + {{ $addon }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @endif

                                            <p class="mt-2 text-lg font-semibold text-[#FBAB18]">&#8369;{{ number_format($item['price'], 2) }}</p>
                                        </div>

                                        <form action="{{ route('cart.remove', ['id' => $key]) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-sm font-semibold text-[#DC3545] hover:underline">Remove</button>
                                        </form>
                                    </div>

                                    <div class="mt-4 flex items-center justify-between gap-4">
                                        <div class="inline-flex items-center rounded-full border border-[#333333] bg-[#1A1A1A] px-3 py-2">
                                            <form action="{{ route('cart.quantity', ['id' => $key]) }}" method="POST">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="quantity" value="{{ max(0, $item['quantity'] - 1) }}">
                                                <button type="submit" aria-label="Decrease {{ $item['name'] }} quantity" class="h-8 w-8 text-xl font-bold text-[#FFFFFF]">-</button>
                                            </form>

                                            <span class="w-10 text-center text-lg font-bold text-[#FFFFFF]">{{ $item['quantity'] }}</span>

                                            <form action="{{ route('cart.quantity', ['id' => $key]) }}" method="POST">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="quantity" value="{{ $item['quantity'] + 1 }}">
                                                <button type="submit" aria-label="Increase {{ $item['name'] }} quantity" class="h-8 w-8 text-xl font-bold text-[#FFFFFF]">+</button>
                                            </form>
                                        </div>

                                        <div class="text-xl font-black text-[#FFFFFF]">&#8369;{{ number_format($item['price'] * $item['quantity'], 2) }}</div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <aside class="rounded-[30px] border border-[#333333] bg-[#201D19] p-6 shadow-[0_14px_40px_rgba(229,169,0,0.10)]">
                        <h2 class="text-2xl font-black text-[#FFFFFF]">Order Summary</h2>
                        <div class="mt-6 space-y-4 text-[#B8B8B8]">
                            <div class="flex items-center justify-between">
                                <span>Subtotal</span>
                                <span class="font-semibold text-[#FFFFFF]">&#8369;{{ number_format($subtotal, 2) }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span>Delivery Fee</span>
                                <span class="font-semibold text-[#FFFFFF]">&#8369;{{ number_format($deliveryFee, 2) }}</span>
                            </div>
                            <div class="flex items-center justify-between border-t border-dashed border-[#333333] pt-4">
                                <span class="text-lg font-bold text-[#FFFFFF]">Total</span>
                                <span class="text-2xl font-black text-[#FFFFFF]">&#8369;{{ number_format($total, 2) }}</span>
                            </div>
                        </div>

                        <div class="mt-8 space-y-3">
                            <a href="{{ route('menu') }}" class="inline-flex w-full items-center justify-center rounded-full border border-[#FBAB18] bg-[#201D19] px-6 py-3.5 text-base font-semibold text-[#FBAB18] transition hover:bg-[#201D19]">
                                Continue Shopping
                            </a>
                            <a href="{{ route('checkout') }}" class="inline-flex w-full items-center justify-center rounded-full bg-[#FBAB18] px-6 py-3.5 text-base font-semibold text-[#FFFFFF] shadow-lg shadow-[#FBAB18]/25 transition hover:bg-[#FBAB18]">
                                Proceed to Checkout
                            </a>
                        </div>
                    </aside>
                </div>
            @endif
        </div>
    </section>
@endsection
