@extends('layouts.app')

@section('title', 'About MixEat')

@section('content')
    <section class="py-14">
        <div class="mixeat-shell px-4">
            <div class="grid items-center gap-10 lg:grid-cols-2">
                <div>
                    <p class="text-sm font-bold uppercase tracking-[0.18em] text-[#FBAB18]">Our Culinary Story</p>
                    <h1 class="mt-3 text-4xl font-black text-[#FFFFFF]">We Cater, We Serve, We Celebrate</h1>
                    <p class="mt-5 text-lg leading-8 text-[#B8B8B8]">
                        MixEat was establish to redifinehow quick Asian rice bowls and large-scale event catering are served. Combining high-heat wok techniques with small-batch glazes, every bowl delivers an unforgettable taste experience.
                    </p>
                    <div class="mt-8 grid gap-4 sm:grid-cols-2">
                        <div class="rounded-[20px] border border-[#333333] bg-[#201D19] p-5">
                            <div class="mb-3 text-[#FBAB18]">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#FBAB18" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                            </div>
                            <h3 class="text-lg font-bold text-[#FFFFFF]">Hygiene Certified</h3>
                            <p class="mt-2 text-sm text-[#B8B8B8]">Strict clean kitchen standards.</p>
                        </div>
                        <div class="rounded-[20px] border border-[#333333] bg-[#201D19] p-5">
                            <div class="mb-3 text-[#22C55E]">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#22C55E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10Z"/><path d="M2 21c0-3 1.85-5.36 5.08-6C9.5 14.52 12 13 13 12"/></svg>
                            </div>
                            <h3 class="text-lg font-bold text-[#FFFFFF]">Eco Bento Packs</h3>
                            <p class="mt-2 text-sm text-[#B8B8B8]">Compostable bento bowls.</p>
                        </div>
                    </div>
                    {{-- <p class="mt-5 text-lg leading-8 text-[#B8B8B8]">
                        Our mission is simple: serve quality meals while creating a welcoming experience for every guest, from first order to final bite.
                    </p> --}}
                </div>

                <div class="overflow-hidden rounded-[32px] border border-[#333333] bg-[#201D19] p-4 shadow-[0_20px_60px_rgba(229,169,0,0.12)]">
                    <img src="{{ asset('images/About-us.jpg') }}" alt="MixEat restaurant" class="h-[280px] sm:h-[380px] md:h-[480px] w-full rounded-[24px] object-cover">
                </div>
            </div>

            {{-- <div class="mt-16 grid gap-6 md:grid-cols-3">
                <div class="rounded-[28px] border border-[#333333] bg-[#201D19] p-6 shadow-[0_14px_40px_rgba(229,169,0,0.10)]">
                    <p class="text-sm font-bold uppercase tracking-[0.12em] text-[#FBAB18]">Mission</p>
                    <p class="mt-4 text-base leading-7 text-[#B8B8B8]">To serve delicious, satisfying meals with speed, consistency, and care across every MixEat branch.</p>
                </div>
                <div class="rounded-[28px] border border-[#333333] bg-[#201D19] p-6 shadow-[0_14px_40px_rgba(229,169,0,0.10)]">
                    <p class="text-sm font-bold uppercase tracking-[0.12em] text-[#FBAB18]">Vision</p>
                    <p class="mt-4 text-base leading-7 text-[#B8B8B8]">To become the go-to food experience for modern customers who value convenience and flavor.</p>
                </div>
                <div class="rounded-[28px] border border-[#333333] bg-[#201D19] p-6 shadow-[0_14px_40px_rgba(229,169,0,0.10)]">
                    <p class="text-sm font-bold uppercase tracking-[0.12em] text-[#FBAB18]">Why MixEat</p>
                    <p class="mt-4 text-base leading-7 text-[#B8B8B8]">Fast ordering, fresh ingredients, branch convenience, and a friendly experience built around everyday cravings.</p>
                </div>
            </div> --}}
        </div>
    </section>
@endsection


