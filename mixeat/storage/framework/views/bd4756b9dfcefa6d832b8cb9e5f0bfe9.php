

<?php $__env->startSection('content'); ?>
<?php echo $__env->make('admin.marketing.partials.catering-admin-theme', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<div class="catering-admin-theme">
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3 mb-4">
    <div>
        <p class="small fw-bold text-uppercase text-warning-emphasis mb-1">Branch Supervisor</p>
        <h1 class="h2 fw-bold mb-1">Catering Booking Requests</h1>
        <p class="text-secondary mb-0">Review requests, accept or decline bookings, and manage follow-up.</p>
    </div>
    <div class="d-flex flex-wrap gap-2">
    </div>
</div>

<section class="card-custom p-3 p-md-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Client</th>
                    <th>Event</th>
                    <th>Service</th>
                    <th>Guests</th>
                    <th>Submitted</th>
                    <th>Booking Status</th>
                    <th>Payment Status</th>
                    <th><span class="visually-hidden">Actions</span></th>
                </tr>
            </thead>
            <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $bookings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td>
                        <strong><?php echo e($booking->client_name); ?></strong>
                        <?php if($booking->company_name): ?><br><span class="small text-secondary"><?php echo e($booking->company_name); ?></span><?php endif; ?>
                        <br><a href="mailto:<?php echo e($booking->email); ?>"><?php echo e($booking->email); ?></a>
                    </td>
                    <td class="text-nowrap">
                        <?php echo e($booking->event_date->format('M j, Y')); ?>

                        <?php if($booking->event_time): ?><br><span class="small text-secondary"><?php echo e(\Illuminate\Support\Carbon::parse($booking->event_time)->format('g:i A')); ?></span><?php endif; ?>
                    </td>
                    <td><?php echo e($booking->service_type); ?></td>
                    <td><?php echo e(number_format($booking->guest_count)); ?></td>
                    <td class="text-nowrap"><?php echo e($booking->created_at->format('M j, Y g:i A')); ?></td>
                    <td><span class="badge <?php echo e($booking->status === 'New' ? 'text-bg-warning' : 'text-bg-light border'); ?>"><?php echo e($booking->status); ?></span></td>
                    <td>
                        <?php
                            $paymentBadge = match ($booking->payment_summary['status']) {
                                'Pending Verification' => 'text-bg-warning',
                                'Deposit Paid (30%)' => 'text-bg-warning',
                                'Fully Paid' => 'text-bg-success',
                                'Payment Rejected' => 'text-bg-danger',
                                default => 'text-bg-secondary',
                            };
                        ?>
                        <span class="badge <?php echo e($paymentBadge); ?>"><?php echo e($booking->payment_summary['status']); ?></span>
                    </td>
                    <td>
                        <button type="button" class="btn btn-sm btn-outline-dark rounded-pill text-nowrap" data-bs-toggle="modal" data-bs-target="#booking-<?php echo e($booking->id); ?>">Full details</button>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="8" class="py-5 text-center text-secondary">No catering booking requests have been submitted yet.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php echo e($bookings->links()); ?>

</section>

<?php $__currentLoopData = $bookings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="modal fade" id="booking-<?php echo e($booking->id); ?>" tabindex="-1" aria-labelledby="booking-title-<?php echo e($booking->id); ?>" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content rounded-4">
                <div class="modal-header">
                    <div>
                        <p class="small fw-bold text-uppercase text-warning-emphasis mb-1">Catering request</p>
                        <h2 class="modal-title h4 fw-bold" id="booking-title-<?php echo e($booking->id); ?>"><?php echo e($booking->client_name); ?></h2>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6"><span class="small text-secondary d-block">Email</span><a href="mailto:<?php echo e($booking->email); ?>"><?php echo e($booking->email); ?></a></div>
                        <div class="col-md-6"><span class="small text-secondary d-block">Contact number</span><a href="tel:<?php echo e($booking->phone); ?>"><?php echo e($booking->phone); ?></a></div>
                        <div class="col-md-6"><span class="small text-secondary d-block">Company / Organization</span><?php echo e($booking->company_name ?: 'Not provided'); ?></div>
                        <div class="col-md-6"><span class="small text-secondary d-block">Event type</span><?php echo e($booking->event_type ?: 'Not provided'); ?><?php echo e($booking->event_type_details ? ': '.$booking->event_type_details : ''); ?></div>
                        <div class="col-md-6"><span class="small text-secondary d-block">Event date &amp; time</span><?php echo e($booking->event_date->format('F j, Y')); ?><?php echo e($booking->event_time ? ' at '.\Illuminate\Support\Carbon::parse($booking->event_time)->format('g:i A') : ''); ?></div>
                        <div class="col-md-6"><span class="small text-secondary d-block">Venue / Location</span><?php echo e($booking->venue ?: 'Not provided'); ?></div>
                        <div class="col-md-6"><span class="small text-secondary d-block">Service type</span><?php echo e($booking->service_type); ?></div>
                        <div class="col-md-6"><span class="small text-secondary d-block">Package preference</span><?php echo e($booking->package_preference ?: 'No preference'); ?></div>
                        <div class="col-md-6"><span class="small text-secondary d-block"><?php echo e($booking->service_type === 'Packed Meals' ? 'Number of meals' : 'Estimated guest count'); ?></span><?php echo e(number_format($booking->guest_count)); ?></div>
                        <?php if($booking->package_price_per_pax): ?>
                            <div class="col-md-6"><span class="small text-secondary d-block"><?php echo e($booking->service_type === 'Packed Meals' ? 'Price per meal' : 'Package price per pax'); ?></span>₱<?php echo e(number_format((float) $booking->package_price_per_pax, 2)); ?></div>
                        <?php endif; ?>
                        <?php if($booking->estimated_total): ?>
                            <div class="col-md-6"><span class="small text-secondary d-block"><?php echo e($booking->service_type === 'Packed Meals' ? 'Estimated total' : ($booking->service_type === 'Party Trays' ? 'Estimated tray total' : 'Estimated package total')); ?></span>₱<?php echo e(number_format((float) $booking->estimated_total, 2)); ?> <span class="small text-secondary">(estimate only)</span></div>
                        <?php endif; ?>
                        <div class="col-md-6"><span class="small text-secondary d-block">Budget range</span><?php echo e($booking->budget_range ?: 'Not provided'); ?></div>
                        <?php if($booking->menu_selections): ?>
                            <div class="col-12">
                                <span class="small text-secondary d-block">Selected menu</span>
                                <dl class="mb-0 mt-2 grid grid-cols-1 gap-2 sm:grid-cols-2">
                                    <?php $__currentLoopData = ['package' => 'Packed meal package', 'main_dish' => 'Main dish', 'side' => 'Side', 'meat' => 'Meat dishes', 'soup' => 'Soup', 'vegetables' => 'Vegetable', 'pasta' => 'Pasta/Noodles', 'dessert' => 'Dessert', 'rice' => 'Rice', 'drinks' => 'Drink']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php if(!empty($booking->menu_selections[$key])): ?>
                                            <div>
                                                <dt class="font-semibold"><?php echo e($label); ?></dt>
                                                <dd class="mb-0 text-break"><?php echo e(is_array($booking->menu_selections[$key]) ? implode(', ', $booking->menu_selections[$key]) : $booking->menu_selections[$key]); ?></dd>
                                            </div>
                                        <?php endif; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php if($booking->service_type === 'Custom Catering'): ?>
                                        <div class="col-12 rounded-3 border p-3">
                                            <span class="small text-secondary d-block">Custom package selections</span>
                                            <div class="row g-2 mt-1">
                                                <?php $__currentLoopData = ($booking->menu_selections['selections'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category => $dishes): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <?php if(!empty($dishes)): ?>
                                                        <div class="col-md-6">
                                                            <strong class="text-capitalize"><?php echo e($category); ?> (<?php echo e(count($dishes)); ?> selected)</strong>: <?php echo e(implode(', ', $dishes)); ?>

                                                            <?php if(!empty($booking->menu_selections['details'][$category])): ?>
                                                                <ul class="small text-secondary mb-0 mt-1">
                                                                    <?php $__currentLoopData = $booking->menu_selections['details'][$category]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dish): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                        <li><?php echo e($dish['name']); ?><?php echo e(!empty($dish['description']) ? ' — '.$dish['description'] : ''); ?></li>
                                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                                </ul>
                                                            <?php endif; ?>
                                                        </div>
                                                    <?php endif; ?>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                <?php if(empty($booking->menu_selections['selections']) && !empty($booking->menu_selections['counts'])): ?>
                                                    <?php $__currentLoopData = $booking->menu_selections['counts']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category => $count): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <?php if((int) $count > 0): ?>
                                                            <div class="col-md-6"><strong class="text-capitalize"><?php echo e($category); ?> (<?php echo e($count); ?>)</strong>: <?php echo e(implode(', ', $booking->menu_selections['selections'][$category] ?? [])); ?></div>
                                                        <?php endif; ?>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                <?php endif; ?>
                                            </div>
                                            </div>
                                            <?php if(in_array($booking->status, ['Pending Quotation', 'Quotation Sent', 'Quotation Declined'], true)): ?>
                                                <form action="<?php echo e(route('admin.supervisors.catering-bookings.quotation', $booking)); ?>" method="POST" class="row g-2 align-items-end mt-3">
                                                    <?php echo csrf_field(); ?>
                                                    <div class="col-md-4"><label class="form-label small">Manual quotation amount</label><input type="number" name="quote_amount" min="0.01" step="0.01" required value="<?php echo e($booking->quote_amount); ?>" class="form-control"></div>
                                                    <div class="col-md-5"><label class="form-label small">Notes and inclusions</label><textarea name="quotation_notes" rows="2" class="form-control"><?php echo e($booking->quotation_notes); ?></textarea></div>
                                                    <div class="col-md-3"><button class="btn btn-mixeat-yellow w-100" type="submit"><?php echo e($booking->quotation_version ? 'Revise quotation' : 'Send quotation'); ?></button></div>
                                                </form>
                                                <p class="small text-secondary mt-2">Quotation status: <?php echo e($booking->quotation_status ?: 'Pending'); ?></p>
                                                <?php if($booking->quotation_status === 'Sent'): ?>
                                                    <?php
                                                        $quotationUrl = \Illuminate\Support\Facades\URL::temporarySignedRoute('catering.quotation', now()->addDays(14), ['booking' => $booking->id]);
                                                    ?>
                                                    <label class="form-label small mt-2">Secure quotation link (expires in 14 days)</label>
                                                    <input type="url" readonly value="<?php echo e($quotationUrl); ?>" class="form-control form-control-sm">
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                </dl>
                            </div>
                        <?php endif; ?>
                        <?php if($booking->trayItems->isNotEmpty()): ?>
                            <div class="col-12">
                                <span class="small text-secondary d-block">Food Trays &amp; Buffet Setups</span>
                                <div class="table-responsive mt-2">
                                    <table class="table table-sm table-bordered align-middle mb-0">
                                        <thead><tr><th>Category</th><th>Dish</th><th>Size</th><th>Quantity</th><th>Unit price</th><th>Subtotal</th></tr></thead>
                                        <tbody>
                                            <?php $__currentLoopData = $booking->trayItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $trayItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <tr>
                                                    <td><?php echo e($trayItem->category); ?></td>
                                                    <td><?php echo e($trayItem->dish); ?></td>
                                                    <td><?php echo e(ucfirst($trayItem->tray_size)); ?></td>
                                                    <td><?php echo e(number_format($trayItem->quantity)); ?></td>
                                                    <td>₱<?php echo e(number_format((float) $trayItem->unit_price, 2)); ?></td>
                                                    <td>₱<?php echo e(number_format((float) $trayItem->subtotal, 2)); ?></td>
                                                </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        <?php endif; ?>
                        <?php
                            $paymentSummary = $booking->payment_summary;
                        ?>
                        <div class="col-12">
                            <span class="small text-secondary d-block">Payment Summary</span>
                            <?php if(! $paymentSummary['payment_required']): ?>
                                <div class="mt-2 rounded-3 border p-3 text-warning">Awaiting Quotation Acceptance &ndash; Payment Not Yet Required</div>
                            <?php else: ?>
                                <div class="row g-2 mt-1 small">
                                    <div class="col-md-4"><strong>Total booking amount:</strong> ₱<?php echo e(number_format($paymentSummary['total_cents'] / 100, 2)); ?></div>
                                    <div class="col-md-4"><strong>Required deposit (30%):</strong> ₱<?php echo e(number_format($paymentSummary['required_deposit_cents'] / 100, 2)); ?></div>
                                    <div class="col-md-4"><strong>Verified deposit:</strong> ₱<?php echo e(number_format($paymentSummary['verified_deposit_cents'] / 100, 2)); ?></div>
                                    <div class="col-md-4"><strong>Total collected:</strong> ₱<?php echo e(number_format($paymentSummary['collected_cents'] / 100, 2)); ?></div>
                                    <div class="col-md-4"><strong>Remaining balance:</strong> ₱<?php echo e(number_format($paymentSummary['remaining_cents'] / 100, 2)); ?></div>
                                    <?php if($paymentSummary['latest_payment']): ?>
                                        <div class="col-md-4"><strong>Payment method:</strong> <?php echo e($paymentSummary['latest_payment']->payment_method); ?></div>
                                        <div class="col-md-4"><strong>Reference:</strong> <?php echo e($paymentSummary['latest_payment']->payment_reference ?: 'Not provided'); ?></div>
                                        <div class="col-md-4"><strong>Verification:</strong> <?php echo e($paymentSummary['latest_payment']->status); ?></div>
                                        <div class="col-md-4"><strong>Verified date:</strong> <?php echo e($paymentSummary['latest_payment']->verified_at?->format('M j, Y g:i A') ?: 'Not verified'); ?></div>
                                        <div class="col-md-4"><strong>Verified by:</strong> <?php echo e($paymentSummary['latest_payment']->verifiedBy?->name ?: 'Not verified'); ?></div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <?php if($booking->payments->isNotEmpty()): ?>
                            <?php
                                $verifiedDeposit = $booking->payments->first(fn ($payment) => $payment->payment_type === 'deposit' && $payment->status === 'Verified');
                                $paidCents = $paymentSummary['collected_cents'];
                                $totalCents = $paymentSummary['total_cents'];
                                $remainingCents = $paymentSummary['remaining_cents'];
                                $latestDeposit = $booking->payments->where('payment_type', 'deposit')->sortByDesc('id')->first();
                            ?>
                            <div class="col-12">
                                <span class="small text-secondary d-block">Security Bank payment review · <?php echo e($booking->payment_status); ?></span>
                                <div class="mt-2 space-y-3">
                                    <?php $__currentLoopData = $booking->payments->sortBy('id'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="rounded-3 border p-3">
                                            <div class="d-flex flex-wrap justify-content-between gap-2">
                                                <strong><?php echo e(ucfirst($payment->payment_type)); ?> · <?php echo e($payment->payment_method); ?></strong>
                                                <span><?php echo e($payment->status); ?> · ₱<?php echo e(number_format((float) ($payment->verified_amount ?? $payment->amount), 2)); ?></span>
                                            </div>
                                            <?php if($payment->payment_reference): ?>
                                                <div class="mt-1 small">Reference: <code><?php echo e($payment->payment_reference); ?></code></div>
                                            <?php endif; ?>
                                            <?php if($payment->duplicate_reference): ?>
                                                <div class="mt-1 small fw-bold text-danger">Duplicate reference: review this submission carefully before verifying.</div>
                                            <?php endif; ?>
                                            <?php if($payment->verification_remarks): ?>
                                                <div class="mt-1 small text-danger">Review notes: <?php echo e($payment->verification_remarks); ?></div>
                                            <?php endif; ?>
                                            <?php if($payment->verified_at): ?>
                                                <div class="mt-1 small text-secondary"><?php echo e($payment->status); ?> by <?php echo e($payment->verifiedBy?->name ?? 'Former staff member'); ?> · <?php echo e($payment->verified_at->format('M j, Y g:i A')); ?></div>
                                            <?php endif; ?>
                                            <?php if($payment->collected_at): ?>
                                                <div class="mt-1 small text-secondary">Collected by <?php echo e($payment->collectedBy?->name ?? 'Former staff member'); ?> · <?php echo e($payment->collected_at->format('M j, Y g:i A')); ?></div>
                                            <?php endif; ?>
                                            <?php if($payment->payment_type === 'deposit' && $payment->status === 'Pending Verification'): ?>
                                                <div class="d-flex flex-wrap gap-2 mt-3">
                                                    <form action="<?php echo e(route('admin.supervisors.catering-bookings.payments.verify', [$booking, $payment])); ?>" method="POST">
                                                        <?php echo csrf_field(); ?>
                                                        <button type="submit" class="btn btn-sm btn-success">Verify deposit &amp; confirm</button>
                                                    </form>
                                                    <form action="<?php echo e(route('admin.supervisors.catering-bookings.payments.reject', [$booking, $payment])); ?>" method="POST" class="d-flex flex-grow-1 gap-2">
                                                        <?php echo csrf_field(); ?>
                                                        <input type="text" name="verification_remarks" maxlength="1000" required class="form-control form-control-sm" placeholder="Reason for rejection">
                                                        <button type="submit" class="btn btn-sm btn-outline-danger">Reject</button>
                                                    </form>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                                <?php if($latestDeposit?->status === 'Rejected'): ?>
                                    <?php ($correctionUrl = \Illuminate\Support\Facades\URL::temporarySignedRoute('catering.payments.correction', now()->addDays(7), ['booking' => $booking->id])); ?>
                                    <div class="mt-3">
                                        <label for="correction-link-<?php echo e($booking->id); ?>" class="form-label small fw-semibold">Secure correction link (expires in 7 days)</label>
                                        <input id="correction-link-<?php echo e($booking->id); ?>" type="url" readonly value="<?php echo e($correctionUrl); ?>" class="form-control form-control-sm">
                                        <a href="<?php echo e($correctionUrl); ?>" target="_blank" rel="noopener" class="small">Open link to verify it before sharing</a>
                                    </div>
                                <?php endif; ?>
                                <?php if($verifiedDeposit && $remainingCents > 0): ?>
                                    <div class="mt-3 rounded-3 border p-3">
                                        <p class="mb-2"><strong>Remaining balance:</strong> ₱<?php echo e(number_format($remainingCents / 100, 2)); ?> of ₱<?php echo e(number_format($totalCents / 100, 2)); ?></p>
                                        <form action="<?php echo e(route('admin.supervisors.catering-bookings.payments.balance', $booking)); ?>" method="POST" class="row g-2 align-items-end">
                                            <?php echo csrf_field(); ?>
                                            <div class="col-sm-5">
                                                <label for="balance-amount-<?php echo e($booking->id); ?>" class="form-label small">Amount collected</label>
                                                <input id="balance-amount-<?php echo e($booking->id); ?>" type="number" name="amount" min="0.01" max="<?php echo e(number_format($remainingCents / 100, 2, '.', '')); ?>" step="0.01" required class="form-control form-control-sm">
                                            </div>
                                            <div class="col-sm-4">
                                                <label for="balance-method-<?php echo e($booking->id); ?>" class="form-label small">Collection method</label>
                                                <select id="balance-method-<?php echo e($booking->id); ?>" name="payment_method" required class="form-select form-select-sm">
                                                    <?php $__currentLoopData = ['Cash', 'Bank Transfer', 'Security Bank QR']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $method): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($method); ?>"><?php echo e($method); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </select>
                                            </div>
                                            <div class="col-sm-3"><button type="submit" class="btn btn-sm btn-mixeat-yellow w-100">Record payment</button></div>
                                        </form>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                        <div class="col-12"><span class="small text-secondary d-block">Special requests / notes</span><div class="text-break"><?php echo e($booking->special_requests ?: 'None'); ?></div></div>
                        <div class="col-12"><span class="small text-secondary d-block">Submitted</span><?php echo e($booking->created_at->format('F j, Y \a\t g:i A')); ?></div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <span class="small text-secondary">Current status: <strong class="text-dark"><?php echo e($booking->status); ?></strong></span>
                    <?php if($booking->payments->isEmpty()): ?>
                    <form action="<?php echo e(route('admin.supervisors.catering-bookings.status', $booking)); ?>" method="POST" class="d-flex flex-wrap gap-2 align-items-center">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PATCH'); ?>
                        <label class="visually-hidden" for="booking-status-<?php echo e($booking->id); ?>">Booking status</label>
                        <select id="booking-status-<?php echo e($booking->id); ?>" class="form-select form-select-sm" name="status" required>
                            <?php $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($status); ?>" <?php if($booking->status === $status): echo 'selected'; endif; ?>><?php echo e($status); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <button type="submit" class="btn btn-mixeat-yellow btn-sm">Save decision</button>
                    </form>
                    <?php else: ?>
                        <span class="small text-secondary">Booking confirmation is controlled by payment verification; fulfillment status is managed in the event calendar.</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mixeat\mixeat\resources\views\admin\marketing\catering-bookings.blade.php ENDPATH**/ ?>