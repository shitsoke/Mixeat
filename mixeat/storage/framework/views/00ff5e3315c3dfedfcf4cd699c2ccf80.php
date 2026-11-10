

<?php $__env->startSection('title', 'MixEat Catering Quotation'); ?>

<?php $__env->startSection('content'); ?>
<section class="bg-[#1A1A1A] px-4 py-12 text-white md:px-8">
    <div class="mx-auto max-w-4xl">
        <p class="text-xs font-bold uppercase tracking-widest text-[#FBAB18]">MixEat Commissary</p>
        <h1 class="mt-2 text-3xl font-bold">Your Customized Catering Quotation</h1>
        <p class="mt-2 text-[#B8B8B8]">Booking reference: MXC-<?php echo e($booking->created_at->format('Y')); ?>-<?php echo e(str_pad($booking->id, 6, '0', STR_PAD_LEFT)); ?></p>
        <?php if(session('catering_status')): ?>
            <div class="mt-6 rounded-xl border border-green-700 bg-green-950/40 p-4 text-green-200"><?php echo e(session('catering_status')); ?></div>
        <?php endif; ?>
        <div class="mt-8 grid gap-6 md:grid-cols-2">
            <div class="rounded-2xl border border-[#333333] bg-[#201D19] p-6">
                <h2 class="text-xl font-bold">Event Details</h2>
                <dl class="mt-4 space-y-3 text-sm">
                    <div><dt class="text-[#B8B8B8]">Customer</dt><dd><?php echo e($booking->client_name); ?></dd></div>
                    <div><dt class="text-[#B8B8B8]">Event</dt><dd><?php echo e($booking->event_date?->format('F j, Y')); ?> · <?php echo e($booking->venue); ?></dd></div>
                    <div><dt class="text-[#B8B8B8]">Guests</dt><dd><?php echo e(number_format($booking->guest_count)); ?></dd></div>
                    <div><dt class="text-[#B8B8B8]">Status</dt><dd><?php echo e($booking->status); ?></dd></div>
                </dl>
            </div>
            <div class="rounded-2xl border border-[#333333] bg-[#201D19] p-6">
                <h2 class="text-xl font-bold">Quotation</h2>
                <p class="mt-4 text-3xl font-extrabold text-[#FBAB18]">₱<?php echo e(number_format((float) $quotation->amount, 2)); ?></p>
                <p class="mt-3 whitespace-pre-line text-sm text-[#B8B8B8]"><?php echo e($quotation->notes ?: 'No additional notes.'); ?></p>
                <p class="mt-4 text-xs text-[#B8B8B8]">After acceptance, the required 30% deposit will be ₱<?php echo e(number_format((float) $quotation->amount * .30, 2)); ?>.</p>
                <div class="mt-6 flex flex-wrap gap-3">
                    <?php if($booking->status === 'Quotation Sent'): ?>
                        <form method="POST" action="<?php echo e(URL::signedRoute('catering.quotation.accept', ['booking' => $booking->id])); ?>"><?php echo csrf_field(); ?><button class="rounded-full bg-[#FBAB18] px-5 py-3 font-bold text-[#111111]" type="submit">Accept Quotation</button></form>
                        <form method="POST" action="<?php echo e(URL::signedRoute('catering.quotation.decline', ['booking' => $booking->id])); ?>"><?php echo csrf_field(); ?><button class="rounded-full border border-[#555555] px-5 py-3 font-bold text-white" type="submit">Decline Quotation</button></form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php if($booking->status === 'Awaiting Deposit'): ?>
            <div class="mt-6 rounded-2xl border border-[#333333] bg-[#201D19] p-6">
                <h2 class="text-xl font-bold">Submit your 30% deposit</h2>
                <p class="mt-2 text-sm text-[#B8B8B8]">Required deposit: <strong class="text-[#FBAB18]">₱<?php echo e(number_format((float) $quotation->amount * .30, 2)); ?></strong>. Scan the Security Bank QR code, then enter your payment reference.</p>
                <img src="<?php echo e(asset('images/mixeat qr.jpeg')); ?>" alt="MixEat Security Bank QR code" class="mt-4 max-h-56 rounded-lg bg-white p-2">
                <form method="POST" action="<?php echo e(URL::signedRoute('catering.quotation.deposit', ['booking' => $booking->id])); ?>" class="mt-5 grid gap-4 sm:grid-cols-2">
                    <?php echo csrf_field(); ?>
                    <input name="payment_reference" required maxlength="64" pattern="[A-Za-z0-9]+" placeholder="Security Bank payment reference" class="rounded-lg border border-[#555555] bg-[#1A1A1A] px-4 py-3 text-white">
                    <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="payment_confirmation" value="1" required class="accent-[#FBAB18]"> I confirm I paid this deposit.</label>
                    <button class="rounded-full bg-[#FBAB18] px-5 py-3 font-bold text-[#111111] sm:col-span-2" type="submit">Submit Deposit for Verification</button>
                </form>
            </div>
        <?php endif; ?>
        <div class="mt-6 rounded-2xl border border-[#333333] bg-[#201D19] p-6">
            <h2 class="text-xl font-bold">Selected Dishes</h2>
            <div class="mt-4 grid gap-3 sm:grid-cols-2">
                <?php $__currentLoopData = ($booking->menu_selections['selections'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category => $dishes): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if(is_array($dishes) && count($dishes)): ?>
                        <div><p class="font-bold capitalize"><?php echo e($category); ?></p><p class="text-sm text-[#B8B8B8]"><?php echo e(implode(', ', $dishes)); ?></p></div>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mixeat\mixeat\resources\views\storefront\catering-quotation.blade.php ENDPATH**/ ?>