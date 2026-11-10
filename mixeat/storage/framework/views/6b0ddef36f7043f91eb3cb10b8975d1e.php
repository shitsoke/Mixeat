

<?php $__env->startSection('title', 'Manage Promotions | MixEat'); ?>

<?php $__env->startSection('content'); ?>
    <section class="py-12">
        <div class="mixeat-shell px-4">
            <div class="mb-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
                <div>
                    <p class="text-sm font-bold uppercase tracking-[0.18em] text-[#FBAB18]">Marketing</p>
                    <h1 class="mt-2 text-4xl font-black text-[#FFFFFF]">Manage Promotions</h1>
                    <p class="mt-3 text-[#B8B8B8]">Create and manage the offers shown on the Featured page.</p>
                </div>
                <a href="<?php echo e(route('admin.promotions.create')); ?>" class="inline-flex justify-center rounded-full bg-[#FBAB18] px-6 py-3 font-semibold text-[#FFFFFF] hover:bg-[#FBAB18]">Post Promotion</a>
            </div>

            <?php if(session('admin_status')): ?>
                <div class="mb-6 rounded-2xl bg-[#201D19] px-4 py-3 font-semibold text-[#FFFFFF]" role="status"><?php echo e(session('admin_status')); ?></div>
            <?php endif; ?>

            <div class="mb-6">
                <a href="<?php echo e(route('admin.promotion')); ?>" class="text-sm font-semibold text-[#FBAB18] underline">Manage homepage banner</a>
            </div>

            <?php if($promotions->isEmpty()): ?>
                <div class="rounded-[28px] border border-[#333333] bg-[#201D19] p-8 text-center text-[#B8B8B8]">
                    No promotions have been posted yet.
                </div>
            <?php else: ?>
                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    <?php $__currentLoopData = $promotions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $promotion): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <article class="overflow-hidden rounded-[24px] border border-[#333333] bg-[#201D19] shadow-sm">
                            <img src="<?php echo e($promotion->image ? asset('storage/'.$promotion->image) : asset('images/mixeat.jpg')); ?>" alt="<?php echo e($promotion->title); ?>" class="aspect-[4/3] w-full object-cover">
                            <div class="p-5">
                                <div class="flex items-center justify-between gap-3">
                                    <h2 class="font-bold text-[#FFFFFF]"><?php echo e($promotion->title); ?></h2>
                                    <span class="rounded-full px-3 py-1 text-xs font-bold <?php echo e($promotion->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600'); ?>">
                                        <?php echo e($promotion->is_active ? 'Published' : 'Hidden'); ?>

                                    </span>
                                </div>
                                <p class="mt-2 line-clamp-3 text-sm text-[#B8B8B8]"><?php echo e($promotion->description); ?></p>
                                <div class="mt-5 flex items-center justify-between gap-3">
                                    <a href="<?php echo e(route('admin.promotions.edit', $promotion)); ?>" class="font-semibold text-[#FBAB18] underline">Edit</a>
                                    <form action="<?php echo e(route('admin.promotions.destroy', $promotion)); ?>" method="POST" onsubmit="return confirm('Delete this promotion?')">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="font-semibold text-red-600 underline">Delete</button>
                                    </form>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endif; ?>
        </div>
    </section>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mixeat\mixeat\resources\views\admin\promotions\index.blade.php ENDPATH**/ ?>