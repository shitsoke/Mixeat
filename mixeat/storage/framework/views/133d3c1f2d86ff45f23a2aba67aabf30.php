

<?php $__env->startSection('title', 'Edit Profile | MixEat'); ?>

<?php $__env->startSection('content'); ?>
    <section class="py-14">
        <div class="mixeat-shell px-4">
            <div class="mx-auto max-w-2xl rounded-[32px] border border-[#383838] bg-[#211E1B] p-8 shadow-[0_20px_60px_rgba(229,169,0,0.12)]">
                <div class="mb-8">
                    <p class="text-sm font-bold uppercase tracking-[0.18em] text-[#FFC60A]">Account Settings</p>
                    <h1 class="mt-2 text-4xl font-black text-[#FFFFFF]">Edit Profile</h1>
                    <p class="mt-3 text-[#B8B8B8]">Keep your MixEat contact information up to date.</p>
                </div>

                <?php if($errors->any()): ?>
                    <div class="mb-6 rounded-2xl border border-[#DC3545]/30 bg-red-50 px-4 py-3 text-sm text-[#DC3545]" role="alert">
                        <ul class="list-disc space-y-1 pl-5">
                            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li><?php echo e($error); ?></li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form action="<?php echo e(route('profile.update')); ?>" method="POST" enctype="multipart/form-data" class="grid gap-5 md:grid-cols-2">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>
                    <div class="flex items-center gap-4 md:col-span-2">
                        <?php if($user->profile_picture): ?>
                            <img src="<?php echo e(asset('storage/'.$user->profile_picture)); ?>" alt="<?php echo e($user->name); ?>'s current profile picture" class="h-16 w-16 rounded-full object-cover">
                        <?php else: ?>
                            <div class="flex h-16 w-16 items-center justify-center rounded-full bg-gradient-to-br from-[#FFC60A] to-[#FFC60A] text-xl font-black text-white">
                                <?php echo e(strtoupper(substr((string) ($user->first_name ?: $user->name), 0, 1) . substr((string) $user->last_name, 0, 1))); ?>

                            </div>
                        <?php endif; ?>
                        <div class="flex-1">
                            <label for="profile_picture" class="mb-2 block text-sm font-semibold text-[#B8B8B8]">Profile Picture</label>
                            <input id="profile_picture" type="file" name="profile_picture" accept="image/jpeg,image/png,image/webp" class="w-full rounded-2xl border border-[#383838] bg-[#191919] px-4 py-3 text-sm text-[#FFFFFF] focus:outline-none" />
                            <p class="mt-1 text-xs text-[#777777]">JPEG, PNG, or WebP; maximum 5 MB.</p>
                        </div>
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-[#B8B8B8]">First Name</label>
                        <input type="text" name="first_name" value="<?php echo e(old('first_name', $user->first_name)); ?>" required class="w-full rounded-2xl border border-[#383838] bg-[#191919] px-4 py-3 text-sm text-[#FFFFFF] focus:outline-none" />
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-[#B8B8B8]">Last Name</label>
                        <input type="text" name="last_name" value="<?php echo e(old('last_name', $user->last_name)); ?>" required class="w-full rounded-2xl border border-[#383838] bg-[#191919] px-4 py-3 text-sm text-[#FFFFFF] focus:outline-none" />
                    </div>
                    <div class="md:col-span-2">
                        <label class="mb-2 block text-sm font-semibold text-[#B8B8B8]">Email</label>
                        <input type="email" name="email" value="<?php echo e(old('email', $user->email)); ?>" <?php if($user->socialAccounts()->where('provider', 'google')->exists()): ?> readonly <?php endif; ?> required class="w-full rounded-2xl border border-[#383838] bg-[#191919] px-4 py-3 text-sm text-[#FFFFFF] focus:outline-none <?php if($user->socialAccounts()->where('provider', 'google')->exists()): ?> cursor-not-allowed opacity-70 <?php endif; ?>" />
                        <?php if($user->socialAccounts()->where('provider', 'google')->exists()): ?>
                            <p class="mt-1 text-xs text-[#999999]">Google account email addresses cannot be changed.</p>
                        <?php endif; ?>
                    </div>
                    <div class="md:col-span-2">
                        <label class="mb-2 block text-sm font-semibold text-[#B8B8B8]">Phone Number</label>
                        <input type="tel" name="phone" value="<?php echo e(old('phone', $user->phone)); ?>" required class="w-full rounded-2xl border border-[#383838] bg-[#191919] px-4 py-3 text-sm text-[#FFFFFF] focus:outline-none" />
                    </div>
                    <div class="flex flex-col gap-3 pt-2 sm:col-span-2 sm:flex-row sm:justify-end">
                        <a href="<?php echo e(route('profile')); ?>" class="inline-flex items-center justify-center rounded-full border border-[#FFC60A] bg-[#211E1B] px-6 py-3 text-sm font-semibold text-[#FFC60A] transition hover:bg-[#211E1B]">Cancel</a>
                        <button type="submit" class="inline-flex items-center justify-center rounded-full bg-[#FFC60A] px-6 py-3 text-sm font-semibold text-[#FFFFFF] shadow-lg shadow-[#FFC60A]/25 transition hover:bg-[#FFC60A]">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mixeat\mixeat\resources\views\storefront\profile-edit.blade.php ENDPATH**/ ?>