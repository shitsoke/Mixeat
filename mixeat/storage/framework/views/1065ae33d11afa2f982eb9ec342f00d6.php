

<?php $__env->startSection('content'); ?>
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white fw-bold">Update About Page Content</div>
            <div class="card-body">
                <form action="<?php echo e(route('admin.cms.about.update')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="mb-3">
                        <label class="form-label">About Us Heading</label>
                        <input type="text" name="about_heading" class="form-control" value="<?php echo e($aboutHeading ?? ''); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Main Content</label>
                        <textarea name="about_content" class="form-control" rows="8" required><?php echo e($aboutContent ?? ''); ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Publish Updates</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mixeat\mixeat\resources\views\admin\cms\about.blade.php ENDPATH**/ ?>