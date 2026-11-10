<?php $__env->startSection('title', 'Careers'); ?>

<?php $__env->startSection('content'); ?>
<section class="py-14">
    <div class="mixeat-shell px-4 text-center">
        <p class="text-sm font-bold uppercase tracking-[0.18em] text-[#FBAB18]">CAREER OPPORTUNITIES</p>
        <h1 class="mt-3 text-4xl font-black text-[#FFFFFF]">Join The MixEat Team</h1>
        <p class="mt-3 text-lg text-[#B8B8B8]">Grow your career in a fast-growing food service & event catering company.</p>

        <?php if(session('success')): ?>
            <div class="mt-6 rounded-xl border border-[#333333] bg-[#1E1D1B] p-4 text-[#FBAB18]"><?php echo e(session('success')); ?></div>
        <?php endif; ?>

        <div class="mt-10 space-y-4">
            <?php $__empty_1 = true; $__currentLoopData = $jobs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $job): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="flex items-center justify-between gap-4 rounded-2xl border border-[#333333] bg-[#1E1D1B] p-6">
                    <div class="text-left">
                        <h2 class="text-xl font-bold text-[#FFFFFF]"><?php echo e($job->title); ?></h2>
                        <div class="mt-2 flex items-center gap-2 text-sm text-[#B8B8B8]">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#FBAB18]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 21s-6-4.35-6-10a6 6 0 1112 0c0 5.65-6 10-6 10z"/><circle cx="12" cy="11" r="2"/></svg>
                            <?php echo e($job->location); ?>

                        </div>
                        <p class="mt-1 text-sm text-[#B8B8B8]"><?php echo e($job->employment_type); ?></p>
                    </div>
                    <a href="<?php echo e(route('careers.apply.form', $job)); ?>" class="rounded-full bg-[#FBAB18] px-5 py-2 text-sm font-bold text-[#111111]">APPLY NOW</a>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <p class="text-[#B8B8B8]">No open positions at the moment.</p>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mixeat\mixeat\resources\views\storefront\careers.blade.php ENDPATH**/ ?>