<!doctype html>
<html lang="en">
<body style="margin:0;background:#111111;color:#ffffff;font-family:Arial,sans-serif;padding:16px;">
    <div style="max-width:620px;margin:0 auto;background:#201D19;padding:28px;border:1px solid #333333;border-radius:12px;">
        <div style="border-bottom:1px solid #333333;padding-bottom:16px;">
            <p style="color:#FBAB18;font-weight:bold;letter-spacing:2px;margin:0 0 8px;">MIXEAT CATERING</p>
            <h1 style="color:#FBAB18;margin:0;"><?php echo e($title); ?></h1>
        </div>
        <p>Hello <?php echo e($client_name ?? 'Customer'); ?>,</p>
        <p><?php echo e($notification_message); ?></p>
        <table role="presentation" style="width:100%;border-collapse:collapse;background:#1A1A1A;">
            <tr><td style="padding:10px;font-weight:bold;">Booking reference</td><td style="padding:10px;"><?php echo e($booking_reference); ?></td></tr>
            <tr><td style="padding:10px;font-weight:bold;">Customer email</td><td style="padding:10px;"><?php echo e($booking->email ?? ''); ?></td></tr>
            <tr><td style="padding:10px;font-weight:bold;">Phone</td><td style="padding:10px;"><?php echo e($phone); ?></td></tr>
            <?php if($event_date): ?><tr><td style="padding:10px;font-weight:bold;">Event</td><td style="padding:10px;"><?php echo e($event_date); ?> <?php if($event_time): ?> at <?php echo e($event_time); ?><?php endif; ?></td></tr><?php endif; ?>
            <?php if($event_type): ?><tr><td style="padding:10px;font-weight:bold;">Event type</td><td style="padding:10px;"><?php echo e($event_type); ?><?php echo e($event_type_details ? ': '.$event_type_details : ''); ?></td></tr><?php endif; ?>
            <?php if($venue): ?><tr><td style="padding:10px;font-weight:bold;">Venue</td><td style="padding:10px;"><?php echo e($venue); ?></td></tr><?php endif; ?>
            <?php if($service_type): ?><tr><td style="padding:10px;font-weight:bold;">Service</td><td style="padding:10px;"><?php echo e($service_type); ?></td></tr><?php endif; ?>
            <?php if($package): ?><tr><td style="padding:10px;font-weight:bold;">Package</td><td style="padding:10px;"><?php echo e($package); ?></td></tr><?php endif; ?>
            <?php if($guest_count): ?><tr><td style="padding:10px;font-weight:bold;">Guests</td><td style="padding:10px;"><?php echo e($guest_count); ?></td></tr><?php endif; ?>
            <tr><td style="padding:10px;font-weight:bold;">Status</td><td style="padding:10px;"><?php echo e($status); ?></td></tr>
            <?php if($amount): ?><tr><td style="padding:10px;font-weight:bold;">Total amount</td><td style="padding:10px;">₱<?php echo e(number_format((float) $amount, 2)); ?></td></tr><?php endif; ?>
            <?php if($total_paid): ?><tr><td style="padding:10px;font-weight:bold;">Amount paid</td><td style="padding:10px;">₱<?php echo e(number_format((float) $total_paid, 2)); ?></td></tr><?php endif; ?>
            <?php if($remaining_balance !== null): ?><tr><td style="padding:10px;font-weight:bold;">Remaining balance</td><td style="padding:10px;">₱<?php echo e(number_format((float) $remaining_balance, 2)); ?></td></tr><?php endif; ?>
        </table>
        <?php if(!empty($menu_selections)): ?>
            <h2 style="font-size:18px;color:#FBAB18;margin-bottom:8px;">Selected foods</h2>
            <ul style="padding-left:20px;">
                <?php $__currentLoopData = $menu_selections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category => $items): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if(is_array($items)): ?>
                        <?php $__currentLoopData = ($items['selections'] ?? $items); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $foodCategory => $foodItems): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if(is_array($foodItems)): ?>
                                <?php $__currentLoopData = $foodItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php if(is_string($item)): ?><li><?php echo e($foodCategory); ?>: <?php echo e($item); ?></li><?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php elseif(is_string($foodItems)): ?>
                                <li><?php echo e($category); ?>: <?php echo e($foodItems); ?></li>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        <?php endif; ?>
        <?php if($quotation_notes): ?><p><strong>Quotation details:</strong> <?php echo e($quotation_notes); ?></p><?php endif; ?>
        <?php if($reason): ?><p><strong>Remarks:</strong> <?php echo e($reason); ?></p><?php endif; ?>
        <?php if($recipient_type === 'customer'): ?>
            <p><a href="<?php echo e($booking_url); ?>" style="display:inline-block;background:#FBAB18;color:#111111;padding:12px 18px;text-decoration:none;font-weight:bold;border-radius:20px;">View booking</a></p>
        <?php endif; ?>
        <p style="color:#B8B8B8;font-size:12px;">MixEat Commissary</p>
    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\mixeat\mixeat\resources\views\emails\catering-booking.blade.php ENDPATH**/ ?>