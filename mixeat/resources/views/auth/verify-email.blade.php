@extends('layouts.app')

@section('title', 'Verify Your Email | MixEat')

@section('content')
    <section class="py-16">
        <div class="mixeat-shell px-4">
            <div class="mx-auto max-w-md rounded-[32px] border border-[#E8DFAF] bg-white p-8 text-center shadow-[0_14px_40px_rgba(229,169,0,0.10)]">
                <div class="mx-auto mb-6 flex h-20 w-20 items-center justify-center rounded-full bg-[#FFF4BF] text-[#FFC60A]">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>

                <h1 class="text-3xl font-black text-[#090909]">Verify your email</h1>
                
                <p class="mt-3 text-sm text-[#555555]">
                    Thanks for joining MixEat! Before getting started, please check your inbox and click the verification link we just emailed to you.
                </p>

                @if (session('message'))
                    <div class="mt-4 rounded-2xl border border-[#E8DFAF] bg-[#FFF4BF] p-3 text-xs font-semibold text-[#090909]">
                        {{ session('message') }}
                    </div>
                @endif

                <div class="mt-6 space-y-3">
                    <form action="{{ route('verification.send') }}" method="POST">
                        @csrf
                        <button type="submit" class="inline-flex w-full items-center justify-center rounded-full bg-[#FFC60A] px-6 py-3.5 text-sm font-semibold text-[#090909] shadow-lg shadow-[#E5A900]/25 transition hover:bg-[#E5A900]">
                            Resend Verification Email
                        </button>
                    </form>

                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="text-xs font-semibold text-[#777777] underline hover:text-[#090909]">
                            Log Out
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection