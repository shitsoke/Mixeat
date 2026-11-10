<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php echo e($title); ?></title>
    <style>
        @page { size: A4 landscape; margin: 12mm; }
        body { font-family: DejaVu Sans, sans-serif; color: #222; font-size: 8px; }
        h1 { margin: 0 0 4px; font-size: 18px; } h2 { font-size: 11px; margin: 14px 0 5px; }
        .muted { color: #666; } .summary { display: flex; flex-wrap: wrap; gap: 5px; margin: 12px 0; }
        .card { border: 1px solid #bbb; padding: 6px; min-width: 90px; } .value { font-size: 11px; font-weight: bold; }
        table { border-collapse: collapse; width: 100%; table-layout: fixed; } th, td { border: 1px solid #bbb; padding: 4px; word-wrap: break-word; } th { background: #f3b21b; text-align: left; }
        .no-print { margin-bottom: 12px; } @media print { .no-print { display: none; } }
    </style>
</head>
<body>
    <div class="no-print"><button onclick="window.print()">Print this report</button></div>
    <h1><?php echo e($title); ?></h1>
    <div class="muted"><?php echo e($filters['date_basis'] === 'created' ? 'Booking creation date' : 'Event date'); ?> | <?php echo e($filters['date_from'] ?: 'Beginning'); ?> to <?php echo e($filters['date_to'] ?: 'Present'); ?> | Generated <?php echo e(now('Asia/Manila')->format('Y-m-d H:i')); ?></div>
    <div class="summary">
        <?php $__currentLoopData = [['Bookings', $summary['total_bookings'], false], ['Confirmed value', $summary['confirmed_value'], true], ['Verified deposits', $summary['verified_deposits'], true], ['Total collected', $summary['total_collected'], true], ['Outstanding', $summary['outstanding'], true]]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$label, $value, $money]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="card"><div class="muted"><?php echo e($label); ?></div><div class="value"><?php echo e($money ? '₱'.number_format($value, 2) : number_format($value)); ?></div></div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <h2>Detailed Catering Records</h2>
    <table>
        <thead><tr><th>Reference</th><th>Customer / Contact</th><th>Category / Items</th><th>Booking Date</th><th>Event Date</th><th>Guests</th><th>Total Billing</th><th>Required Deposit</th><th>Verified Deposit</th><th>Remaining</th><th>Total Collected</th><th>Payment</th><th>Status</th></tr></thead>
        <tbody>
        <?php $__currentLoopData = $bookings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr><td><?php echo e($booking->report_reference); ?></td><td><?php echo e($booking->client_name); ?><br><?php echo e($booking->phone); ?></td><td><?php echo e($booking->service_type); ?><br><?php echo e($booking->package_preference ?: $booking->report_selected_items); ?></td><td><?php echo e($booking->created_at->timezone('Asia/Manila')->format('Y-m-d')); ?></td><td><?php echo e(optional($booking->event_date)->format('Y-m-d')); ?></td><td><?php echo e($booking->guest_count); ?></td><td>₱<?php echo e(number_format($booking->report_total, 2)); ?></td><td>₱<?php echo e(number_format($booking->report_required_deposit, 2)); ?></td><td>₱<?php echo e(number_format($booking->report_verified_deposit, 2)); ?></td><td>₱<?php echo e(number_format($booking->report_remaining, 2)); ?></td><td>₱<?php echo e(number_format($booking->report_total_collected, 2)); ?></td><td><?php echo e($booking->payment_status); ?></td><td><?php echo e($booking->status); ?></td></tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\mixeat\mixeat\resources\views\admin\marketing\catering-report-print.blade.php ENDPATH**/ ?>