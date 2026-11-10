<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Receipt <?php echo e($order->order_number); ?></title>
    <style>
        @page {
            size: <?php echo e($paperWidth); ?>mm auto;
            margin: 3mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: #f2f2f2;
            color: #111;
            font: 12px/1.35 Arial, Helvetica, sans-serif;
        }

        .receipt {
            width: <?php echo e($paperWidth - 6); ?>mm;
            max-width: 100%;
            margin: 20px auto;
            padding: 4mm;
            background: #fff;
        }

        .center {
            text-align: center;
        }

        .brand {
            margin: 0;
            font-size: 20px;
            font-weight: 900;
            letter-spacing: 1px;
        }

        .branch,
        .muted {
            color: #444;
        }

        .section {
            margin-top: 12px;
        }

        .rule {
            margin: 9px 0;
            border: 0;
            border-top: 1px dashed #555;
        }

        .row {
            display: flex;
            justify-content: space-between;
            gap: 8px;
        }

        .row > :last-child {
            text-align: right;
            overflow-wrap: anywhere;
        }

        .item {
            margin: 8px 0;
            overflow-wrap: anywhere;
        }

        .item-name {
            font-weight: 700;
        }

        .grand-total {
            font-size: 15px;
            font-weight: 900;
        }

        .collection {
            margin-top: 9px;
            padding: 7px;
            border: 1px solid #111;
            font-weight: 700;
        }

        .controls {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin: 20px auto;
        }

        .controls a {
            padding: 9px 12px;
            border: 1px solid #111;
            background: #fff;
            color: #111;
            font-weight: 700;
            text-decoration: none;
        }

        @media print {
            body {
                background: #fff;
            }

            .receipt {
                width: 100%;
                max-width: none;
                margin: 0;
                padding: 0;
            }

            .controls {
                display: none;
            }
        }
    </style>
</head>
<body>
    <main class="receipt">
        <header class="center">
            <h1 class="brand">MIXEAT</h1>
            <div class="branch"><?php echo e($order->branch?->name ?? 'MixEat'); ?></div>
            <?php if($order->branch?->address): ?>
                <div class="muted"><?php echo e($order->branch->address); ?></div>
            <?php endif; ?>
            <div class="section"><strong><?php echo e($copyType === 'rider' ? 'DELIVERY RIDER COPY' : 'CUSTOMER COPY'); ?></strong></div>
            <?php if($isReprint): ?>
                <div><strong>REPRINT</strong></div>
            <?php endif; ?>
        </header>

        <hr class="rule">

        <section>
            <div class="row"><strong>Order #</strong><span><?php echo e($order->order_number); ?></span></div>
            <div class="row"><span>Date</span><span><?php echo e($order->created_at?->timezone('Asia/Manila')->format('M j, Y')); ?></span></div>
            <div class="row"><span>Time</span><span><?php echo e($order->created_at?->timezone('Asia/Manila')->format('g:i A')); ?></span></div>
        </section>

        <hr class="rule">

        <section>
            <div><strong>Customer:</strong> <?php echo e($order->user?->name ?? 'Guest'); ?></div>
            <?php if($order->user?->phone): ?>
                <div><strong>Customer Phone:</strong> <?php echo e($order->user->phone); ?></div>
            <?php endif; ?>
            <div><strong>Type:</strong> <?php echo e($order->type); ?></div>
            <?php if($order->type === 'Delivery'): ?>
                <div><strong>Method:</strong> <?php echo e($order->delivery_method ?: 'Not recorded'); ?></div>
                <div class="section"><strong>Delivery Address</strong></div>
                <div><?php echo e($order->delivery_address ?: 'Not recorded'); ?></div>
            <?php else: ?>
                <div><strong>Method:</strong> Pickup</div>
            <?php endif; ?>
        </section>

        <hr class="rule">

        <section>
            <strong>ORDERED ITEMS</strong>
            <?php $__empty_1 = true; $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="item">
                    <div class="item-name"><?php echo e($item->product_name); ?></div>
                    <?php if($item->addons): ?>
                        <div class="muted">Add-ons: <?php echo e(implode(', ', $item->addons)); ?></div>
                    <?php endif; ?>
                    <div class="row">
                        <span><?php echo e($item->quantity); ?> × &#8369;<?php echo e(number_format((float) $item->price, 2)); ?></span>
                        <strong>&#8369;<?php echo e(number_format((float) $item->price * (int) $item->quantity, 2)); ?></strong>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="muted">No item details were recorded for this order.</div>
            <?php endif; ?>
        </section>

        <hr class="rule">

        <section>
            <div class="row"><span>Items Subtotal</span><span>&#8369;<?php echo e(number_format($order->items->sum(fn ($item) => (float) $item->price * (int) $item->quantity), 2)); ?></span></div>
            <?php if($order->type === 'Delivery' && $order->delivery_method === 'Maxim'): ?>
                <div class="row"><span>Third-party delivery fee</span><span>Paid separately; not recorded</span></div>
            <?php else: ?>
                <div class="row"><span>Delivery Fee</span><span>&#8369;<?php echo e(number_format((float) $order->delivery_fee, 2)); ?></span></div>
            <?php endif; ?>
            <div class="row grand-total section"><span>GRAND TOTAL</span><span>&#8369;<?php echo e(number_format((float) $order->total, 2)); ?></span></div>
            <div class="section"><strong>Payment Method:</strong> <?php echo e($order->payment_method ?: 'Not recorded'); ?></div>
            <?php if($order->gcash_reference_number): ?>
                <div><strong>GCash Reference:</strong> <?php echo e($order->gcash_reference_number); ?></div>
            <?php endif; ?>

            <?php if($copyType === 'rider'): ?>
                <div class="collection">
                    <?php if($order->status === 'Completed'): ?>
                        Payment settlement is not tracked on this order. Confirm the remaining amount with the branch before collecting.
                    <?php elseif($order->payment_method === 'Cash'): ?>
                        Cash to collect from customer: &#8369;<?php echo e(number_format((float) $order->total, 2)); ?>

                    <?php else: ?>
                        Payment status and amount collected are not recorded. Verify with the branch before collecting.
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </section>

        <hr class="rule">

        <footer class="center">
            <div>Thank you for ordering from MixEat!</div>
            <div class="muted">Printed <?php echo e(now('Asia/Manila')->format('M j, Y g:i A')); ?></div>
        </footer>
    </main>

    <nav class="controls" aria-label="Receipt printing options">
        <a href="<?php echo e(route('admin.supervisors.orders.receipt', ['order' => $order->id, 'copy' => $copyType, 'paper' => 58, 'auto' => 1, 'reprint' => 1])); ?>">Print on 58mm</a>
        <a href="<?php echo e(route('admin.supervisors.orders.receipt', ['order' => $order->id, 'copy' => $copyType, 'paper' => 80, 'auto' => 1, 'reprint' => 1])); ?>">Print on 80mm</a>
    </nav>

    <?php if($autoPrint): ?>
        <script>
            window.addEventListener('load', function () {
                const receiptUrl = new URL(window.location.href);
                receiptUrl.searchParams.delete('auto');
                window.history.replaceState(null, '', receiptUrl.toString());
                window.setTimeout(function () {
                    window.print();
                }, 250);
            }, { once: true });
        </script>
    <?php endif; ?>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\mixeat\mixeat\resources\views\admin\supervisors\receipt.blade.php ENDPATH**/ ?>