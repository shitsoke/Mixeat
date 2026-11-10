

<?php $__env->startSection('title', ($branch->exists ? 'Edit Branch' : 'Add Branch').' | MixEat'); ?>

<?php $__env->startSection('content'); ?>
<section class="py-14"><div class="mixeat-shell px-4"><div class="mx-auto max-w-2xl rounded-[30px] border border-[#333333] bg-[#201D19] p-8 shadow-sm">
    <p class="text-sm font-bold uppercase tracking-[0.18em] text-[#FBAB18]">Admin</p>
    <h1 class="mt-2 text-4xl font-black"><?php echo e($branch->exists ? 'Edit Branch' : 'Add Branch'); ?></h1>
    <?php if($errors->any()): ?><div class="mt-6 rounded-2xl bg-red-50 p-4 text-sm text-[#DC3545]"><ul class="list-disc pl-5"><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($error); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul></div><?php endif; ?>
    <form action="<?php echo e($branch->exists ? route('admin.branches.update', $branch) : route('admin.branches.store')); ?>" method="POST" class="mt-8 grid gap-5"><?php echo csrf_field(); ?> <?php if($branch->exists): ?> <?php echo method_field('PUT'); ?> <?php endif; ?>
        <label class="grid gap-2 text-sm font-semibold">Branch Name<input name="name" required value="<?php echo e(old('name', $branch->name)); ?>" class="rounded-2xl border border-[#333333] bg-[#1A1A1A] px-4 py-3"></label>
        <label class="grid gap-2 text-sm font-semibold">Address<input name="address" required value="<?php echo e(old('address', $branch->address)); ?>" class="rounded-2xl border border-[#333333] bg-[#1A1A1A] px-4 py-3"></label>
        <label class="grid gap-2 text-sm font-semibold">Opening Hours<input name="opening_hours" required value="<?php echo e(old('opening_hours', $branch->opening_hours)); ?>" placeholder="8:00 AM - 9:00 PM" class="rounded-2xl border border-[#333333] bg-[#1A1A1A] px-4 py-3"></label>
        <label class="grid gap-2 text-sm font-semibold">Telephone Number (Optional)<input name="telephone_number" value="<?php echo e(old('telephone_number', $branch->telephone_number)); ?>" placeholder="032-123-4567" class="rounded-2xl border border-[#333333] bg-[#1A1A1A] px-4 py-3"></label>
        <div class="flex gap-3"><a href="<?php echo e(route('admin.branches.index')); ?>" class="rounded-full border border-[#FBAB18] px-5 py-3 font-semibold text-[#FBAB18]">Cancel</a><button class="rounded-full bg-[#FBAB18] px-5 py-3 font-semibold text-[#FFFFFF]"><?php echo e($branch->exists ? 'Update Branch' : 'Save Branch'); ?></button></div>
    </form>
</div></div></section>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mixeat\mixeat\resources\views\admin\branches\form.blade.php ENDPATH**/ ?>