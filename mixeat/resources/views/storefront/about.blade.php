@extends('layouts.app')

@section('title', 'About MixEat')

@section('content')
    <section class="py-14">
        <div class="mixeat-shell px-4">
            <div class="grid items-center gap-10 lg:grid-cols-2">
                <div>
                    <p class="text-sm font-bold uppercase tracking-[0.18em] text-[#FFC60A]">About MixEat</p>
                    <h1 class="mt-3 text-4xl font-black text-[#090909]">Fresh food, made for real life.</h1>
                    <p class="mt-5 text-lg leading-8 text-[#555555]">
                        MixEat is a modern food brand built to make everyday dining easier, faster, and more enjoyable. From favorite comfort meals to quick snacks and refreshing drinks, we bring delicious food closer to the people who need it most.
                    </p>
                    <p class="mt-5 text-lg leading-8 text-[#555555]">
                        Our mission is simple: serve quality meals while creating a welcoming experience for every guest, from first order to final bite.
                    </p>
                </div>

                <div class="overflow-hidden rounded-[32px] border border-[#E8DFAF] bg-white p-4 shadow-[0_20px_60px_rgba(229,169,0,0.12)]">
                    <img src="https://images.unsplash.com/photo-1559339352-11d035aa65de?auto=format&fit=crop&w=1200&q=80" alt="MixEat restaurant" class="h-[480px] w-full rounded-[24px] object-cover">
                </div>
            </div>

            <div class="mt-16 grid gap-6 md:grid-cols-3">
                <div class="rounded-[28px] border border-[#E8DFAF] bg-white p-6 shadow-[0_14px_40px_rgba(229,169,0,0.10)]">
                    <p class="text-sm font-bold uppercase tracking-[0.12em] text-[#FFC60A]">Mission</p>
                    <p class="mt-4 text-base leading-7 text-[#555555]">To serve delicious, satisfying meals with speed, consistency, and care across every MixEat branch.</p>
                </div>
                <div class="rounded-[28px] border border-[#E8DFAF] bg-white p-6 shadow-[0_14px_40px_rgba(229,169,0,0.10)]">
                    <p class="text-sm font-bold uppercase tracking-[0.12em] text-[#FFC60A]">Vision</p>
                    <p class="mt-4 text-base leading-7 text-[#555555]">To become the go-to food experience for modern customers who value convenience and flavor.</p>
                </div>
                <div class="rounded-[28px] border border-[#E8DFAF] bg-white p-6 shadow-[0_14px_40px_rgba(229,169,0,0.10)]">
                    <p class="text-sm font-bold uppercase tracking-[0.12em] text-[#FFC60A]">Why MixEat</p>
                    <p class="mt-4 text-base leading-7 text-[#555555]">Fast ordering, fresh ingredients, branch convenience, and a friendly experience built around everyday cravings.</p>
                </div>
            </div>
        </div>
    </section>
@endsection


