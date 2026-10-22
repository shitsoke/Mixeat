@extends('layouts.app')

@section('title', $product['name'] . ' | MixEat')

@section('content')
    <section class="py-14">
        <div class="mixeat-shell px-4">
            <a href="{{ route('menu') }}" class="mb-8 inline-flex items-center gap-2 rounded-full border border-[#333333] bg-[#201D19] px-4 py-2 text-sm font-semibold text-[#B8B8B8] transition hover:border-[#FBAB18] hover:text-[#FBAB18]">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Back to Menu
            </a>

            <div class="grid gap-8 lg:grid-cols-2">
                <div class="overflow-hidden rounded-[32px] border border-[#333333] bg-[#201D19] p-4 shadow-[0_20px_60px_rgba(229,169,0,0.12)]">
                    <img src="{{ $product['image'] }}" alt="{{ $product['name'] }}" class="h-[500px] w-full rounded-[24px] object-cover">
                </div>

                <div class="flex flex-col justify-center">
                    <p class="text-sm font-bold uppercase tracking-[0.18em] text-[#FBAB18]">{{ $product['category'] }}</p>
                    <h1 class="mt-3 text-4xl font-black text-[#FFFFFF]">{{ $product['name'] }}</h1>
                    <p class="mt-5 text-lg leading-8 text-[#B8B8B8]">{{ $product['description'] }}</p>

                    <!-- Dynamic Base & Total Price Display -->
                    <div class="mt-6 flex items-center gap-4">
                        <div class="text-4xl font-black text-[#FFFFFF]">
                            &#8369;<span id="display-total-price">{{ number_format($product['price'], 2) }}</span>
                        </div>
                        <span class="rounded-full bg-[#EAF6EE] px-3 py-1 text-sm font-semibold text-[#198754]">
                            @if($product['available'] ?? true)
                                Available
                            @else
                                Sold Out
                            @endif
                        </span>
                    </div>

                    @if ($product['available'] ?? true)
                        <form action="{{ route('cart.add', ['id' => $product['id']]) }}" method="POST" id="add-to-cart-form">
                            @csrf

                            <!-- Optional Add-ons Section -->
                            <div class="mt-8 rounded-[28px] border border-[#333333] bg-[#201D19] p-5 shadow-[0_14px_40px_rgba(229,169,0,0.08)]">
                                <div>
                                    <h3 class="mb-3 text-sm font-bold uppercase tracking-[0.12em] text-[#B8B8B8]">Optional add-ons</h3>
                                    <div class="space-y-3 text-sm text-[#FFFFFF]">
                                        <label class="flex items-center justify-between rounded-2xl border border-[#333333] bg-[#1A1A1A] px-4 py-3 cursor-pointer">
                                            <span class="flex items-center gap-3">
                                                <input type="checkbox" name="addons[]" value="Extra Rice" data-price="20" class="addon-checkbox h-4 w-4 accent-[#FBAB18]" />
                                                <span>Extra Rice</span>
                                            </span>
                                            <span class="font-semibold text-[#FBAB18]">+&#8369;20</span>
                                        </label>

                                        <label class="flex items-center justify-between rounded-2xl border border-[#333333] bg-[#1A1A1A] px-4 py-3 cursor-pointer">
                                            <span class="flex items-center gap-3">
                                                <input type="checkbox" name="addons[]" value="Extra Sauce" data-price="10" class="addon-checkbox h-4 w-4 accent-[#FBAB18]" />
                                                <span>Extra Sauce</span>
                                            </span>
                                            <span class="font-semibold text-[#FBAB18]">+&#8369;10</span>
                                        </label>

                                        <label class="flex items-center justify-between rounded-2xl border border-[#333333] bg-[#1A1A1A] px-4 py-3 cursor-pointer">
                                            <span class="flex items-center gap-3">
                                                <input type="checkbox" name="addons[]" value="Soft Drink" data-price="30" class="addon-checkbox h-4 w-4 accent-[#FBAB18]" />
                                                <span>Soft Drink</span>
                                            </span>
                                            <span class="font-semibold text-[#FBAB18]">+&#8369;30</span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Quantity Controls -->
                            <div class="mt-6 flex items-center gap-4">
                                <span class="text-sm font-bold text-[#B8B8B8]">Quantity:</span>
                                <div class="flex items-center gap-3 rounded-full border border-[#333333] bg-[#1A1A1A] px-4 py-1.5">
                                    <button type="button" id="btn-decrease-qty" class="text-lg font-bold text-[#FFFFFF] hover:text-[#FBAB18]">-</button>
                                    <input type="number" id="input-quantity" name="quantity" value="1" min="1" readonly class="w-8 bg-transparent text-center font-bold text-[#FFFFFF] focus:outline-none" />
                                    <button type="button" id="btn-increase-qty" class="text-lg font-bold text-[#FFFFFF] hover:text-[#FBAB18]">+</button>
                                </div>
                            </div>

                            <div class="mt-8 flex flex-col gap-4 sm:flex-row">
                                <button type="submit" class="inline-flex flex-1 items-center justify-center rounded-full bg-[#FBAB18] px-7 py-4 text-base font-semibold text-[#FFFFFF] shadow-lg shadow-[#FBAB18]/25 transition hover:bg-[#FBAB18]">
                                    Add to Cart
                                </button>
                                <a href="{{ route('menu') }}" class="inline-flex items-center justify-center rounded-full border border-[#FBAB18] bg-[#201D19] px-7 py-4 text-base font-semibold text-[#FBAB18] transition hover:bg-[#201D19]">
                                    Continue Shopping
                                </a>
                            </div>
                        </form>
                    @else
                        <div class="mt-8 flex flex-col gap-4 sm:flex-row">
                            <button type="button" disabled class="inline-flex flex-1 cursor-not-allowed items-center justify-center rounded-full bg-[#333333] px-7 py-4 text-base font-semibold text-[#B8B8B8]">
                                Sold Out
                            </button>
                            <a href="{{ route('menu') }}" class="inline-flex items-center justify-center rounded-full border border-[#FBAB18] bg-[#201D19] px-7 py-4 text-base font-semibold text-[#FBAB18] transition hover:bg-[#201D19]">
                                Continue Shopping
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <!-- Dynamic Price Recalculation Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const basePrice = {{ (float) $product['price'] }};
            const addonCheckboxes = document.querySelectorAll('.addon-checkbox');
            const displayPrice = document.getElementById('display-total-price');
            const qtyInput = document.getElementById('input-quantity');
            const btnDecrease = document.getElementById('btn-decrease-qty');
            const btnIncrease = document.getElementById('btn-increase-qty');

            function updatePrice() {
                let addonsTotal = 0;
                addonCheckboxes.forEach(checkbox => {
                    if (checkbox.checked) {
                        addonsTotal += parseFloat(checkbox.dataset.price) || 0;
                    }
                });

                const quantity = parseInt(qtyInput.value) || 1;
                const grandTotal = (basePrice + addonsTotal) * quantity;

                displayPrice.textContent = grandTotal.toLocaleString('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            }

            // Recalculate when an add-on is toggled
            addonCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', updatePrice);
            });

            // Quantity event listeners
            if (btnDecrease && btnIncrease) {
                btnDecrease.addEventListener('click', function () {
                    let currentQty = parseInt(qtyInput.value) || 1;
                    if (currentQty > 1) {
                        qtyInput.value = currentQty - 1;
                        updatePrice();
                    }
                });

                btnIncrease.addEventListener('click', function () {
                    let currentQty = parseInt(qtyInput.value) || 1;
                    qtyInput.value = currentQty + 1;
                    updatePrice();
                });
            }
        });
    </script>
@endsection

