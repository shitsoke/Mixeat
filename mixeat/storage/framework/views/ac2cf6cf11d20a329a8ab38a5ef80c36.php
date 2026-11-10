

<?php $__env->startSection('content'); ?>
<?php
    $isParty = $type === 'party-trays';
    $included = $item->included_dishes ?: [];
    $dishLines = implode("\n", $item->dishes ?: []);
    $categoryLines = implode(', ', $included['categories'] ?? []);
    $sideLines = implode("\n", $included['sides'] ?? []);
    $fixedLines = implode("\n", $included['fixed'] ?? []);
?>
<div class="mb-4"><p class="small fw-bold text-uppercase text-warning-emphasis mb-1">Marketing Admin</p><h1 class="h2 fw-bold"><?php echo e($item->exists ? 'Edit' : 'Add'); ?> <?php echo e(str_replace('-', ' ', $type)); ?></h1></div>
<section class="card-custom p-3 p-md-4">
<?php if($errors->any()): ?><div class="alert alert-danger"><ul class="mb-0"><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($error); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul></div><?php endif; ?>
<form method="POST" enctype="multipart/form-data" action="<?php echo e($item->exists ? route('admin.marketing.catering-menu.catalog.update', [$type, $item->id]) : route('admin.marketing.catering-menu.catalog.store', $type)); ?>" class="row g-3">
<?php echo csrf_field(); ?> <?php if($item->exists): ?> <?php echo method_field('PUT'); ?> <?php endif; ?>
<?php if(!$isParty): ?><div class="col-md-4"><label class="form-label">Code</label><input name="code" required value="<?php echo e(old('code', $item->code)); ?>" class="form-control"></div><?php endif; ?>
<div class="col-md-<?php echo e($isParty ? '6' : '8'); ?>"><label class="form-label">Name</label><input name="name" required value="<?php echo e(old('name', $item->name)); ?>" class="form-control"></div>
<div class="col-12"><label class="form-label">Description</label><textarea name="description" rows="3" class="form-control"><?php echo e(old('description', $item->description)); ?></textarea></div>
<?php if($isParty): ?>
<div class="col-md-3"><label class="form-label">Full price (PHP)</label><input type="number" step="0.01" min="0" name="full_price" value="<?php echo e(old('full_price', $item->full_price)); ?>" class="form-control" required></div>
<div class="col-md-3"><label class="form-label">Half price (PHP)</label><input type="number" step="0.01" min="0" name="half_price" value="<?php echo e(old('half_price', $item->half_price)); ?>" class="form-control" required></div>
<div class="col-md-3"><label class="form-label">Full tray label</label><input name="full_size" value="<?php echo e(old('full_size', $item->sizes['full'] ?? 'Full')); ?>" class="form-control" required></div>
<div class="col-md-3"><label class="form-label">Half tray label</label><input name="half_size" value="<?php echo e(old('half_size', $item->sizes['half'] ?? 'Half')); ?>" class="form-control" required></div>
<div class="col-12"><label class="form-label">Available dishes</label><textarea name="dish_lines" rows="6" required class="form-control" placeholder="Enter one dish per line"><?php echo e(old('dish_lines', $dishLines)); ?></textarea><div class="form-text">Enter one dish name per line.</div></div>
<?php else: ?>
<div class="col-md-4"><label class="form-label">Price per person (PHP)</label><input type="number" step="0.01" min="0" name="price" value="<?php echo e(old('price', $item->price)); ?>" class="form-control" required></div>
<?php if($type === 'packages'): ?>
<div class="col-md-2"><label class="form-label">Min guests</label><input type="number" min="1" name="min_guests" value="<?php echo e(old('min_guests', $item->min_guests)); ?>" class="form-control"></div><div class="col-md-2"><label class="form-label">Max guests</label><input type="number" min="1" name="max_guests" value="<?php echo e(old('max_guests', $item->max_guests)); ?>" class="form-control"></div>
<div class="col-md-3"><label class="form-label">Meat dishes included</label><input type="number" min="0" name="meat_count" value="<?php echo e(old('meat_count', $included['meat_count'] ?? 0)); ?>" class="form-control"></div>
<div class="col-md-5"><label class="form-label">Other categories</label><input name="included_categories" value="<?php echo e(old('included_categories', $categoryLines)); ?>" class="form-control" placeholder="Vegetables, Soup, Rice"></div>
<?php else: ?>
<div class="col-12"><label class="form-label">Side options</label><textarea name="side_lines" rows="3" class="form-control" placeholder="Enter one side per line"><?php echo e(old('side_lines', $sideLines)); ?></textarea></div>
<div class="col-12"><label class="form-label">Included items</label><textarea name="fixed_lines" rows="3" class="form-control" placeholder="Enter one included item per line"><?php echo e(old('fixed_lines', $fixedLines)); ?></textarea></div>
<?php endif; ?>
<?php endif; ?>
<div class="col-md-8"><label class="form-label">Optional image</label><input type="file" name="image" accept="image/jpeg,image/png,image/webp" class="form-control"><div class="form-text">JPG, PNG, or WebP up to 2 MB.</div></div>
<div class="col-md-4 d-flex align-items-center"><label class="form-check mt-3"><input type="checkbox" name="is_active" value="1" class="form-check-input" <?php if(old('is_active', $item->is_active)): echo 'checked'; endif; ?>> Available to customers</label></div>
<div class="col-12 d-flex gap-2"><a class="btn btn-outline-secondary" href="<?php echo e(route('admin.marketing.catering-menu.index', ['tab' => $type])); ?>">Cancel</a><button class="btn btn-mixeat-yellow">Save</button></div>
</form></section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mixeat\mixeat\resources\views\admin\marketing\catering-menu\catalog-form.blade.php ENDPATH**/ ?>