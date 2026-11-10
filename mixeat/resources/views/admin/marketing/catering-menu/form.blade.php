@extends('layouts.admin')

@section('content')
<div class="mb-4">
    <p class="small fw-bold text-uppercase text-warning-emphasis mb-1">Marketing Admin</p>
    <h1 class="h2 fw-bold mb-1">{{ $item->exists ? 'Edit catering food' : 'Add catering food' }}</h1>
</div>
<section class="card-custom p-3 p-md-4">
    @if ($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
    <form action="{{ $item->exists ? route('admin.marketing.catering-menu.update', $item) : route('admin.marketing.catering-menu.store') }}" method="POST" enctype="multipart/form-data" class="row g-3">
        @csrf
        @if ($item->exists) @method('PUT') @endif
        <div class="col-md-6"><label class="form-label" for="name">Food name</label><input id="name" name="name" required maxlength="160" value="{{ old('name', $item->name) }}" class="form-control"></div>
        <div class="col-md-6"><label class="form-label" for="category">Category</label><select id="category" name="category" required class="form-select"><option value="">Select category</option>@foreach ($categories as $key => $label)<option value="{{ $key }}" @selected(old('category', $item->category) === $key)>{{ $label }}</option>@endforeach</select></div>
        <div class="col-12"><label class="form-label" for="description">Description</label><textarea id="description" name="description" rows="4" maxlength="1000" class="form-control">{{ old('description', $item->description) }}</textarea></div>
        <div class="col-md-8"><label class="form-label" for="image">Optional image</label><input id="image" type="file" name="image" accept="image/jpeg,image/png,image/webp" class="form-control"><div class="form-text">JPG, PNG, or WebP up to 2 MB.</div></div>
        <div class="col-md-4 d-flex align-items-center"><div class="form-check mt-3"><input id="is_active" type="checkbox" name="is_active" value="1" class="form-check-input" @checked(old('is_active', $item->is_active))><label class="form-check-label" for="is_active">Available to customers</label></div></div>
        @if ($item->imageUrl())<div class="col-12"><img src="{{ $item->imageUrl() }}" alt="" class="rounded-3" style="width: 120px; height: 120px; object-fit: cover"></div>@endif
        <div class="col-12 d-flex gap-2"><a class="btn btn-outline-secondary" href="{{ route('admin.marketing.catering-menu.index') }}">Cancel</a><button class="btn btn-mixeat-yellow">Save catering food</button></div>
    </form>
</section>
@endsection
