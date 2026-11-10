<!doctype html>
<html lang="en">
<body style="margin:0;background:#111111;color:#ffffff;font-family:Arial,sans-serif;padding:16px;">
    <div style="max-width:640px;margin:0 auto;background:#201D19;padding:28px;border:1px solid #333333;border-radius:12px;">
        <p style="color:#FBAB18;font-weight:bold;letter-spacing:2px;margin:0 0 8px;">MIXEAT CATERING ADMIN</p>
        <h1 style="color:#FBAB18;margin:0 0 20px;">New Catering Booking</h1>
        <p>A new catering booking requires supervisor review.</p>
        <table role="presentation" style="width:100%;border-collapse:collapse;background:#1A1A1A;">
            <tr><td style="padding:10px;font-weight:bold;">Booking reference</td><td style="padding:10px;"><?php echo e($booking_reference); ?></td></tr>
            <tr><td style="padding:10px;font-weight:bold;">Customer name</td><td style="padding:10px;"><?php echo e($client_name); ?></td></tr>
            <tr><td style="padding:10px;font-weight:bold;">Customer email</td><td style="padding:10px;"><?php echo e($booking->email); ?></td></tr>
            <tr><td style="padding:10px;font-weight:bold;">Phone</td><td style="padding:10px;"><?php echo e($phone); ?></td></tr>
            <tr><td style="padding:10px;font-weight:bold;">Service</td><td style="padding:10px;"><?php echo e($service_type); ?></td></tr>
            <tr><td style="padding:10px;font-weight:bold;">Event</td><td style="padding:10px;"><?php echo e($event_date); ?> <?php if($event_time): ?> at <?php echo e($event_time); ?><?php endif; ?></td></tr>
            <tr><td style="padding:10px;font-weight:bold;">Event type</td><td style="padding:10px;"><?php echo e($event_type); ?><?php echo e($event_type_details ? ': '.$event_type_details : ''); ?></td></tr>
            <tr><td style="padding:10px;font-weight:bold;">Venue</td><td style="padding:10px;"><?php echo e($venue); ?></td></tr>
            <tr><td style="padding:10px;font-weight:bold;">Package</td><td style="padding:10px;"><?php echo e($package); ?></td></tr>
            <tr><td style="padding:10px;font-weight:bold;">Guests</td><td style="padding:10px;"><?php echo e($guest_count); ?></td></tr>
            <tr><td style="padding:10px;font-weight:bold;">Status</td><td style="padding:10px;"><?php echo e($status); ?></td></tr>
        </table>
        <?php if(!empty($menu_selections)): ?>
            <h2 style="font-size:18px;color:#FBAB18;">Selected foods</h2>
            <pre style="white-space:pre-wrap;color:#DDDDDD;"><?php echo e(json_encode($menu_selections, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)); ?></pre>
        <?php endif; ?>
        <p><a href="<?php echo e(route('admin.supervisors.catering-bookings')); ?>" style="display:inline-block;background:#FBAB18;color:#111111;padding:12px 18px;text-decoration:none;font-weight:bold;border-radius:20px;">Review booking</a></p>
        <p style="color:#B8B8B8;font-size:12px;">MixEat Commissary</p>
    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\mixeat\mixeat\resources\views/emails/catering-booking-admin.blade.php ENDPATH**/ ?>