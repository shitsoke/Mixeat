

<?php $__env->startSection('title', 'Assign Branch Supervisor | MixEat'); ?>

<?php $__env->startSection('content'); ?>
<section class="py-10">
    <div class="mixeat-shell px-4">
        <!-- Header -->
        <div class="mb-8 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.18em] text-[#FBAB18]">Marketing Admin</p>
                <h1 class="mt-1 text-3xl font-black text-[#FFFFFF]">Assign Branch Supervisor</h1>
                <p class="mt-1 text-sm font-medium text-[#B8B8B8]">Enter an email address to assign a user as a supervisor for a specific branch.</p>
            </div>
            <a href="<?php echo e(route('home')); ?>" class="inline-flex rounded-full border border-[#FBAB18] px-5 py-2.5 text-xs font-bold text-[#FBAB18] transition hover:bg-[#201D19]">
                Back to Dashboard
            </a>
        </div>

        <?php if(session('admin_status')): ?>
            <div class="mb-6 rounded-2xl bg-[#201D19] px-4 py-3 font-semibold text-[#FFFFFF]">
                <?php echo e(session('admin_status')); ?>

            </div>
        <?php endif; ?>

        <?php if($errors->any()): ?>
            <div class="mb-6 rounded-2xl bg-[#FCE8E6] px-4 py-3 font-semibold text-[#D93025]">
                <?php echo e($errors->first()); ?>

            </div>
        <?php endif; ?>

        <!-- 1. EMAIL ASSIGNMENT FORM -->
        <div class="mb-10 rounded-[28px] border border-[#333333] bg-[#201D19] p-6 shadow-sm">
            <h2 class="mb-4 text-lg font-bold text-[#FFFFFF]">Assign New Supervisor</h2>
            
            <form action="<?php echo e(route('admin.supervisors.assign')); ?>" method="POST" class="flex flex-col gap-4 md:flex-row md:items-end">
                <?php echo csrf_field(); ?>
                
                <div class="flex-1">
                    <label for="email" class="mb-2 block text-xs font-bold uppercase tracking-wider text-[#777777]">User Email Address</label>
                    <input type="email" id="email" name="email" value="<?php echo e(old('email')); ?>" placeholder="e.g. salabat@gmail.com" required
                        class="w-full rounded-full border border-[#333333] bg-[#1A1A1A] px-5 py-3 text-sm font-bold text-[#FFFFFF] focus:border-[#FBAB18] focus:outline-none focus:ring-2 focus:ring-[#FBAB18]/30">
                </div>

                <div class="w-full md:w-64">
                    <label for="branch_id" class="mb-2 block text-xs font-bold uppercase tracking-wider text-[#777777]">Assigned Branch</label>
                    <select id="branch_id" name="branch_id" required
                        class="w-full rounded-full border border-[#333333] bg-[#1A1A1A] px-5 py-3 text-sm font-bold text-[#FFFFFF] focus:border-[#FBAB18] focus:outline-none focus:ring-2 focus:ring-[#FBAB18]/30">
                        <option value="">-- Select Branch --</option>
                        <?php $__currentLoopData = $branches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $branch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($branch->id); ?>"><?php echo e($branch->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <button type="submit" class="rounded-full bg-[#FBAB18] px-7 py-3 text-sm font-bold text-[#FFFFFF] shadow-md shadow-[#FBAB18]/25 transition hover:bg-[#FBAB18]">
                    Assign Supervisor
                </button>
            </form>
        </div>

        <!-- 2. CURRENTLY ASSIGNED SUPERVISORS LIST -->
        <div class="rounded-[28px] border border-[#333333] bg-[#201D19] p-6 shadow-sm">
            <h2 class="mb-4 text-lg font-bold text-[#FFFFFF]">Active Branch Supervisors</h2>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-[#333333] text-xs font-bold uppercase tracking-wider text-[#777777]">
                            <th class="py-3 px-4">Supervisor Name</th>
                            <th class="py-3 px-4">Email</th>
                            <th class="py-3 px-4">Assigned Branch</th>
                            <th class="py-3 px-4 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#333333]/60 text-sm font-medium">
                        <?php $__empty_1 = true; $__currentLoopData = $supervisors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $supervisor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td class="py-4 px-4 font-bold text-[#FFFFFF]"><?php echo e($supervisor->name); ?></td>
                                <td class="py-4 px-4 text-[#B8B8B8]"><?php echo e($supervisor->email); ?></td>
                                <td class="py-4 px-4">
                                    <span class="inline-block rounded-full bg-[#E6F4EA] px-3 py-1 text-xs font-bold text-[#1E8E3E]">
                                        <?php echo e($supervisor->branch?->name ?? 'Unassigned'); ?>

                                    </span>
                                </td>
                                <td class="py-4 px-4 text-right">
                                    <form action="<?php echo e(route('admin.supervisors.remove', $supervisor->id)); ?>" method="POST" class="inline-block" onsubmit="return confirm('Remove supervisor access for this user?')">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="text-xs font-bold text-[#DC3545] hover:underline">
                                            Remove Supervisor
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="4" class="py-6 text-center text-sm font-medium text-[#777777]">No supervisors currently assigned. Use the form above to assign one.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mixeat\mixeat\resources\views/admin/supervisors/manage.blade.php ENDPATH**/ ?>