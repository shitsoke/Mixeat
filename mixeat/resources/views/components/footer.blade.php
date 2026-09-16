<footer class="mt-20 border-t border-[#E8DFAF] bg-[#090909] text-white">
    <div class="mixeat-shell grid gap-10 px-4 py-14 md:grid-cols-2 lg:grid-cols-5">
        <div class="lg:col-span-2">
            <div class="mb-4 flex items-center gap-3">
                <img src="{{ asset('images/mixeat.jpg') }}" alt="MixEat" class="h-14 w-14 rounded-xl object-contain" />
                <div class="text-xl font-extrabold tracking-[0.16em] text-[#FFC60A]">MIXEAT</div>
            </div>
            <p class="max-w-sm text-sm leading-7 text-[#555555]">
                Fresh, flavorful meals made for busy days and great moments. Enjoy your favorites from the nearest MixEat branch.
            </p>
        </div>

        <div>
            <h3 class="mb-4 text-base font-bold uppercase tracking-[0.08em] text-[#FFC60A]">Quick Links</h3>
            <ul class="space-y-3 text-sm text-white/75">
                <li><a href="{{ route('home') }}" class="hover:text-[#FFC60A]">Home</a></li>
                <li><a href="{{ route('menu') }}" class="hover:text-[#FFC60A]">Menu</a></li>
                <li><a href="{{ route('featured') }}" class="hover:text-[#FFC60A]">Featured</a></li>
                <li><a href="{{ route('branches') }}" class="hover:text-[#FFC60A]">Branches</a></li>
                <li><a href="{{ route('about') }}" class="hover:text-[#FFC60A]">About</a></li>
                <li><a href="{{ route('contact') }}" class="hover:text-[#FFC60A]">Contact</a></li>
            </ul>
        </div>

        <div>
            <h3 class="mb-4 text-base font-bold uppercase tracking-[0.08em] text-[#FFC60A]">Customer</h3>
            <ul class="space-y-3 text-sm text-white/75">
                <li><a href="{{ route('profile') }}" class="hover:text-[#FFC60A]">My Account</a></li>
                <li><a href="{{ route('orders') }}" class="hover:text-[#FFC60A]">My Orders</a></li>
                <li><a href="{{ route('cart') }}" class="hover:text-[#FFC60A]">Cart</a></li>
            </ul>
        </div>

        <div>
            <h3 class="mb-4 text-base font-bold uppercase tracking-[0.08em] text-[#FFC60A]">Contact</h3>
            <ul class="space-y-3 text-sm text-white/75">
                <li>+63 32 123 4567</li>
                <li>hello@mixeat.ph</li>
                <li>123 Mango Avenue, Cebu City</li>
            </ul>
        </div>
    </div>

    <div class="border-t border-white/10 bg-[#090909]">
        <div class="mixeat-shell flex flex-col items-center justify-between gap-3 px-4 py-5 text-sm text-white/60 md:flex-row">
            <p>(c) 2026 MixEat. All Rights Reserved.</p>
            <div class="flex gap-4">
                <span>Instagram</span>
                <span>Facebook</span>
                <span>Messenger</span>
            </div>
        </div>
    </div>
</footer>


