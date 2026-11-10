<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'product',
    'compact' => false,
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
    'product',
    'compact' => false,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php ($orderingOpen = app(\App\Services\OrderingHours::class)->isOpen()); ?>

<div class="product-card group flex h-full min-w-0 flex-col overflow-hidden rounded-[28px] border border-[#333333] bg-[#201D19] shadow-[0_14px_40px_rgba(229,169,0,0.12)] transition duration-200 hover:-translate-y-1 hover:shadow-[0_20px_50px_rgba(229,169,0,0.16)]">
    <div class="product-image-frame relative overflow-hidden">
        <img src="<?php echo e($product['image']); ?>" alt="<?php echo e($product['name']); ?>" onerror="this.onerror=null; this.src='<?php echo e(asset('images/mixeat.jpg')); ?>';" class="object-cover transition duration-300 group-hover:scale-105">
        <?php if(!empty($product['badge'])): ?>
            <span class="absolute left-4 top-4 rounded-full bg-[#FBAB18] px-3 py-1 text-[10px] font-bold uppercase tracking-[0.12em] text-[#FFFFFF] shadow-md">
                <?php echo e($product['badge']); ?>

            </span>
        <?php endif; ?>
    </div>

    <div class="flex flex-1 flex-col space-y-4 p-5">
        <div class="flex min-w-0 items-start justify-between gap-4">
            <div class="min-w-0">
                <p class="text-xs font-semibold uppercase tracking-[0.12em] text-[#FBAB18]"><?php echo e($product['category']); ?></p>
                <h3 class="mt-2 break-words text-xl font-bold text-[#FFFFFF]"><?php echo e($product['name']); ?></h3>
            </div>
            <div class="rounded-full bg-[#201D19] px-2.5 py-1 text-sm font-bold text-[#FBAB18]">
                <?php if($product['available'] ?? true): ?>
                    Available
                <?php else: ?>
                    Sold Out
                <?php endif; ?>
            </div>
        </div>

        <p class="min-h-12 text-sm leading-6 text-[#B8B8B8]"><?php echo e($product['description']); ?></p>

        <div class="mt-auto flex flex-wrap items-center justify-between gap-3 pt-2">
            <div class="text-2xl font-extrabold text-[#FBAB18]">&#8369;<?php echo e(number_format($product['price'], 2)); ?></div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="<?php echo e(route('product', ['id' => $product['id']])); ?>" class="rounded-full border border-[#FBAB18] px-3 py-2 text-sm font-semibold text-[#FBAB18] transition hover:bg-[#201D19]">
                    View Details
                </a>
                <?php if($product['available'] ?? true): ?>
                    <form action="<?php echo e(route('cart.add', ['id' => $product['id']])); ?>" method="POST" data-ordering-action="add-to-cart">
                        <?php echo csrf_field(); ?>
                        <button type="submit" data-ordering-open-label="Add to Cart" <?php if(!$orderingOpen): echo 'disabled'; endif; ?> class="rounded-full bg-[#FBAB18] px-4 py-2.5 text-sm font-semibold text-[#FFFFFF] shadow-lg shadow-[#FBAB18]/25 transition hover:bg-[#FBAB18] disabled:cursor-not-allowed disabled:bg-[#333333] disabled:text-[#B8B8B8]">
                            <?php echo e($orderingOpen ? 'Add to Cart' : 'Online Ordering Closed'); ?>

                        </button>
                    </form>
                <?php else: ?>
                    <button type="button" disabled class="cursor-not-allowed rounded-full bg-[#333333] px-4 py-2.5 text-sm font-semibold text-[#B8B8B8]">
                        Sold Out
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>





<?php /**PATH C:\xampp\htdocs\mixeat\mixeat\resources\views\components\product-card.blade.php ENDPATH**/ ?>