<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Update Catering Payment | MixEat</title>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
</head>
<body class="min-h-screen bg-[#111111] px-4 py-10 text-white">
    <main class="mx-auto max-w-3xl rounded-3xl border border-[#333333] bg-[#201D19] p-6 md:p-10">
        <p class="mb-1 text-xs font-bold uppercase tracking-widest text-[#FBAB18]">Payment correction</p>
        <h1 class="mb-3 text-3xl font-bold">Update your deposit reference</h1>
        <p class="mb-6 text-[#B8B8B8]">Booking <?php echo e($bookingReference); ?> · <?php echo e($booking->service_type); ?> · <?php echo e($booking->event_date->format('F j, Y')); ?></p>
        <div class="grid gap-5 rounded-2xl border border-[#333333] bg-[#1A1A1A] p-5 md:grid-cols-[1fr_180px] md:items-center">
            <div>
                <p class="mb-2 text-sm text-[#B8B8B8]">The previous reference was rejected. Please check the reason below, confirm that you paid the deposit, then enter the correct Security Bank transaction reference.</p>
                <?php if($payment->verification_remarks): ?>
                    <p class="mb-3 rounded-lg border border-red-900 bg-red-950/40 p-3 text-sm text-red-200"><strong>Reason:</strong> <?php echo e($payment->verification_remarks); ?></p>
                <?php endif; ?>
                <dl class="grid gap-2 text-sm sm:grid-cols-3">
                    <div><dt class="text-[#B8B8B8]">Estimated total</dt><dd class="font-bold">₱<?php echo e($estimatedTotal); ?></dd></div>
                    <div><dt class="text-[#B8B8B8]">30% deposit</dt><dd class="font-bold text-[#FBAB18]">₱<?php echo e($depositAmount); ?></dd></div>
                    <div><dt class="text-[#B8B8B8]">Remaining balance</dt><dd class="font-bold">₱<?php echo e($remainingBalance); ?></dd></div>
                </dl>
            </div>
            <img src="<?php echo e(asset('images/mixeat qr.jpeg')); ?>" alt="MixEat Security Bank QR code" class="mx-auto max-h-44 w-auto rounded-lg bg-white p-2">
        </div>
        <form action="<?php echo e($correctionSubmitUrl); ?>" method="POST" class="mt-6 space-y-5">
            <?php echo csrf_field(); ?>
            <div>
                <label for="payment-reference" class="mb-1 block text-sm font-semibold">Security Bank payment reference</label>
                <input id="payment-reference" name="payment_reference" type="text" maxlength="64" pattern="[A-Za-z0-9]+" autocomplete="off" required value="<?php echo e(old('payment_reference')); ?>" class="w-full rounded-lg border border-[#555555] bg-[#111111] px-4 py-3 text-white focus:border-[#FBAB18] focus:outline-none">
                <?php $__errorArgs = ['payment_reference'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-red-400"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <label class="flex items-start gap-3 text-sm">
                <input type="checkbox" name="payment_confirmation" value="1" required <?php if(old('payment_confirmation')): echo 'checked'; endif; ?> class="mt-1 accent-[#FBAB18]">
                <span>I confirm that I paid the deposit using the Security Bank QR code.</span>
            </label>
            <?php $__errorArgs = ['payment_confirmation'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-sm text-red-400"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            <button type="submit" class="w-full rounded-full bg-[#FBAB18] px-6 py-3 font-bold text-black transition hover:bg-[#FFC247]">Send corrected payment reference</button>
        </form>
    </main>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\mixeat\mixeat\resources\views\storefront\catering-payment-correction.blade.php ENDPATH**/ ?>