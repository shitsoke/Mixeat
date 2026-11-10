



<?php $__env->startSection('title', 'Customer Feedback | MixEat'); ?>



<?php $__env->startSection('content'); ?>

    <section class="py-12 md:py-16">

        <div class="mixeat-shell px-4">

            <div class="mb-8">

                <p class="text-sm font-bold uppercase tracking-[0.18em] text-[#FBAB18]">Customer feedback</p>

                <h1 class="mt-2 text-4xl font-black text-[#FFFFFF]">What customers are saying</h1>

                <p class="mt-3 max-w-2xl text-base leading-7 text-[#B8B8B8]">Read feedback shared by the MixEat community, or tell us about your own experience.</p>

            </div>



            <div class="grid items-start gap-8 lg:grid-cols-[minmax(0,1fr)_360px]">

                <section aria-label="Approved customer reviews">

                    <?php if($feedbackError): ?>

                        <div role="status" class="rounded-2xl border border-[#333333] bg-[#201D19] p-5 text-sm text-[#B8B8B8]">

                            <?php echo e($feedbackError); ?>


                        </div>

                    <?php elseif(count($reviews) === 0): ?>

                        <div class="rounded-2xl border border-[#333333] bg-[#201D19] p-6 text-sm text-[#B8B8B8]">

                            No approved customer reviews are available yet.

                        </div>

                    <?php else: ?>

                        <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-3">

                            <?php $__currentLoopData = $reviews; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $review): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                                <?php ($rating = is_numeric($review['rating']) ? max(0, min(5, (int) round((float) $review['rating']))) : null); ?>

                                <article class="flex h-full flex-col overflow-hidden rounded-[24px] border border-[#333333] bg-[#201D19] shadow-[0_10px_30px_rgba(229,169,0,0.08)]">

                                    <?php if($review['image']): ?>

                                        <img src="<?php echo e($review['image']); ?>" alt="Photo shared with <?php echo e($review['name']); ?>'s feedback" loading="lazy" class="h-48 w-full bg-[#1A1A1A] object-cover">

                                    <?php endif; ?>

                                    <div class="flex flex-1 flex-col p-5">

                                        <div class="flex flex-wrap items-start justify-between gap-3">

                                            <h2 class="text-lg font-bold text-[#FFFFFF]"><?php echo e($review['name']); ?></h2>

                                            <?php if($rating !== null): ?>

                                                <span class="text-sm font-semibold text-[#FBAB18]" aria-label="<?php echo e($rating); ?> out of 5 stars">

                                                    <?php echo e(str_repeat('â˜…', $rating)); ?><?php echo e(str_repeat('â˜†', 5 - $rating)); ?>


                                                </span>

                                            <?php endif; ?>

                                        </div>

                                        <p class="mt-3 line-clamp-3 whitespace-pre-line leading-6 text-[#B8B8B8]"><?php echo e($review['review']); ?></p>

                                        <div class="mt-auto flex items-center justify-between gap-3 pt-5">

                                            <?php if($review['date'] !== ''): ?>

                                                <p class="text-xs font-medium text-[#777777]"><?php echo e($review['date']); ?></p>

                                            <?php else: ?>

                                                <span></span>

                                            <?php endif; ?>

                                            <button

                                                type="button"

                                                data-feedback-details

                                                data-name="<?php echo e($review['name']); ?>"

                                                data-review="<?php echo e($review['review']); ?>"

                                                data-rating="<?php echo e($rating ?? ''); ?>"

                                                data-date="<?php echo e($review['date']); ?>"

                                                data-image="<?php echo e($review['image'] ?? ''); ?>"

                                                class="shrink-0 rounded-full bg-[#FBAB18] px-4 py-2 text-sm font-bold text-[#FFFFFF] transition hover:bg-[#FBAB18] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#FBAB18]"

                                            >

                                                View details

                                            </button>

                                        </div>

                                    </div>

                                </article>

                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                        </div>

                    <?php endif; ?>

                </section>



                <aside class="rounded-[28px] border border-[#333333] bg-[#1A1A1A] p-6 text-center shadow-[0_14px_40px_rgba(229,169,0,0.10)]">

                    <h2 class="text-2xl font-black text-[#FFFFFF]">Share your feedback</h2>

                    <p class="mt-3 text-sm leading-6 text-[#B8B8B8]">Scan the QR code with your phone camera to open our feedback form.</p>

                    <img src="<?php echo e(asset(config('feedback.qr_image', 'images/feedback-qr-placeholder.svg'))); ?>" alt="QR code placeholder for the MixEat Google feedback form" class="mx-auto mt-5 aspect-square w-full max-w-[240px] rounded-2xl border border-[#333333] bg-[#201D19] object-contain p-3">



                    <?php if(filter_var($formUrl, FILTER_VALIDATE_URL) && in_array(parse_url($formUrl, PHP_URL_SCHEME), ['http', 'https'], true)): ?>

                        <a href="<?php echo e($formUrl); ?>" target="_blank" rel="noopener noreferrer" class="mt-5 inline-flex w-full items-center justify-center rounded-full bg-[#FBAB18] px-6 py-3.5 text-sm font-bold text-[#FFFFFF] transition hover:bg-[#FBAB18]">

                            Open the feedback form

                        </a>

                    <?php else: ?>

                        <p class="mt-5 rounded-2xl bg-[#201D19] p-4 text-sm text-[#777777]">The feedback form link will be available soon.</p>

                    <?php endif; ?>

                </aside>

            </div>

        </div>

    </section>



    <dialog id="feedback-details-dialog" class="m-auto max-h-[90vh] w-[calc(100%-2rem)] max-w-2xl overflow-y-auto rounded-[28px] border border-[#333333] bg-[#201D19] p-0 text-[#FFFFFF] shadow-2xl backdrop:bg-[#FFFFFF]/60" aria-labelledby="feedback-details-name">

        <div class="p-6 md:p-8">

            <div class="mb-5 flex items-start justify-between gap-4">

                <div>

                    <p class="text-xs font-bold uppercase tracking-[0.16em] text-[#FBAB18]">Customer feedback</p>

                    <h2 id="feedback-details-name" class="mt-2 text-2xl font-black"></h2>

                    <p id="feedback-details-rating" class="mt-2 text-sm font-semibold text-[#FBAB18]"></p>

                </div>

                <button type="button" data-feedback-close aria-label="Close feedback details" class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full border border-[#333333] text-xl text-[#B8B8B8] transition hover:bg-[#1A1A1A]">&times;</button>

            </div>

            <p id="feedback-details-date" class="mb-4 text-xs font-medium text-[#777777]"></p>

            <img id="feedback-details-image" alt="" class="mb-5 hidden max-h-[60vh] w-full rounded-2xl bg-[#1A1A1A] object-contain">

            <p id="feedback-details-review" class="whitespace-pre-line leading-7 text-[#B8B8B8]"></p>

        </div>

    </dialog>

<?php $__env->stopSection(); ?>



<?php $__env->startPush('scripts'); ?>

    <script>

        (() => {

            const dialog = document.getElementById('feedback-details-dialog');

            if (!dialog) return;



            const name = document.getElementById('feedback-details-name');

            const rating = document.getElementById('feedback-details-rating');

            const date = document.getElementById('feedback-details-date');

            const image = document.getElementById('feedback-details-image');

            const review = document.getElementById('feedback-details-review');



            document.querySelectorAll('[data-feedback-details]').forEach((button) => {

                button.addEventListener('click', () => {

                    name.textContent = button.dataset.name || 'MixEat customer';

                    const numericRating = Number(button.dataset.rating);
                    rating.textContent = button.dataset.rating

                        ? `${'â˜…'.repeat(numericRating)}${'â˜†'.repeat(5 - numericRating)}`

                        : '';

                    date.textContent = button.dataset.date || '';

                    review.textContent = button.dataset.review || '';



                    if (button.dataset.image) {

                        image.src = button.dataset.image;

                        image.alt = `Photo shared with ${name.textContent}'s feedback`;

                        image.classList.remove('hidden');

                    } else {

                        image.removeAttribute('src');

                        image.alt = '';

                        image.classList.add('hidden');

                    }



                    dialog.showModal();

                });

            });



            dialog.querySelector('[data-feedback-close]').addEventListener('click', () => dialog.close());

            dialog.addEventListener('click', (event) => {

                if (event.target === dialog) dialog.close();

            });

        })();

    </script>

<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mixeat\mixeat\resources\views\feedback.blade.php ENDPATH**/ ?>