

<?php $__env->startSection('title', 'Checkout | MixEat'); ?>

<?php $__env->startSection('content'); ?>
    <?php ($orderingOpen = app(\App\Services\OrderingHours::class)->isOpen()); ?>
    <section class="py-14">
        <div class="mixeat-shell px-4">
            <div class="mb-8 text-center md:text-left">
                <img src="<?php echo e(asset('images/mixeat.jpg')); ?>" alt="MixEat" class="mx-auto mb-4 h-16 w-16 rounded-2xl object-contain md:mx-0" />
                <p class="text-sm font-bold uppercase tracking-[0.18em] text-[#FBAB18]">Checkout</p>
                <h1 class="mt-2 text-4xl font-black text-[#FFFFFF]">Complete your order.</h1>
            </div>

            <?php $__errorArgs = ['ordering_hours'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <div class="mb-6 rounded-2xl border border-[#FBAB18] bg-[#201D19] px-4 py-3 text-sm font-semibold text-[#FFC247]" role="alert"><?php echo e($message); ?></div>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

            <form id="checkout-form" action="<?php echo e(route('orders.place')); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <div class="grid gap-8 lg:grid-cols-[minmax(0,1.35fr)_380px]">
                <div class="space-y-6">
                    <div class="rounded-[30px] border border-[#333333] bg-[#201D19] p-6 shadow-[0_14px_40px_rgba(229,169,0,0.10)]">
                        <h2 class="mb-5 text-2xl font-black text-[#FFFFFF]">1. Customer Information</h2>
                        <div class="grid gap-5 md:grid-cols-2">
                            <div>
                                <label class="mb-2 block text-sm font-semibold text-[#B8B8B8]">Full Name</label>
                                <input type="text" value="<?php echo e(auth()->user()?->name ?? ''); ?>" class="w-full rounded-2xl border border-[#333333] bg-[#1A1A1A] px-4 py-3 text-sm text-[#FFFFFF] focus:outline-none" />
                            </div>
                            <div>
                                <label class="mb-2 block text-sm font-semibold text-[#B8B8B8]">Email</label>
                                <input type="email" value="<?php echo e(auth()->user()?->email ?? ''); ?>" class="w-full rounded-2xl border border-[#333333] bg-[#1A1A1A] px-4 py-3 text-sm text-[#FFFFFF] focus:outline-none" />
                            </div>
                            <div class="md:col-span-2">
                                <label class="mb-2 block text-sm font-semibold text-[#B8B8B8]">Phone Number</label>
                                <input type="tel" value="<?php echo e(auth()->user()?->phone ?? ''); ?>" class="w-full rounded-2xl border border-[#333333] bg-[#1A1A1A] px-4 py-3 text-sm text-[#FFFFFF] focus:outline-none" />
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
                            <input id="pickup_at" name="pickup_at" type="datetime-local" required min="<?php echo e(now()->format('Y-m-d\TH:i')); ?>" value="<?php echo e(old('pickup_at')); ?>" class="w-full rounded-2xl border border-[#333333] bg-[#1A1A1A] px-4 py-3 text-sm text-[#FFFFFF] focus:outline-none md:max-w-sm">
                            <?php $__errorArgs = ['pickup_at'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-2 text-sm text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>

                    <div class="rounded-[30px] border border-[#333333] bg-[#201D19] p-6 shadow-[0_14px_40px_rgba(229,169,0,0.10)]">
                        <div class="flex items-center justify-between gap-4">
                            <h2 class="text-2xl font-black text-[#FFFFFF]">3. Branch</h2>
                        </div>
                        <div class="mt-5 rounded-[24px] bg-[#1A1A1A] p-5">
                            <p class="text-xs font-bold uppercase tracking-wider text-[#FBAB18]">Default order branch</p>
                            <h3 class="mt-1 text-xl font-black text-[#FFFFFF]"><?php echo e($defaultOrderBranch->name); ?></h3>
                            <p class="mt-2 text-sm text-[#B8B8B8]"><?php echo e($defaultOrderBranch->address); ?></p>
                            <p class="mt-3 text-sm text-[#B8B8B8]"><strong class="text-white">Opening Hours:</strong> <?php echo e($defaultOrderBranch->opening_hours); ?></p>
                        </div>
                    </div>

                    <div class="rounded-[30px] border border-[#333333] bg-[#201D19] p-6 shadow-[0_14px_40px_rgba(229,169,0,0.10)]">
                        <h2 class="mb-5 text-2xl font-black text-[#FFFFFF]">4. Delivery Address</h2>
                        <div id="delivery-address-section" class="hidden">
                            <label for="delivery_address" class="mb-2 block text-sm font-semibold text-[#B8B8B8]">Full delivery address</label>
                            <textarea id="delivery_address" name="delivery_address" rows="3" maxlength="1000" placeholder="House/unit, street, barangay, and city" class="w-full rounded-2xl border border-[#333333] bg-[#1A1A1A] px-4 py-3 text-sm text-[#FFFFFF] focus:outline-none"><?php echo e(old('delivery_address')); ?></textarea>
                            <?php $__errorArgs = ['delivery_address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-2 text-sm text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <p id="delivery-address-hint" class="text-sm text-[#777777]">Select Delivery to enter your delivery address.</p>
                    </div>

                    <div class="rounded-[30px] border border-[#333333] bg-[#201D19] p-6 shadow-[0_14px_40px_rgba(229,169,0,0.10)]">
                        <h2 class="mb-5 text-2xl font-black text-[#FFFFFF]">5. Payment Method</h2>
                        <div class="flex flex-col gap-4 md:flex-row">
                            <label id="cash-option" class="flex items-center gap-3 rounded-2xl border border-[#333333] bg-[#1A1A1A] px-4 py-3 text-sm font-semibold text-[#FFFFFF]">
                                <input id="cash-payment" type="radio" name="payment_method" value="Cash" class="h-4 w-4 accent-[#FBAB18]" <?php if(old('payment_method', $subtotal > $cashPaymentMaximum ? 'QR Code' : 'Cash') === 'Cash'): echo 'checked'; endif; ?> <?php if($subtotal > $cashPaymentMaximum): echo 'disabled'; endif; ?> />
                                Cash
                            </label>
                            <label class="flex items-center gap-3 rounded-2xl border border-[#333333] bg-[#1A1A1A] px-4 py-3 text-sm font-semibold text-[#FFFFFF]">
                                <input id="qr-payment" type="radio" name="payment_method" value="QR Code" class="h-4 w-4 accent-[#FBAB18]" <?php if(old('payment_method', $subtotal > $cashPaymentMaximum ? 'QR Code' : 'Cash') === 'QR Code'): echo 'checked'; endif; ?> />
                                QR Code
                            </label>
                        </div>
                        <p id="payment-hint" class="mt-3 text-sm text-[#777777]">Cash is available for orders up to &#8369;<?php echo e(number_format($cashPaymentMaximum, 2)); ?>.</p>
                        <?php $__errorArgs = ['payment_method'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-2 text-sm text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        <div id="qr-payment-details" class="<?php echo e(old('payment_method', $subtotal > $cashPaymentMaximum ? 'QR Code' : 'Cash') === 'QR Code' ? '' : 'hidden'); ?> mt-5 rounded-2xl border border-[#333333] bg-[#1A1A1A] p-5 text-center">
                            <p class="mb-3 text-sm font-bold text-[#FFFFFF]">Scan this GCash QR code to pay</p>
                            <img src="<?php echo e(asset('images/mixeat qr.jpeg')); ?>" alt="MixEat GCash payment QR code" class="mx-auto max-h-80 w-auto max-w-full rounded-xl object-contain">
                            <p class="mt-3 text-xs text-[#B8B8B8]">Please verify the recipient and payment amount in GCash before confirming your payment.</p>
                            <div class="mt-5 text-left">
                                <label for="gcash_reference_number" class="mb-2 block text-sm font-semibold text-[#FFFFFF]">GCash reference number</label>
                                <input id="gcash_reference_number" name="gcash_reference_number" type="text" maxlength="100" value="<?php echo e(old('gcash_reference_number')); ?>" placeholder="Enter the reference number from your GCash receipt" class="w-full rounded-2xl border border-[#333333] bg-[#201D19] px-4 py-3 text-sm text-[#FFFFFF] focus:outline-none" <?php if(old('payment_method', $subtotal > $cashPaymentMaximum ? 'QR Code' : 'Cash') === 'QR Code'): ?> required <?php else: ?> disabled <?php endif; ?>>
                                <p class="mt-2 text-xs text-[#B8B8B8]">Complete your GCash payment first, then enter its reference number.</p>
                                <?php $__errorArgs = ['gcash_reference_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-2 text-sm text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>
                    </div>
                </div>

                <aside class="rounded-[30px] border border-[#333333] bg-[#201D19] p-6 shadow-[0_14px_40px_rgba(229,169,0,0.10)]">
                    <h2 class="text-2xl font-black text-[#FFFFFF]">Order Summary</h2>
                    <div class="mt-6 space-y-4">
                        <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="flex items-start gap-4 border-b border-dashed border-[#333333] pb-4">
                                <img src="<?php echo e($item['image']); ?>" alt="<?php echo e($item['name']); ?>" class="h-16 w-16 rounded-2xl object-cover">
                                <div class="flex-1">
                                    <p class="font-bold text-[#FFFFFF]"><?php echo e($item['name']); ?></p>
                                    
                                    
                                    <?php if(!empty($item['addons'])): ?>
                                        <div class="mt-1 flex flex-wrap gap-1">
                                            <?php $__currentLoopData = $item['addons']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $addon): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <span class="inline-block rounded-full bg-[#1A1A1A] border border-[#333333] px-2 py-0.5 text-[10px] font-bold text-[#FFFFFF]">
                                                    + <?php echo e($addon); ?>

                                                </span>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>
                                    <?php endif; ?>

                                    <p class="mt-1 text-sm text-[#B8B8B8]">Qty: <?php echo e($item['quantity']); ?></p>
                                </div>
                                <div class="text-sm font-bold text-[#FFFFFF]">&#8369;<?php echo e(number_format($item['price'] * $item['quantity'], 2)); ?></div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>

                    <div class="mt-6 space-y-4 text-[#B8B8B8]">
                        <div class="flex items-center justify-between">
                            <span>Subtotal</span>
                            <span class="font-semibold text-[#FFFFFF]">&#8369;<?php echo e(number_format($subtotal, 2)); ?></span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span>Delivery Fee</span>
                            <span id="delivery-fee-summary" class="font-semibold text-[#FFFFFF]">None</span>
                        </div>
                        <div class="flex items-center justify-between border-t border-dashed border-[#333333] pt-4">
                            <span class="text-lg font-bold text-[#FFFFFF]">Total</span>
                            <span class="text-2xl font-black text-[#FFFFFF]">&#8369;<?php echo e(number_format($total, 2)); ?></span>
                        </div>
                    </div>

                    <button type="submit" data-ordering-action="place-order" data-ordering-open-label="Place Order" <?php if(!$orderingOpen): echo 'disabled'; endif; ?> class="mt-8 inline-flex w-full items-center justify-center rounded-full bg-[#FBAB18] px-6 py-3.5 text-base font-semibold text-[#FFFFFF] shadow-lg shadow-[#FBAB18]/25 transition hover:bg-[#FBAB18] disabled:cursor-not-allowed disabled:bg-[#333333] disabled:text-[#B8B8B8]">
                        <?php echo e($orderingOpen ? 'Place Order' : 'Online Ordering Closed'); ?>

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
            const pickupAt = document.getElementById('pickup_at');
            const deliveryAddress = document.getElementById('delivery_address');
            const subtotal = Number(<?php echo json_encode($subtotal, 15, 512) ?>);
            const pickupSchedule = document.getElementById('pickup-schedule');
            const deliveryAddressSection = document.getElementById('delivery-address-section');
            const deliveryAddressHint = document.getElementById('delivery-address-hint');
            const cashPayment = document.getElementById('cash-payment');
            const qrPayment = document.getElementById('qr-payment');
            const cashOption = document.getElementById('cash-option');
            const paymentHint = document.getElementById('payment-hint');
            const qrPaymentDetails = document.getElementById('qr-payment-details');
            const gcashReferenceNumber = document.getElementById('gcash_reference_number');
            const deliveryFeeSummary = document.getElementById('delivery-fee-summary');
            const cashMaximum = Number(<?php echo json_encode($cashPaymentMaximum, 15, 512) ?>);
            const oldOrderType = <?php echo json_encode(old('order_type', 'Pickup'), 512) ?>;
            const oldPaymentMethod = <?php echo json_encode(old('payment_method'), 15, 512) ?>;

            if (oldOrderType === 'Delivery') {
                form.querySelector('input[name="order_type"][value="Delivery"]').checked = true;
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
                deliveryAddressSection.classList.toggle('hidden', !isDelivery);
                deliveryAddressHint.classList.toggle('hidden', isDelivery);
                deliveryAddress.required = isDelivery;
                deliveryAddress.disabled = !isDelivery;
                deliveryFeeSummary.textContent = !isDelivery
                    ? 'None'
                    : 'In-house delivery; fee confirmed by the branch';

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
            form.querySelectorAll('input[name="payment_method"]').forEach((radio) => radio.addEventListener('change', updateCheckout));
            updateCheckout();
        })();
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mixeat\mixeat\resources\views\storefront\checkout.blade.php ENDPATH**/ ?>