

<?php $__env->startSection('title', 'Supervisor Dashboard | MixEat'); ?>

<?php $__env->startSection('content'); ?>
<section class="py-10">
    <div class="supervisor-dashboard">
        <!-- Header with Export Button -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.18em] text-[#FBAB18]">SUPERVISOR</p>
                <h1 class="mt-1 text-3xl font-black text-[#FFFFFF] md:text-4xl">Supervisor Dashboard</h1>
                <p class="mt-1 text-sm font-medium text-[#B8B8B8]">Managing Branch: <strong class="text-[#FFFFFF]"><?php echo e($branch->name); ?></strong></p>
            </div>

            <!-- Export Daily Sales Report Excel Button -->
            <div class="flex items-center gap-3 self-start sm:self-auto">
                <button id="enable-sound-btn" type="button" title="Click once to enable order sounds for this dashboard session." class="rounded-full border border-[#333333] bg-[#1A1A1A] px-3 py-2 text-xs font-semibold text-[#FFFFFF]">Enable Sound</button>
                <div class="relative" id="notif-bell-container">
                    <button id="notif-bell-btn" type="button" class="relative inline-flex items-center justify-center rounded-full bg-[#201D19] border border-[#333333] p-2.5 text-[#FFFFFF]">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        <span id="notif-count" class="absolute -top-0.5 -right-0.5 hidden min-w-[18px] h-[18px] items-center justify-center rounded-full bg-red-600 text-[10px] font-bold text-white leading-none px-1">0</span>
                    </button>
                    <div id="notif-dropdown" class="hidden absolute right-0 mt-2 w-80 max-h-96 overflow-auto rounded-2xl border border-[#333333] bg-[#201D19] shadow-lg z-50">
                        <div class="px-4 py-3 border-b border-[#333333] flex items-center justify-between">
                            <p class="text-sm font-bold text-[#FFFFFF]">Notifications</p>
                            <button id="mark-all-read" type="button" class="text-xs text-[#FBAB18] hover:underline">Mark all as read</button>
                        </div>
                        <div id="notif-list" class="divide-y divide-[#333333]"></div>
                        <div id="notif-empty" class="px-4 py-4 text-center text-sm text-[#B8B8B8] hidden">No new notifications</div>
                    </div>
                </div>
                <a href="<?php echo e(route('admin.supervisors.export-daily-sales')); ?>" class="inline-flex items-center gap-2 rounded-full bg-[#107C41] px-5 py-2.5 text-xs font-bold text-white shadow-md transition hover:bg-[#0E6C38]">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Export Daily Sales (Excel)
                </a>
            </div>
        </div>

        <?php if(session('admin_status')): ?>
            <div class="mb-6 rounded-2xl bg-[#201D19] px-4 py-3 font-semibold text-[#FFFFFF]">
                <?php echo e(session('admin_status')); ?>

            </div>
        <?php endif; ?>

        <!-- Responsive Daily Inventory & Revenue Metrics Grid -->
       <section class="supervisor-stats">
            <!-- Today's Orders -->
            <div class="rounded-[20px] border border-[#333333] bg-[#1A1A1A] p-5 shadow-sm h-full w-full">
                <p class="text-xs font-bold uppercase tracking-wider text-[#777777]">
                    TODAY'S ORDERS
                </p>
                <h3 id="live-total-orders" class="mt-2 text-2xl font-black text-[#FFFFFF]">
                    <?php echo e($dailySummary['total_orders']); ?>

                </h3>
            </div>

            <!-- In Preparation -->
            <div class="rounded-[20px] border border-[#333333] bg-[#FFF3CD] p-5 shadow-sm h-full w-full">
                <p class="text-xs font-bold uppercase tracking-wider text-[#856404]">
                    IN PREPARATION
                </p>
                <h3 id="live-preparing-orders" class="mt-2 text-2xl font-black text-[#856404]">
                    <?php echo e($dailySummary['preparing_count']); ?>

                </h3>
            </div>

            <!-- Ready for Pickup -->
            <div class="rounded-[20px] border border-[#C3E6CB] bg-[#E6F4EA] p-5 shadow-sm h-full w-full">
                <p class="text-xs font-bold uppercase tracking-wider text-[#1E8E3E]">
                    READY FOR PICKUP
                </p>
                <h3 id="live-ready-orders" class="mt-2 text-2xl font-black text-[#1E8E3E]">
                    <?php echo e($dailySummary['ready_count']); ?>

                </h3>
            </div>

            <!-- Estimated Revenue -->
            <div class="rounded-[20px] border border-[#333333] bg-[#201D19] p-5 shadow-sm h-full w-full">
                <p class="text-xs font-bold uppercase tracking-wider text-[#777777]">
                    ESTIMATED REVENUE
                </p>
                <h3 id="live-revenue" class="mt-2 text-2xl font-black text-[#FFFFFF]">
                    &#8369;<?php echo e(number_format($dailySummary['total_revenue'], 2)); ?>

                </h3>
            </div>
        </section>

        <!-- Branch Orders Section -->
        <div class="supervisor-orders rounded-[28px] border border-[#333333] bg-[#201D19] p-6 shadow-sm">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-xl font-bold text-[#FFFFFF]">Branch Orders</h2>
                <div class="flex items-center gap-2">
                    <label for="receipt-paper-width" class="text-xs font-semibold text-[#B8B8B8]">Receipt width</label>
                    <select id="receipt-paper-width" class="rounded-lg border border-[#333333] bg-[#111111] px-2 py-1 text-xs font-semibold text-[#FFFFFF]">
                        <option value="80">80mm</option>
                        <option value="58">58mm</option>
                    </select>
                    <span class="rounded-full bg-[#FFF3CD] px-3 py-1 text-xs font-bold text-[#856404]">Pending: <span id="live-pending-orders"><?php echo e($dailySummary['pending_count']); ?></span></span>
                    <span id="live-orders-status" role="status" class="rounded-full bg-[#FBAB18] px-3 py-1 text-xs font-bold text-[#111111]">Live updates</span>
                </div>
            </div>

            <div id="receipt-print-notice" role="status" class="mb-4 hidden rounded-xl border border-[#FBAB18] bg-[#2A2418] px-4 py-3 text-sm text-[#FFFFFF]"></div>
            <div id="new-order-alert" role="status" class="mb-4 hidden rounded-xl border border-[#FBAB18] bg-[#2A2418] px-4 py-3 text-sm font-bold text-[#FFFFFF]">
                <span id="new-order-alert-text"></span>
            </div>

            <div class="overflow-x-auto">
                <table class="branch-orders-table w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-[#333333] text-xs font-bold uppercase tracking-wider text-[#C5C5C5]">
                            <th class="py-3 px-4">Order #</th>
                            <th class="py-3 px-4">Type</th>
                            <th class="py-3 px-4">Total</th>
                            <th class="py-3 px-4 text-center">Status</th>
                            <th class="py-3 px-4 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody id="live-orders-table" class="divide-y divide-[#333333] text-sm font-medium">
                        <?php echo $__env->make('admin.supervisors.partials.order-rows', ['orders' => $orders], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Menu Availability Section -->
        <div class="supervisor-menu rounded-[28px] border border-[#333333] bg-[#201D19] p-6 shadow-sm">
            <h2 class="mb-4 text-xl font-bold text-[#FFFFFF]">Menu Availability</h2>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-[#333333] text-xs font-bold uppercase tracking-wider text-[#777777]">
                            <th class="py-3 px-4">Item Name</th>
                            <th class="py-3 px-4">Price</th>
                            <th class="py-3 px-4 text-center">Status</th>
                            <th class="py-3 px-4 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#333333]/60 text-sm font-medium">
                        <?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php
                                $branchPivot = $product->branches->first()?->pivot;
                                $isAvailable = $branchPivot ? (bool) $branchPivot->available : true;
                            ?>
                            <tr>
                                <td class="py-4 px-4 font-bold text-[#FFFFFF]"><?php echo e($product->name); ?></td>
                                <td class="py-4 px-4 text-[#FFFFFF]">&#8369;<?php echo e(number_format($product->price, 2)); ?></td>
                                <td class="py-4 px-4 text-center">
                                    <?php if($isAvailable): ?>
                                        <span class="inline-block rounded-full bg-[#E6F4EA] px-3 py-1 text-xs font-bold text-[#1E8E3E]">Available</span>
                                    <?php else: ?>
                                        <span class="inline-block rounded-full bg-[#FCE8E6] px-3 py-1 text-xs font-bold text-[#D93025]">Sold Out</span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-4 px-4 text-right">
                                    <form action="<?php echo e(route('admin.supervisors.toggle', $product->id)); ?>" method="POST" class="inline-block">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('PATCH'); ?>
                                        <input type="hidden" name="available" value="<?php echo e($isAvailable ? '0' : '1'); ?>">
                                        <button type="submit" class="text-xs font-bold <?php echo e($isAvailable ? 'text-[#DC3545] hover:underline' : 'text-[#28A745] hover:underline'); ?>">
                                            Set as <?php echo e($isAvailable ? 'Sold Out' : 'Available'); ?>

                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="4" class="py-6 text-center text-sm font-medium text-[#777777]">No menu items found in the system.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<audio id="order-alert-sound" src="<?php echo e(asset('sounds/notification.wav')); ?>" preload="auto"></audio>
<script>
(function(){
    const notifBellBtn = document.getElementById('notif-bell-btn');
    const notifDropdown = document.getElementById('notif-dropdown');
    const notifCount = document.getElementById('notif-count');
    const notifList = document.getElementById('notif-list');
    const notifEmpty = document.getElementById('notif-empty');
    const developmentLogging = <?php echo json_encode(app()->isLocal(), 15, 512) ?>;
    let soundActivated = false;
    let audioBlocked = false;
    let alarmWasActive = false;
    const enableSoundBtn = document.getElementById('enable-sound-btn');
    const alertSound = document.getElementById('order-alert-sound') || (function(){
        const a = document.createElement('audio');
        a.id = 'order-alert-sound';
        a.src = "<?php echo e(asset('sounds/notification.wav')); ?>";
        a.preload = 'auto';
        document.body.appendChild(a);
        return a;
    })();

    const pendingOrders = new Map();
    function setSoundEnabled(enabled, message){
        if(enabled){
            soundActivated = true;
            audioBlocked = false;
        }
        if(!enableSoundBtn) return;
        enableSoundBtn.textContent = enabled ? 'Sound Enabled' : 'Enable Sound';
        enableSoundBtn.title = message || (enabled
            ? 'Order sounds are enabled for this dashboard session.'
            : 'Click once to enable order sounds for this dashboard session.');
    }

    function handlePlaybackFailure(error){
        audioBlocked = true;
        soundActivated = false;
        alarmWasActive = false;
        alertSound.pause();
        alertSound.currentTime = 0;
        setSoundEnabled(false, 'Your browser blocked the chime. Click Enable Sound to allow audio.');
        console.error('Chime playback failed. Click Enable Sound to allow audio.', error);
        if(developmentLogging) console.debug('[Supervisor orders] Audio playback failed.', error);
    }

    function syncAlarmWithPendingOrders(){
        if(!pendingOrders.size){
            if(alarmWasActive && developmentLogging) console.debug('[Supervisor orders] Alarm stopped; no pending orders remain.');
            alarmWasActive = false;
            alertSound.pause();
            alertSound.currentTime = 0;
            alertSound.loop = false;
            return;
        }
        if(audioBlocked || (!alertSound.paused && alertSound.loop)) return;

        alertSound.loop = true;
        alertSound.currentTime = 0;
        if(developmentLogging) console.debug('[Supervisor orders] Alarm start requested.', { pendingOrderCount: pendingOrders.size });
        try {
            const playback = alertSound.play();
            if(playback && typeof playback.then === 'function'){
                playback.then(function(){
                    if(!pendingOrders.size){
                        syncAlarmWithPendingOrders();
                        return;
                    }
                    soundActivated = true;
                    alarmWasActive = true;
                    setSoundEnabled(true);
                    if(developmentLogging) console.debug('[Supervisor orders] Alarm started.', { pendingOrderCount: pendingOrders.size });
                }).catch(handlePlaybackFailure);
            } else {
                soundActivated = true;
                alarmWasActive = true;
                setSoundEnabled(true);
                if(developmentLogging) console.debug('[Supervisor orders] Alarm started.', { pendingOrderCount: pendingOrders.size });
            }
        } catch(error){
            handlePlaybackFailure(error);
        }
    }

    alertSound.addEventListener('error', function(){
        if(!pendingOrders.size) return;
        handlePlaybackFailure(alertSound.error || new Error('Notification sound could not be loaded.'));
    });

    if(enableSoundBtn){
        enableSoundBtn.addEventListener('click', function(){
            if(soundActivated && !audioBlocked) return;

            alertSound.muted = true;
            try {
                const unlock = alertSound.play();
                if(unlock && typeof unlock.then === 'function'){
                    unlock.then(function(){
                        alertSound.pause();
                        alertSound.currentTime = 0;
                        alertSound.muted = false;
                        setSoundEnabled(true);
                        syncAlarmWithPendingOrders();
                    }).catch(function(error){
                        alertSound.muted = false;
                        handlePlaybackFailure(error);
                    });
                } else {
                    alertSound.pause();
                    alertSound.currentTime = 0;
                    alertSound.muted = false;
                    setSoundEnabled(true);
                    syncAlarmWithPendingOrders();
                }
            } catch(error){
                alertSound.muted = false;
                handlePlaybackFailure(error);
            }
        });
    }

    function updatePendingOrderAlert(){
        const alert = document.getElementById('new-order-alert');
        const alertText = document.getElementById('new-order-alert-text');
        const pending = Array.from(pendingOrders.values());
        if(alert && alertText){
            if(pending.length){
                const orderNumbers = pending.map(function(order){ return order.number; }).filter(Boolean);
                alertText.textContent = pending.length === 1
                    ? `New order received: ${orderNumbers[0] || 'Order awaiting acceptance'}`
                    : `${pending.length} orders are waiting for acceptance: ${orderNumbers.join(', ')}`;
                alert.classList.remove('hidden');
            } else {
                alert.classList.add('hidden');
            }
        }
    }

    document.addEventListener('supervisor:live-state', function(event){
        const data = event.detail || {};
        pendingOrders.clear();
        (Array.isArray(data.pending_orders) ? data.pending_orders : []).forEach(function(order){
            pendingOrders.set(String(order.id), { number: order.number || '' });
        });
        updatePendingOrderAlert();
        syncAlarmWithPendingOrders();
        renderNotifications(data.notification_state || { count: 0, notifications: [] });
    });
    if(notifBellBtn){
        notifBellBtn.addEventListener('click', function(e){ e.stopPropagation(); notifDropdown.classList.toggle('hidden'); });
        document.addEventListener('click', function(){ notifDropdown.classList.add('hidden'); });
        notifDropdown.addEventListener('click', function(e){ e.stopPropagation(); });
    }

    notifList.addEventListener('click', async function(event){
        const link = event.target.closest('a[data-view-order]');
        if(!link) return;

        event.preventDefault();
        try {
            const response = await fetch(link.href, {
                headers: { Accept: 'application/json' },
                credentials: 'same-origin',
                cache: 'no-store',
            });
            if(!response.ok) throw new Error(`View order request failed (${response.status})`);
            document.dispatchEvent(new CustomEvent('supervisor:refresh-orders'));
        } catch(error) {
            console.error('Unable to view order notification.', error);
            window.location.assign(link.href);
        }
    });

    function renderNotifications(data){
        const notifications = Array.isArray(data.notifications) ? data.notifications : [];

        const count = Number(data.count) || 0;
        notifCount.textContent = count > 9 ? '9+' : String(count);
        notifCount.classList.toggle('hidden', count === 0);
        notifCount.classList.toggle('flex', count > 0);
        notifList.replaceChildren();
        notifEmpty.classList.toggle('hidden', notifications.length > 0);

        notifications.forEach(function(notification){
            const details = notification.data || {};
            const item = document.createElement('div');
            item.className = 'px-4 py-3 hover:bg-[#2a2720]';

            const title = document.createElement('p');
            title.className = 'text-sm font-bold text-[#FFFFFF]';
            title.textContent = details.message || 'New order received';
            item.appendChild(title);

            const order = document.createElement('p');
            order.className = 'mt-1 text-xs text-[#B8B8B8]';
            order.textContent = `Order ${details.order_number || ''} · ${details.branch_name || ''}`;
            item.appendChild(order);

            const total = document.createElement('p');
            total.className = 'mt-1 text-xs text-[#B8B8B8]';
            total.textContent = `Total: ₱${(Number(details.total) || 0).toFixed(2)}`;
            item.appendChild(total);

            const createdAt = document.createElement('p');
            createdAt.className = 'mt-1 text-xs text-[#777777]';
            createdAt.textContent = notification.created_at || '';
            item.appendChild(createdAt);

            const actions = document.createElement('div');
            actions.className = 'mt-2 flex gap-2';
            const viewOrder = document.createElement('a');
            viewOrder.href = <?php echo json_encode(route('admin.supervisors.notifications.order', ['id' => '__notification_id__']), 512) ?>
                .replace('__notification_id__', encodeURIComponent(notification.id));
            viewOrder.dataset.viewOrder = 'true';
            viewOrder.className = 'text-xs font-bold text-[#FBAB18] hover:underline';
            viewOrder.textContent = 'View Order';
            actions.appendChild(viewOrder);

            const markRead = document.createElement('button');
            markRead.type = 'button';
            markRead.dataset.read = notification.id;
            markRead.className = 'text-xs text-[#B8B8B8] hover:text-[#FFFFFF]';
            markRead.textContent = 'Mark as Read';
            actions.appendChild(markRead);
            item.appendChild(actions);
            notifList.appendChild(item);
        });
    }

    notifList.addEventListener('click', async function(event){
        const button = event.target.closest('[data-read]');
        if(!button) return;

        try {
            const response = await fetch(<?php echo json_encode(route('admin.supervisors.notifications.read', ['id' => '__notification_id__']), 512) ?>
                .replace('__notification_id__', encodeURIComponent(button.dataset.read)), {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': <?php echo json_encode(csrf_token(), 15, 512) ?>,
                    Accept: 'application/json',
                },
                credentials: 'same-origin',
            });
            if(!response.ok) throw new Error(`Mark-read request failed (${response.status})`);
            document.dispatchEvent(new CustomEvent('supervisor:refresh-orders'));
        } catch(error){
            console.error('Unable to mark notification as read.', error);
        }
    });

    const markAllReadButton = document.getElementById('mark-all-read');
    if(markAllReadButton){
        markAllReadButton.addEventListener('click', async function(){
            try {
                const response = await fetch(<?php echo json_encode(route('admin.supervisors.notifications.read-all'), 15, 512) ?>, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': <?php echo json_encode(csrf_token(), 15, 512) ?>,
                        Accept: 'application/json',
                    },
                    credentials: 'same-origin',
                });
                if(!response.ok) throw new Error(`Mark-all-read request failed (${response.status})`);
                document.dispatchEvent(new CustomEvent('supervisor:refresh-orders'));
            } catch(error){
                console.error('Unable to mark notifications as read.', error);
            }
        });
    }

})();
</script>
<script>
(() => {
    const table = document.getElementById('live-orders-table');
    const indicator = document.getElementById('live-orders-status');
    if (!table || !indicator) return;

    const developmentLogging = <?php echo json_encode(app()->isLocal(), 15, 512) ?>;
    const receiptPaperWidth = document.getElementById('receipt-paper-width');
    const receiptNotice = document.getElementById('receipt-print-notice');
    if (receiptPaperWidth) {
        const savedWidth = window.localStorage.getItem('supervisor-receipt-paper-width');
        if (savedWidth === '58' || savedWidth === '80') receiptPaperWidth.value = savedWidth;
        receiptPaperWidth.addEventListener('change', function () {
            window.localStorage.setItem('supervisor-receipt-paper-width', receiptPaperWidth.value);
        });
    }

    function showReceiptNotice(message, receiptUrl) {
        if (!receiptNotice) return;
        receiptNotice.replaceChildren();
        receiptNotice.classList.remove('hidden');
        const text = document.createElement('span');
        text.textContent = message;
        receiptNotice.appendChild(text);
        if (receiptUrl) {
            const retry = document.createElement('a');
            retry.href = receiptUrl;
            retry.target = '_blank';
            retry.rel = 'noopener';
            retry.className = 'ml-2 inline-block font-bold text-[#FBAB18] underline';
            retry.textContent = 'Retry receipt';
            receiptNotice.appendChild(retry);
        }
    }

    let polling = false;
    const seenOrderIds = new Set(Array.from(table.querySelectorAll('tr[data-order-id]')).map(function(row){
        return row.dataset.orderId;
    }));
    const newPendingOrderIds = new Set();

    const markNewOrderRows = () => {
        newPendingOrderIds.forEach(function(orderId){
            const row = Array.from(table.querySelectorAll('tr[data-order-id]'))
                .find(function(candidate){ return candidate.dataset.orderId === orderId; });
            const orderNumber = row && row.querySelector('.order-number');
            if (!orderNumber || orderNumber.querySelector('[data-new-order-badge]')) return;

            const badge = document.createElement('span');
            badge.dataset.newOrderBadge = 'true';
            badge.className = 'ml-2 inline-block rounded-full bg-[#FBAB18] px-2 py-0.5 text-[10px] font-bold text-[#111111]';
            badge.textContent = 'NEW';
            orderNumber.appendChild(badge);
        });
    };

    const refreshOrders = async () => {
        if (polling) return;
        polling = true;
        if (developmentLogging) console.debug('[Supervisor orders] Polling started.');

        const controller = new AbortController();
        const requestTimeout = window.setTimeout(function(){ controller.abort(); }, 10000);
        try {
            if (developmentLogging) console.debug('[Supervisor orders] Sending live-state request.');
            const response = await fetch(<?php echo json_encode(route('admin.supervisors.orders.live'), 15, 512) ?>, {
                headers: { Accept: 'application/json' },
                credentials: 'same-origin',
                cache: 'no-store',
                signal: controller.signal,
            });
            if (response.status === 401 || response.status === 419 || response.redirected) {
                indicator.textContent = 'Session expired';
                indicator.className = 'rounded-full bg-[#FCE8E6] px-3 py-1 text-xs font-bold text-[#D93025]';
                return;
            }
            if (!response.ok) throw new Error(`Live order request failed (${response.status})`);

            const data = await response.json();
            if (developmentLogging) console.debug('[Supervisor orders] Live-state response received.', {
                orderCount: data.order_count,
                pendingOrderCount: Array.isArray(data.pending_orders) ? data.pending_orders.length : null,
            });
            const rows = document.createElement('tbody');
            rows.innerHTML = data.orders_html || '';
            if (!Array.isArray(data.pending_orders)) throw new Error('Live order response is missing pending order data.');
            if (!data.notification_state || !Array.isArray(data.notification_state.notifications)) throw new Error('Live order response is missing notification state.');
            const pendingOrders = data.pending_orders;
            const currentPendingIds = new Set(pendingOrders.map(function(order){ return String(order.id); }));
            rows.querySelectorAll('tr[data-order-id]').forEach(function(row){
                const orderId = String(row.dataset.orderId);
                if (!seenOrderIds.has(orderId)) {
                    seenOrderIds.add(orderId);
                    if (row.dataset.orderStatus === 'Pending') {
                        newPendingOrderIds.add(orderId);
                    }
                    if (developmentLogging) console.debug('[Supervisor orders] New order detected by live feed.', { orderId, status: row.dataset.orderStatus });
                }
            });
            newPendingOrderIds.forEach(function(orderId){
                if (!currentPendingIds.has(orderId)) newPendingOrderIds.delete(orderId);
            });

            table.innerHTML = rows.innerHTML;
            markNewOrderRows();
            if (data.daily_summary) {
                document.getElementById('live-total-orders').textContent = data.daily_summary.total_orders;
                document.getElementById('live-preparing-orders').textContent = data.daily_summary.preparing_count;
                document.getElementById('live-ready-orders').textContent = data.daily_summary.ready_count;
                document.getElementById('live-revenue').textContent = `₱${Number(data.daily_summary.total_revenue || 0).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
                document.getElementById('live-pending-orders').textContent = data.daily_summary.pending_count;
            }

            if (developmentLogging) console.debug('[Supervisor orders] Live feed received and dashboard DOM updated.', {
                orderIds: Array.from(table.querySelectorAll('tr[data-order-id]')).map(function(row){ return row.dataset.orderId; }),
                pendingCount: data.daily_summary.pending_count,
                totalOrders: data.daily_summary.total_orders,
                unreadNotifications: data.notification_state.count,
            });
            document.dispatchEvent(new CustomEvent('supervisor:live-state', { detail: data }));
            indicator.textContent = 'Live updates';
            indicator.className = 'rounded-full bg-[#E6F4EA] px-3 py-1 text-xs font-bold text-[#1E8E3E]';
        } catch (error) {
            console.error('Unable to refresh branch orders.', error);
            if (developmentLogging) console.debug('[Supervisor orders] Poll failed.', error);
            indicator.textContent = 'Live updates unavailable';
            indicator.className = 'rounded-full bg-[#FCE8E6] px-3 py-1 text-xs font-bold text-[#D93025]';
        } finally {
            window.clearTimeout(requestTimeout);
            polling = false;
        }
    };

    document.addEventListener('supervisor:refresh-orders', refreshOrders);
    if (developmentLogging) console.debug('[Supervisor orders] Polling controller loaded.', { endpoint: <?php echo json_encode(route('admin.supervisors.orders.live'), 15, 512) ?> });
    refreshOrders();
    const ordersInterval = window.setInterval(refreshOrders, 3000);
    table.addEventListener('click', function(event){
        const link = event.target.closest('a[data-receipt-print]');
        if (!link || !receiptPaperWidth) return;
        const receiptUrl = new URL(link.href, window.location.origin);
        receiptUrl.searchParams.set('paper', receiptPaperWidth.value);
        link.href = receiptUrl.toString();
    });
    table.addEventListener('submit', async function(event){
        const form = event.target.closest('form[data-live-order-action]');
        if (!form) return;

        event.preventDefault();
        const autoPrint = form.dataset.autoReceipt === 'true';
        const printWindow = autoPrint ? window.open('about:blank', '_blank') : null;
        let orderUpdated = false;
        const button = form.querySelector('button[type="submit"]');
        if (button) {
            button.disabled = true;
            button.style.opacity = '0.6';
        }

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                body: new FormData(form),
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    Accept: 'application/json',
                },
                credentials: 'same-origin',
            });
            const result = await response.json();
            if (!response.ok) throw new Error(result.message || `Order update failed (${response.status})`);
            orderUpdated = true;
            if (printWindow && result.receipt_url) {
                const receiptUrl = new URL(result.receipt_url, window.location.origin);
                receiptUrl.searchParams.set('auto', '1');
                receiptUrl.searchParams.set('paper', receiptPaperWidth ? receiptPaperWidth.value : '80');
                let receiptOpened = false;
                try {
                    printWindow.location.replace(receiptUrl.toString());
                    receiptOpened = true;
                } catch(error) {
                    console.error('Unable to open the receipt after accepting the order.', error);
                    printWindow.close();
                }
                showReceiptNotice(
                    receiptOpened
                        ? 'Order accepted. The browser print dialog is opening; verify the printer output. If printing fails or is cancelled, retry here.'
                        : 'Order accepted, but the receipt could not be opened. Use this link to retry.',
                    receiptUrl.toString()
                );
            } else if (autoPrint && result.receipt_url) {
                const receiptUrl = new URL(result.receipt_url, window.location.origin);
                receiptUrl.searchParams.set('auto', '1');
                receiptUrl.searchParams.set('paper', receiptPaperWidth ? receiptPaperWidth.value : '80');
                showReceiptNotice('Order accepted, but the receipt window was blocked. Open it to print, then verify the printer output.', receiptUrl.toString());
            } else if (printWindow) {
                printWindow.close();
            }
            await refreshOrders();
        } catch(error) {
            if (printWindow && !orderUpdated) printWindow.close();
            console.error(orderUpdated ? 'Order changed, but the dashboard could not refresh.' : 'Unable to update order status.', error);
            indicator.textContent = orderUpdated ? 'Order updated; dashboard refresh failed.' : error.message;
            indicator.className = 'rounded-full bg-[#FCE8E6] px-3 py-1 text-xs font-bold text-[#D93025]';
            if (!orderUpdated) await refreshOrders();
        } finally {
            if (button) {
                button.disabled = false;
                button.style.opacity = '';
            }
        }
    });
    document.addEventListener('visibilitychange', function(){
        if (!document.hidden) refreshOrders();
    });
    window.addEventListener('beforeunload', function(){ window.clearInterval(ordersInterval); });
})();
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mixeat\mixeat\resources\views\admin\supervisors\index.blade.php ENDPATH**/ ?>