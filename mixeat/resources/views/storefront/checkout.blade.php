@extends('layouts.app')

@section('title', 'Checkout | MixEat')

@section('content')
    <section class="py-14">
        <div class="mixeat-shell px-4">
            <div class="mb-8 text-center md:text-left">
                <img src="{{ asset('images/mixeat.jpg') }}" alt="MixEat" class="mx-auto mb-4 h-16 w-16 rounded-2xl object-contain md:mx-0" />
                <p class="text-sm font-bold uppercase tracking-[0.18em] text-[#FBAB18]">Checkout</p>
                <h1 class="mt-2 text-4xl font-black text-[#FFFFFF]">Complete your order.</h1>
            </div>

            <form id="checkout-form" action="{{ route('orders.place') }}" method="POST">
            @csrf
            <div class="grid gap-8 lg:grid-cols-[minmax(0,1.35fr)_380px]">
                <div class="space-y-6">
                    <div class="rounded-[30px] border border-[#333333] bg-[#201D19] p-6 shadow-[0_14px_40px_rgba(229,169,0,0.10)]">
                        <h2 class="mb-5 text-2xl font-black text-[#FFFFFF]">1. Customer Information</h2>
                        <div class="grid gap-5 md:grid-cols-2">
                            <div>
                                <label class="mb-2 block text-sm font-semibold text-[#B8B8B8]">Full Name</label>
                                <input type="text" value="{{ auth()->user()?->name ?? '' }}" class="w-full rounded-2xl border border-[#333333] bg-[#1A1A1A] px-4 py-3 text-sm text-[#FFFFFF] focus:outline-none" />
                            </div>
                            <div>
                                <label class="mb-2 block text-sm font-semibold text-[#B8B8B8]">Email</label>
                                <input type="email" value="{{ auth()->user()?->email ?? '' }}" class="w-full rounded-2xl border border-[#333333] bg-[#1A1A1A] px-4 py-3 text-sm text-[#FFFFFF] focus:outline-none" />
                            </div>
                            <div class="md:col-span-2">
                                <label class="mb-2 block text-sm font-semibold text-[#B8B8B8]">Phone Number</label>
                                <input type="tel" value="{{ auth()->user()?->phone ?? '' }}" class="w-full rounded-2xl border border-[#333333] bg-[#1A1A1A] px-4 py-3 text-sm text-[#FFFFFF] focus:outline-none" />
                            </div>
                        </div>
                    </div>

                    <div class="rounded-[30px] border border-[#333333] bg-[#201D19] p-6 shadow-[0_14px_40px_rgba(229,169,0,0.10)]">
                        <h2 class="mb-5 text-2xl font-black text-[#FFFFFF]">2. Order Type</h2>
                        <div class="flex flex-col gap-4 md:flex-row">
                            <label class="flex items-center gap-3 rounded-2xl border border-[#333333] bg-[#1A1A1A] px-4 py-3 text-sm font-semibold text-[#FFFFFF]">
                                <input type="radio" name="order_type" value="Pickup" checked class="h-4 w-4 accent-[#FBAB18]" />
                                Pickup
                            </label>
                            <label class="flex items-center gap-3 rounded-2xl border border-[#333333] bg-[#1A1A1A] px-4 py-3 text-sm font-semibold text-[#FFFFFF]">
                                <input type="radio" name="order_type" value="Delivery" class="h-4 w-4 accent-[#FBAB18]" />
                                Delivery
                            </label>
                        </div>
                        <div id="pickup-schedule" class="mt-5">
                            <label for="pickup_at" class="mb-2 block text-sm font-semibold text-[#B8B8B8]">Pickup date and time</label>
                            <input id="pickup_at" name="pickup_at" type="datetime-local" required min="{{ now()->format('Y-m-d\TH:i') }}" value="{{ old('pickup_at') }}" class="w-full rounded-2xl border border-[#333333] bg-[#1A1A1A] px-4 py-3 text-sm text-[#FFFFFF] focus:outline-none md:max-w-sm">
                            @error('pickup_at')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div id="delivery-options" class="mt-5 hidden">
                            <p class="mb-3 text-sm font-semibold text-[#B8B8B8]">Choose a delivery option</p>
                            <div class="flex flex-col gap-3 md:flex-row">
                                <label id="in-house-option" class="flex items-start gap-3 rounded-2xl border border-[#333333] bg-[#1A1A1A] px-4 py-3 text-sm font-semibold text-[#FFFFFF]">
                                    <input type="radio" name="delivery_method" value="In-house" class="mt-0.5 h-4 w-4 accent-[#FBAB18]" @disabled($subtotal < $inHouseDeliveryMinimum)>
                                    <span>In-house delivery <span class="block text-xs font-normal text-[#777777]">Minimum purchase: &#8369;{{ number_format($inHouseDeliveryMinimum, 2) }}</span></span>
                                </label>
                                <label class="flex items-start gap-3 rounded-2xl border border-[#333333] bg-[#1A1A1A] px-4 py-3 text-sm font-semibold text-[#FFFFFF]">
                                    <input type="radio" name="delivery_method" value="Maxim" class="mt-0.5 h-4 w-4 accent-[#FBAB18]">
                                    <span>Deliver via Maxim <span class="block text-xs font-normal text-[#777777]">You will shoulder the Maxim delivery fee.</span></span>
                                </label>
                            </div>
                            <p id="delivery-method-error" class="mt-2 text-sm text-red-600">@error('delivery_method'){{ $message }}@enderror</p>
                        </div>
                    </div>

                    <div class="rounded-[30px] border border-[#333333] bg-[#201D19] p-6 shadow-[0_14px_40px_rgba(229,169,0,0.10)]">
                        <div class="flex items-center justify-between gap-4">
                            <h2 class="text-2xl font-black text-[#FFFFFF]">3. Branch</h2>
                            <a href="{{ route('branches') }}" class="text-sm font-semibold text-[#FBAB18]">Change Branch</a>
                        </div>
                        <div class="mt-5 rounded-[24px] bg-[#1A1A1A] p-5">
                            <h3 class="text-xl font-black text-[#FFFFFF]">{{ $selectedBranch['name'] }}</h3>
                            <p class="mt-2 text-sm text-[#B8B8B8]">{{ $selectedBranch['address'] }}</p>
                        </div>
                    </div>

                    <div class="rounded-[30px] border border-[#333333] bg-[#201D19] p-6 shadow-[0_14px_40px_rgba(229,169,0,0.10)]">
                        <h2 class="mb-5 text-2xl font-black text-[#FFFFFF]">4. Delivery Address</h2>
                        <div id="delivery-address-section" class="hidden">
                            <label for="delivery_address" class="mb-2 block text-sm font-semibold text-[#B8B8B8]">Full delivery address</label>
                            <textarea id="delivery_address" name="delivery_address" rows="3" maxlength="1000" placeholder="House/unit, street, barangay, and city" class="w-full rounded-2xl border border-[#333333] bg-[#1A1A1A] px-4 py-3 text-sm text-[#FFFFFF] focus:outline-none">{{ old('delivery_address') }}</textarea>
                            @error('delivery_address')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <p id="delivery-address-hint" class="text-sm text-[#777777]">Select Delivery to enter your delivery address.</p>
                    </div>

                    <div class="rounded-[30px] border border-[#333333] bg-[#201D19] p-6 shadow-[0_14px_40px_rgba(229,169,0,0.10)]">
                        <h2 class="mb-5 text-2xl font-black text-[#FFFFFF]">5. Payment Method</h2>
                        <div class="flex flex-col gap-4 md:flex-row">
                            <label id="cash-option" class="flex items-center gap-3 rounded-2xl border border-[#333333] bg-[#1A1A1A] px-4 py-3 text-sm font-semibold text-[#FFFFFF]">
                                <input id="cash-payment" type="radio" name="payment_method" value="Cash" class="h-4 w-4 accent-[#FBAB18]" @checked(old('payment_method', $subtotal > $cashPaymentMaximum ? 'QR Code' : 'Cash') === 'Cash') @disabled($subtotal > $cashPaymentMaximum) />
                                Cash
                            </label>
                            <label class="flex items-center gap-3 rounded-2xl border border-[#333333] bg-[#1A1A1A] px-4 py-3 text-sm font-semibold text-[#FFFFFF]">
                                <input id="qr-payment" type="radio" name="payment_method" value="QR Code" class="h-4 w-4 accent-[#FBAB18]" @checked(old('payment_method', $subtotal > $cashPaymentMaximum ? 'QR Code' : 'Cash') === 'QR Code') />
                                QR Code
                            </label>
                        </div>
                        <p id="payment-hint" class="mt-3 text-sm text-[#777777]">Cash is available for orders up to &#8369;{{ number_format($cashPaymentMaximum, 2) }}.</p>
                        @error('payment_method')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                        <div id="qr-payment-details" class="{{ old('payment_method', $subtotal > $cashPaymentMaximum ? 'QR Code' : 'Cash') === 'QR Code' ? '' : 'hidden' }} mt-5 rounded-2xl border border-[#333333] bg-[#1A1A1A] p-5 text-center">
                            <p class="mb-3 text-sm font-bold text-[#FFFFFF]">Scan this GCash QR code to pay</p>
                            <img src="{{ asset('images/Gcash.jpeg') }}" alt="MixEat GCash payment QR code" class="mx-auto max-h-80 w-auto max-w-full rounded-xl object-contain">
                            <p class="mt-3 text-xs text-[#B8B8B8]">Please verify the recipient and payment amount in GCash before confirming your payment.</p>
                            <div class="mt-5 text-left">
                                <label for="gcash_reference_number" class="mb-2 block text-sm font-semibold text-[#FFFFFF]">GCash reference number</label>
                                <input id="gcash_reference_number" name="gcash_reference_number" type="text" maxlength="100" value="{{ old('gcash_reference_number') }}" placeholder="Enter the reference number from your GCash receipt" class="w-full rounded-2xl border border-[#333333] bg-[#201D19] px-4 py-3 text-sm text-[#FFFFFF] focus:outline-none" @if (old('payment_method', $subtotal > $cashPaymentMaximum ? 'QR Code' : 'Cash') === 'QR Code') required @else disabled @endif>
                                <p class="mt-2 text-xs text-[#B8B8B8]">Complete your GCash payment first, then enter its reference number.</p>
                                @error('gcash_reference_number')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                <aside class="rounded-[30px] border border-[#333333] bg-[#201D19] p-6 shadow-[0_14px_40px_rgba(229,169,0,0.10)]">
                    <h2 class="text-2xl font-black text-[#FFFFFF]">6. Order Summary</h2>
                    <div class="mt-6 space-y-4">
                        @foreach ($items as $item)
                            <div class="flex items-start gap-4 border-b border-dashed border-[#333333] pb-4">
                                <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" class="h-16 w-16 rounded-2xl object-cover">
                                <div class="flex-1">
                                    <p class="font-bold text-[#FFFFFF]">{{ $item['name'] }}</p>
                                    
                                    {{-- Selected Add-ons Display --}}
                                    @if (!empty($item['addons']))
                                        <div class="mt-1 flex flex-wrap gap-1">
                                            @foreach ($item['addons'] as $addon)
                                                <span class="inline-block rounded-full bg-[#1A1A1A] border border-[#333333] px-2 py-0.5 text-[10px] font-bold text-[#FFFFFF]">
                                                    + {{ $addon }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif

                                    <p class="mt-1 text-sm text-[#B8B8B8]">Qty: {{ $item['quantity'] }}</p>
                                </div>
                                <div class="text-sm font-bold text-[#FFFFFF]">&#8369;{{ number_format($item['price'] * $item['quantity'], 2) }}</div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-6 space-y-4 text-[#B8B8B8]">
                        <div class="flex items-center justify-between">
                            <span>Subtotal</span>
                            <span class="font-semibold text-[#FFFFFF]">&#8369;{{ number_format($subtotal, 2) }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span>Delivery Fee</span>
                            <span id="delivery-fee-summary" class="font-semibold text-[#FFFFFF]">None</span>
                        </div>
                        <div class="flex items-center justify-between border-t border-dashed border-[#333333] pt-4">
                            <span class="text-lg font-bold text-[#FFFFFF]">Total</span>
                            <span class="text-2xl font-black text-[#FFFFFF]">&#8369;{{ number_format($total, 2) }}</span>
                        </div>
                    </div>

                    <button type="submit" class="mt-8 inline-flex w-full items-center justify-center rounded-full bg-[#FBAB18] px-6 py-3.5 text-base font-semibold text-[#FFFFFF] shadow-lg shadow-[#FBAB18]/25 transition hover:bg-[#FBAB18]">
                        Place Order
                    </button>
                </aside>
            </div>
            </form>
        </div>
    </section>
    <script>
        (() => {
            const form = document.getElementById('checkout-form');
            const orderTypeRadios = form.querySelectorAll('input[name="order_type"]');
            const deliveryMethodRadios = form.querySelectorAll('input[name="delivery_method"]');
            const pickupAt = document.getElementById('pickup_at');
            const deliveryAddress = document.getElementById('delivery_address');
            const subtotal = Number(@json($subtotal));
            const pickupSchedule = document.getElementById('pickup-schedule');
            const deliveryOptions = document.getElementById('delivery-options');
            const deliveryAddressSection = document.getElementById('delivery-address-section');
            const deliveryAddressHint = document.getElementById('delivery-address-hint');
            const cashPayment = document.getElementById('cash-payment');
            const qrPayment = document.getElementById('qr-payment');
            const cashOption = document.getElementById('cash-option');
            const paymentHint = document.getElementById('payment-hint');
            const qrPaymentDetails = document.getElementById('qr-payment-details');
            const gcashReferenceNumber = document.getElementById('gcash_reference_number');
            const deliveryFeeSummary = document.getElementById('delivery-fee-summary');
            const inHouseOption = form.querySelector('input[value="In-house"]');
            const inHouseLabel = document.getElementById('in-house-option');
            const inHouseMinimum = Number(@json($inHouseDeliveryMinimum));
            const cashMaximum = Number(@json($cashPaymentMaximum));
            const oldOrderType = @json(old('order_type', 'Pickup'));
            const oldDeliveryMethod = @json(old('delivery_method'));
            const oldPaymentMethod = @json(old('payment_method'));

            if (oldOrderType === 'Delivery') {
                form.querySelector('input[name="order_type"][value="Delivery"]').checked = true;
            }
            const previousDeliveryMethod = Array.from(deliveryMethodRadios).find((radio) => radio.value === oldDeliveryMethod);
            if (previousDeliveryMethod && !(previousDeliveryMethod.value === 'In-house' && subtotal < inHouseMinimum)) {
                previousDeliveryMethod.checked = true;
            }
            const previousPaymentMethod = Array.from(form.querySelectorAll('input[name="payment_method"]'))
                .find((radio) => radio.value === oldPaymentMethod);
            if (previousPaymentMethod) {
                previousPaymentMethod.checked = true;
            }

            const updateCheckout = () => {
                const isDelivery = form.querySelector('input[name="order_type"]:checked').value === 'Delivery';
                qrPaymentDetails.classList.toggle('hidden', !qrPayment.checked);
                gcashReferenceNumber.required = qrPayment.checked;
                gcashReferenceNumber.disabled = !qrPayment.checked;
                pickupSchedule.classList.toggle('hidden', isDelivery);
                pickupAt.required = !isDelivery;
                pickupAt.disabled = isDelivery;
                deliveryOptions.classList.toggle('hidden', !isDelivery);
                deliveryAddressSection.classList.toggle('hidden', !isDelivery);
                deliveryAddressHint.classList.toggle('hidden', isDelivery);
                deliveryAddress.required = isDelivery;
                deliveryAddress.disabled = !isDelivery;
                deliveryMethodRadios.forEach((radio) => {
                    radio.required = isDelivery;
                    radio.disabled = !isDelivery || (radio.value === 'In-house' && subtotal < inHouseMinimum);
                });
                inHouseLabel.classList.toggle('cursor-not-allowed', subtotal < inHouseMinimum);
                inHouseLabel.classList.toggle('opacity-50', subtotal < inHouseMinimum);

                const selectedDeliveryMethod = form.querySelector('input[name="delivery_method"]:checked')?.value;
                deliveryFeeSummary.textContent = !isDelivery
                    ? 'None'
                    : selectedDeliveryMethod === 'Maxim'
                        ? 'Paid directly to Maxim'
                        : 'Not included; confirmed by the branch';

                const cashRequired = subtotal > cashMaximum;
                cashPayment.disabled = cashRequired;
                cashOption.classList.toggle('cursor-not-allowed', cashRequired);
                cashOption.classList.toggle('bg-[#F3F3F3]', cashRequired);
                cashOption.classList.toggle('text-[#999999]', cashRequired);
                if (cashRequired) {
                    qrPayment.checked = true;
                    paymentHint.textContent = 'Orders above ₱1,500 must be paid using QR Code.';
                } else {
                    if (!cashPayment.checked && !qrPayment.checked) {
                        cashPayment.checked = true;
                    }
                    paymentHint.textContent = 'Cash is available for orders up to ₱1,500.';
                }
            };

            orderTypeRadios.forEach((radio) => radio.addEventListener('change', updateCheckout));
            deliveryMethodRadios.forEach((radio) => radio.addEventListener('change', updateCheckout));
            form.querySelectorAll('input[name="payment_method"]').forEach((radio) => radio.addEventListener('change', updateCheckout));
            updateCheckout();
        })();
    </script>
@endsection


