

<?php $__env->startSection('title', 'My Profile | MixEat'); ?>

<?php $__env->startSection('content'); ?>
    <section class="py-14">
        <div class="mixeat-shell px-4">
            <div class="mx-auto max-w-3xl rounded-[32px] border border-[#333333] bg-[#201D19] p-8 shadow-[0_20px_60px_rgba(229,169,0,0.12)]">
                <?php if(session('profile_status')): ?>
                    <div class="mb-6 rounded-2xl border border-[#333333] bg-[#201D19] px-4 py-3 text-sm font-semibold text-[#FFFFFF]" role="status">
                        <?php echo e(session('profile_status')); ?>

                    </div>
                <?php endif; ?>
                <div class="mb-8 flex flex-col items-center gap-4 text-center md:flex-row md:text-left">
                    <?php if($user->profile_picture): ?>
                        <img src="<?php echo e(asset('storage/'.$user->profile_picture)); ?>" alt="<?php echo e($user->name); ?>'s profile picture" class="h-20 w-20 rounded-full object-cover shadow-lg shadow-[#FBAB18]/25">
                    <?php else: ?>
                        <div class="flex h-20 w-20 items-center justify-center rounded-full bg-gradient-to-br from-[#FBAB18] to-[#FBAB18] text-2xl font-black text-white shadow-lg shadow-[#FBAB18]/25">
                            <?php echo e(strtoupper(substr((string) ($user->first_name ?: $user->name), 0, 1) . substr((string) $user->last_name, 0, 1))); ?>

                        </div>
                    <?php endif; ?>
                    <div>
                        <h1 class="text-3xl font-black text-[#FFFFFF]"><?php echo e($user->name); ?></h1>
                        <p class="text-sm font-medium text-[#777777]">
                            <?php if($user->isMarketing() || $user->isSupervisor() || $user->is_admin): ?>
                                Admin Account
                            <?php else: ?>
                                Customer Account
                            <?php endif; ?>
                        </p>
                    </div>
                </div>

                <div class="grid gap-6 md:grid-cols-2">
                    <div class="rounded-[24px] bg-[#1A1A1A] p-5">
                        <p class="text-sm font-bold uppercase tracking-[0.12em] text-[#B8B8B8]">Profile Information</p>
                        <dl class="mt-4 space-y-3 text-sm text-[#B8B8B8]">
                            <div class="flex items-center justify-between gap-3"><dt>Name</dt><dd class="font-semibold text-[#FFFFFF]"><?php echo e($user->name); ?></dd></div>
                            <div class="flex items-center justify-between gap-3"><dt>Email</dt><dd class="font-semibold text-[#FFFFFF]"><?php echo e($user->email); ?></dd></div>
                            <div class="flex items-center justify-between gap-3"><dt>Phone</dt><dd class="font-semibold text-[#FFFFFF]"><?php echo e($user->phone ?: 'Not provided'); ?></dd></div>
                        </dl>
                    </div>

                    <div class="rounded-[24px] bg-[#1A1A1A] p-5 flex flex-col justify-center">
                        <p class="text-sm font-bold uppercase tracking-[0.12em] text-[#B8B8B8]">Account Actions</p>
                        <div class="mt-4 space-y-3">
                            <a href="<?php echo e(route('profile.edit')); ?>" class="block w-full rounded-full border border-[#FBAB18] bg-[#201D19] px-5 py-3 text-center text-sm font-semibold text-[#FBAB18] transition hover:bg-[#201D19]">Edit Profile</a>
                            
                            <?php if(! $user->isMarketing()): ?>
                                <a href="<?php echo e(route('password.change')); ?>" class="block w-full rounded-full border border-[#FBAB18] bg-[#201D19] px-5 py-3 text-center text-sm font-semibold text-[#FBAB18] transition hover:bg-[#201D19]">Change Password</a>
                            <?php else: ?>
                                <button type="button" disabled class="block w-full cursor-not-allowed rounded-full border border-gray-200 bg-gray-100 px-5 py-3 text-center text-sm font-semibold text-gray-400">Unavailable</button>
                            <?php endif; ?>

                            <form action="<?php echo e(route('logout')); ?>" method="POST">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="w-full rounded-full bg-[#FBAB18] px-5 py-3 text-sm font-semibold text-[#FFFFFF] shadow-lg shadow-[#FBAB18]/25 transition hover:bg-[#FBAB18]">Logout</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mixeat\mixeat\resources\views\storefront\profile.blade.php ENDPATH**/ ?>