

<?php $__env->startSection('title', 'Promotion | MixEat'); ?>

<?php $__env->startSection('content'); ?>
<section class="py-14"><div class="mixeat-shell px-4"><div class="mx-auto max-w-2xl rounded-[30px] border border-[#333333] bg-[#201D19] p-8 shadow-sm">
    <p class="text-sm font-bold uppercase tracking-[0.18em] text-[#FBAB18]">Admin</p>
    <h1 class="mt-2 text-4xl font-black">Homepage Promotion</h1>
    <p class="mt-3 text-[#B8B8B8]">Change the promotional image and text shown in the homepage feature banner.</p>
    <?php if(session('admin_status')): ?><div class="mt-6 rounded-2xl bg-[#201D19] px-4 py-3 font-semibold"><?php echo e(session('admin_status')); ?></div><?php endif; ?>
    <?php if($errors->any()): ?><div class="mt-6 rounded-2xl bg-red-50 p-4 text-sm text-[#DC3545]"><ul class="list-disc pl-5"><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($error); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul></div><?php endif; ?>
    <form action="<?php echo e(route('admin.promotion.update')); ?>" method="POST" enctype="multipart/form-data" class="mt-8 grid gap-5"><?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
        <label class="grid gap-2 text-sm font-semibold">Label<input name="label" required value="<?php echo e(old('label', $promotion['promotion_label'] ?? "Today's favorite")); ?>" class="rounded-2xl border border-[#333333] bg-[#1A1A1A] px-4 py-3"></label>
        <label class="grid gap-2 text-sm font-semibold">Title<input name="title" required value="<?php echo e(old('title', $promotion['promotion_title'] ?? 'Chicken Meal Combo')); ?>" class="rounded-2xl border border-[#333333] bg-[#1A1A1A] px-4 py-3"></label>
        <label class="grid gap-2 text-sm font-semibold">
            Price <span class="font-normal text-[#777777]">(Optional)</span>
            <input name="price" type="number" step="0.01" min="0" placeholder="Leave empty if not applicable" value="<?php echo e(old('price', $promotion['promotion_price'] ?? '')); ?>" class="rounded-2xl border border-[#333333] bg-[#1A1A1A] px-4 py-3">
        </label>
        <label class="grid gap-2 text-sm font-semibold">Promotion Image<input name="image" type="file" accept="image/jpeg,image/png,image/webp" class="rounded-2xl border border-[#333333] bg-[#1A1A1A] px-4 py-3"><span class="text-xs font-normal text-[#B8B8B8]">Upload a new image to replace the current promotion image.</span></label>
        <div class="flex gap-3"><a href="<?php echo e(route('admin.promotions.index')); ?>" class="rounded-full border border-[#FBAB18] px-5 py-3 font-semibold text-[#FBAB18]">Back</a><button class="rounded-full bg-[#FBAB18] px-5 py-3 font-semibold text-[#FFFFFF]">Save Promotion</button></div>
    </form>
</div></div></section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mixeat\mixeat\resources\views/admin/promotion.blade.php ENDPATH**/ ?>