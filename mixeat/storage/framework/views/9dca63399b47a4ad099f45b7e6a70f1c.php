

<?php $__env->startSection('title', 'My Catering Bookings | MixEat'); ?>

<?php $__env->startSection('content'); ?>
<section class="mixeat-shell px-4 py-10">
    <h1 class="text-3xl font-heading font-extrabold text-white">My Catering Bookings</h1>
    <div class="mt-6 space-y-4">
        <?php $__empty_1 = true; $__currentLoopData = $bookings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <article class="rounded-2xl border border-[#333333] bg-[#201D19] p-5">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <h2 class="text-xl font-bold text-white">MXC-<?php echo e($booking->created_at->format('Y')); ?>-<?php echo e(str_pad($booking->id, 6, '0', STR_PAD_LEFT)); ?></h2>
                        <p class="text-sm text-[#B8B8B8]"><?php echo e($booking->service_type); ?> · Event <?php echo e($booking->event_date->format('M j, Y')); ?></p>
                    </div>
                    <span class="rounded-full bg-[#FBAB18] px-3 py-1 text-xs font-bold text-[#111111]"><?php echo e($booking->status); ?></span>
                </div>
                <div class="mt-4 grid gap-3 text-sm text-[#B8B8B8] sm:grid-cols-3">
                    <div>Quotation: <strong class="text-white">₱<?php echo e(number_format((float) ($booking->quote_amount ?: 0), 2)); ?></strong></div>
                    <div>Payment: <strong class="text-white"><?php echo e($booking->payment_status); ?></strong></div>
                    <div>Package: <strong class="text-white"><?php echo e($booking->package_preference ?: 'Custom quotation'); ?></strong></div>
                </div>
                <?php if($booking->service_type === 'Custom Catering' && in_array($booking->status, ['Quotation Sent', 'Quotation Declined'], true)): ?>
                    <a class="mt-4 inline-flex rounded-full bg-[#FBAB18] px-4 py-2 text-sm font-bold text-[#111111]" href="<?php echo e(\Illuminate\Support\Facades\URL::temporarySignedRoute('catering.quotation', now()->addDays(14), ['booking' => $booking->id])); ?>">Review quotation</a>
                <?php endif; ?>
            </article>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <p class="rounded-2xl border border-[#333333] bg-[#201D19] p-6 text-[#B8B8B8]">No catering bookings are linked to your account yet.</p>
        <?php endif; ?>
    </div>
    <div class="mt-6"><?php echo e($bookings->links()); ?></div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mixeat\mixeat\resources\views\storefront\catering-bookings.blade.php ENDPATH**/ ?>