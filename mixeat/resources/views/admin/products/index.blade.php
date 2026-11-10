@extends('layouts.app')

@section('title', ($branchScoped ?? false) ? 'Commissary Foods | MixEat' : 'Admin Foods | MixEat')

@section('content')
<section class="py-14">
    <div class="mixeat-shell px-4">
        <div class="mb-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
            <div>
                <p class="text-sm font-bold uppercase tracking-[0.18em] text-[#FBAB18]">
                    {{ ($branchScoped ?? false) ? 'MixEat Commissary' : 'Admin' }}
                </p>
                <h1 class="mt-2 text-4xl font-black text-[#FFFFFF]">Manage Foods</h1>
            </div>
            <a href="{{ route(($manageRoute ?? 'admin.products').'.create') }}" class="rounded-full bg-[#FBAB18] px-5 py-3 font-semibold text-[#111111]">
                Add Food
            </a>
        </div>

        @if (session('admin_status'))
            <div class="mb-6 rounded-2xl bg-[#201D19] px-4 py-3 font-semibold">{{ session('admin_status') }}</div>
        @endif

        @php
            $commissaryBranch = ($branchScoped ?? false)
                ? $branches->first()
                : $branches->first(fn ($branch) => str_contains(strtolower($branch->name), 'mixeat commissary'));
        @endphp

        <div class="overflow-x-auto rounded-[28px] border border-[#333333] bg-[#201D19] shadow-sm">
            <table class="w-full min-w-[860px] table-fixed text-left text-sm">
                <colgroup>
                    <col class="w-[36%]">
                    <col class="w-[16%]">
                    <col class="w-[14%]">
                    <col class="w-[18%]">
                    <col class="w-[16%]">
                </colgroup>
                <thead class="bg-[#1A1A1A] text-[#B8B8B8]">
                    <tr>
                        <th scope="col" class="p-4">Food</th>
                        <th scope="col" class="p-4">Category</th>
                        <th scope="col" class="p-4">Price</th>
                        <th scope="col" class="p-4">MixEat Commissary</th>
                        <th scope="col" class="p-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse ($products as $product)
                    @php
                        $commissaryPivot = $commissaryBranch
                            ? $product->branches->firstWhere('id', $commissaryBranch->id)?->pivot
                            : null;
                        $status = $commissaryPivot?->available ?? false;
                        $price = $commissaryPivot?->price ?? $product->price;
                        $availabilityParameters = ($manageRoute ?? 'admin.products') === 'admin.products' && $commissaryBranch
                            ? [$product, $commissaryBranch]
                            : $product;
                    @endphp
                    <tr class="border-t border-[#333333] align-top">
                        <td class="p-4">
                            <div class="flex min-w-0 items-start gap-3">
                                <img src="{{ $product->imageUrl() }}" alt="" class="h-14 w-14 shrink-0 rounded-xl object-cover">
                                <div class="min-w-0">
                                    <strong class="block break-words text-[#FFFFFF]">{{ $product->name }}</strong>
                                    <p class="mt-1 break-words text-xs leading-5 text-[#B8B8B8]">{{ $product->description }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="break-words p-4 text-[#B8B8B8]">{{ $product->category }}</td>
                        <td class="whitespace-nowrap p-4 font-semibold text-[#FFFFFF]">&#8369;{{ number_format((float) $price, 2) }}</td>
                        <td class="p-4">
                            @if ($commissaryBranch)
                                <form action="{{ route(($manageRoute ?? 'admin.products').'.availability', $availabilityParameters) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="available" value="{{ $status ? 0 : 1 }}">
                                    <button type="submit" class="whitespace-nowrap rounded-full px-3 py-1 text-xs font-bold {{ $status ? 'bg-[#EAF6EE] text-[#198754]' : 'bg-red-50 text-[#DC3545]' }}">
                                        {{ $status ? 'Available' : 'Unavailable' }}
                                    </button>
                                </form>
                            @else
                                <span class="text-xs text-[#B8B8B8]">Not assigned</span>
                            @endif
                        </td>
                        <td class="p-4">
                            <div class="flex flex-wrap gap-x-3 gap-y-2">
                                <a href="{{ route(($manageRoute ?? 'admin.products').'.edit', $product) }}" class="font-semibold text-[#FBAB18]">Edit</a>
                                <form action="{{ route(($manageRoute ?? 'admin.products').'.destroy', $product) }}" method="POST" onsubmit="return confirm('Delete this food item?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="font-semibold text-[#DC3545]">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="p-8 text-center text-[#B8B8B8]">No food items found.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</section>
@endsection
