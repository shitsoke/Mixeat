@extends('layouts.admin')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3 mb-4">
    <div><p class="small fw-bold text-uppercase text-warning-emphasis mb-1">Marketing Admin</p><h1 class="h2 fw-bold mb-1">Manage Catering Menu</h1><p class="text-secondary mb-0">Manage current catering foods, prices, inclusions, and availability.</p></div>
    <a class="btn btn-mixeat-yellow" href="{{ $tab === 'foods' ? route('admin.marketing.catering-menu.create') : route('admin.marketing.catering-menu.catalog.create', $tab) }}">Add {{ $tab === 'foods' ? 'food' : str_replace('-', ' ', $tab) }}</a>
</div>
@if (session('success'))<div class="alert alert-warning border-0 shadow-sm rounded-4">{{ session('success') }}</div>@endif

<ul class="nav nav-pills mb-4 gap-2">
    @foreach (['foods' => 'Catering Foods', 'packages' => 'Catering Packages', 'packed-meals' => 'Packed Meals', 'party-trays' => 'Party Trays'] as $key => $label)
        <li class="nav-item"><a class="nav-link {{ $tab === $key ? 'active bg-dark' : 'border text-dark' }}" href="{{ route('admin.marketing.catering-menu.index', ['tab' => $key]) }}">{{ $label }}</a></li>
    @endforeach
</ul>

@if ($tab === 'foods')
    <section class="card-custom p-3 p-md-4">
        <form method="GET" class="row g-2 mb-4">
            <input type="hidden" name="tab" value="foods">
            <div class="col-md-5"><input name="search" value="{{ request('search') }}" class="form-control" placeholder="Search food name or description"></div>
            <div class="col-md-3"><select name="category" class="form-select"><option value="">All categories</option>@foreach ($categories as $key => $label)<option value="{{ $key }}" @selected(request('category') === $key)>{{ $label }}</option>@endforeach</select></div>
            <div class="col-md-2"><select name="status" class="form-select"><option value="">All status</option><option value="active" @selected(request('status') === 'active')>Active</option><option value="inactive" @selected(request('status') === 'inactive')>Inactive</option></select></div>
            <div class="col-md-2"><button class="btn btn-outline-dark w-100">Search</button></div>
        </form>
        <div class="table-responsive"><table class="table table-hover align-middle"><thead><tr><th>Food</th><th>Category</th><th>Status</th><th class="text-end">Actions</th></tr></thead><tbody>
        @forelse ($items as $item)
            <tr><td><div class="d-flex align-items-center gap-3">@if($item->imageUrl())<img src="{{ $item->imageUrl() }}" class="rounded-3" style="width:56px;height:56px;object-fit:cover">@endif<div><strong>{{ $item->name }}</strong><div class="small text-secondary">{{ $item->description ?: 'No description' }}</div></div></div></td><td>{{ $categories[$item->category] }}</td><td><span class="badge {{ $item->is_active && ! $item->trashed() ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $item->is_active && ! $item->trashed() ? 'Active' : 'Inactive' }}</span></td><td class="text-end"><a class="btn btn-sm btn-outline-dark" href="{{ route('admin.marketing.catering-menu.edit', $item) }}">Edit</a> <form class="d-inline" method="POST" action="{{ route('admin.marketing.catering-menu.toggle', $item) }}">@csrf @method('PATCH')<button class="btn btn-sm btn-outline-dark">{{ $item->is_active ? 'Deactivate' : 'Activate' }}</button></form> <form class="d-inline" method="POST" action="{{ route('admin.marketing.catering-menu.destroy', $item) }}" onsubmit="return confirm('Archive this catering food?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Delete</button></form></td></tr>
        @empty <tr><td colspan="4" class="text-center text-secondary py-5">No catering foods found.</td></tr>@endforelse
        </tbody></table></div>{{ $items->links('pagination::bootstrap-5') }}
    </section>
@else
    @php
        $catalogItems = ['packages' => $packages, 'packed-meals' => $packedMeals, 'party-trays' => $partyTrays][$tab];
    @endphp
    <section class="card-custom p-3 p-md-4">
        <div class="table-responsive"><table class="table table-hover align-middle"><thead><tr><th>Name</th><th>Description / details</th><th>Price</th><th>Status</th><th class="text-end">Actions</th></tr></thead><tbody>
        @forelse ($catalogItems as $item)
            <tr><td><strong>{{ $item->name }}</strong><div class="small text-secondary">{{ $item->code ?? '' }}</div></td><td class="small text-secondary">{{ $item->description }} @if($tab === 'party-trays')<br>{{ count($item->dishes ?? []) }} dishes · {{ implode(', ', array_values($item->sizes ?? [])) }}@endif</td><td class="text-nowrap">@if($tab === 'party-trays')₱{{ number_format((float)$item->full_price, 2) }} / ₱{{ number_format((float)$item->half_price, 2) }}@else₱{{ number_format((float)$item->price, 2) }}@endif</td><td><span class="badge {{ $item->is_active && ! $item->trashed() ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $item->is_active && ! $item->trashed() ? 'Active' : 'Inactive' }}</span></td><td class="text-end text-nowrap"><a class="btn btn-sm btn-outline-dark" href="{{ route('admin.marketing.catering-menu.catalog.edit', [$tab, $item->id]) }}">Edit</a> <form class="d-inline" method="POST" action="{{ route('admin.marketing.catering-menu.catalog.toggle', [$tab, $item->id]) }}">@csrf @method('PATCH')<button class="btn btn-sm btn-outline-dark">{{ $item->is_active ? 'Deactivate' : 'Activate' }}</button></form> <form class="d-inline" method="POST" action="{{ route('admin.marketing.catering-menu.catalog.destroy', [$tab, $item->id]) }}" onsubmit="return confirm('Archive this item? Existing bookings will keep their saved details.')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Delete</button></form></td></tr>
        @empty <tr><td colspan="5" class="text-center text-secondary py-5">No items found.</td></tr>@endforelse
        </tbody></table></div>
    </section>
@endif
@endsection
