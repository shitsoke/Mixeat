

<?php $__env->startSection('content'); ?>
<div class="mb-4">
    <p class="small fw-bold text-uppercase text-warning-emphasis mb-1">Marketing Admin</p>
    <h1 class="h2 fw-bold mb-1"><?php echo e($item->exists ? 'Edit catering food' : 'Add catering food'); ?></h1>
</div>
<section class="card-custom p-3 p-md-4">
    <?php if($errors->any()): ?><div class="alert alert-danger"><ul class="mb-0"><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($error); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul></div><?php endif; ?>
    <form action="<?php echo e($item->exists ? route('admin.marketing.catering-menu.update', $item) : route('admin.marketing.catering-menu.store')); ?>" method="POST" enctype="multipart/form-data" class="row g-3">
        <?php echo csrf_field(); ?>
        <?php if($item->exists): ?> <?php echo method_field('PUT'); ?> <?php endif; ?>
        <div class="col-md-6"><label class="form-label" for="name">Food name</label><input id="name" name="name" required maxlength="160" value="<?php echo e(old('name', $item->name)); ?>" class="form-control"></div>
        <div class="col-md-6"><label class="form-label" for="category">Category</label><select id="category" name="category" required class="form-select"><option value="">Select category</option><?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($key); ?>" <?php if(old('category', $item->category) === $key): echo 'selected'; endif; ?>><?php echo e($label); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></div>
        <div class="col-12"><label class="form-label" for="description">Description</label><textarea id="description" name="description" rows="4" maxlength="1000" class="form-control"><?php echo e(old('description', $item->description)); ?></textarea></div>
        <div class="col-md-8"><label class="form-label" for="image">Optional image</label><input id="image" type="file" name="image" accept="image/jpeg,image/png,image/webp" class="form-control"><div class="form-text">JPG, PNG, or WebP up to 2 MB.</div></div>
        <div class="col-md-4 d-flex align-items-center"><div class="form-check mt-3"><input id="is_active" type="checkbox" name="is_active" value="1" class="form-check-input" <?php if(old('is_active', $item->is_active)): echo 'checked'; endif; ?>><label class="form-check-label" for="is_active">Available to customers</label></div></div>
        <?php if($item->imageUrl()): ?><div class="col-12"><img src="<?php echo e($item->imageUrl()); ?>" alt="" class="rounded-3" style="width: 120px; height: 120px; object-fit: cover"></div><?php endif; ?>
        <div class="col-12 d-flex gap-2"><a class="btn btn-outline-secondary" href="<?php echo e(route('admin.marketing.catering-menu.index')); ?>">Cancel</a><button class="btn btn-mixeat-yellow">Save catering food</button></div>
    </form>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mixeat\mixeat\resources\views\admin\marketing\catering-menu\form.blade.php ENDPATH**/ ?>