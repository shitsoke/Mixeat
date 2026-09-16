@extends('layouts.app')

@section('title', 'Admin Foods | MixEat')

@section('content')
<section class="py-14">
    <div class="mixeat-shell px-4">
        <div class="mb-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
            <div><p class="text-sm font-bold uppercase tracking-[0.18em] text-[#FFC60A]">Admin</p><h1 class="mt-2 text-4xl font-black text-[#090909]">Manage Foods</h1></div>
            <a href="{{ route('admin.products.create') }}" class="rounded-full bg-[#FFC60A] px-5 py-3 font-semibold text-[#090909]">Add Food</a>
        </div>
        @if (session('admin_status'))<div class="mb-6 rounded-2xl bg-[#FFF4BF] px-4 py-3 font-semibold">{{ session('admin_status') }}</div>@endif
        <div class="overflow-x-auto rounded-[28px] border border-[#E8DFAF] bg-white shadow-sm">
            <table class="w-full min-w-[900px] text-left text-sm"><thead class="bg-[#FFF9E6] text-[#555555]"><tr><th class="p-4">Food</th><th class="p-4">Category</th><th class="p-4">Price</th>@foreach($branches as $branch)<th class="p-4">{{ $branch->name }}</th>@endforeach<th class="p-4">Actions</th></tr></thead>
            <tbody>@foreach($products as $product)<tr class="border-t border-[#E8DFAF] align-top"><td class="p-4"><div class="flex items-center gap-3"><img src="{{ $product->imageUrl() }}" alt="" class="h-14 w-14 rounded-xl object-cover"><div><strong>{{ $product->name }}</strong><p class="text-xs text-[#555555]">{{ $product->description }}</p></div></div></td><td class="p-4">{{ $product->category }}</td><td class="p-4">&#8369;{{ number_format($product->price, 2) }}</td>@foreach($branches as $branch) @php($status = $product->branches->firstWhere('id', $branch->id)?->pivot->available ?? false) <td class="p-4"><form action="{{ route('admin.products.availability', [$product, $branch]) }}" method="POST">@csrf @method('PATCH')<input type="hidden" name="available" value="{{ $status ? 0 : 1 }}"><button class="rounded-full px-3 py-1 text-xs font-bold {{ $status ? 'bg-[#EAF6EE] text-[#198754]' : 'bg-red-50 text-[#DC3545]' }}">{{ $status ? 'Available' : 'Sold Out' }}</button></form></td>@endforeach<td class="p-4"><div class="flex gap-2"><a href="{{ route('admin.products.edit', $product) }}" class="font-semibold text-[#E5A900]">Edit</a><form action="{{ route('admin.products.destroy', $product) }}" method="POST">@csrf @method('DELETE')<button class="font-semibold text-[#DC3545]">Delete</button></form></div></td></tr>@endforeach</tbody></table>
        </div>
    </div>
</section>
@endsection
