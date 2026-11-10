

<?php $__env->startSection('title', 'Branches | MixEat'); ?>

<?php $__env->startSection('content'); ?>
    <section class="py-14">
        <div class="mixeat-shell px-4">
            <div class="mb-8 text-center">
                <p class="text-sm font-bold uppercase tracking-[0.18em] text-[#FBAB18]">Our Locations</p>
                <h1 class="mt-2 text-4xl font-black text-[#FFFFFF]">Find MixEat Near You</h1>
                <p class="mt-3 text-lg text-[#B8B8B8]">Check out our branch locations and visit us anytime.</p>
            </div>


            <div class="grid gap-6 lg:grid-cols-3">
                <?php $__currentLoopData = $branches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $branch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="rounded-[30px] border border-[#333333] bg-[#111111] p-6 shadow-[0_14px_40px_rgba(229,169,0,0.10)]">
                        <div class="mb-4 flex items-center justify-between gap-3">
                            <h2 class="text-2xl font-black text-[#FFFFFF]"><?php echo e($branch['name']); ?></h2>
                        </div>
                        <div class="space-y-3 text-[#B8B8B8]">
                            <p><span class="font-semibold text-[#FFFFFF]">Address:</span> <?php echo e($branch['address']); ?></p>
                            <p><span class="font-semibold text-[#FFFFFF]">Opening Hours:</span> <?php echo e($branch['opening_hours']); ?></p>
                            <?php if(!empty($branch['telephone_number'])): ?><p><span class="font-semibold text-[#FFFFFF]">Telephone:</span> <?php echo e($branch['telephone_number']); ?></p><?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </section>
<?php $__env->stopSection(); ?>





<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mixeat\mixeat\resources\views\storefront\branches.blade.php ENDPATH**/ ?>