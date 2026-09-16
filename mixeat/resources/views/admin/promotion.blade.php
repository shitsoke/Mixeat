@extends('layouts.app')

@section('title', 'Promotion | MixEat')

@section('content')
<section class="py-14"><div class="mixeat-shell px-4"><div class="mx-auto max-w-2xl rounded-[30px] border border-[#E8DFAF] bg-white p-8 shadow-sm">
    <p class="text-sm font-bold uppercase tracking-[0.18em] text-[#FFC60A]">Admin</p>
    <h1 class="mt-2 text-4xl font-black">Homepage Promotion</h1>
    <p class="mt-3 text-[#555555]">Change the promotional image and text shown in the homepage feature banner.</p>
    @if(session('admin_status'))<div class="mt-6 rounded-2xl bg-[#FFF4BF] px-4 py-3 font-semibold">{{ session('admin_status') }}</div>@endif
    @if($errors->any())<div class="mt-6 rounded-2xl bg-red-50 p-4 text-sm text-[#DC3545]"><ul class="list-disc pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
    <form action="{{ route('admin.promotion.update') }}" method="POST" enctype="multipart/form-data" class="mt-8 grid gap-5">@csrf @method('PUT')
        <label class="grid gap-2 text-sm font-semibold">Label<input name="label" required value="{{ old('label', $promotion['promotion_label'] ?? "Today's favorite") }}" class="rounded-2xl border border-[#E8DFAF] bg-[#FFF9E6] px-4 py-3"></label>
        <label class="grid gap-2 text-sm font-semibold">Title<input name="title" required value="{{ old('title', $promotion['promotion_title'] ?? 'Chicken Meal Combo') }}" class="rounded-2xl border border-[#E8DFAF] bg-[#FFF9E6] px-4 py-3"></label>
        <label class="grid gap-2 text-sm font-semibold">Price<input name="price" type="number" step="0.01" min="0" value="{{ old('price', $promotion['promotion_price'] ?? 149) }}" class="rounded-2xl border border-[#E8DFAF] bg-[#FFF9E6] px-4 py-3"></label>
        <label class="grid gap-2 text-sm font-semibold">Promotion Image<input name="image" type="file" accept="image/jpeg,image/png,image/webp" class="rounded-2xl border border-[#E8DFAF] bg-[#FFF9E6] px-4 py-3"><span class="text-xs font-normal text-[#555555]">Upload a new image to replace the current promotion image.</span></label>
        <div class="flex gap-3"><a href="{{ route('admin.products.index') }}" class="rounded-full border border-[#FFC60A] px-5 py-3 font-semibold text-[#E5A900]">Back</a><button class="rounded-full bg-[#FFC60A] px-5 py-3 font-semibold text-[#090909]">Save Promotion</button></div>
    </form>
</div></div></section>
@endsection