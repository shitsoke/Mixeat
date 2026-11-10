

<?php $__env->startSection('content'); ?>
<?php echo $__env->make('admin.marketing.partials.catering-admin-theme', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<div class="catering-admin-theme">
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3 mb-4">
    <div>
        <p class="small fw-bold text-uppercase text-warning-emphasis mb-1">Marketing Admin</p>
        <h1 class="h2 fw-bold mb-1">Catering Event Calendar</h1>
        <p class="text-secondary mb-0">Accept or decline catering bookings, prepare quotes, track payment, and assign fulfillment.</p>
    </div>
    <a class="btn btn-outline-dark rounded-pill" href="<?php echo e(route('admin.marketing.catering-bookings')); ?>">Back to Catering Bookings</a>
</div>

<?php ($calendar = $bookings->groupBy(fn ($booking) => $booking->event_date->format('Y-m-d'))); ?>
<?php $__empty_1 = true; $__currentLoopData = $calendar; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $date => $dayBookings): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <section class="card-custom p-3 p-md-4 mb-3" aria-label="Events on <?php echo e($date); ?>">
        <div class="d-flex flex-column flex-sm-row justify-content-between gap-1 mb-3">
            <h2 class="h5 fw-bold mb-0"><?php echo e(\Illuminate\Support\Carbon::parse($date)->format('l, F j, Y')); ?></h2>
            <span class="badge <?php echo e($dayBookings->count() > 3 ? 'text-bg-danger' : 'text-bg-warning'); ?> align-self-start"><?php echo e($dayBookings->count()); ?> event<?php echo e($dayBookings->count() === 1 ? '' : 's'); ?> scheduled</span>
        </div>
        <div class="row g-3">
        <?php $__currentLoopData = $dayBookings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="col-12">
                <article class="rounded-4 border p-3">
                    <div class="row g-3">
                        <div class="col-lg-4">
                            <h3 class="h6 fw-bold mb-1"><?php echo e($booking->client_name); ?> <span class="badge text-bg-light border"><?php echo e($booking->status); ?></span></h3>
                            <div><?php echo e($booking->service_type); ?> &middot; <?php echo e(number_format($booking->guest_count)); ?> guests</div>
                            <div class="small text-secondary"><?php echo e($booking->event_type ?: 'Event type not provided'); ?><?php echo e($booking->event_type_details ? ': '.$booking->event_type_details : ''); ?></div>
                            <div class="small text-secondary"><?php echo e($booking->package_preference ?: 'No package preference'); ?></div>
                            <?php if($booking->trayItems->isNotEmpty()): ?>
                                <div class="small mt-1">
                                    <?php $__currentLoopData = $booking->trayItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $trayItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div><?php echo e($trayItem->category); ?> · <?php echo e($trayItem->dish); ?> · <?php echo e(ucfirst($trayItem->tray_size)); ?> × <?php echo e(number_format($trayItem->quantity)); ?> · ₱<?php echo e(number_format((float) $trayItem->subtotal, 2)); ?></div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <strong>Tray estimate: ₱<?php echo e(number_format((float) $booking->estimated_total, 2)); ?></strong>
                                </div>
                            <?php endif; ?>
                            <?php if($booking->venue): ?>
                                <div class="small text-secondary mt-1"><?php echo e($booking->venue); ?></div>
                            <?php endif; ?>
                            <?php if($booking->special_requests): ?>
                                <p class="small mt-2 mb-1"><strong>Requests:</strong> <?php echo e($booking->special_requests); ?></p>
                            <?php endif; ?>
                            <div class="d-flex flex-wrap gap-2 mt-2">
                                <a class="btn btn-sm btn-outline-dark" href="mailto:<?php echo e($booking->email); ?>?subject=<?php echo e(rawurlencode('MixEat Catering - '.$booking->event_date->format('M j, Y'))); ?>">Email client</a>
                                <a class="btn btn-sm btn-outline-dark" href="tel:<?php echo e($booking->phone); ?>">Call <?php echo e($booking->phone); ?></a>
                            </div>
                        </div>
                        <div class="col-lg-8">
                            <form action="<?php echo e(route('admin.marketing.catering-calendar.update', $booking)); ?>" method="POST" class="row g-2 align-items-end">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('PATCH'); ?>
                                <?php if($booking->payments->isEmpty()): ?>
                                <div class="col-sm-6 col-lg-4">
                                    <label for="booking-status-<?php echo e($booking->id); ?>" class="form-label small fw-semibold">Booking decision / status</label>
                                    <select id="booking-status-<?php echo e($booking->id); ?>" class="form-select form-select-sm" name="status" required>
                                        <?php $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($status); ?>" <?php if($booking->status === $status): echo 'selected'; endif; ?>><?php echo e($status); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                                <div class="col-sm-6 col-lg-4">
                                    <label for="booking-payment-<?php echo e($booking->id); ?>" class="form-label small fw-semibold">Payment status</label>
                                    <select id="booking-payment-<?php echo e($booking->id); ?>" class="form-select form-select-sm" name="payment_status" required>
                                        <?php $__currentLoopData = ['Unpaid', 'Deposit Paid', 'Fully Paid']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($payment); ?>" <?php if($booking->payment_status === $payment): echo 'selected'; endif; ?>><?php echo e($payment); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                                <?php else: ?>
                                <div class="col-sm-6 col-lg-8">
                                    <?php if($booking->payments->where('payment_type', 'deposit')->where('status', 'Verified')->isNotEmpty()): ?>
                                        <label for="booking-status-<?php echo e($booking->id); ?>" class="form-label small fw-semibold">Fulfillment status</label>
                                        <select id="booking-status-<?php echo e($booking->id); ?>" class="form-select form-select-sm" name="status" required>
                                            <?php $__currentLoopData = ['Confirmed', 'Preparing', 'Out for Delivery', 'Delivered', 'Cancelled']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($status); ?>" <?php if($booking->status === $status): echo 'selected'; endif; ?>><?php echo e($status); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    <?php else: ?>
                                        <span class="form-label small fw-semibold d-block">Booking / payment status</span>
                                        <div class="small"><?php echo e($booking->status); ?> · <?php echo e($booking->payment_status); ?></div>
                                        <p class="small text-secondary mb-0">Deposit verification and payment collection are managed from the booking details.</p>
                                    <?php endif; ?>
                                </div>
                                <?php endif; ?>
                                <div class="col-sm-6 col-lg-4">
                                    <label for="booking-quote-<?php echo e($booking->id); ?>" class="form-label small fw-semibold">Quote (PHP)</label>
                                    <input id="booking-quote-<?php echo e($booking->id); ?>" class="form-control form-control-sm" type="number" name="quote_amount" min="0" step="0.01" value="<?php echo e($booking->quote_amount); ?>">
                                </div>
                                <div class="col-sm-6">
                                    <label for="booking-branch-<?php echo e($booking->id); ?>" class="form-label small fw-semibold">Fulfilling branch</label>
                                    <select id="booking-branch-<?php echo e($booking->id); ?>" class="form-select form-select-sm" name="branch_id">
                                        <option value="">Unassigned</option>
                                        <?php $__currentLoopData = $branches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $branch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($branch->id); ?>" <?php if($booking->branch_id === $branch->id): echo 'selected'; endif; ?>><?php echo e($branch->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                                <div class="col-sm-6">
                                    <label for="booking-staff-<?php echo e($booking->id); ?>" class="form-label small fw-semibold">Kitchen staff</label>
                                    <select id="booking-staff-<?php echo e($booking->id); ?>" class="form-select form-select-sm" name="assigned_staff_id">
                                        <option value="">Unassigned</option>
                                        <?php $__currentLoopData = $staffByBranch; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $branchId => $members): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php if($branchId): ?>
                                                <optgroup label="<?php echo e($members->first()->branch?->name ?? 'Branch staff'); ?>">
                                                    <?php $__currentLoopData = $members; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $member): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($member->id); ?>" <?php if($booking->assigned_staff_id === $member->id): echo 'selected'; endif; ?>><?php echo e($member->name); ?></option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </optgroup>
                                            <?php endif; ?>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                                <div class="col-12 d-flex flex-wrap justify-content-between align-items-center gap-2 mt-3">
                                    <span class="small text-secondary">
                                        <?php if($booking->quote_amount !== null): ?>
                                            Quote preview: <strong class="text-dark">&#8369;<?php echo e(number_format((float) $booking->quote_amount, 2)); ?></strong>
                                        <?php else: ?>
                                            Quote not prepared
                                        <?php endif; ?>
                                        &middot; <?php echo e($booking->payment_status); ?>

                                    </span>
                                    <button type="submit" class="btn btn-sm btn-mixeat-yellow">Save booking</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </article>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </section>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
    <div class="card-custom p-5 text-center text-secondary">No catering events are scheduled yet.</div>
<?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mixeat\mixeat\resources\views\admin\marketing\catering-calendar.blade.php ENDPATH**/ ?>