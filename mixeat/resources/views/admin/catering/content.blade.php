@extends('layouts.admin')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3 mb-4">
    <div>
        <p class="small fw-bold text-uppercase text-warning-emphasis mb-1">Marketing Admin</p>
        <h1 class="h2 fw-bold mb-1">Catering Packages &amp; Promotions</h1>
        <p class="text-secondary mb-0">Update the package cards, seasonal banner, and landing-page highlights.</p>
    </div>
    <a class="btn btn-outline-dark rounded-pill" href="{{ route('admin.catering.marketing') }}">Back to Catering Dashboard</a>
</div>

<section class="card-custom p-4">
    <form action="{{ route('admin.catering.content.update') }}" method="POST">
        @csrf
        @method('PUT')
        <h2 class="h5 fw-bold mb-3">Catering packages</h2>
        <div class="row g-3 mb-4">
            @foreach ($content['packages'] as $index => $package)
                <div class="col-lg-4">
                    <div class="rounded-4 border p-3 h-100">
                        <label class="form-label" for="package-name-{{ $index }}">Package name</label>
                        <input id="package-name-{{ $index }}" class="form-control mb-2" name="packages[{{ $index }}][name]" value="{{ old("packages.$index.name", $package['name']) }}" required maxlength="100">
                        <label class="form-label" for="package-price-{{ $index }}">Starting price (PHP)</label>
                        <input id="package-price-{{ $index }}" class="form-control mb-2" type="number" min="0" step="0.01" name="packages[{{ $index }}][price]" value="{{ old("packages.$index.price", $package['price'] ?? '') }}">
                        <label class="form-label" for="package-description-{{ $index }}">Description</label>
                        <textarea id="package-description-{{ $index }}" class="form-control" name="packages[{{ $index }}][description]" rows="3" maxlength="1000" required>{{ old("packages.$index.description", $package['description']) }}</textarea>
                    </div>
                </div>
            @endforeach
        </div>

        <h2 class="h5 fw-bold mb-3">Seasonal banner &amp; highlights</h2>
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label for="catering-banner-title" class="form-label">Banner title</label>
                <input id="catering-banner-title" class="form-control" name="banner_title" value="{{ old('banner_title', $content['banner_title']) }}" required maxlength="120">
            </div>
            <div class="col-md-6">
                <label for="catering-banner-copy" class="form-label">Banner copy</label>
                <input id="catering-banner-copy" class="form-control" name="banner_copy" value="{{ old('banner_copy', $content['banner_copy']) }}" required maxlength="255">
            </div>
            @foreach ($content['highlights'] as $index => $highlight)
                <div class="col-md-4">
                    <label for="catering-highlight-{{ $index }}" class="form-label">Highlight {{ $index + 1 }}</label>
                    <input id="catering-highlight-{{ $index }}" class="form-control" name="highlights[{{ $index }}]" value="{{ old("highlights.$index", $highlight) }}" required maxlength="120">
                </div>
            @endforeach
        </div>
        <button type="submit" class="btn btn-mixeat-yellow">Save Catering Content</button>
    </form>
</section>
@endsection
