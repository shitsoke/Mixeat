

<?php $__env->startSection('title', 'MixEat Catering | Good Food. Great Moments.'); ?>

<?php $__env->startSection('content'); ?>
    <section id="catering" class="scroll-mt-24 bg-[#1A1A1A] py-12 md:py-16">
        <div class="w-full px-4 md:px-7">
            <div class="mb-10 flex justify-center md:mb-12">
                <img
                    src="<?php echo e(asset('images/MIXEAT Catering Gold Emblem.png')); ?>"
                    alt="MixEat Catering"
                    class="h-auto w-40 object-contain sm:w-48 md:w-56 lg:w-64"
                >
            </div>

            <div class="mb-12 grid grid-cols-1 items-center gap-6 lg:grid-cols-12">
                <div class="lg:col-span-12">
                    <p class="text-sm font-bold uppercase tracking-[0.18em] text-[#FBAB18]">MixEat Catering</p>
                    <h1 class="catering-hero-title mt-2">Planning an Event or Office Party?</h1>
                    <p class="mt-3 max-w-2xl text-base leading-7 text-[#B8B8B8]">We offer customized tray packages, packed meals, and full-service catering for corporate events, parties, and celebrations.</p>
                </div>
            </div>

            <div class="mb-12 grid grid-cols-1 gap-4 md:grid-cols-3">
                <?php $__currentLoopData = $cateringContent['highlights']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $highlight): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php ($package = $cateringContent['packages'][$index] ?? null); ?>
                    <button
                        type="button"
                        class="catering-highlight-card flex min-h-[108px] items-center justify-center gap-4 rounded-[20px] border border-[#333333] bg-[#201D19] px-6 py-6 text-left shadow-sm transition hover:-translate-y-1 hover:shadow-md focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#FBAB18]"
                        data-catering-package
                        data-catering-category-card
                        data-catering-category="<?php echo e(['packed-meals', 'party-trays', 'catering'][$index] ?? 'other'); ?>"
                        aria-pressed="false"
                        data-package-name="<?php echo e($package['name'] ?? $highlight); ?>"
                        data-package-description="<?php echo e($package['description'] ?? 'Contact us to learn more about this catering service.'); ?>"
                        data-package-price="<?php echo e(is_numeric($package['price'] ?? null) ? number_format((float) $package['price'], 2, '.', '') : ''); ?>"
                        <?php if($index === 2): ?>
                            data-catering-flow
                        <?php endif; ?>
                        <?php if($index === 0): ?>
                            data-catering-service="Packed Meals"
                        <?php endif; ?>
                        <?php if(str_contains(strtolower($highlight), 'party trays')): ?>
                            data-catering-tray-category
                        <?php endif; ?>
                    >
                        <?php if($index === 2): ?>
                            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#FBAB18" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="shrink-0" aria-hidden="true"><path d="M3 14h18"/><path d="M5 14v-2a7 7 0 0 1 14 0v2"/><path d="M12 4v-2"/><path d="M8 14v3a2 2 0 0 0 2 2h4a2 2 0 0 0 2-2v-3"/></svg>
                            <span class="font-bold text-[#FFFFFF]">Catering</span>
                        <?php else: ?>
                            <?php if($index === 0): ?>
                                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#FBAB18" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="shrink-0" aria-hidden="true"><path d="M16.5 9.4 7.55 4.24"/><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.29 7 12 12 20.71 7"/><line x1="12" y1="22" x2="12" y2="12"/></svg>
                            <?php else: ?>
                                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#FBAB18" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="shrink-0" aria-hidden="true"><path d="M3 11h18"/><path d="M5 11l1 9h12l1-9"/><path d="M12 4a3 3 0 0 1 3 3H9a3 3 0 0 1 3-3z"/></svg>
                            <?php endif; ?>
                            <span class="font-bold text-[#FFFFFF]"><?php echo e($highlight); ?></span>
                        <?php endif; ?>
                    </button>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            <?php if(session('catering_status')): ?>
                <div class="mb-8 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-green-800" role="status">
                    <p class="mb-1"><?php echo e(session('catering_status')); ?></p>
                    <?php if(session('catering_payment_confirmation')): ?>
                        <div class="mt-2 border-t border-green-200 pt-2 text-sm">
                            <strong>Booking reference:</strong> <?php echo e(session('catering_payment_confirmation.booking_reference')); ?><br>
                            Estimated total: ₱<?php echo e(session('catering_payment_confirmation.estimated_total')); ?> ·
                            30% deposit: ₱<?php echo e(session('catering_payment_confirmation.deposit_amount')); ?> ·
                            Remaining balance: ₱<?php echo e(session('catering_payment_confirmation.remaining_balance')); ?>

                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if($errors->any()): ?>
                <div class="mb-8 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-red-800" role="alert">
                    <p class="mb-1 font-bold">Please check the details below.</p>
                    <ul class="mb-0"><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($error); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul>
                </div>
            <?php endif; ?>

            <?php ($showTraySelection = old('service_type', request('service')) === 'Party Trays'); ?>
            <?php ($trayInitialItems = \App\Services\CateringPackageCatalog::validPartyTrayItems(old('tray_items', []))); ?>
            <section id="party-trays-menu" class="mb-12 <?php echo e($showTraySelection ? '' : 'hidden'); ?> scroll-mt-24" aria-labelledby="party-trays-heading" data-party-trays-panel data-tray-catalog="<?php echo e(base64_encode(json_encode($partyTrayCatalog, JSON_THROW_ON_ERROR))); ?>" data-tray-initial="<?php echo e(base64_encode(json_encode($trayInitialItems, JSON_THROW_ON_ERROR))); ?>">
                <div class="mb-6">
                    <p class="text-xs font-bold uppercase tracking-[0.16em] text-[#FBAB18]">Food Trays &amp; Buffet Setups</p>
                    <h2 id="party-trays-heading" class="mt-2 text-3xl font-bold text-white">Build your tray selection</h2>
                    <p class="mt-3 max-w-2xl text-base leading-7 text-[#B8B8B8]">Choose a category, dish, tray size, and quantity. Full and half tray prices are shown before you add each item.</p>
                </div>

                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                    <?php $__currentLoopData = $partyTrayCatalog; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $categoryName => $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <button type="button" class="rounded-2xl border border-[#333333] bg-[#201D19] p-4 text-left transition hover:border-[#FBAB18]" data-tray-category="<?php echo e($categoryName); ?>" aria-pressed="false">
                            <span class="block font-bold text-white"><?php echo e($categoryName); ?></span>
                            <span class="mt-1 block text-sm text-[#B8B8B8]">Full ₱<?php echo e(number_format($category['full_price'])); ?> · Half ₱<?php echo e(number_format($category['half_price'])); ?></span>
                        </button>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>

                <div class="mt-5 grid gap-4 rounded-[20px] border border-[#333333] bg-[#201D19] p-5 md:grid-cols-3 md:p-6" data-tray-builder>
                    <div>
                        <label for="tray-dish" class="mb-1 block text-sm font-semibold text-white">Dish</label>
                        <select id="tray-dish" class="w-full rounded-lg border border-[#555555] bg-[#1A1A1A] px-4 py-3 text-white" data-tray-dish disabled>
                            <option value="">Choose a category first</option>
                        </select>
                    </div>
                    <fieldset>
                        <legend class="mb-1 block text-sm font-semibold text-white">Tray size</legend>
                        <div class="flex gap-3">
                            <label class="flex flex-1 items-center gap-2 rounded-lg border border-[#333333] px-3 py-3 text-sm text-white">
                                <input type="radio" name="tray-builder-size" value="full" class="h-4 w-4 accent-[#FBAB18]" data-tray-size disabled>
                                Full <span data-tray-full-price></span>
                            </label>
                            <label class="flex flex-1 items-center gap-2 rounded-lg border border-[#333333] px-3 py-3 text-sm text-white">
                                <input type="radio" name="tray-builder-size" value="half" class="h-4 w-4 accent-[#FBAB18]" data-tray-size disabled>
                                Half <span data-tray-half-price></span>
                            </label>
                        </div>
                    </fieldset>
                    <div>
                        <label for="tray-quantity" class="mb-1 block text-sm font-semibold text-white">Quantity</label>
                        <input id="tray-quantity" type="number" min="1" max="5000" value="1" class="w-full rounded-lg border border-[#555555] bg-[#1A1A1A] px-4 py-3 text-white" data-tray-quantity disabled>
                    </div>
                    <p class="text-sm text-red-300 md:col-span-3" data-tray-error role="status"></p>
                    <div class="md:col-span-3 md:text-right">
                        <button type="button" class="rounded-full bg-[#FBAB18] px-6 py-3 font-bold text-[#111111] transition hover:bg-[#FFC247] disabled:cursor-not-allowed disabled:opacity-50" data-tray-add disabled>Add Tray to Inquiry</button>
                    </div>
                </div>

                <div class="mt-6 hidden rounded-[20px] border border-[#333333] bg-[#201D19] p-5 md:p-6" data-tray-selection-summary>
                    <h3 class="text-xl font-bold text-white">Selected trays</h3>
                    <div class="mt-4 space-y-3" data-tray-items></div>
                    <div class="mt-4 flex flex-wrap items-center justify-between gap-3 border-t border-[#333333] pt-4">
                        <p class="font-bold text-[#FBAB18]" data-tray-total>Estimated total: ₱0.00</p>
                        <button type="button" class="rounded-full bg-[#FBAB18] px-6 py-3 font-bold text-[#111111] transition hover:bg-[#FFC247] disabled:cursor-not-allowed disabled:opacity-50" data-tray-continue disabled>Continue to Booking</button>
                    </div>
                    <div class="mt-4">
                        <button type="button" class="rounded-full border border-[#333333] px-5 py-2.5 font-bold text-white transition hover:border-[#FBAB18]" data-tray-back-categories>Back to categories</button>
                    </div>
                </div>
            </section>

            <section id="packed-meals-packages" class="mb-12 <?php echo e(old('service_type', request('service')) === 'Packed Meals' ? '' : 'hidden'); ?> scroll-mt-24" data-packed-meals-panel aria-labelledby="packed-meals-heading">
                <div class="mb-6">
                    <p class="text-xs font-bold uppercase tracking-[0.16em] text-[#FBAB18]">Packed Meals &amp; Meal Boxes</p>
                    <h2 id="packed-meals-heading" class="mt-2 text-3xl font-bold text-white">Choose your meal package</h2>
                    <p class="mt-3 max-w-2xl text-base leading-7 text-[#B8B8B8]">Select one package, then choose the available side.</p>
                </div>

                <input type="hidden" name="packed_meal_package" value="<?php echo e(old('packed_meal_package')); ?>" form="catering-inquiry-form" data-packed-meal-package>

                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <?php $__currentLoopData = $packedMealPackages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $packageCode => $package): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php ($mainDish = $package->name); ?>
                        <article class="rounded-[20px] border border-[#333333] bg-[#201D19] p-5 transition" data-packed-meal-card="<?php echo e($packageCode); ?>" data-packed-meal-main-dish="<?php echo e($mainDish); ?>" data-packed-meal-price="<?php echo e($package->price); ?>" data-packed-meal-fixed-inclusions="<?php echo e(implode(', ', $package->included_dishes['fixed'] ?? [])); ?>">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="text-xs font-bold uppercase tracking-[0.16em] text-[#FBAB18]">Option <?php echo e($packageCode); ?></p>
                                    <h3 class="mt-2 text-xl font-extrabold text-white"><?php echo e($mainDish); ?></h3>
                                </div>
                                <span class="whitespace-nowrap text-lg font-extrabold text-[#FBAB18]">₱<?php echo e(number_format((float) $package->price, 2)); ?></span>
                            </div>
                            <p class="mt-4 text-sm font-semibold text-white">Includes</p>
                            <ul class="mt-2 space-y-1 text-sm leading-6 text-[#B8B8B8]">
                                <li><?php echo e($mainDish); ?></li>
                                <?php $__currentLoopData = ($package->included_dishes['sides'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $side): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li><?php echo e($side); ?></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php $__currentLoopData = ($package->included_dishes['fixed'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inclusion): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li><?php echo e($inclusion); ?></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                            <button type="button" class="mt-5 w-full rounded-full border border-[#333333] px-5 py-2.5 font-bold text-white transition hover:border-[#FBAB18] focus-visible:outline focus-visible:outline-2 focus-visible:outline-[#FBAB18]" data-packed-meal-select="<?php echo e($packageCode); ?>" aria-pressed="<?php echo e(old('packed_meal_package') === $packageCode ? 'true' : 'false'); ?>">
                                Select Package <?php echo e($packageCode); ?>

                            </button>
                        </article>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>

                <div class="mt-6 hidden rounded-[20px] border border-[#333333] bg-[#201D19] p-5 md:p-6" data-packed-meal-selection>
                    <h3 class="text-xl font-bold text-white" data-packed-meal-selected-title></h3>
                    <p class="mt-2 text-sm text-[#B8B8B8]" data-packed-meal-selected-inclusions></p>
                    <fieldset class="mt-5">
                        <legend class="mb-3 text-sm font-bold text-white">Choose your side</legend>
                        <div class="flex flex-wrap gap-3">
                            <?php $__currentLoopData = \App\Services\CateringPackageCatalog::PACKED_MEAL_SIDES; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $side): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <label class="flex cursor-pointer items-center gap-2 rounded-full border border-[#333333] px-4 py-2.5 text-sm text-white transition hover:border-[#FBAB18]">
                                    <input type="radio" name="packed_meal_side" value="<?php echo e($side); ?>" form="catering-inquiry-form" class="h-4 w-4 accent-[#FBAB18]" data-packed-meal-side <?php if(old('packed_meal_side') === $side): echo 'checked'; endif; ?>>
                                    <?php echo e($side); ?>

                                </label>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </fieldset>
                    <p class="mt-3 text-sm text-red-300" data-packed-meal-error role="status"><?php $__errorArgs = ['packed_meal_package'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><?php echo e($message); ?><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?> <?php $__errorArgs = ['packed_meal_side'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><?php echo e($message); ?><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?></p>
                    <div class="mt-6 flex flex-wrap justify-between gap-3">
                        <button type="button" class="rounded-full border border-[#333333] px-6 py-3 font-bold text-white transition hover:border-[#FBAB18]" data-packed-meal-back-categories>Back to categories</button>
                        <button type="button" class="rounded-full bg-[#FBAB18] px-6 py-3 font-bold text-[#111111] transition hover:bg-[#FFC247] disabled:cursor-not-allowed disabled:opacity-50" data-packed-meal-continue disabled>Continue to Booking</button>
                    </div>
                </div>
            </section>

            <div class="mb-12 rounded-[20px] border border-[#333333] bg-[#201D19] px-6 py-12 md:px-10">
                <p class="mb-6 text-center text-xs font-bold tracking-[0.16em] text-[#777777]">OUR CATERING CLIENTS &amp; PARTNERS</p>
                <div class="catering-partner-marquee" role="region" aria-label="Catering clients and partners">
                    <div class="catering-partner-track">
                        <?php $__currentLoopData = [false, true]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $duplicate): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="catering-partner-group" <?php if($duplicate): ?> aria-hidden="true" inert <?php endif; ?>>
                                <?php $__currentLoopData = $cateringContent['partners']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $partner): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="catering-partner-card">
                                        <?php if($partner['website_url'] && ! $duplicate): ?>
                                            <a class="catering-partner-link" href="<?php echo e($partner['website_url']); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo e($partner['name']); ?> website">
                                        <?php endif; ?>
                                            <?php if($partner['logo_url']): ?>
                                                <img src="<?php echo e($partner['logo_url']); ?>" alt="<?php echo e($duplicate ? '' : $partner['name'].' logo'); ?>" class="catering-partner-logo">
                                            <?php else: ?>
                                                <span class="catering-partner-name"><?php echo e($partner['name']); ?></span>
                                            <?php endif; ?>
                                        <?php if($partner['website_url'] && ! $duplicate): ?>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>

            <section id="catering-package-flow" class="mb-8 hidden scroll-mt-24" data-catering-flow-panel aria-labelledby="catering-flow-heading">
                <div class="mb-6">
                    <p class="text-xs font-bold uppercase tracking-[0.16em] text-[#FBAB18]">Full-service catering</p>
                    <h2 id="catering-flow-heading" class="mt-2 text-3xl font-bold text-[#FFFFFF]">Build your catering package</h2>
                </div>

                <ol class="mb-8 grid grid-cols-3 gap-3" aria-label="Catering booking steps">
                    <?php $__currentLoopData = ['Select package', 'Choose dishes', 'Event details']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stepIndex => $stepLabel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li class="rounded-xl border border-[#333333] bg-[#201D19] p-3 text-center text-sm font-semibold text-[#B8B8B8]" data-catering-step-indicator="<?php echo e($stepIndex + 1); ?>">
                            <span class="mb-1 block text-xs text-[#FBAB18]">Step <?php echo e($stepIndex + 1); ?></span><?php echo e($stepLabel); ?>

                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ol>

                <div data-catering-step="1">
                    <div class="grid gap-4 md:grid-cols-2">
                        <?php $__currentLoopData = $cateringPackages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $packageCode => $package): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <button type="button" class="rounded-[20px] border border-[#333333] bg-[#201D19] p-6 text-left transition hover:border-[#FBAB18] focus-visible:outline focus-visible:outline-2 focus-visible:outline-[#FBAB18]" data-select-catering-package="<?php echo e($packageCode); ?>" data-package-price="<?php echo e($package['price']); ?>" data-meat-count="<?php echo e($package['meat_count']); ?>" data-package-label="<?php echo e($package['label']); ?>" aria-pressed="<?php echo e(old('catering_package') === $packageCode ? 'true' : 'false'); ?>">
                                <span class="block text-xs font-bold uppercase tracking-widest text-[#FBAB18]"><?php echo e($package['label']); ?></span>
                                <span class="mt-2 block text-3xl font-extrabold text-white">₱<?php echo e(number_format($package['price'])); ?> <span class="text-base font-semibold text-[#B8B8B8]">per pax</span></span>
                                <span class="mt-4 block text-sm leading-6 text-[#B8B8B8]">
                                    Choose exactly <?php echo e($package['meat_count']); ?> meat dishes, plus 1 vegetable, 1 soup, 1 pasta/noodle, 1 dessert, 1 rice, and 1 drink.
                                </span>
                            </button>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <button type="button" class="rounded-[20px] border border-[#333333] bg-[#201D19] p-6 text-left transition hover:border-[#FBAB18]" data-custom-catering-package>
                            <span class="block text-xs font-bold uppercase tracking-widest text-[#FBAB18]">Option 3</span>
                            <span class="mt-2 block text-2xl font-extrabold text-white">Customize Your Own Package</span>
                            <span class="mt-2 block text-lg font-bold text-[#FBAB18]">Request a Quotation</span>
                            <span class="mt-4 block text-sm leading-6 text-[#B8B8B8]">Choose the dishes you want. Our MixEat Commissary team will review your selections and provide a customized quotation.</span>
                        </button>
                    </div>
                    <p class="mt-4 text-sm text-[#B8B8B8]" data-package-error role="status">Select one package to continue.</p>
                    <div class="mt-6 flex justify-end">
                        <button type="button" class="rounded-full bg-[#FBAB18] px-6 py-3 font-bold text-[#111111] transition hover:bg-[#FFC247] disabled:cursor-not-allowed disabled:opacity-50" data-package-continue disabled>Continue to food selection</button>
                    </div>
                </div>

                <div class="hidden" data-catering-step="2">
                    <div class="mb-5 rounded-2xl border border-[#333333] bg-[#201D19] p-4">
                        <p class="font-bold text-white" data-meat-counter>Meat dishes: 0/0 selected</p>
                        <p class="mt-1 text-sm text-[#B8B8B8]">Choose any combination across Beef, Pork, Chicken, and Seafood.</p>
                    </div>
                    <div class="grid gap-4 md:grid-cols-2">
                        <?php $__currentLoopData = ['beef', 'pork', 'chicken', 'seafood']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $meatCategory): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php ($category = $cateringMenu[$meatCategory]); ?>
                            <fieldset class="rounded-[20px] border border-[#333333] bg-[#201D19] p-5">
                                <legend class="px-2 text-lg font-bold text-white"><?php echo e($category['label']); ?></legend>
                                <div class="grid gap-2 sm:grid-cols-2">
                                    <?php $__currentLoopData = $category['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dish): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <label class="flex min-h-11 cursor-pointer items-center gap-3 rounded-lg border border-[#333333] px-3 py-2 text-sm text-[#FFFFFF] transition hover:border-[#FBAB18]">
                                            <input type="checkbox" form="catering-inquiry-form" class="h-4 w-4 accent-[#FBAB18]" name="menu_selections[meat][]" value="<?php echo e($dish); ?>" data-meat-option <?php if(in_array($dish, (array) old('menu_selections.meat', []), true)): echo 'checked'; endif; ?>>
                                            <span><?php echo e($dish); ?></span>
                                        </label>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            </fieldset>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <div class="mt-5 grid gap-4 md:grid-cols-2">
                        <?php $__currentLoopData = ['soup', 'vegetables', 'pasta', 'dessert', 'rice', 'drinks']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $categoryKey): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php ($category = $cateringMenu[$categoryKey]); ?>
                            <fieldset class="rounded-[20px] border border-[#333333] bg-[#201D19] p-5">
                                <legend class="px-2 text-lg font-bold text-white">
                                    <?php echo e($category['label']); ?>

                                    <span class="ml-2 text-sm font-normal text-[#B8B8B8]" data-category-counter="<?php echo e($categoryKey); ?>">0/1 selected</span>
                                </legend>
                                <div class="grid gap-2 sm:grid-cols-2">
                                    <?php $__currentLoopData = $category['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dish): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <label class="flex min-h-11 cursor-pointer items-center gap-3 rounded-lg border border-[#333333] px-3 py-2 text-sm text-[#FFFFFF] transition hover:border-[#FBAB18]">
                                            <input type="radio" form="catering-inquiry-form" class="h-4 w-4 accent-[#FBAB18]" name="menu_selections[<?php echo e($categoryKey); ?>]" value="<?php echo e($dish); ?>" data-single-category="<?php echo e($categoryKey); ?>" <?php if(old("menu_selections.$categoryKey") === $dish): echo 'checked'; endif; ?>>
                                            <span><?php echo e($dish); ?></span>
                                        </label>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            </fieldset>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <p class="mt-4 text-sm text-[#B8B8B8]" data-food-error role="status">Choose the required number of meat dishes and one dish from each other category to continue.</p>
                    <div class="mt-6 flex flex-wrap justify-between gap-3">
                        <button type="button" class="rounded-full border border-[#333333] px-6 py-3 font-bold text-white transition hover:border-[#FBAB18]" data-catering-back="1">Back to packages</button>
                        <button type="button" class="rounded-full bg-[#FBAB18] px-6 py-3 font-bold text-[#111111] transition hover:bg-[#FFC247] disabled:cursor-not-allowed disabled:opacity-50" data-food-continue disabled>Continue to event details</button>
                    </div>
                </div>
            </section>

            <section id="custom-catering-builder" class="mb-12 hidden scroll-mt-24" data-custom-catering-builder>
                <div class="mb-6">
                    <p class="text-xs font-bold uppercase tracking-[0.16em] text-[#FBAB18]">Option 3 · Custom package</p>
                    <h2 class="mt-2 text-3xl font-bold text-white">Customize your own package</h2>
                    <p class="mt-3 max-w-2xl text-[#B8B8B8]">Select as many dishes as you like from each category. Our Commissary Supervisor will review your selections and provide the price manually.</p>
                </div>
                <div class="columns-1 gap-4 md:columns-2">
                    <?php $__currentLoopData = \App\Models\CateringMenuItem::CATEGORIES; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customKey => $customLabel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <fieldset class="mb-4 break-inside-avoid rounded-2xl border border-[#333333] bg-[#201D19] p-5">
                            <legend class="px-2 text-lg font-bold text-white"><?php echo e($customLabel); ?> <span class="text-sm font-normal text-[#B8B8B8]">&mdash; <span data-custom-count-label="<?php echo e($customKey); ?>">0</span> selected</span></legend>
                            <div class="grid gap-2 sm:grid-cols-2">
                                <?php $__currentLoopData = ($customCateringMenu[$customKey] ?? collect()); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customDish): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <label class="flex cursor-pointer items-center gap-2 rounded-lg border border-[#333333] px-3 py-2 text-sm text-white">
                                        <input type="checkbox" name="menu_selections[<?php echo e($customKey); ?>][]" value="<?php echo e($customDish->name); ?>" form="catering-inquiry-form" class="accent-[#FBAB18]" data-custom-dish="<?php echo e($customKey); ?>" <?php if(in_array($customDish->name, (array) old("menu_selections.$customKey", []), true)): echo 'checked'; endif; ?>>
                                        <span><?php echo e($customDish->name); ?></span>
                                    </label>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                            <?php if(($customCateringMenu[$customKey] ?? collect())->isEmpty()): ?>
                                <p class="mt-2 text-sm text-[#B8B8B8]">No dishes are currently available in this category.</p>
                            <?php endif; ?>
                        </fieldset>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <div class="mt-5 rounded-2xl border border-[#333333] bg-[#201D19] p-5" data-custom-summary aria-live="polite">
                    <h3 class="text-lg font-bold text-white">Selected Foods Summary</h3>
                    <div class="mt-3 space-y-3 text-sm text-[#B8B8B8]" data-custom-summary-content>
                        <p>No dishes selected yet.</p>
                    </div>
                </div>
                <p class="mt-4 text-sm text-red-300" data-custom-error role="status"></p>
                <button type="button" class="mt-6 rounded-full bg-[#FBAB18] px-6 py-3 font-bold text-[#111111]" data-custom-continue>Continue to event details</button>
            </section>

            <div id="catering-inquiry" class="w-full scroll-mt-24 <?php echo e((old('service_type') === 'Packed Meals' && old('packed_meal_package') && old('packed_meal_side')) || (old('service_type') === 'Party Trays' && !empty(old('tray_items'))) ? '' : 'hidden'); ?>" data-catering-inquiry>
                <div class="overflow-hidden rounded-[24px] border border-[#333333] bg-[#201D19] shadow-sm">
                    <div class="p-6 md:p-10">
                        <div class="mb-6">
                            <p class="mb-1 text-xs font-bold uppercase tracking-widest text-[#FBAB18]">Tell us about your event</p>
                            <h2 class="text-3xl font-bold text-[#FFFFFF]">Book Catering</h2>
                            <p class="mb-0 text-[#B8B8B8]">Share a few details and our catering team will contact you to plan your event.</p>
                        </div>

                        <form id="catering-inquiry-form" action="<?php echo e(route('catering.inquiries.store')); ?>" method="POST" class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="campaign_source" value="<?php echo e($campaignSource); ?>">
                            <input type="hidden" name="catering_package" value="<?php echo e(old('catering_package')); ?>" data-selected-catering-package>
                            <input type="hidden" name="submission_token" value="<?php echo e(old('submission_token', (string) \Illuminate\Support\Str::uuid())); ?>" data-catering-submission-token>
                            <div class="hidden rounded-2xl border border-[#333333] bg-[#1A1A1A] p-5 md:col-span-2" data-packed-meal-inquiry-summary aria-live="polite">
                                <div class="mb-3 flex flex-wrap items-baseline justify-between gap-2">
                                    <h3 class="text-lg font-bold text-white">Your Packed Meals order</h3>
                                    <p class="font-bold text-[#FBAB18]" data-packed-meal-estimated-total>Estimated total: ₱0.00</p>
                                </div>
                                <p class="mb-2 text-sm text-[#B8B8B8]" data-packed-meal-inquiry-details></p>
                                <ul class="list-disc pl-5 text-sm text-white">
                                    <li data-packed-meal-inquiry-side></li>
                                    <?php $__currentLoopData = \App\Services\CateringPackageCatalog::PACKED_MEAL_FIXED_INCLUSIONS; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inclusion): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li><?php echo e($inclusion); ?></li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                                <p class="mt-3 text-xs text-[#B8B8B8]">₱<?php echo e(number_format(\App\Services\CateringPackageCatalog::PACKED_MEAL_PRICE)); ?> per meal · estimate only; the final quotation will be confirmed by our team.</p>
                            </div>
                            <div class="hidden rounded-2xl border border-[#333333] bg-[#1A1A1A] p-5 md:col-span-2" data-party-tray-inquiry-summary aria-live="polite">
                                <div class="mb-3 flex flex-wrap items-baseline justify-between gap-2">
                                    <h3 class="text-lg font-bold text-white">Your Food Trays &amp; Buffet Setups order</h3>
                                    <p class="font-bold text-[#FBAB18]" data-party-tray-inquiry-total>Estimated total: ₱0.00</p>
                                </div>
                                <ul class="space-y-1 text-sm text-white" data-party-tray-inquiry-lines></ul>
                                <p class="mt-3 text-xs text-[#B8B8B8]">Estimate only; the final quotation will be confirmed by our team.</p>
                            </div>
                            <div class="hidden rounded-2xl border border-[#333333] bg-[#1A1A1A] p-5 md:col-span-2" data-catering-summary aria-live="polite">
                                <div class="mb-3 flex flex-wrap items-baseline justify-between gap-2">
                                    <h3 class="text-lg font-bold text-white">Your catering package</h3>
                                    <p class="font-bold text-[#FBAB18]" data-estimated-total>Estimated total: ₱0.00</p>
                                </div>
                                <p class="mb-3 text-sm text-[#B8B8B8]" data-summary-package></p>
                                <ul class="grid list-disc gap-x-6 gap-y-1 pl-5 text-sm text-white sm:grid-cols-2" data-summary-selections></ul>
                                <p class="mt-3 text-xs text-[#B8B8B8]">Estimated package cost only; this is not a confirmed quotation. No additional charges are included.</p>
                            </div>
                            <section class="rounded-2xl border border-[#333333] bg-[#1A1A1A] p-5 md:col-span-2" data-catering-payment-summary aria-labelledby="catering-payment-title">
                                <div class="grid gap-5 md:grid-cols-[minmax(0,1fr)_220px] md:items-center">
                                    <div>
                                        <p class="mb-1 text-xs font-bold uppercase tracking-widest text-[#FBAB18]">Security Bank QR deposit</p>
                                        <h3 id="catering-payment-title" class="mb-3 text-xl font-bold text-white">Pay a 30% deposit</h3>
                                        <p class="mb-2 text-sm text-[#B8B8B8]">Scan the QR code to pay the deposit, then enter the payment reference from your receipt. Your booking stays unconfirmed until our team verifies it.</p>
                                        <dl class="grid grid-cols-1 gap-1 text-sm text-white sm:grid-cols-3">
                                            <div><dt class="text-[#B8B8B8]">Estimated total</dt><dd class="font-bold" data-payment-total>₱0.00</dd></div>
                                            <div><dt class="text-[#B8B8B8]">30% deposit</dt><dd class="font-bold text-[#FBAB18]" data-payment-deposit>₱0.00</dd></div>
                                            <div><dt class="text-[#B8B8B8]">Remaining balance</dt><dd class="font-bold" data-payment-balance>₱0.00</dd></div>
                                        </dl>
                                    </div>
                                    <img src="<?php echo e(asset('images/mixeat qr.jpeg')); ?>" alt="MixEat Security Bank QR code" class="mx-auto max-h-56 w-auto rounded-lg bg-white p-2">
                                </div>
                                <div class="mt-5 grid gap-4 md:grid-cols-2">
                                    <div data-payment-field>
                                        <label for="catering-payment-reference" class="mb-1 block text-sm font-semibold text-white">Security Bank payment reference</label>
                                        <input id="catering-payment-reference" class="w-full rounded-lg border border-[#555555] bg-[#201D19] px-4 py-3 text-base text-white focus:border-[#FBAB18] focus:outline-none focus:ring-2 focus:ring-[#FBAB18]/30" type="text" name="payment_reference" value="<?php echo e(old('payment_reference')); ?>" maxlength="64" pattern="[A-Za-z0-9]+" autocomplete="off" required>
                                        <?php $__errorArgs = ['payment_reference'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-red-400"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>
                                    <label class="flex items-start gap-3 pt-2 text-sm text-white">
                                        <input type="checkbox" name="payment_confirmation" value="1" <?php if(old('payment_confirmation')): echo 'checked'; endif; ?> required class="mt-1 accent-[#FBAB18]">
                                        <span>I confirm that I paid the displayed deposit using the Security Bank QR code.</span>
                                    </label>
                                    <?php $__errorArgs = ['payment_confirmation'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-sm text-red-400 md:col-span-2"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </section>
                            <div class="hidden md:col-span-2" data-catering-form-back>
                                <button type="button" class="rounded-full border border-[#333333] px-5 py-2.5 font-bold text-white transition hover:border-[#FBAB18]" data-catering-back="2">Back to food selection</button>
                            </div>
                            <div class="hidden md:col-span-2" data-packed-meal-form-back>
                                <button type="button" class="rounded-full border border-[#333333] px-5 py-2.5 font-bold text-white transition hover:border-[#FBAB18]" data-packed-meal-back>Back to package selection</button>
                            </div>
                            <div class="hidden md:col-span-2" data-party-tray-form-back>
                                <button type="button" class="rounded-full border border-[#333333] px-5 py-2.5 font-bold text-white transition hover:border-[#FBAB18]" data-party-tray-back>Back to tray selection</button>
                            </div>
                            <div>
                                <label for="catering-client-name" class="mb-1 block text-sm font-semibold text-[#FFFFFF]">Client Name</label>
                                <input id="catering-client-name" class="w-full rounded-lg border border-[#D6D6D6] bg-[#201D19] px-4 py-3 text-base text-[#FFFFFF] focus:border-[#FBAB18] focus:outline-none focus:ring-2 focus:ring-[#FBAB18]/30" type="text" name="client_name" value="<?php echo e(old('client_name')); ?>" autocomplete="name" maxlength="120" required>
                            </div>
                            <div>
                                <label for="catering-email" class="mb-1 block text-sm font-semibold text-[#FFFFFF]">Email</label>
                                <input id="catering-email" class="w-full rounded-lg border border-[#D6D6D6] bg-[#201D19] px-4 py-3 text-base text-[#FFFFFF] focus:border-[#FBAB18] focus:outline-none focus:ring-2 focus:ring-[#FBAB18]/30" type="email" name="email" value="<?php echo e(old('email', auth()->user()?->role === 'customer' ? auth()->user()->email : null)); ?>" autocomplete="email" maxlength="255" <?php if(auth()->user()?->role === 'customer'): ?> readonly <?php endif; ?> required>
                            </div>
                            <div>
                                <label for="catering-phone" class="mb-1 block text-sm font-semibold text-[#FFFFFF]">Phone</label>
                                <input id="catering-phone" class="w-full rounded-lg border border-[#D6D6D6] bg-[#201D19] px-4 py-3 text-base text-[#FFFFFF] focus:border-[#FBAB18] focus:outline-none focus:ring-2 focus:ring-[#FBAB18]/30" type="tel" name="phone" value="<?php echo e(old('phone')); ?>" autocomplete="tel" maxlength="30" required>
                            </div>
                            <div>
                                <label for="catering-company" class="mb-1 block text-sm font-semibold text-[#FFFFFF]">Company / Organization <span class="font-normal text-[#777777]">(optional)</span></label>
                                <input id="catering-company" class="w-full rounded-lg border border-[#D6D6D6] bg-[#201D19] px-4 py-3 text-base text-[#FFFFFF] focus:border-[#FBAB18] focus:outline-none focus:ring-2 focus:ring-[#FBAB18]/30" type="text" name="company_name" value="<?php echo e(old('company_name')); ?>" maxlength="160">
                            </div>
                            <div>
                                <label for="catering-date" class="mb-1 block text-sm font-semibold text-[#FFFFFF]">Event Date</label>
                                <input id="catering-date" class="w-full rounded-lg border border-[#D6D6D6] bg-[#201D19] px-4 py-3 text-base text-[#FFFFFF] focus:border-[#FBAB18] focus:outline-none focus:ring-2 focus:ring-[#FBAB18]/30" type="date" name="event_date" value="<?php echo e(old('event_date')); ?>" min="<?php echo e(now()->toDateString()); ?>" required>
                            </div>
                            <div>
                                <label for="catering-time" class="mb-1 block text-sm font-semibold text-[#FFFFFF]">Event Time</label>
                                <input id="catering-time" class="w-full rounded-lg border border-[#D6D6D6] bg-[#201D19] px-4 py-3 text-base text-[#FFFFFF] focus:border-[#FBAB18] focus:outline-none focus:ring-2 focus:ring-[#FBAB18]/30" type="time" name="event_time" value="<?php echo e(old('event_time')); ?>" required>
                            </div>
                            <div>
                                <label for="catering-venue" class="mb-1 block text-sm font-semibold text-[#FFFFFF]">Venue / Location</label>
                                <input id="catering-venue" class="w-full rounded-lg border border-[#D6D6D6] bg-[#201D19] px-4 py-3 text-base text-[#FFFFFF] focus:border-[#FBAB18] focus:outline-none focus:ring-2 focus:ring-[#FBAB18]/30" type="text" name="venue" value="<?php echo e(old('venue')); ?>" maxlength="255" required>
                            </div>
                            <fieldset class="md:col-span-2" data-event-type-fieldset>
                                <legend class="mb-2 block text-sm font-semibold text-[#FFFFFF]">Event Type</legend>
                                <div class="grid grid-cols-2 gap-2 sm:grid-cols-3">
                                    <?php $__currentLoopData = ['Meeting', 'Wedding', 'Birthday', 'Anniversary', 'Graduation', 'Other']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $eventType): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <label class="flex min-h-11 cursor-pointer items-center gap-2 rounded-lg border border-[#333333] bg-[#1A1A1A] px-3 py-2 text-sm text-white transition hover:border-[#FBAB18] has-[:checked]:border-[#FBAB18] has-[:checked]:bg-[#FBAB18]/10">
                                            <input type="radio" name="event_type" value="<?php echo e($eventType); ?>" class="h-4 w-4 accent-[#FBAB18]" data-event-type-option required <?php if(old('event_type') === $eventType): echo 'checked'; endif; ?>>
                                            <span><?php echo e($eventType); ?></span>
                                        </label>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                                <?php $__errorArgs = ['event_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <p class="mt-1 text-sm text-red-300"><?php echo e($message); ?></p>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </fieldset>
                            <div class="<?php echo e(old('event_type') === 'Other' ? '' : 'hidden'); ?> md:col-span-2" data-event-type-other-container>
                                <label for="catering-event-type-other" class="mb-1 block text-sm font-semibold text-white">Please specify the event type</label>
                                <input id="catering-event-type-other" class="w-full rounded-lg border border-[#D6D6D6] bg-[#201D19] px-4 py-3 text-base text-white focus:border-[#FBAB18] focus:outline-none focus:ring-2 focus:ring-[#FBAB18]/30" type="text" name="event_type_details" value="<?php echo e(old('event_type_details')); ?>" maxlength="160" data-event-type-other>
                                <?php $__errorArgs = ['event_type_details'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <p class="mt-1 text-sm text-red-300"><?php echo e($message); ?></p>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div>
                                <label for="catering-service" class="mb-1 block text-sm font-semibold text-[#FFFFFF]">Service Type</label>
                                <select id="catering-service" class="w-full rounded-lg border border-[#D6D6D6] bg-[#201D19] px-4 py-3 text-base text-[#FFFFFF] focus:border-[#FBAB18] focus:outline-none focus:ring-2 focus:ring-[#FBAB18]/30" name="service_type" required>
                                    <option value="">Choose a service</option>
                                    <?php $__currentLoopData = ['Packed Meals', 'Party Trays', 'Full-Service Catering', 'Custom Catering']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($service); ?>" <?php if(old('service_type', request('service')) === $service): echo 'selected'; endif; ?>><?php echo e($service); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div>
                                <label for="catering-guests" class="mb-1 block text-sm font-semibold text-[#FFFFFF]">Guest Count <span class="hidden font-normal text-[#B8B8B8]" data-packed-meal-count-label>(Number of Meals)</span></label>
                                <input id="catering-guests" class="w-full rounded-lg border border-[#D6D6D6] bg-[#201D19] px-4 py-3 text-base text-[#FFFFFF] focus:border-[#FBAB18] focus:outline-none focus:ring-2 focus:ring-[#FBAB18]/30" type="number" name="guest_count" value="<?php echo e(old('guest_count')); ?>" min="1" max="5000" required>
                            </div>
                            <div>
                                <label for="catering-budget" class="mb-1 block text-sm font-semibold text-[#FFFFFF]">Budget Range <span class="font-normal text-[#777777]">(optional)</span></label>
                                <input id="catering-budget" class="w-full rounded-lg border border-[#D6D6D6] bg-[#201D19] px-4 py-3 text-base text-[#FFFFFF] focus:border-[#FBAB18] focus:outline-none focus:ring-2 focus:ring-[#FBAB18]/30" type="text" name="budget_range" value="<?php echo e(old('budget_range')); ?>" maxlength="100" placeholder="e.g. ₱10,000 - ₱20,000">
                            </div>
                            <div class="md:col-span-2">
                                <label for="catering-package" class="mb-1 block text-sm font-semibold text-[#FFFFFF]">Package Preference <span class="font-normal text-[#777777]">(optional)</span></label>
                                <select id="catering-package" class="w-full rounded-lg border border-[#D6D6D6] bg-[#201D19] px-4 py-3 text-base text-[#FFFFFF] focus:border-[#FBAB18] focus:outline-none focus:ring-2 focus:ring-[#FBAB18]/30" name="package_preference">
                                    <option value="">No preference yet</option>
                                    <option value="Option 1 (₱550 per pax)" <?php if(old('package_preference') === 'Option 1 (₱550 per pax)'): echo 'selected'; endif; ?>>Option 1 (₱550 per pax)</option>
                                    <option value="Option 2 (₱650 per pax)" <?php if(old('package_preference') === 'Option 2 (₱650 per pax)'): echo 'selected'; endif; ?>>Option 2 (₱650 per pax)</option>
                                    <?php $__currentLoopData = $cateringContent['packages']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $package): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($package['name']); ?>" <?php if(old('package_preference', request('package')) === $package['name']): echo 'selected'; endif; ?>><?php echo e($package['name']); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div class="md:col-span-2">
                                <label for="catering-requests" class="mb-1 block text-sm font-semibold text-[#FFFFFF]">Special Requests</label>
                                <textarea id="catering-requests" class="w-full rounded-lg border border-[#D6D6D6] bg-[#201D19] px-4 py-3 text-base text-[#FFFFFF] focus:border-[#FBAB18] focus:outline-none focus:ring-2 focus:ring-[#FBAB18]/30" name="special_requests" rows="4" maxlength="2000"><?php echo e(old('special_requests')); ?></textarea>
                            </div>
                            <div class="pt-2 md:col-span-2">
                                <button type="submit" class="w-full rounded-full bg-[#FBAB18] px-6 py-3 font-bold text-[#FFFFFF] shadow-sm transition hover:bg-[#FBAB18]" data-catering-submit>Send Catering Inquiry</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <dialog id="catering-package-dialog" class="catering-package-dialog w-[calc(100%-2rem)] max-w-lg rounded-[24px] border border-[#333333] bg-[#201D19] p-0 text-[#FFFFFF] shadow-2xl backdrop:bg-[#FFFFFF]/50" aria-labelledby="catering-package-title">
        <div class="p-6 md:p-8">
            <div class="mb-5 flex items-start justify-between gap-4">
                <div>
                    <p class="mb-1 text-xs font-bold uppercase tracking-widest text-[#FBAB18]">Catering package</p>
                    <h2 id="catering-package-title" class="text-2xl font-bold"></h2>
                </div>
                <button type="button" class="rounded-full border border-[#333333] px-3 py-1 text-xl leading-none text-[#B8B8B8] hover:bg-[#1A1A1A]" data-close-catering-package aria-label="Close package details">&times;</button>
            </div>
            <p id="catering-package-description" class="text-base leading-7 text-[#B8B8B8]"></p>
            <p id="catering-package-price" class="hidden mb-6 font-bold text-[#FBAB18]"></p>
            <button type="button" class="mt-4 w-full rounded-full bg-[#FBAB18] px-6 py-3 font-bold text-[#FFFFFF] shadow-sm transition hover:bg-[#FBAB18]" data-book-catering-package>Choose Package &amp; Book</button>
        </div>
    </dialog>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mixeat\mixeat\resources\views\storefront\catering.blade.php ENDPATH**/ ?>