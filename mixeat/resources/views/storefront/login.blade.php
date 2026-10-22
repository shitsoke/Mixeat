@extends('layouts.app')

@section('title', 'Login | MixEat')

@section('content')
    <section class="py-16">
        <div class="mixeat-shell grid items-center justify-center px-4">
            <div class="w-full max-w-md rounded-[32px] border border-[#333333] bg-[#201D19] p-8 shadow-[0_20px_60px_rgba(229,169,0,0.12)]">
                <div class="mb-8 text-center">
                    <img src="{{ asset('images/mixeat.jpg') }}" alt="MixEat" class="mx-auto mb-4 h-16 w-16 rounded-2xl object-contain shadow-lg shadow-[#FBAB18]/25" />
                    <h1 class="text-3xl font-black text-[#FFFFFF]">Welcome back</h1>
                    <p class="mt-2 text-[#B8B8B8]">Sign in to your MixEat account</p>
                </div>

                @if (empty($socialProviders))
                    <div class="mb-6 rounded-2xl border border-[#333333] bg-[#1A1A1A] px-4 py-3 text-sm text-[#B8B8B8]" role="status">
                        Google and Facebook sign-in will be available once configured.
                    </div>
                @endif

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
                        <label class="mb-2 block text-sm font-semibold text-[#B8B8B8]">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" class="w-full rounded-2xl border border-[#333333] bg-[#1A1A1A] px-4 py-3 text-sm text-[#FFFFFF] focus:outline-none" placeholder="you@example.com" required />
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-[#B8B8B8]">Password</label>
                        <input type="password" name="password" class="w-full rounded-2xl border border-[#333333] bg-[#1A1A1A] px-4 py-3 text-sm text-[#FFFFFF] focus:outline-none" placeholder="********" required />
                    </div>

                    @if (!empty($socialProviders))
                        <div class="space-y-4">
                            <div class="flex items-center gap-4 text-xs font-semibold uppercase tracking-wider text-[#888888]">
                                <span class="h-px flex-1 bg-[#333333]"></span>
                                <span>or continue with</span>
                                <span class="h-px flex-1 bg-[#333333]"></span>
                            </div>
                            <div class="flex justify-center gap-4">
                                @if (in_array('google', $socialProviders, true))
                                    <a href="{{ route('social.redirect', 'google') }}" aria-label="Continue with Google" title="Continue with Google" class="flex h-12 w-12 items-center justify-center rounded-full border border-[#D6D6D6] bg-[#201D19] transition hover:bg-[#F7F7F7] focus:outline-none focus:ring-2 focus:ring-[#FBAB18] focus:ring-offset-2">
                                        <svg aria-hidden="true" viewBox="0 0 48 48" class="h-6 w-6">
                                            <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
                                            <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.72 7.18l7.62 5.91c4.45-4.11 7.14-10.17 7.14-17.56z"/>
                                            <path fill="#FBBC05" d="M10.53 28.59A14.4 14.4 0 0 1 9.75 24c0-1.59.27-3.13.76-4.59l-7.98-6.19A23.9 23.9 0 0 0 0 24c0 3.87.93 7.53 2.56 10.78l7.97-6.19z"/>
                                            <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.9-5.89l-7.62-5.91c-2.12 1.42-4.84 2.27-8.28 2.27-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>
                                        </svg>
                                    </a>
                                @endif
                                @if (in_array('facebook', $socialProviders, true))
                                    <a href="{{ route('social.redirect', 'facebook') }}" aria-label="Continue with Facebook" title="Continue with Facebook" class="flex h-12 w-12 items-center justify-center rounded-full bg-[#1877F2] text-white transition hover:bg-[#166FE5] focus:outline-none focus:ring-2 focus:ring-[#FBAB18] focus:ring-offset-2">
                                        <svg aria-hidden="true" viewBox="0 0 24 24" class="h-6 w-6 fill-current">
                                            <path d="M24 12.073C24 5.405 18.627 0 12 0S0 5.405 0 12.073C0 18.1 4.388 23.094 10.125 24v-8.437H7.078v-3.49h3.047V9.413c0-3.025 1.792-4.697 4.533-4.697 1.312 0 2.686.236 2.686.236v2.97h-1.513c-1.491 0-1.956.932-1.956 1.888v2.263h3.328l-.532 3.49h-2.796V24C19.612 23.094 24 18.1 24 12.073z"/>
                                        </svg>
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endif

                    <button type="submit" class="w-full rounded-full bg-[#FBAB18] px-6 py-3.5 text-base font-semibold text-[#FFFFFF] shadow-lg shadow-[#FBAB18]/25 transition hover:bg-[#FBAB18]">
                        Login
                    </button>
                </form>

                <div class="mt-6 flex items-center justify-between text-sm text-[#B8B8B8]">
                    <a href="#" class="font-semibold text-[#FBAB18]">Forgot Password?</a>
                    <a href="{{ route('register') }}" class="font-semibold text-[#FBAB18]">Create Account</a>
                </div>
            </div>
        </div>
    </section>
@endsection

