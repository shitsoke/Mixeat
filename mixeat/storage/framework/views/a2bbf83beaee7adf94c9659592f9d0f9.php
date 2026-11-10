

<?php $__env->startSection('title', 'Featured Promotions | MixEat'); ?>

<?php $__env->startSection('content'); ?>
    <section class="py-12 md:py-16">
        <div class="mixeat-shell px-4">
            <div class="mb-8">
                <p class="text-sm font-bold uppercase tracking-[0.18em] text-[#FBAB18]">MixEat offers</p>
                <h1 class="mt-2 text-4xl font-black text-[#FFFFFF]">Featured Promotions</h1>
                <p class="mt-3 max-w-2xl text-base leading-7 text-[#B8B8B8]">Explore the latest offers from MixEat.</p>
            </div>

            <?php if($promotions->isEmpty()): ?>
                <div class="rounded-[28px] border border-[#333333] bg-[#1A1A1A] px-6 py-12 text-center">
                    <h2 class="text-xl font-bold text-[#FFFFFF]">No promotions right now</h2>
                    <p class="mt-2 text-sm text-[#B8B8B8]">Please check back soon for new MixEat offers.</p>
                </div>
            <?php else: ?>
                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    <?php $__currentLoopData = $promotions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $promotion): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <article class="overflow-hidden rounded-[24px] border border-[#333333] bg-[#201D19] shadow-[0_14px_40px_rgba(229,169,0,0.10)]">
                            <img src="<?php echo e($promotion->image ? asset('storage/'.$promotion->image) : asset('images/mixeat.jpg')); ?>" alt="<?php echo e($promotion->title); ?>" class="aspect-[4/3] w-full object-cover">
                            <div class="flex min-h-48 flex-col p-5">
                                <?php if($promotion->label): ?>
                                    <p class="text-xs font-bold uppercase tracking-[0.12em] text-[#FBAB18]"><?php echo e($promotion->label); ?></p>
                                <?php endif; ?>
                                <div class="mt-1 flex items-start justify-between gap-3">
                                    <h2 class="text-lg font-bold text-[#FFFFFF]"><?php echo e($promotion->title); ?></h2>
                                    <?php if($promotion->price !== null): ?>
                                        <span class="shrink-0 font-bold text-[#FBAB18]">&#8369;<?php echo e(number_format((float) $promotion->price, 2)); ?></span>
                                    <?php endif; ?>
                                </div>
                                <p class="mt-2 flex-1 whitespace-pre-line text-sm leading-6 text-[#B8B8B8]"><?php echo e(\Illuminate\Support\Str::limit($promotion->description, 140)); ?></p>
                                <a href="<?php echo e(route('featured.show', $promotion)); ?>" class="mt-4 inline-flex self-start rounded-full border border-[#FBAB18] px-5 py-2.5 text-sm font-semibold text-[#FBAB18] transition hover:bg-[#201D19]">
                                    View details
                                </a>
                                <?php if($promotion->button_text && $promotion->button_url): ?>
                                    <a href="<?php echo e($promotion->button_url); ?>" class="mt-3 inline-flex self-start rounded-full bg-[#FBAB18] px-5 py-2.5 text-sm font-semibold text-[#FFFFFF] transition hover:bg-[#FBAB18]">
                                        <?php echo e($promotion->button_text); ?>

                                    </a>
                                <?php endif; ?>
                            </div>
                        </article>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endif; ?>
        </div>
    </section>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mixeat\mixeat\resources\views\storefront\featured.blade.php ENDPATH**/ ?>