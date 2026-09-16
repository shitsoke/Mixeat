@extends('layouts.app')

@section('title', 'Login | MixEat')

@section('content')
    <section class="py-16">
        <div class="mixeat-shell grid items-center justify-center px-4">
            <div class="w-full max-w-md rounded-[32px] border border-[#E8DFAF] bg-white p-8 shadow-[0_20px_60px_rgba(229,169,0,0.12)]">
                <div class="mb-8 text-center">
                    <img src="{{ asset('images/mixeat.jpg') }}" alt="MixEat" class="mx-auto mb-4 h-16 w-16 rounded-2xl object-contain shadow-lg shadow-[#E5A900]/25" />
                    <h1 class="text-3xl font-black text-[#090909]">Welcome back</h1>
                    <p class="mt-2 text-[#555555]">Sign in to your MixEat account</p>
                </div>

                @if ($errors->any())
                    <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700" role="alert">
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <form action="{{ url('/login') }}" method="POST" class="space-y-5">
                    @csrf
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-[#555555]">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" class="w-full rounded-2xl border border-[#E8DFAF] bg-[#FFF9E6] px-4 py-3 text-sm text-[#090909] focus:outline-none" placeholder="you@example.com" required />
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-[#555555]">Password</label>
                        <input type="password" name="password" class="w-full rounded-2xl border border-[#E8DFAF] bg-[#FFF9E6] px-4 py-3 text-sm text-[#090909] focus:outline-none" placeholder="********" required />
                    </div>

                    <button type="submit" class="w-full rounded-full bg-[#FFC60A] px-6 py-3.5 text-base font-semibold text-[#090909] shadow-lg shadow-[#E5A900]/25 transition hover:bg-[#E5A900]">
                        Login
                    </button>
                </form>

                <div class="mt-6 flex items-center justify-between text-sm text-[#555555]">
                    <a href="#" class="font-semibold text-[#FFC60A]">Forgot Password?</a>
                    <a href="{{ route('register') }}" class="font-semibold text-[#FFC60A]">Create Account</a>
                </div>
            </div>
        </div>
    </section>
@endsection


