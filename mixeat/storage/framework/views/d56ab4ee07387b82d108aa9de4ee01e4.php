<!doctype html>
<html lang="en">
<body style="margin:0;background:#111111;color:#ffffff;font-family:Arial,sans-serif;padding:16px;">
    <div style="max-width:620px;margin:0 auto;background:#201D19;padding:28px;border:1px solid #333333;border-radius:12px;">
        <p style="color:#FBAB18;font-weight:bold;letter-spacing:2px;margin:0 0 8px;">MIXEAT CATERING</p>
        <h1 style="color:#FBAB18;margin:0 0 20px;"><?php echo e($title); ?></h1>
        <p>Hello <?php echo e($client_name); ?>,</p>
        <?php if($event === 'inquiry_submitted'): ?>
            <p>Thank you for submitting your catering booking with MixEat. Our team will review it, and another email will be sent when the booking has been reviewed.</p>
        <?php else: ?>
            <p><?php echo e($notification_message); ?></p>
        <?php endif; ?>
        <table role="presentation" style="width:100%;border-collapse:collapse;background:#1A1A1A;">
            <tr><td style="padding:10px;font-weight:bold;">Booking reference</td><td style="padding:10px;"><?php echo e($booking_reference); ?></td></tr>
            <tr><td style="padding:10px;font-weight:bold;">Service</td><td style="padding:10px;"><?php echo e($service_type); ?></td></tr>
            <tr><td style="padding:10px;font-weight:bold;">Event date</td><td style="padding:10px;"><?php echo e($event_date); ?></td></tr>
            <tr><td style="padding:10px;font-weight:bold;">Status</td><td style="padding:10px;"><?php echo e($status); ?></td></tr>
        </table>
        <?php if($reason): ?><p><strong>Remarks:</strong> <?php echo e($reason); ?></p><?php endif; ?>
        <?php if($event !== 'inquiry_submitted'): ?>
            <p><a href="<?php echo e($booking_url); ?>" style="display:inline-block;background:#FBAB18;color:#111111;padding:12px 18px;text-decoration:none;font-weight:bold;border-radius:20px;">View booking</a></p>
        <?php endif; ?>
        <p style="color:#B8B8B8;font-size:12px;">MixEat Commissary</p>
    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\mixeat\mixeat\resources\views/emails/catering-booking-customer.blade.php ENDPATH**/ ?>