@extends('layouts.app')

@section('title', 'Contact MixEat')

@section('content')
    <section class="py-14">
        <div class="mixeat-shell px-4">
            <div class="mb-8 text-center">
                <p class="text-sm font-bold uppercase tracking-[0.18em] text-[#FFC60A]">Contact Us</p>
                <h1 class="mt-2 text-4xl font-black text-[#090909]">We'd love to hear from you.</h1>
            </div>

            <div class="grid gap-8 lg:grid-cols-[380px_minmax(0,1fr)]">
                <aside class="rounded-[30px] border border-[#E8DFAF] bg-white p-6 shadow-[0_14px_40px_rgba(229,169,0,0.10)]">
                    <h2 class="text-2xl font-black text-[#090909]">Contact Information</h2>
                    <div class="mt-6 space-y-4 text-sm text-[#555555]">
                        <div>
                            <p class="font-bold uppercase tracking-[0.12em] text-[#090909]">Phone</p>
                            <p class="mt-2">0920 967 5537</p>
                        </div>
                        <div>
                            <p class="font-bold uppercase tracking-[0.12em] text-[#090909]">Email</p>
                            <p class="mt-2">joniesmarketingassistant@gmail.com</p>
                        </div>
                        <div>
                            <p class="font-bold uppercase tracking-[0.12em] text-[#090909]">Address</p>
                            <p class="mt-2">1058 Hernan Cortes St., Subangdaku, Mandaue City, Philippines, 6014</p>
                        </div>
                    </div>

                    <!-- Social Media Links -->
                    <div class="mt-6">
                        <p class="font-bold uppercase tracking-[0.12em] text-[#090909] text-sm">Follow Us</p>
                        <div class="mt-3 flex items-center space-x-3">
                            <!-- Facebook -->
                            <a href="https://www.facebook.com/mixeatbyjonies" target="_blank" rel="noopener noreferrer" 
                               class="flex h-10 w-10 items-center justify-center rounded-full bg-[#FFF9E6] border border-[#E8DFAF] text-[#090909] transition hover:bg-[#FFC60A]"
                               title="Facebook">
                                <svg class="h-5 w-5 fill-current" viewBox="0 0 24 24">
                                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                </svg>
                            </a>

                            <!-- Instagram -->
                            <a href="https://instagram.com/mixeat" target="_blank" rel="noopener noreferrer" 
                               class="flex h-10 w-10 items-center justify-center rounded-full bg-[#FFF9E6] border border-[#E8DFAF] text-[#090909] transition hover:bg-[#FFC60A]"
                               title="Instagram">
                                <svg class="h-5 w-5 fill-current" viewBox="0 0 24 24">
                                    <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                                </svg>
                            </a>

                            <!-- X (Twitter) -->
                            <a href="https://x.com/mixeat" target="_blank" rel="noopener noreferrer" 
                               class="flex h-10 w-10 items-center justify-center rounded-full bg-[#FFF9E6] border border-[#E8DFAF] text-[#090909] transition hover:bg-[#FFC60A]"
                               title="X">
                                <svg class="h-4 w-4 fill-current" viewBox="0 0 24 24">
                                    <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                                </svg>
                            </a>
                        </div>
                    </div>

                    <div class="mt-8 rounded-[24px] bg-[#FFF9E6] p-5">
                        <h3 class="text-lg font-black text-[#090909]">Branch Locations</h3>
                        <ul class="mt-4 space-y-3 text-sm text-[#555555]">
                            <li>MixEat Banilad</li>
                            <li>MixEat Mandaue</li>
                            <li>MixEat Talamban</li>
                        </ul>
                    </div>
                </aside>

                <div class="rounded-[30px] border border-[#E8DFAF] bg-white p-6 shadow-[0_14px_40px_rgba(229,169,0,0.10)]">
                    <h2 class="text-2xl font-black text-[#090909]">Send us a message</h2>
                    <form class="mt-6 grid gap-5 md:grid-cols-2">
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-[#555555]">Name</label>
                            <input type="text" class="w-full rounded-2xl border border-[#E8DFAF] bg-[#FFF9E6] px-4 py-3 text-sm text-[#090909] focus:outline-none" />
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-[#555555]">Email</label>
                            <input type="email" class="w-full rounded-2xl border border-[#E8DFAF] bg-[#FFF9E6] px-4 py-3 text-sm text-[#090909] focus:outline-none" />
                        </div>
                        <div class="md:col-span-2">
                            <label class="mb-2 block text-sm font-semibold text-[#555555]">Subject</label>
                            <input type="text" class="w-full rounded-2xl border border-[#E8DFAF] bg-[#FFF9E6] px-4 py-3 text-sm text-[#090909] focus:outline-none" />
                        </div>
                        <div class="md:col-span-2">
                            <label class="mb-2 block text-sm font-semibold text-[#555555]">Message</label>
                            <textarea rows="5" class="w-full rounded-2xl border border-[#E8DFAF] bg-[#FFF9E6] px-4 py-3 text-sm text-[#090909] focus:outline-none"></textarea>
                        </div>
                        <div class="md:col-span-2">
                            <button type="submit" class="inline-flex items-center justify-center rounded-full bg-[#FFC60A] px-6 py-3.5 text-base font-semibold text-[#090909] shadow-lg shadow-[#E5A900]/25 transition hover:bg-[#E5A900]">
                                Send Message
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection