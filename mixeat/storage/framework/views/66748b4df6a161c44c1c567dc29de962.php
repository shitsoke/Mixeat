

<?php $__env->startSection('title', $promotion->title.' | MixEat Promotions'); ?>

<?php $__env->startSection('content'); ?>
    <section class="py-12 md:py-16">
        <div class="mixeat-shell px-4">
            <a href="<?php echo e(route('featured')); ?>" class="inline-flex items-center gap-2 text-sm font-semibold text-[#A87900] hover:underline">
                <span aria-hidden="true">&larr;</span> Back to Featured Promotions
            </a>

            <article class="mx-auto mt-6 grid max-w-5xl overflow-hidden rounded-[32px] border border-[#E8DFAF] bg-white shadow-[0_20px_60px_rgba(229,169,0,0.12)] md:grid-cols-2">
                <img src="<?php echo e($promotion->image ? asset('storage/'.$promotion->image) : asset('images/mixeat.jpg')); ?>" alt="<?php echo e($promotion->title); ?>" class="h-full min-h-72 w-full object-cover">
                <div class="p-6 sm:p-9">
                    <?php if($promotion->label): ?>
                        <p class="text-sm font-bold uppercase tracking-[0.16em] text-[#E5A900]"><?php echo e($promotion->label); ?></p>
                    <?php endif; ?>
                    <div class="mt-2 flex flex-wrap items-start justify-between gap-3">
                        <h1 class="text-3xl font-black text-[#090909]"><?php echo e($promotion->title); ?></h1>
                        <?php if($promotion->price !== null): ?>
                            <span class="rounded-full bg-[#FFF4BF] px-4 py-2 text-lg font-bold text-[#A87900]">&#8369;<?php echo e(number_format((float) $promotion->price, 2)); ?></span>
                        <?php endif; ?>
                    </div>
                    <?php if($promotion->description): ?>
                        <p class="mt-4 whitespace-pre-line text-base leading-7 text-[#555555]"><?php echo e($promotion->description); ?></p>
                    <?php endif; ?>

                    <div class="mt-7 border-t border-[#E8DFAF] pt-6">
                        <h2 class="text-lg font-bold text-[#090909]">Promotion details</h2>
                        <div class="mt-3 whitespace-pre-line text-sm leading-7 text-[#555555]"><?php echo e($promotion->details ?: $promotion->description); ?></div>
                    </div>

                    <?php if($promotion->button_text && $promotion->button_url): ?>
                        <a href="<?php echo e($promotion->button_url); ?>" class="mt-7 inline-flex rounded-full bg-[#FFC60A] px-6 py-3 text-sm font-semibold text-[#090909] transition hover:bg-[#E5A900]">
                            <?php echo e($promotion->button_text); ?>

                        </a>
                    <?php endif; ?>
                </div>
            </article>
        </div>
    </section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mixeat\mixeat\resources\views\storefront\featured-promotion.blade.php ENDPATH**/ ?>