@extends('layouts.admin')

@section('content')
@php
    $isParty = $type === 'party-trays';
    $included = $item->included_dishes ?: [];
    $dishLines = implode("\n", $item->dishes ?: []);
    $categoryLines = implode(', ', $included['categories'] ?? []);
    $sideLines = implode("\n", $included['sides'] ?? []);
    $fixedLines = implode("\n", $included['fixed'] ?? []);
@endphp
<div class="mb-4"><p class="small fw-bold text-uppercase text-warning-emphasis mb-1">Marketing Admin</p><h1 class="h2 fw-bold">{{ $item->exists ? 'Edit' : 'Add' }} {{ str_replace('-', ' ', $type) }}</h1></div>
<section class="card-custom p-3 p-md-4">
@if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<form method="POST" enctype="multipart/form-data" action="{{ $item->exists ? route('admin.marketing.catering-menu.catalog.update', [$type, $item->id]) : route('admin.marketing.catering-menu.catalog.store', $type) }}" class="row g-3">
@csrf @if($item->exists) @method('PUT') @endif
@if(!$isParty)<div class="col-md-4"><label class="form-label">Code</label><input name="code" required value="{{ old('code', $item->code) }}" class="form-control"></div>@endif
<div class="col-md-{{ $isParty ? '6' : '8' }}"><label class="form-label">Name</label><input name="name" required value="{{ old('name', $item->name) }}" class="form-control"></div>
<div class="col-12"><label class="form-label">Description</label><textarea name="description" rows="3" class="form-control">{{ old('description', $item->description) }}</textarea></div>
@if($isParty)
<div class="col-md-3"><label class="form-label">Full price (PHP)</label><input type="number" step="0.01" min="0" name="full_price" value="{{ old('full_price', $item->full_price) }}" class="form-control" required></div>
<div class="col-md-3"><label class="form-label">Half price (PHP)</label><input type="number" step="0.01" min="0" name="half_price" value="{{ old('half_price', $item->half_price) }}" class="form-control" required></div>
<div class="col-md-3"><label class="form-label">Full tray label</label><input name="full_size" value="{{ old('full_size', $item->sizes['full'] ?? 'Full') }}" class="form-control" required></div>
<div class="col-md-3"><label class="form-label">Half tray label</label><input name="half_size" value="{{ old('half_size', $item->sizes['half'] ?? 'Half') }}" class="form-control" required></div>
<div class="col-12"><label class="form-label">Available dishes</label><textarea name="dish_lines" rows="6" required class="form-control" placeholder="Enter one dish per line">{{ old('dish_lines', $dishLines) }}</textarea><div class="form-text">Enter one dish name per line.</div></div>
@else
<div class="col-md-4"><label class="form-label">Price per person (PHP)</label><input type="number" step="0.01" min="0" name="price" value="{{ old('price', $item->price) }}" class="form-control" required></div>
@if($type === 'packages')
<div class="col-md-2"><label class="form-label">Min guests</label><input type="number" min="1" name="min_guests" value="{{ old('min_guests', $item->min_guests) }}" class="form-control"></div><div class="col-md-2"><label class="form-label">Max guests</label><input type="number" min="1" name="max_guests" value="{{ old('max_guests', $item->max_guests) }}" class="form-control"></div>
<div class="col-md-3"><label class="form-label">Meat dishes included</label><input type="number" min="0" name="meat_count" value="{{ old('meat_count', $included['meat_count'] ?? 0) }}" class="form-control"></div>
<div class="col-md-5"><label class="form-label">Other categories</label><input name="included_categories" value="{{ old('included_categories', $categoryLines) }}" class="form-control" placeholder="Vegetables, Soup, Rice"></div>
@else
<div class="col-12"><label class="form-label">Side options</label><textarea name="side_lines" rows="3" class="form-control" placeholder="Enter one side per line">{{ old('side_lines', $sideLines) }}</textarea></div>
<div class="col-12"><label class="form-label">Included items</label><textarea name="fixed_lines" rows="3" class="form-control" placeholder="Enter one included item per line">{{ old('fixed_lines', $fixedLines) }}</textarea></div>
@endif
@endif
<div class="col-md-8"><label class="form-label">Optional image</label><input type="file" name="image" accept="image/jpeg,image/png,image/webp" class="form-control"><div class="form-text">JPG, PNG, or WebP up to 2 MB.</div></div>
<div class="col-md-4 d-flex align-items-center"><label class="form-check mt-3"><input type="checkbox" name="is_active" value="1" class="form-check-input" @checked(old('is_active', $item->is_active))> Available to customers</label></div>
<div class="col-12 d-flex gap-2"><a class="btn btn-outline-secondary" href="{{ route('admin.marketing.catering-menu.index', ['tab' => $type]) }}">Cancel</a><button class="btn btn-mixeat-yellow">Save</button></div>
</form></section>
@endsection
