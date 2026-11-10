

<?php $__env->startSection('title', ($branchScoped ?? false) ? 'Commissary Foods | MixEat' : 'Admin Foods | MixEat'); ?>

<?php $__env->startSection('content'); ?>
<section class="py-14">
    <div class="mixeat-shell px-4">
        <div class="mb-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
            <div>
                <p class="text-sm font-bold uppercase tracking-[0.18em] text-[#FBAB18]">
                    <?php echo e(($branchScoped ?? false) ? 'MixEat Commissary' : 'Admin'); ?>

                </p>
                <h1 class="mt-2 text-4xl font-black text-[#FFFFFF]">Manage Foods</h1>
            </div>
            <a href="<?php echo e(route(($manageRoute ?? 'admin.products').'.create')); ?>" class="rounded-full bg-[#FBAB18] px-5 py-3 font-semibold text-[#111111]">
                Add Food
            </a>
        </div>

        <?php if(session('admin_status')): ?>
            <div class="mb-6 rounded-2xl bg-[#201D19] px-4 py-3 font-semibold"><?php echo e(session('admin_status')); ?></div>
        <?php endif; ?>

        <?php
            $commissaryBranch = ($branchScoped ?? false)
                ? $branches->first()
                : $branches->first(fn ($branch) => str_contains(strtolower($branch->name), 'mixeat commissary'));
        ?>

        <div class="overflow-x-auto rounded-[28px] border border-[#333333] bg-[#201D19] shadow-sm">
            <table class="w-full min-w-[860px] table-fixed text-left text-sm">
                <colgroup>
                    <col class="w-[36%]">
                    <col class="w-[16%]">
                    <col class="w-[14%]">
                    <col class="w-[18%]">
                    <col class="w-[16%]">
                </colgroup>
                <thead class="bg-[#1A1A1A] text-[#B8B8B8]">
                    <tr>
                        <th scope="col" class="p-4">Food</th>
                        <th scope="col" class="p-4">Category</th>
                        <th scope="col" class="p-4">Price</th>
                        <th scope="col" class="p-4">MixEat Commissary</th>
                        <th scope="col" class="p-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php
                        $commissaryPivot = $commissaryBranch
                            ? $product->branches->firstWhere('id', $commissaryBranch->id)?->pivot
                            : null;
                        $status = $commissaryPivot?->available ?? false;
                        $price = $commissaryPivot?->price ?? $product->price;
                        $availabilityParameters = ($manageRoute ?? 'admin.products') === 'admin.products' && $commissaryBranch
                            ? [$product, $commissaryBranch]
                            : $product;
                    ?>
                    <tr class="border-t border-[#333333] align-top">
                        <td class="p-4">
                            <div class="flex min-w-0 items-start gap-3">
                                <img src="<?php echo e($product->imageUrl()); ?>" alt="" class="h-14 w-14 shrink-0 rounded-xl object-cover">
                                <div class="min-w-0">
                                    <strong class="block break-words text-[#FFFFFF]"><?php echo e($product->name); ?></strong>
                                    <p class="mt-1 break-words text-xs leading-5 text-[#B8B8B8]"><?php echo e($product->description); ?></p>
                                </div>
                            </div>
                        </td>
                        <td class="break-words p-4 text-[#B8B8B8]"><?php echo e($product->category); ?></td>
                        <td class="whitespace-nowrap p-4 font-semibold text-[#FFFFFF]">&#8369;<?php echo e(number_format((float) $price, 2)); ?></td>
                        <td class="p-4">
                            <?php if($commissaryBranch): ?>
                                <form action="<?php echo e(route(($manageRoute ?? 'admin.products').'.availability', $availabilityParameters)); ?>" method="POST">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('PATCH'); ?>
                                    <input type="hidden" name="available" value="<?php echo e($status ? 0 : 1); ?>">
                                    <button type="submit" class="whitespace-nowrap rounded-full px-3 py-1 text-xs font-bold <?php echo e($status ? 'bg-[#EAF6EE] text-[#198754]' : 'bg-red-50 text-[#DC3545]'); ?>">
                                        <?php echo e($status ? 'Available' : 'Unavailable'); ?>

                                    </button>
                                </form>
                            <?php else: ?>
                                <span class="text-xs text-[#B8B8B8]">Not assigned</span>
                            <?php endif; ?>
                        </td>
                        <td class="p-4">
                            <div class="flex flex-wrap gap-x-3 gap-y-2">
                                <a href="<?php echo e(route(($manageRoute ?? 'admin.products').'.edit', $product)); ?>" class="font-semibold text-[#FBAB18]">Edit</a>
                                <form action="<?php echo e(route(($manageRoute ?? 'admin.products').'.destroy', $product)); ?>" method="POST" onsubmit="return confirm('Delete this food item?')">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="font-semibold text-[#DC3545]">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="5" class="p-8 text-center text-[#B8B8B8]">No food items found.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mixeat\mixeat\resources\views\admin\products\index.blade.php ENDPATH**/ ?>