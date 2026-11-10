

<?php $__env->startSection('content'); ?>
<div class="mb-4">
    <p class="small fw-bold text-uppercase text-warning-emphasis mb-1">Marketing Admin</p>
    <h1 class="h2 fw-bold mb-1">Marketing Dashboard</h1>
    <p class="text-secondary mb-0">A quick overview of catering activity and shortcuts to your marketing tools.</p>
</div>

<section class="row g-3 mb-4" aria-label="Marketing dashboard summary">
    <div class="col-sm-6 col-lg-3">
        <div class="card-custom p-3 h-100">
            <div class="small text-secondary">Catering booking requests</div>
            <div class="fs-3 fw-bold"><?php echo e($totalBookings); ?></div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card-custom p-3 h-100">
            <div class="small text-secondary">Awaiting review</div>
            <div class="fs-3 fw-bold"><?php echo e($awaitingReview); ?></div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card-custom p-3 h-100">
            <div class="small text-secondary">Accepted / fulfillment</div>
            <div class="fs-3 fw-bold"><?php echo e($acceptedBookings); ?></div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card-custom p-3 h-100">
            <div class="small text-secondary">Visible catering partners</div>
            <div class="fs-3 fw-bold"><?php echo e($visiblePartners); ?></div>
        </div>
    </div>
</section>

<section class="row g-3" aria-label="Marketing tools">
    <div class="col-md-6 col-xl-3">
        <a class="card-custom d-block h-100 p-4 text-decoration-none text-dark" href="<?php echo e(route('admin.marketing.catering-bookings')); ?>">
            <h2 class="h5 fw-bold">Catering Bookings</h2>
            <p class="mb-0 text-secondary">Review event requests and accept, decline, or update booking statuses.</p>
        </a>
    </div>
    <div class="col-md-6 col-xl-3">
        <a class="card-custom d-block h-100 p-4 text-decoration-none text-dark" href="<?php echo e(route('admin.marketing.catering-calendar')); ?>">
            <h2 class="h5 fw-bold">Event Calendar &amp; Fulfillment</h2>
            <p class="mb-0 text-secondary">Plan dates, prepare quotes, track payments, and assign branches and staff.</p>
        </a>
    </div>
    <div class="col-md-6 col-xl-3">
        <a class="card-custom d-block h-100 p-4 text-decoration-none text-dark" href="<?php echo e(route('admin.marketing.catering-partners')); ?>">
            <h2 class="h5 fw-bold">Clients &amp; Partners</h2>
            <p class="mb-0 text-secondary">Manage partner logos and their visibility on the Catering page.</p>
        </a>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mixeat\mixeat\resources\views\admin\marketing\dashboard.blade.php ENDPATH**/ ?>