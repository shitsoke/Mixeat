

<?php $__env->startSection('title', 'Admin Branches | MixEat'); ?>

<?php $__env->startSection('content'); ?>
<section class="py-14"><div class="mixeat-shell px-4">
    <div class="mb-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
        <div><p class="text-sm font-bold uppercase tracking-[0.18em] text-[#FBAB18]">Admin</p><h1 class="mt-2 text-4xl font-black text-[#FFFFFF]">Manage Branches</h1></div>
        <a href="<?php echo e(route('admin.branches.create')); ?>" class="rounded-full bg-[#FBAB18] px-5 py-3 font-semibold text-[#FFFFFF]">Add Branch</a>
    </div>
    <?php if(session('admin_status')): ?><div class="mb-6 rounded-2xl bg-[#201D19] px-4 py-3 font-semibold"><?php echo e(session('admin_status')); ?></div><?php endif; ?>
    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
        <?php $__currentLoopData = $branches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $branch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="rounded-[28px] border border-[#333333] bg-[#201D19] p-6 shadow-sm">
                <h2 class="text-2xl font-black text-[#FFFFFF]"><?php echo e($branch->name); ?></h2>
                <div class="mt-4 space-y-2 text-sm text-[#B8B8B8]"><p><?php echo e($branch->address); ?></p><p><?php echo e($branch->opening_hours); ?></p><p><?php echo e($branch->distance); ?></p></div>
                <div class="mt-6 flex gap-3"><a href="<?php echo e(route('admin.branches.edit', $branch)); ?>" class="rounded-full border border-[#FBAB18] px-4 py-2 font-semibold text-[#FBAB18]">Edit</a><form action="<?php echo e(route('admin.branches.destroy', $branch)); ?>" method="POST"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?><button class="rounded-full border border-red-200 px-4 py-2 font-semibold text-[#DC3545]">Delete</button></form></div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</div></section>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mixeat\mixeat\resources\views\admin\branches\index.blade.php ENDPATH**/ ?>