<?php $__env->startSection('title', 'Track Order | MixEat'); ?>

<?php $__env->startSection('content'); ?>
    <?php
        $trackingFlow = ['Pending', 'Preparing', 'Ready for Pickup', 'Completed'];
        $trackingCurrent = $order['status'];
        $trackingCurrentIndex = array_search($trackingCurrent, $trackingFlow, true);
        $trackingIsCancelled = in_array($trackingCurrent, ['Cancelled', 'Declined'], true);
    ?>

    <section class="order-tracking py-14">
        <div class="mixeat-shell px-4">
            <div class="mb-8">
                <p class="text-sm font-bold uppercase tracking-[0.18em] text-[#FBAB18]">Order Tracking</p>
                <h1 class="mt-2 text-4xl font-black text-[#FFFFFF]">Order #<?php echo e($order['id']); ?></h1>
                <p class="mt-2 text-lg text-[#B8B8B8]"><?php echo e($order['branch']); ?></p>
            </div>

            <div class="tracking-status-container rounded-[30px] border border-[#333333] bg-[#201D19] p-6 shadow-[0_14px_40px_rgba(0,0,0,0.35)]"
                data-live-url="<?php echo e(route('orders.status.live', ['id' => (int) str_replace('MX-', '', $order['id'])])); ?>">
                <div class="mb-8">
                    <p class="mb-3 text-sm font-bold uppercase tracking-[0.12em] text-[#B8B8B8]">Order Status</p>
                    <p id="order-live-update-status" class="mb-4 text-xs text-[#B8B8B8]" role="status">Status updates automatically.</p>
                    <div class="grid gap-4 md:grid-cols-6">
                        <?php $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $isCancelledStep = in_array($status, ['Cancelled', 'Declined'], true);
                                $statusIndex = array_search($status, $trackingFlow, true);

                                if ($status === $trackingCurrent) {
                                    $stepState = $isCancelledStep ? 'cancelled' : 'current';
                                } elseif (! $trackingIsCancelled && $trackingCurrentIndex !== false && $statusIndex !== false && $statusIndex < $trackingCurrentIndex) {
                                    $stepState = 'done';
                                } else {
                                    $stepState = 'upcoming';
                                }
                            ?>
                            <div class="flex flex-col items-center text-center">
                                <div data-tracking-step="<?php echo e($status); ?>" class="tracking-step tracking-step-<?php echo e($stepState); ?> flex h-12 w-12 items-center justify-center rounded-full border text-lg font-bold">
                                    <?php if($stepState === 'done'): ?>
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M5 13l4 4L19 7" /></svg>
                                    <?php elseif($stepState === 'current'): ?>
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="5" /></svg>
                                    <?php elseif($stepState === 'cancelled'): ?>
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M6 6l12 12M18 6L6 18" /></svg>
                                    <?php else: ?>
                                        <span class="text-[#B8B8B8]">&#8211;</span>
                                    <?php endif; ?>
                                </div>
                                <span data-tracking-step-label="<?php echo e($status); ?>" class="mt-2 text-[11px] font-semibold uppercase tracking-[0.08em] <?php echo e($stepState === 'current' || $stepState === 'cancelled' ? 'text-[#FFFFFF]' : 'text-[#B8B8B8]'); ?>"><?php echo e($status); ?></span>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>

                <div class="grid gap-6 lg:grid-cols-2">
                    <div class="tracking-inner-card rounded-[24px] border border-[#333333] bg-[#1A1A1A] p-5">
                        <h2 class="text-xl font-black text-[#FFFFFF]">Order Details</h2>
                        <div class="mt-4 space-y-3 text-sm text-[#B8B8B8]">
                            <div class="flex items-center justify-between"><span>Order Number</span><span class="font-semibold text-[#FFFFFF]"><?php echo e($order['id']); ?></span></div>
                            <div class="flex items-center justify-between"><span>Branch</span><span class="font-semibold text-[#FFFFFF]"><?php echo e($order['branch']); ?></span></div>
                            <div class="flex items-center justify-between"><span>Order Type</span><span class="font-semibold text-[#FFFFFF]"><?php echo e($order['type']); ?></span></div>
                            <?php if(!empty($order['pickup_at'])): ?>
                                <div class="flex items-center justify-between"><span>Pickup</span><span class="font-semibold text-[#FFFFFF]"><?php echo e($order['pickup_at']); ?></span></div>
                            <?php endif; ?>
                            <?php if(!empty($order['delivery_method'])): ?>
                                <div class="flex items-center justify-between"><span>Delivery Method</span><span class="font-semibold text-[#FFFFFF]"><?php echo e($order['delivery_method']); ?></span></div>
                                <div class="flex items-center justify-between gap-4"><span>Delivery Address</span><span class="text-right font-semibold text-[#FFFFFF]"><?php echo e($order['delivery_address']); ?></span></div>
                            <?php endif; ?>
                            <?php if(!empty($order['payment_method'])): ?>
                                <div class="flex items-center justify-between"><span>Payment</span><span class="font-semibold text-[#FFFFFF]"><?php echo e($order['payment_method']); ?></span></div>
                            <?php endif; ?>
                            <div class="flex items-center justify-between"><span>Status</span><span id="order-current-status" class="font-semibold text-[#FFFFFF]"><?php echo e($order['status']); ?></span></div>
                            <div class="flex items-center justify-between"><span>Total</span><span class="font-bold text-[#FBAB18]">&#8369;<?php echo e(number_format($order['total'], 2)); ?></span></div>
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
<?php $__env->stopSection(); ?>

<?php if(auth()->guard()->check()): ?>
    <?php $__env->startPush('scripts'); ?>
    <script>
        (() => {
            const tracker = document.querySelector('.tracking-status-container');
            const statusText = document.getElementById('order-current-status');
            const updateStatus = document.getElementById('order-live-update-status');
            if (!tracker || !statusText || !updateStatus) return;

            const flow = ['Pending', 'Preparing', 'Ready for Pickup', 'Completed'];
            let polling = false;
            const renderStatus = (status) => {
                statusText.textContent = status;
                const currentIndex = flow.indexOf(status);
                const isCancelled = ['Cancelled', 'Declined'].includes(status);

                tracker.querySelectorAll('[data-tracking-step]').forEach((step) => {
                    const stepStatus = step.dataset.trackingStep;
                    const stepIndex = flow.indexOf(stepStatus);
                    let state = 'upcoming';
                    if (stepStatus === status) state = isCancelled ? 'cancelled' : 'current';
                    else if (!isCancelled && currentIndex >= 0 && stepIndex >= 0 && stepIndex < currentIndex) state = 'done';

                    step.classList.remove('tracking-step-current', 'tracking-step-done', 'tracking-step-upcoming', 'tracking-step-cancelled');
                    step.classList.add(`tracking-step-${state}`);
                    step.textContent = state === 'done' ? '✓' : state === 'cancelled' ? '×' : state === 'current' ? '•' : '–';

                    const label = tracker.querySelector(`[data-tracking-step-label="${CSS.escape(stepStatus)}"]`);
                    if (label) {
                        label.classList.toggle('text-[#FFFFFF]', state === 'current' || state === 'cancelled');
                        label.classList.toggle('text-[#B8B8B8]', state !== 'current' && state !== 'cancelled');
                    }
                });
            };

            const refreshStatus = async () => {
                if (polling) return;
                polling = true;
                try {
                    const response = await fetch(tracker.dataset.liveUrl, {
                        headers: { Accept: 'application/json' },
                        credentials: 'same-origin',
                        cache: 'no-store',
                    });
                    if (!response.ok) throw new Error(`Order status request failed (${response.status})`);
                    const order = await response.json();
                    renderStatus(order.status);
                    updateStatus.textContent = 'Status updates automatically.';
                } catch (error) {
                    console.error('Unable to refresh order status.', error);
                    updateStatus.textContent = 'Live updates temporarily unavailable. Retrying automatically.';
                } finally {
                    polling = false;
                }
            };

            refreshStatus();
            const interval = window.setInterval(refreshStatus, 3000);
            document.addEventListener('visibilitychange', function(){
                if (!document.hidden) refreshStatus();
            });
            window.addEventListener('beforeunload', function(){ window.clearInterval(interval); });
        })();
    </script>
    <?php $__env->stopPush(); ?>
<?php endif; ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mixeat\mixeat\resources\views\storefront\order-detail.blade.php ENDPATH**/ ?>