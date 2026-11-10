<?php $__env->startSection('content'); ?>
<div class="p-6">
    <h1 class="text-2xl font-bold text-white mb-4">Job Openings</h1>
    <a href="<?php echo e(route('admin.jobs.create')); ?>" class="bg-[#FBAB18] text-black px-4 py-2 rounded-full font-bold">Add Job</a>
    <table class="w-full mt-6 text-white">
        <thead><tr><th>Title</th><th>Location</th><th>Type</th><th>Active</th><th>Actions</th></tr></thead>
        <tbody>
        <?php $__currentLoopData = $jobs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $job): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr class="border-t border-[#333]">
                <td><?php echo e($job->title); ?></td>
                <td><?php echo e($job->location); ?></td>
                <td><?php echo e($job->employment_type); ?></td>
                <td><?php echo e($job->is_active ? 'Yes' : 'No'); ?></td>
                <td>
                    <a href="<?php echo e(route('admin.jobs.edit', $job)); ?>" class="text-[#FBAB18]">Edit</a>
                    <form action="<?php echo e(route('admin.jobs.toggle', $job)); ?>" method="POST" class="inline"><?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?><button class="ml-2 text-white">Toggle</button></form>
                    <form action="<?php echo e(route('admin.jobs.destroy', $job)); ?>" method="POST" class="inline"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?><button class="ml-2 text-red-400">Delete</button></form>
                </td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>

    <h2 class="text-xl font-bold text-white mt-10 mb-4">Applications</h2>
    <table class="w-full text-white">
        <thead><tr><th>Name</th><th>Email</th><th>Position</th><th>Resume</th></tr></thead>
        <tbody>
        <?php $__currentLoopData = $applications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $app): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr class="border-t border-[#333]">
                <td><?php echo e($app->full_name); ?></td>
                <td><?php echo e($app->email); ?></td>
                <td><?php echo e($app->jobOpening->title ?? ''); ?></td>
                <td><a href="<?php echo e(route('admin.jobs.resume', $app)); ?>" class="text-[#FBAB18]">Download</a></td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mixeat\mixeat\resources\views\admin\jobs\index.blade.php ENDPATH**/ ?>