@extends('layouts.app')

@section('title', ($product->exists ? 'Edit Food' : 'Add Food').' | MixEat')

@section('content')
<section class="py-14"><div class="mixeat-shell px-4"><div class="mx-auto max-w-2xl rounded-[30px] border border-[#333333] bg-[#201D19] p-8 shadow-sm"><p class="text-sm font-bold uppercase tracking-[0.18em] text-[#FBAB18]">Admin</p><h1 class="mt-2 text-4xl font-black">{{ $product->exists ? 'Edit Food' : 'Add Food' }}</h1>
@if($errors->any())<div class="mt-6 rounded-2xl bg-red-50 p-4 text-sm text-[#DC3545]"><ul class="list-disc pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<form action="{{ $product->exists ? route('admin.products.update', $product) : route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="mt-8 grid gap-5">@csrf @if($product->exists) @method('PUT') @endif
<label class="grid gap-2 text-sm font-semibold">Name<input name="name" required value="{{ old('name', $product->name) }}" class="rounded-2xl border border-[#333333] bg-[#1A1A1A] px-4 py-3"></label>
<label class="grid gap-2 text-sm font-semibold">Category<select name="category" required class="rounded-2xl border border-[#333333] bg-[#1A1A1A] px-4 py-3"><option value="">Select a category</option>@foreach($categories as $category)<option value="{{ $category }}" @selected(old('category', $product->category) === $category)>{{ $category }}</option>@endforeach</select></label>
<label class="grid gap-2 text-sm font-semibold">Subcategory (Food Bowls)<select name="subcategory" class="rounded-2xl border border-[#333333] bg-[#1A1A1A] px-4 py-3"><option value="">Select</option><option value="Chicken">Chicken</option><option value="Pork">Pork</option><option value="Beef">Beef</option><option value="Fish">Fish</option></select></label>
<label class="flex items-center gap-2 text-sm font-semibold"><input type="checkbox" name="is_best_seller" value="1"> Best Seller</label>
<label class="grid gap-2 text-sm font-semibold">Description<textarea name="description" required rows="4" class="rounded-2xl border border-[#333333] bg-[#1A1A1A] px-4 py-3">{{ old('description', $product->description) }}</textarea></label>
<label class="grid gap-2 text-sm font-semibold">Price<input name="price" type="number" step="0.01" min="0" required value="{{ old('price', $product->price) }}" class="rounded-2xl border border-[#333333] bg-[#1A1A1A] px-4 py-3"></label>
<label class="grid gap-2 text-sm font-semibold">Food Image<input name="image" type="file" accept="image/jpeg,image/png,image/webp" class="rounded-2xl border border-[#333333] bg-[#1A1A1A] px-4 py-3">@if($product->image)<span class="text-xs font-normal text-[#B8B8B8]">Choose a new image to replace the current one.</span>@endif</label>
<label class="grid gap-2 text-sm font-semibold">Badge<input name="badge" value="{{ old('badge', $product->badge) }}" class="rounded-2xl border border-[#333333] bg-[#1A1A1A] px-4 py-3"></label>
<div><p class="mb-3 text-sm font-bold">Branch Availability</p><div class="grid gap-3 sm:grid-cols-3">@foreach($branches as $branch) @php($available = $product->exists ? ($product->branches->firstWhere('id', $branch->id)?->pivot->available ?? false) : true)<label class="flex items-center gap-2 rounded-2xl border border-[#333333] bg-[#1A1A1A] p-3 text-sm"><input type="checkbox" name="branches[{{ $branch->id }}]" value="1" @checked(old('branches.'.$branch->id, $available))> {{ $branch->name }}</label>@endforeach</div></div>
<div class="flex gap-3"><a href="{{ route('admin.products.index') }}" class="rounded-full border border-[#FBAB18] px-5 py-3 font-semibold text-[#FBAB18]">Cancel</a><button class="rounded-full bg-[#FBAB18] px-5 py-3 font-semibold text-[#FFFFFF]">Save Food</button></div></form></div></div></section>
@endsection



