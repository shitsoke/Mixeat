

<?php $__env->startSection('content'); ?>
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white fw-bold">Update Contact Information</div>
            <div class="card-body">
                <form action="<?php echo e(route('admin.cms.contact.update')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="mb-3">
                        <label class="form-label">Contact Phone / Mobile</label>
                        <input type="text" name="contact_phone" class="form-control" value="<?php echo e($contactPhone ?? ''); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="contact_email" class="form-control" value="<?php echo e($contactEmail ?? ''); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Main Office Address</label>
                        <textarea name="contact_address" class="form-control" rows="4" required><?php echo e($contactAddress ?? ''); ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Save Contact Details</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mixeat\mixeat\resources\views\admin\cms\contact.blade.php ENDPATH**/ ?>