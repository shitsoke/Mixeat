@extends('layouts.app')

@section('title', 'Change Password | MixEat')

@section('content')
<section class="py-16">
    <div class="mixeat-shell px-4">
        <div class="mx-auto max-w-md rounded-[32px] border border-[#E8DFAF] bg-white p-8 shadow-[0_14px_40px_rgba(229,169,0,0.10)]">
            <h1 class="text-2xl font-black text-[#090909]">Change Password</h1>
            <p class="mt-1 text-sm text-[#777777]">Update your account password below.</p>

            @if ($errors->any())
                <div class="mt-4 rounded-2xl border border-red-200 bg-red-50 p-3 text-xs text-red-600">
                    <ul class="list-inside list-disc">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('password.update') }}" method="POST" class="mt-6 space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-semibold text-[#555555]">Current Password</label>
                    <input type="password" name="current_password" required class="mt-1 w-full rounded-full border border-[#E8DFAF] bg-[#FDFBF4] px-4 py-3 text-sm focus:border-[#FFC60A] focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-[#555555]">New Password</label>
                    <input type="password" name="password" required class="mt-1 w-full rounded-full border border-[#E8DFAF] bg-[#FDFBF4] px-4 py-3 text-sm focus:border-[#FFC60A] focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-[#555555]">Confirm New Password</label>
                    <input type="password" name="password_confirmation" required class="mt-1 w-full rounded-full border border-[#E8DFAF] bg-[#FDFBF4] px-4 py-3 text-sm focus:border-[#FFC60A] focus:outline-none">
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full rounded-full bg-[#FFC60A] py-3.5 text-sm font-semibold text-[#090909] shadow-lg shadow-[#E5A900]/25 transition hover:bg-[#E5A900]">
                        Update Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection