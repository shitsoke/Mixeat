

<?php $__env->startSection('content'); ?>
<style>
    body:has(.catering-marketing-page) {
        background: #111111;
        color: #FFFFFF;
    }

    body:has(.catering-marketing-page) .admin-navbar {
        background: #1A1A1A !important;
        border-color: #333333 !important;
    }

    body:has(.catering-marketing-page) .admin-navbar .brand-name,
    body:has(.catering-marketing-page) .admin-navbar .nav-link,
    body:has(.catering-marketing-page) .admin-navbar .admin-account {
        color: #FFFFFF !important;
    }

    body:has(.catering-marketing-page) .admin-navbar .nav-link:hover,
    body:has(.catering-marketing-page) .admin-navbar .nav-link:focus {
        color: #FBAB18 !important;
    }

    body:has(.catering-marketing-page) .admin-navbar .navbar-toggler {
        border-color: #555555;
    }

    body:has(.catering-marketing-page) .admin-navbar .navbar-toggler-icon {
        filter: invert(1);
    }

    .catering-marketing-page {
        color: #FFFFFF;
    }

    .catering-marketing-page .text-secondary {
        color: #B8B8B8 !important;
    }

    .catering-marketing-page .card-custom {
        border-color: #333333;
        background: #201D19;
        box-shadow: 0 12px 30px rgb(0 0 0 / 18%);
        color: #FFFFFF;
    }

    .catering-marketing-page h1,
    .catering-marketing-page h2,
    .catering-marketing-page h3 {
        color: #FFFFFF;
        font-family: 'Montserrat', sans-serif;
    }

    .catering-marketing-page .btn-outline-dark {
        border-color: #555555;
        color: #FFFFFF;
    }

    .catering-marketing-page .btn-outline-dark:hover,
    .catering-marketing-page .btn-outline-dark:focus {
        border-color: #FBAB18;
        background: #FBAB18;
        color: #111111;
    }

    .catering-marketing-page .table {
        --bs-table-color: #FFFFFF;
        --bs-table-bg: transparent;
        --bs-table-border-color: #333333;
        --bs-table-hover-color: #FFFFFF;
        --bs-table-hover-bg: rgb(251 171 24 / 6%);
        margin-bottom: 0;
    }

    .catering-marketing-page .table thead th {
        border-bottom-color: #555555;
        color: #FBAB18;
        font-size: 0.75rem;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .catering-marketing-page .table td {
        vertical-align: middle;
    }

    .catering-marketing-page a:not(.btn) {
        color: #FFC247;
    }

    .catering-marketing-page .badge.text-bg-light {
        border-color: #555555 !important;
        background: #292929 !important;
        color: #FFFFFF !important;
    }

    .catering-marketing-page .pagination {
        --bs-pagination-bg: #1A1A1A;
        --bs-pagination-color: #FFFFFF;
        --bs-pagination-border-color: #333333;
        --bs-pagination-hover-bg: #292929;
        --bs-pagination-hover-color: #FFC247;
        --bs-pagination-hover-border-color: #FBAB18;
        --bs-pagination-active-bg: #FBAB18;
        --bs-pagination-active-color: #111111;
        --bs-pagination-active-border-color: #FBAB18;
        --bs-pagination-disabled-bg: #1A1A1A;
        --bs-pagination-disabled-color: #777777;
        --bs-pagination-disabled-border-color: #333333;
    }

    @media (max-width: 767.98px) {
        .catering-marketing-page .card-custom {
            border-radius: 16px;
        }

        .catering-marketing-page > .d-flex:first-child .btn {
            width: 100%;
        }
    }
</style>
<div class="catering-marketing-page">
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3 mb-4">
    <div>
        <p class="small fw-bold text-uppercase text-warning-emphasis mb-1">Marketing Admin</p>
        <h1 class="h2 fw-bold mb-1">Catering Leads & Inquiries</h1>
        <p class="text-secondary mb-0">Review inquiries, manage landing page content, and track campaign performance.</p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a class="btn btn-outline-dark rounded-pill" href="<?php echo e(route('admin.marketing.catering-partners')); ?>">Manage Clients &amp; Partners</a>
    </div>
</div>

<section class="row g-3 mb-4" aria-label="Catering inquiry analytics">
    <div class="col-sm-6 col-lg-3"><div class="card-custom p-3 h-100"><div class="small text-secondary">Total inquiries</div><div class="fs-3 fw-bold"><?php echo e($bookings->total()); ?></div></div></div>
    <div class="col-sm-6 col-lg-3"><div class="card-custom p-3 h-100"><div class="small text-secondary">Inquiry conversion</div><div class="fs-3 fw-bold"><?php echo e(number_format($conversionRate, 1)); ?>%</div><div class="small text-secondary">Approved or fulfilled</div></div></div>
    <div class="col-sm-6 col-lg-3"><div class="card-custom p-3 h-100"><div class="small text-secondary">Popular packages</div>
        <?php $__empty_1 = true; $__currentLoopData = $popularPackages->take(2); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $package => $count): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><div class="fw-semibold"><?php echo e($package); ?> <span class="badge text-bg-warning"><?php echo e($count); ?></span></div><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><p class="mb-0 text-secondary">No package preferences yet.</p><?php endif; ?>
    </div></div>
    <div class="col-sm-6 col-lg-3"><div class="card-custom p-3 h-100"><div class="small text-secondary">Recent inquiry trends</div>
        <?php $__empty_1 = true; $__currentLoopData = $inquiryTrends; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $month => $count): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><div class="d-flex justify-content-between"><span><?php echo e(\Illuminate\Support\Carbon::createFromFormat('Y-m', $month)->format('M Y')); ?></span><strong><?php echo e($count); ?></strong></div><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><p class="mb-0 text-secondary">No inquiries yet.</p><?php endif; ?>
    </div></div>
</section>

<section class="card-custom p-4 mb-4">
    <h2 class="h4 fw-bold mb-3">Campaign Conversion</h2>
    <div class="table-responsive">
        <table class="table table-sm align-middle mb-0">
            <thead><tr><th>Campaign</th><th>Inquiries</th><th>Approved / fulfilled</th><th>Conversion</th></tr></thead>
            <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $campaignStats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $campaign => $stats): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr><td><?php echo e($campaign); ?></td><td><?php echo e($stats['inquiries']); ?></td><td><?php echo e($stats['converted']); ?></td><td><?php echo e($stats['inquiries'] ? number_format($stats['converted'] / $stats['inquiries'] * 100, 1) : '0.0'); ?>%</td></tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="4" class="text-secondary">Campaign conversion data will appear when inquiries arrive.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<section class="card-custom p-4">
    <div class="d-flex flex-column flex-md-row justify-content-between gap-2 mb-3">
        <div><h2 class="h4 fw-bold mb-1">Catering Inquiries</h2><p class="text-secondary mb-0">New requests are added with New status for supervisor follow-up.</p></div>
        <span class="badge text-bg-warning align-self-start"><?php echo e($bookings->total()); ?> total</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead><tr><th>Event</th><th>Client</th><th>Service / Package</th><th>Guests</th><th>Menu selections</th><th>Special requests</th><th>Campaign</th><th>Status</th></tr></thead>
            <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $bookings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td class="text-nowrap"><?php echo e($booking->event_date->format('M j, Y')); ?></td>
                    <td><strong><?php echo e($booking->client_name); ?></strong><br><a href="mailto:<?php echo e($booking->email); ?>"><?php echo e($booking->email); ?></a><br><a href="tel:<?php echo e($booking->phone); ?>"><?php echo e($booking->phone); ?></a></td>
                    <td>
                        <span class="small text-secondary"><?php echo e($booking->event_type ?: 'Event type not provided'); ?><?php echo e($booking->event_type_details ? ': '.$booking->event_type_details : ''); ?></span><br>
                        <?php echo e($booking->service_type); ?><br>
                        <span class="small text-secondary"><?php echo e($booking->package_preference ?: 'No package preference'); ?></span>
                        <?php if($booking->package_price_per_pax): ?>
                            <br><span class="small text-secondary">₱<?php echo e(number_format((float) $booking->package_price_per_pax, 2)); ?> per pax · estimated ₱<?php echo e(number_format((float) $booking->estimated_total, 2)); ?></span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo e(number_format($booking->guest_count)); ?></td>
                    <td class="small">
                        <?php if($booking->menu_selections): ?>
                            <?php $__currentLoopData = ['package' => 'Packed meal package', 'main_dish' => 'Main dish', 'side' => 'Side', 'meat' => 'Meat', 'soup' => 'Soup', 'vegetables' => 'Vegetable', 'pasta' => 'Pasta/Noodles', 'dessert' => 'Dessert', 'rice' => 'Rice', 'drinks' => 'Drink']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php if(!empty($booking->menu_selections[$key])): ?>
                                    <div><strong><?php echo e($label); ?>:</strong> <?php echo e(is_array($booking->menu_selections[$key]) ? implode(', ', $booking->menu_selections[$key]) : $booking->menu_selections[$key]); ?></div>
                                <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php elseif($booking->trayItems->isEmpty()): ?>
                            <span class="text-secondary">Not selected</span>
                        <?php endif; ?>
                        <?php if($booking->trayItems->isNotEmpty()): ?>
                            <div class="mt-1"><strong>Food Trays &amp; Buffet Setups:</strong>
                                <?php $__currentLoopData = $booking->trayItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $trayItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div><?php echo e($trayItem->category); ?> · <?php echo e($trayItem->dish); ?> · <?php echo e(ucfirst($trayItem->tray_size)); ?> × <?php echo e(number_format($trayItem->quantity)); ?> · ₱<?php echo e(number_format((float) $trayItem->unit_price, 2)); ?> each = ₱<?php echo e(number_format((float) $trayItem->subtotal, 2)); ?></div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <div class="fw-bold">Estimated total: ₱<?php echo e(number_format((float) $booking->estimated_total, 2)); ?></div>
                            </div>
                        <?php endif; ?>
                    </td>
                    <td><?php echo e($booking->special_requests ?: '—'); ?></td>
                    <td><?php echo e($booking->campaign_source); ?></td>
                    <td><span class="badge text-bg-light border"><?php echo e($booking->status); ?></span></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="8" class="py-4 text-center text-secondary">No catering inquiries yet.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php echo e($bookings->links()); ?>

</section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mixeat\mixeat\resources\views\admin\catering\marketing.blade.php ENDPATH**/ ?>