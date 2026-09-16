@extends('layouts.app')

@section('title', ($branch->exists ? 'Edit Branch' : 'Add Branch').' | MixEat')

@section('content')
<section class="py-14"><div class="mixeat-shell px-4"><div class="mx-auto max-w-2xl rounded-[30px] border border-[#E8DFAF] bg-white p-8 shadow-sm">
    <p class="text-sm font-bold uppercase tracking-[0.18em] text-[#FFC60A]">Admin</p>
    <h1 class="mt-2 text-4xl font-black">{{ $branch->exists ? 'Edit Branch' : 'Add Branch' }}</h1>
    @if($errors->any())<div class="mt-6 rounded-2xl bg-red-50 p-4 text-sm text-[#DC3545]"><ul class="list-disc pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
    <form action="{{ $branch->exists ? route('admin.branches.update', $branch) : route('admin.branches.store') }}" method="POST" class="mt-8 grid gap-5">@csrf @if($branch->exists) @method('PUT') @endif
        <label class="grid gap-2 text-sm font-semibold">Branch Name<input name="name" required value="{{ old('name', $branch->name) }}" class="rounded-2xl border border-[#E8DFAF] bg-[#FFF9E6] px-4 py-3"></label>
        <label class="grid gap-2 text-sm font-semibold">Address<input name="address" required value="{{ old('address', $branch->address) }}" class="rounded-2xl border border-[#E8DFAF] bg-[#FFF9E6] px-4 py-3"></label>
        <label class="grid gap-2 text-sm font-semibold">Opening Hours<input name="opening_hours" required value="{{ old('opening_hours', $branch->opening_hours) }}" placeholder="8:00 AM - 9:00 PM" class="rounded-2xl border border-[#E8DFAF] bg-[#FFF9E6] px-4 py-3"></label>
        <label class="grid gap-2 text-sm font-semibold">Distance<input name="distance" required value="{{ old('distance', $branch->distance) }}" placeholder="1.2 km away" class="rounded-2xl border border-[#E8DFAF] bg-[#FFF9E6] px-4 py-3"></label>
        <div class="flex gap-3"><a href="{{ route('admin.branches.index') }}" class="rounded-full border border-[#FFC60A] px-5 py-3 font-semibold text-[#E5A900]">Cancel</a><button class="rounded-full bg-[#FFC60A] px-5 py-3 font-semibold text-[#090909]">{{ $branch->exists ? 'Update Branch' : 'Save Branch' }}</button></div>
    </form>
</div></div></section>
@endsection
