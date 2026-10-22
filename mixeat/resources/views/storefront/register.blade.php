@extends('layouts.app')

@section('title', 'Register | MixEat')

@section('content')
    <section class="py-16" style="background:#111111;">
        <div class="mixeat-shell grid items-center justify-center px-4">
            <div class="w-full max-w-xl rounded-[32px] border border-[#333333] bg-[#201D19] p-8 shadow-[0_20px_60px_rgba(0,0,0,0.4)]">
                <div class="mb-8 text-center">
                    <img src="{{ asset('images/mixeat.jpg') }}" alt="MixEat" class="mx-auto mb-4 h-16 w-16 rounded-2xl object-contain" />
                    <h1 class="text-3xl font-black text-[#FFFFFF]">Create your account</h1>
                </div>

                @if ($errors->any())
                    <div class="mb-6 rounded-2xl border border-red-500/40 bg-red-900/30 px-4 py-3 text-sm text-red-300" role="alert">
                        <p class="font-semibold">Please check the following:</p>
                        <ul class="mt-1 list-disc pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ url('/register') }}" method="POST" class="grid gap-5 md:grid-cols-2">
                    @csrf
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-[#E5E5E5]">First Name</label>
                        <input type="text" name="first_name" value="{{ old('first_name') }}" placeholder="First Name" class="w-full rounded-2xl border border-[#333333] bg-[#1A1A1A] px-4 py-3 text-sm text-white placeholder-[#999999] focus:border-[#FBAB18] focus:outline-none" required />
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-[#E5E5E5]">Last Name</label>
                        <input type="text" name="last_name" value="{{ old('last_name') }}" placeholder="Last Name" class="w-full rounded-2xl border border-[#333333] bg-[#1A1A1A] px-4 py-3 text-sm text-white placeholder-[#999999] focus:border-[#FBAB18] focus:outline-none" required />
                    </div>
                    <div class="md:col-span-2">
                        <label class="mb-2 block text-sm font-semibold text-[#E5E5E5]">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="Email" class="w-full rounded-2xl border border-[#333333] bg-[#1A1A1A] px-4 py-3 text-sm text-white placeholder-[#999999] focus:border-[#FBAB18] focus:outline-none" required />
                    </div>
                    <div class="md:col-span-2">
                        <label class="mb-2 block text-sm font-semibold text-[#E5E5E5]">Phone Number</label>
                        <input type="tel" name="phone" value="{{ old('phone') }}" placeholder="Phone Number" class="w-full rounded-2xl border border-[#333333] bg-[#1A1A1A] px-4 py-3 text-sm text-white placeholder-[#999999] focus:border-[#FBAB18] focus:outline-none" required />
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-[#E5E5E5]">Password</label>
                        <input type="password" name="password" placeholder="Password" class="w-full rounded-2xl border border-[#333333] bg-[#1A1A1A] px-4 py-3 text-sm text-white placeholder-[#999999] focus:border-[#FBAB18] focus:outline-none" required />
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-[#E5E5E5]">Confirm Password</label>
                        <input type="password" name="password_confirmation" placeholder="Confirm Password" class="w-full rounded-2xl border border-[#333333] bg-[#1A1A1A] px-4 py-3 text-sm text-white placeholder-[#999999] focus:border-[#FBAB18] focus:outline-none" required />
                    </div>

                    <div class="md:col-span-2">
                        <button type="submit" class="w-full rounded-full bg-[#FBAB18] px-6 py-3.5 text-base font-semibold text-[#111111] transition hover:bg-[#FFC247]">
                            Create Account
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection


