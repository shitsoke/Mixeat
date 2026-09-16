@extends('layouts.app')

@section('title', $product['name'] . ' | MixEat')

@section('content')
    <section class="py-14">
        <div class="mixeat-shell px-4">
            <a href="{{ route('menu') }}" class="mb-8 inline-flex items-center gap-2 rounded-full border border-[#E8DFAF] bg-white px-4 py-2 text-sm font-semibold text-[#555555] transition hover:border-[#FFC60A] hover:text-[#FFC60A]">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Back to Menu
            </a>

            <div class="grid gap-8 lg:grid-cols-2">
                <div class="overflow-hidden rounded-[32px] border border-[#E8DFAF] bg-white p-4 shadow-[0_20px_60px_rgba(229,169,0,0.12)]">
                    <img src="{{ $product['image'] }}" alt="{{ $product['name'] }}" class="h-[500px] w-full rounded-[24px] object-cover">
                </div>

                <div class="flex flex-col justify-center">
                    <p class="text-sm font-bold uppercase tracking-[0.18em] text-[#FFC60A]">{{ $product['category'] }}</p>
                    <h1 class="mt-3 text-4xl font-black text-[#090909]">{{ $product['name'] }}</h1>
                    <p class="mt-5 text-lg leading-8 text-[#555555]">{{ $product['description'] }}</p>

                    <div class="mt-6 flex items-center gap-4">
                        <div class="text-4xl font-black text-[#090909]">&#8369;{{ number_format($product['price'], 2) }}</div>
                        <span class="rounded-full bg-[#EAF6EE] px-3 py-1 text-sm font-semibold text-[#198754]">
                            @if($product['available'])
                                Available
                            @else
                                Sold Out
                            @endif
                        </span>
                    </div>

                    <div class="mt-8 rounded-[28px] border border-[#E8DFAF] bg-white p-5 shadow-[0_14px_40px_rgba(229,169,0,0.08)]">
                        <div>
                            <h3 class="mb-3 text-sm font-bold uppercase tracking-[0.12em] text-[#555555]">Optional add-ons</h3>
                            <div class="space-y-3 text-sm text-[#090909]">
                                <label class="flex items-center justify-between rounded-2xl border border-[#E8DFAF] bg-[#FFF9E6] px-4 py-3">
                                    <span class="flex items-center gap-3">
                                        <input type="checkbox" class="h-4 w-4 accent-[#FFC60A]" />
                                        <span>Extra Rice</span>
                                    </span>
                                    <span class="font-semibold text-[#FFC60A]">+&#8369;20</span>
                                </label>
                                <label class="flex items-center justify-between rounded-2xl border border-[#E8DFAF] bg-[#FFF9E6] px-4 py-3">
                                    <span class="flex items-center gap-3">
                                        <input type="checkbox" class="h-4 w-4 accent-[#FFC60A]" />
                                        <span>Extra Sauce</span>
                                    </span>
                                    <span class="font-semibold text-[#FFC60A]">+&#8369;10</span>
                                </label>
                                <label class="flex items-center justify-between rounded-2xl border border-[#E8DFAF] bg-[#FFF9E6] px-4 py-3">
                                    <span class="flex items-center gap-3">
                                        <input type="checkbox" class="h-4 w-4 accent-[#FFC60A]" />
                                        <span>Soft Drink</span>
                                    </span>
                                    <span class="font-semibold text-[#FFC60A]">+&#8369;30</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 flex flex-col gap-4 sm:flex-row">
                        @if ($product['available'])
                            <form action="{{ route('cart.add', ['id' => $product['id']]) }}" method="POST" class="flex-1">
                                @csrf
                                <button type="submit" class="inline-flex w-full items-center justify-center rounded-full bg-[#FFC60A] px-7 py-4 text-base font-semibold text-[#090909] shadow-lg shadow-[#E5A900]/25 transition hover:bg-[#E5A900]">
                                    Add to Cart
                                </button>
                            </form>
                        @else
                            <button type="button" disabled class="inline-flex flex-1 cursor-not-allowed items-center justify-center rounded-full bg-[#E8DFAF] px-7 py-4 text-base font-semibold text-[#555555]">
                                Sold Out
                            </button>
                        @endif
                        <a href="{{ route('menu') }}" class="inline-flex items-center justify-center rounded-full border border-[#FFC60A] bg-white px-7 py-4 text-base font-semibold text-[#FFC60A] transition hover:bg-[#FFF4BF]">
                            Continue Shopping
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection


