<?php $__env->startSection('title', 'Menu | MixEat'); ?>

<?php $__env->startSection('content'); ?>
    <section class="py-14">
        <div class="mixeat-shell px-4">
            <div class="mb-8 text-center">
                <p class="text-sm font-bold uppercase tracking-[0.18em] text-[#FBAB18]">Our Menu</p>
                <h1 class="mt-2 text-4xl font-black text-white">Rice Bowl Menu</h1>
                <p class="mt-2 text-[#B8B8B8]">Pick from our chef signature bowls or build your customized flavor combination!</p>
            </div>

            <div class="mb-6 flex flex-wrap justify-center gap-3">
                <?php $__currentLoopData = ['All Bowls','Chicken','Pork','Beef','Fish','Best Seller']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php ($active = ($filters['category'] ?? 'All') === $cat || (($filters['category'] ?? '') === 'Food Bowls' && $cat === 'All Bowls')); ?>
                    <a href="<?php echo e(route('menu', ['category' => $cat])); ?>" class="rounded-full px-5 py-2 text-sm font-semibold <?php echo e($active ? 'bg-[#FBAB18] text-[#111111]' : 'bg-[#201D19] text-white border border-[#333333]'); ?>"><?php echo e($cat); ?></a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            <div>
                <?php if(count($products)): ?>
                    <div class="product-grid">
                        <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if (isset($component)) { $__componentOriginal3fd2897c1d6a149cdb97b41db9ff827a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3fd2897c1d6a149cdb97b41db9ff827a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.product-card','data' => ['product' => $product]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('product-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['product' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($product)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3fd2897c1d6a149cdb97b41db9ff827a)): ?>
<?php $attributes = $__attributesOriginal3fd2897c1d6a149cdb97b41db9ff827a; ?>
<?php unset($__attributesOriginal3fd2897c1d6a149cdb97b41db9ff827a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3fd2897c1d6a149cdb97b41db9ff827a)): ?>
<?php $component = $__componentOriginal3fd2897c1d6a149cdb97b41db9ff827a; ?>
<?php unset($__componentOriginal3fd2897c1d6a149cdb97b41db9ff827a); ?>
<?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php else: ?>
                    <div class="rounded-[28px] border border-[#333333] bg-[#201D19] p-8 text-center">
                        <h2 class="text-xl font-bold text-white">No products found</h2>
                        <p class="mt-2 text-sm text-[#B8B8B8]">Try another category or adjust your filters.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mixeat\mixeat\resources\views\storefront\menu.blade.php ENDPATH**/ ?>