

<?php $__env->startSection('title', ($promotion->exists ? 'Edit' : 'Post').' Promotion | MixEat'); ?>

<?php $__env->startSection('content'); ?>
    <section class="py-12">
        <div class="mixeat-shell px-4">
            <div class="mx-auto max-w-2xl rounded-[30px] border border-[#E8DFAF] bg-white p-8 shadow-sm">
                <p class="text-sm font-bold uppercase tracking-[0.18em] text-[#E5A900]">Marketing</p>
                <h1 class="mt-2 text-4xl font-black text-[#090909]"><?php echo e($promotion->exists ? 'Edit Promotion' : 'Post Promotion'); ?></h1>
                <p class="mt-3 text-[#555555]">Add the promotion details and image that customers will see on the Featured page.</p>

                <?php if($errors->any()): ?>
                    <div class="mt-6 rounded-2xl bg-red-50 p-4 text-sm text-red-700" role="alert">
                        <ul class="list-disc space-y-1 pl-5">
                            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li><?php echo e($error); ?></li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form action="<?php echo e($promotion->exists ? route('admin.promotions.update', $promotion) : route('admin.promotions.store')); ?>" method="POST" enctype="multipart/form-data" class="mt-8 grid gap-5">
                    <?php echo csrf_field(); ?>
                    <?php if($promotion->exists): ?>
                        <?php echo method_field('PUT'); ?>
                    <?php endif; ?>

                    <label class="grid gap-2 text-sm font-semibold text-[#555555]">
                        Label <span class="font-normal">(Optional)</span>
                        <input name="label" maxlength="100" value="<?php echo e(old('label', $promotion->label)); ?>" class="rounded-2xl border border-[#E8DFAF] bg-[#FFF9E6] px-4 py-3">
                    </label>
                    <label class="grid gap-2 text-sm font-semibold text-[#555555]">
                        Title
                        <input name="title" required maxlength="255" value="<?php echo e(old('title', $promotion->title)); ?>" class="rounded-2xl border border-[#E8DFAF] bg-[#FFF9E6] px-4 py-3">
                    </label>
                    <label class="grid gap-2 text-sm font-semibold text-[#555555]">
                        Short Description
                        <textarea name="description" required maxlength="2000" rows="3" class="rounded-2xl border border-[#E8DFAF] bg-[#FFF9E6] px-4 py-3"><?php echo e(old('description', $promotion->description)); ?></textarea>
                        <span class="text-xs font-normal">A short summary shown on the Featured page.</span>
                    </label>
                    <label class="grid gap-2 text-sm font-semibold text-[#555555]">
                        Full Promotion Details
                        <textarea name="details" required maxlength="10000" rows="8" class="rounded-2xl border border-[#E8DFAF] bg-[#FFF9E6] px-4 py-3"><?php echo e(old('details', $promotion->details)); ?></textarea>
                        <span class="text-xs font-normal">Include the full offer information, terms, dates, eligibility, and other important conditions.</span>
                    </label>
                    <label class="grid gap-2 text-sm font-semibold text-[#555555]">
                        Price <span class="font-normal">(Optional)</span>
                        <input name="price" type="number" step="0.01" min="0" value="<?php echo e(old('price', $promotion->price)); ?>" class="rounded-2xl border border-[#E8DFAF] bg-[#FFF9E6] px-4 py-3">
                    </label>
                    <label class="grid gap-2 text-sm font-semibold text-[#555555]">
                        Image
                        <input name="image" type="file" accept="image/jpeg,image/png,image/webp" <?php echo e($promotion->exists ? '' : 'required'); ?> class="rounded-2xl border border-[#E8DFAF] bg-[#FFF9E6] px-4 py-3">
                        <span class="text-xs font-normal">JPEG, PNG, or WebP; maximum 5 MB.</span>
                    </label>
                    <?php if($promotion->image): ?>
                        <img src="<?php echo e(asset('storage/'.$promotion->image)); ?>" alt="Current promotion image" class="max-h-56 w-full rounded-2xl object-cover">
                    <?php endif; ?>
                    <div class="grid gap-5 sm:grid-cols-2">
                        <label class="grid gap-2 text-sm font-semibold text-[#555555]">
                            Button Text <span class="font-normal">(Optional)</span>
                            <input name="button_text" maxlength="60" value="<?php echo e(old('button_text', $promotion->button_text)); ?>" class="rounded-2xl border border-[#E8DFAF] bg-[#FFF9E6] px-4 py-3">
                        </label>
                        <label class="grid gap-2 text-sm font-semibold text-[#555555]">
                            Button URL <span class="font-normal">(Optional)</span>
                            <input name="button_url" type="url" maxlength="2048" value="<?php echo e(old('button_url', $promotion->button_url)); ?>" placeholder="https://..." class="rounded-2xl border border-[#E8DFAF] bg-[#FFF9E6] px-4 py-3">
                        </label>
                    </div>
                    <label class="flex items-center gap-3 text-sm font-semibold text-[#555555]">
                        <input type="checkbox" name="is_active" value="1" <?php if(old('is_active', $promotion->exists ? $promotion->is_active : true)): echo 'checked'; endif; ?> class="h-4 w-4 rounded border-[#E8DFAF] text-[#E5A900] focus:ring-[#FFC60A]">
                        Publish this promotion on the Featured page
                    </label>

                    <div class="flex flex-col gap-3 pt-2 sm:flex-row sm:justify-end">
                        <a href="<?php echo e(route('admin.promotions.index')); ?>" class="inline-flex justify-center rounded-full border border-[#FFC60A] px-6 py-3 font-semibold text-[#A87900]">Cancel</a>
                        <button type="submit" class="inline-flex justify-center rounded-full bg-[#FFC60A] px-6 py-3 font-semibold text-[#090909] hover:bg-[#E5A900]"><?php echo e($promotion->exists ? 'Save Changes' : 'Post Promotion'); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mixeat\mixeat\resources\views\admin\promotions\form.blade.php ENDPATH**/ ?>