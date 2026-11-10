<?php $__env->startSection('content'); ?>
<div class="p-6 max-w-xl">
    <h1 class="text-2xl font-bold text-white mb-4">Create Job</h1>
    <form action="<?php echo e(route('admin.jobs.store')); ?>" method="POST" class="space-y-4">
        <?php echo csrf_field(); ?>
        <input name="title" placeholder="Title" required class="w-full p-3 rounded-xl bg-[#1E1D1B] text-white border border-[#333]" />
        <input name="location" placeholder="Location" required class="w-full p-3 rounded-xl bg-[#1E1D1B] text-white border border-[#333]" />
        <input name="employment_type" placeholder="Employment Type" required class="w-full p-3 rounded-xl bg-[#1E1D1B] text-white border border-[#333]" />
        <label class="text-white"><input type="checkbox" name="is_active" checked /> Active</label>
        <button class="bg-[#FBAB18] text-black px-4 py-2 rounded-full font-bold">Save</button>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mixeat\mixeat\resources\views\admin\jobs\create.blade.php ENDPATH**/ ?>