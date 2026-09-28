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

            <!-- Social Media Icon Links -->
            <div class="mt-6 flex items-center space-x-3">
                <!-- Facebook -->
                <a href="https://www.facebook.com/mixeatbyjonies" target="_blank" rel="noopener noreferrer"
                   class="flex h-9 w-9 items-center justify-center rounded-full bg-white/10 text-white transition hover:bg-[#FFC60A] hover:text-[#090909]"
                   title="Facebook">
                    <svg class="h-4 w-4 fill-current" viewBox="0 0 24 24">
                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                    </svg>
                </a>

                <!-- Instagram -->
                <a href="https://instagram.com/mixeat" target="_blank" rel="noopener noreferrer"
                   class="flex h-9 w-9 items-center justify-center rounded-full bg-white/10 text-white transition hover:bg-[#FFC60A] hover:text-[#090909]"
                   title="Instagram">
                    <svg class="h-4 w-4 fill-current" viewBox="0 0 24 24">
                        <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                    </svg>
                </a>

                <!-- X (Twitter) -->
                <a href="https://x.com/mixeat" target="_blank" rel="noopener noreferrer"
                   class="flex h-9 w-9 items-center justify-center rounded-full bg-white/10 text-white transition hover:bg-[#FFC60A] hover:text-[#090909]"
                   title="X">
                    <svg class="h-3.5 w-3.5 fill-current" viewBox="0 0 24 24">
                        <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                    </svg>
                </a>
            </div>
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
                <li>0920 967 5537</li>
                <li>joniesmarketingassistant@gmail.com</li>
                <li>1058 Hernan Cortes St., Subangdaku, Mandaue City, Philippines, 6014</li>
            </ul>
        </div>
    </div>

    <div class="border-t border-white/10 bg-[#090909]">
        <div class="mixeat-shell flex flex-col items-center justify-between gap-3 px-4 py-5 text-sm text-white/60 md:flex-row">
            <p>&copy; 2026 MixEat. All Rights Reserved.</p>
            <div class="flex gap-4">
                <a href="https://instagram.com/mixeat" target="_blank" rel="noopener noreferrer" class="hover:text-[#FFC60A] transition">Instagram</a>
                <a href="https://www.facebook.com/mixeatbyjonies" target="_blank" rel="noopener noreferrer" class="hover:text-[#FFC60A] transition">Facebook</a>
                <a href="https://x.com/mixeat" target="_blank" rel="noopener noreferrer" class="hover:text-[#FFC60A] transition">X</a>
            </div>
        </div>
    </div>
</footer>