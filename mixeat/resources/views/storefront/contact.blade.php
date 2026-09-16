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
                            <p class="mt-2">+63 32 123 4567</p>
                        </div>
                        <div>
                            <p class="font-bold uppercase tracking-[0.12em] text-[#090909]">Email</p>
                            <p class="mt-2">ragnarokgenshin@gmail.com</p>
                        </div>
                        <div>
                            <p class="font-bold uppercase tracking-[0.12em] text-[#090909]">Address</p>
                            <p class="mt-2">123 Mango Avenue, Cebu City</p>
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


