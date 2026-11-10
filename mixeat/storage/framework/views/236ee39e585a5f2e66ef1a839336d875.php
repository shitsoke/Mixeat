

<?php $__env->startSection('content'); ?>
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3 mb-4">
    <div><p class="small fw-bold text-uppercase text-warning-emphasis mb-1">Marketing Admin</p><h1 class="h2 fw-bold mb-1">Manage Catering Menu</h1><p class="text-secondary mb-0">Manage current catering foods, prices, inclusions, and availability.</p></div>
    <a class="btn btn-mixeat-yellow" href="<?php echo e($tab === 'foods' ? route('admin.marketing.catering-menu.create') : route('admin.marketing.catering-menu.catalog.create', $tab)); ?>">Add <?php echo e($tab === 'foods' ? 'food' : str_replace('-', ' ', $tab)); ?></a>
</div>
<?php if(session('success')): ?><div class="alert alert-warning border-0 shadow-sm rounded-4"><?php echo e(session('success')); ?></div><?php endif; ?>

<ul class="nav nav-pills mb-4 gap-2">
    <?php $__currentLoopData = ['foods' => 'Catering Foods', 'packages' => 'Catering Packages', 'packed-meals' => 'Packed Meals', 'party-trays' => 'Party Trays']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <li class="nav-item"><a class="nav-link <?php echo e($tab === $key ? 'active bg-dark' : 'border text-dark'); ?>" href="<?php echo e(route('admin.marketing.catering-menu.index', ['tab' => $key])); ?>"><?php echo e($label); ?></a></li>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</ul>

<?php if($tab === 'foods'): ?>
    <section class="card-custom p-3 p-md-4">
        <form method="GET" class="row g-2 mb-4">
            <input type="hidden" name="tab" value="foods">
            <div class="col-md-5"><input name="search" value="<?php echo e(request('search')); ?>" class="form-control" placeholder="Search food name or description"></div>
            <div class="col-md-3"><select name="category" class="form-select"><option value="">All categories</option><?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($key); ?>" <?php if(request('category') === $key): echo 'selected'; endif; ?>><?php echo e($label); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></div>
            <div class="col-md-2"><select name="status" class="form-select"><option value="">All status</option><option value="active" <?php if(request('status') === 'active'): echo 'selected'; endif; ?>>Active</option><option value="inactive" <?php if(request('status') === 'inactive'): echo 'selected'; endif; ?>>Inactive</option></select></div>
            <div class="col-md-2"><button class="btn btn-outline-dark w-100">Search</button></div>
        </form>
        <div class="table-responsive"><table class="table table-hover align-middle"><thead><tr><th>Food</th><th>Category</th><th>Status</th><th class="text-end">Actions</th></tr></thead><tbody>
        <?php $__empty_1 = true; $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr><td><div class="d-flex align-items-center gap-3"><?php if($item->imageUrl()): ?><img src="<?php echo e($item->imageUrl()); ?>" class="rounded-3" style="width:56px;height:56px;object-fit:cover"><?php endif; ?><div><strong><?php echo e($item->name); ?></strong><div class="small text-secondary"><?php echo e($item->description ?: 'No description'); ?></div></div></div></td><td><?php echo e($categories[$item->category]); ?></td><td><span class="badge <?php echo e($item->is_active && ! $item->trashed() ? 'text-bg-success' : 'text-bg-secondary'); ?>"><?php echo e($item->is_active && ! $item->trashed() ? 'Active' : 'Inactive'); ?></span></td><td class="text-end"><a class="btn btn-sm btn-outline-dark" href="<?php echo e(route('admin.marketing.catering-menu.edit', $item)); ?>">Edit</a> <form class="d-inline" method="POST" action="<?php echo e(route('admin.marketing.catering-menu.toggle', $item)); ?>"><?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?><button class="btn btn-sm btn-outline-dark"><?php echo e($item->is_active ? 'Deactivate' : 'Activate'); ?></button></form> <form class="d-inline" method="POST" action="<?php echo e(route('admin.marketing.catering-menu.destroy', $item)); ?>" onsubmit="return confirm('Archive this catering food?')"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?><button class="btn btn-sm btn-outline-danger">Delete</button></form></td></tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?> <tr><td colspan="4" class="text-center text-secondary py-5">No catering foods found.</td></tr><?php endif; ?>
        </tbody></table></div><?php echo e($items->links('pagination::bootstrap-5')); ?>

    </section>
<?php else: ?>
    <?php
        $catalogItems = ['packages' => $packages, 'packed-meals' => $packedMeals, 'party-trays' => $partyTrays][$tab];
    ?>
    <section class="card-custom p-3 p-md-4">
        <div class="table-responsive"><table class="table table-hover align-middle"><thead><tr><th>Name</th><th>Description / details</th><th>Price</th><th>Status</th><th class="text-end">Actions</th></tr></thead><tbody>
        <?php $__empty_1 = true; $__currentLoopData = $catalogItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr><td><strong><?php echo e($item->name); ?></strong><div class="small text-secondary"><?php echo e($item->code ?? ''); ?></div></td><td class="small text-secondary"><?php echo e($item->description); ?> <?php if($tab === 'party-trays'): ?><br><?php echo e(count($item->dishes ?? [])); ?> dishes · <?php echo e(implode(', ', array_values($item->sizes ?? []))); ?><?php endif; ?></td><td class="text-nowrap"><?php if($tab === 'party-trays'): ?>₱<?php echo e(number_format((float)$item->full_price, 2)); ?> / ₱<?php echo e(number_format((float)$item->half_price, 2)); ?><?php else: ?>₱<?php echo e(number_format((float)$item->price, 2)); ?><?php endif; ?></td><td><span class="badge <?php echo e($item->is_active && ! $item->trashed() ? 'text-bg-success' : 'text-bg-secondary'); ?>"><?php echo e($item->is_active && ! $item->trashed() ? 'Active' : 'Inactive'); ?></span></td><td class="text-end text-nowrap"><a class="btn btn-sm btn-outline-dark" href="<?php echo e(route('admin.marketing.catering-menu.catalog.edit', [$tab, $item->id])); ?>">Edit</a> <form class="d-inline" method="POST" action="<?php echo e(route('admin.marketing.catering-menu.catalog.toggle', [$tab, $item->id])); ?>"><?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?><button class="btn btn-sm btn-outline-dark"><?php echo e($item->is_active ? 'Deactivate' : 'Activate'); ?></button></form> <form class="d-inline" method="POST" action="<?php echo e(route('admin.marketing.catering-menu.catalog.destroy', [$tab, $item->id])); ?>" onsubmit="return confirm('Archive this item? Existing bookings will keep their saved details.')"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?><button class="btn btn-sm btn-outline-danger">Delete</button></form></td></tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?> <tr><td colspan="5" class="text-center text-secondary py-5">No items found.</td></tr><?php endif; ?>
        </tbody></table></div>
    </section>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mixeat\mixeat\resources\views\admin\marketing\catering-menu\index.blade.php ENDPATH**/ ?>