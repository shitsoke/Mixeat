<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Payment Submitted | MixEat</title>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
</head>
<body class="min-h-screen bg-[#111111] px-4 py-10 text-white">
    <main class="mx-auto max-w-2xl rounded-3xl border border-[#333333] bg-[#201D19] p-6 text-center md:p-10">
        <p class="mb-1 text-xs font-bold uppercase tracking-widest text-[#FBAB18]">Received for review</p>
        <h1 class="mb-3 text-3xl font-bold">Corrected reference submitted</h1>
        <p class="mb-5 text-[#B8B8B8]">Booking <?php echo e($bookingReference); ?> is still awaiting payment verification. It is not confirmed until the MixEat team reviews the payment.</p>
        <a href="<?php echo e(route('catering')); ?>" class="inline-flex rounded-full bg-[#FBAB18] px-6 py-3 font-bold text-black hover:bg-[#FFC247]">Return to MixEat</a>
    </main>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\mixeat\mixeat\resources\views\storefront\catering-payment-correction-complete.blade.php ENDPATH**/ ?>