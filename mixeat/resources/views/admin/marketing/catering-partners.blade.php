@extends('layouts.admin')

@section('content')
<style>
    body:has(.catering-partners-page) {
        background: #111111;
        color: #FFFFFF;
    }

    body:has(.catering-partners-page) .admin-navbar {
        background: #1A1A1A !important;
        border-color: #333333 !important;
    }

    body:has(.catering-partners-page) .admin-navbar .brand-name,
    body:has(.catering-partners-page) .admin-navbar .nav-link,
    body:has(.catering-partners-page) .admin-navbar .admin-account {
        color: #FFFFFF !important;
    }

    body:has(.catering-partners-page) .admin-navbar .nav-link:hover,
    body:has(.catering-partners-page) .admin-navbar .nav-link:focus {
        color: #FBAB18 !important;
    }

    body:has(.catering-partners-page) .admin-navbar .navbar-toggler {
        border-color: #555555;
    }

    body:has(.catering-partners-page) .admin-navbar .navbar-toggler-icon {
        filter: invert(1);
    }

    .catering-partners-page {
        color: #FFFFFF;
    }

    .catering-partners-page .text-secondary {
        color: #B8B8B8 !important;
    }

    .catering-partners-page .card-custom {
        border-color: #333333;
        background: #201D19;
        box-shadow: 0 12px 30px rgb(0 0 0 / 18%);
        color: #FFFFFF;
    }

    .catering-partners-page h1,
    .catering-partners-page h2 {
        color: #FFFFFF;
        font-family: 'Montserrat', sans-serif;
    }

    .catering-partners-page .form-control {
        border-color: #444444;
        background-color: #1A1A1A;
        color: #FFFFFF;
    }

    .catering-partners-page .form-control::placeholder {
        color: #888888;
    }

    .catering-partners-page .form-control:focus {
        border-color: #FBAB18;
        background-color: #1A1A1A;
        color: #FFFFFF;
        box-shadow: 0 0 0 0.2rem rgb(251 171 24 / 20%);
    }

    .catering-partners-page input[type="file"]::file-selector-button {
        border-color: #444444;
        background: #292929;
        color: #FFFFFF;
    }

    .catering-partners-page input[type="file"]:hover::file-selector-button {
        background: #333333;
    }

    .catering-partners-page .form-check-input {
        border-color: #777777;
        background-color: #1A1A1A;
    }

    .catering-partners-page .form-check-input:checked {
        border-color: #FBAB18;
        background-color: #FBAB18;
    }

    .catering-partners-page .btn-outline-dark {
        border-color: #555555;
        color: #FFFFFF;
    }

    .catering-partners-page .btn-outline-dark:hover,
    .catering-partners-page .btn-outline-dark:focus {
        border-color: #FBAB18;
        background: #FBAB18;
        color: #111111;
    }

    .catering-partners-page .btn-outline-danger {
        border-color: #dc3545;
        color: #ff7b86;
    }

    .catering-partners-page .btn-outline-danger:hover,
    .catering-partners-page .btn-outline-danger:focus {
        background: #dc3545;
        color: #FFFFFF;
    }

    .catering-partners-page .partner-logo-preview {
        border-color: #444444 !important;
        background: #FFFFFF;
    }

    @media (max-width: 767.98px) {
        .catering-partners-page .card-custom {
            border-radius: 16px;
        }

        .catering-partners-page > .d-flex:first-child .btn {
            width: 100%;
        }
    }
</style>
<div class="catering-partners-page">
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3 mb-4">
    <div>
        <p class="small fw-bold text-uppercase text-warning-emphasis mb-1">Marketing Admin</p>
        <h1 class="h2 fw-bold mb-1">Catering Clients &amp; Partners</h1>
        <p class="text-secondary mb-0">Manage partner logos displayed on the public Catering page. Lower order numbers appear first.</p>
    </div>
    <a class="btn btn-outline-dark rounded-pill" href="{{ route('catering') }}" target="_blank" rel="noopener noreferrer">View Catering Page</a>
</div>

<section class="card-custom p-4 mb-4">
    <h2 class="h4 fw-bold mb-3">Add a Partner</h2>
    <form action="{{ route('admin.marketing.catering-partners.store') }}" method="POST" enctype="multipart/form-data" class="row g-3 align-items-end">
        @csrf
        <div class="col-md-4">
            <label for="new-partner-name" class="form-label fw-semibold">Client / Partner Name</label>
            <input id="new-partner-name" class="form-control" name="name" maxlength="120" required>
        </div>
        <div class="col-md-4">
            <label for="new-partner-website" class="form-label fw-semibold">Website URL <span class="text-secondary fw-normal">(optional)</span></label>
            <input id="new-partner-website" class="form-control" type="url" name="website_url" maxlength="2048" placeholder="https://example.com">
        </div>
        <div class="col-md-4">
            <label for="new-partner-logo" class="form-label fw-semibold">Logo (PNG, JPG, JPEG, SVG, WebP)</label>
            <input id="new-partner-logo" class="form-control" type="file" name="logo" accept=".png,.jpg,.jpeg,.svg,.webp,image/png,image/jpeg,image/svg+xml,image/webp" required>
        </div>
        <div class="col-sm-4 col-md-2">
            <label for="new-partner-order" class="form-label fw-semibold">Display order</label>
            <input id="new-partner-order" class="form-control" type="number" name="sort_order" min="0" max="100000" value="{{ ($partners->max('sort_order') ?? -1) + 1 }}" required>
        </div>
        <div class="col-sm-4 col-md-2">
            <div class="form-check mb-2">
                <input id="new-partner-visible" class="form-check-input" type="checkbox" name="is_visible" value="1" checked>
                <label class="form-check-label" for="new-partner-visible">Visible</label>
            </div>
        </div>
        <div class="col-sm-4 col-md-3"><button type="submit" class="btn btn-mixeat-yellow">Upload Partner Logo</button></div>
    </form>
</section>

<div class="d-grid gap-3">
    @forelse ($partners as $partner)
        <section class="card-custom p-3 p-md-4">
            <form action="{{ route('admin.marketing.catering-partners.update', $partner) }}" method="POST" enctype="multipart/form-data" class="row g-3 align-items-end">
                @csrf
                @method('PUT')
                <div class="col-md-2">
                    <div class="partner-logo-preview d-flex min-vh-10 align-items-center justify-content-center rounded-3 border bg-white p-3">
                        @if ($partner->publicLogoUrl())
                            <img src="{{ $partner->publicLogoUrl() }}" alt="{{ $partner->name }} logo" class="img-fluid" style="max-height: 4rem">
                        @else
                            <span class="small text-secondary">No logo</span>
                        @endif
                    </div>
                </div>
                <div class="col-md-3">
                    <label for="partner-name-{{ $partner->id }}" class="form-label fw-semibold">Partner name</label>
                    <input id="partner-name-{{ $partner->id }}" class="form-control" name="name" value="{{ $partner->name }}" maxlength="120" required>
                </div>
                <div class="col-md-3">
                    <label for="partner-website-{{ $partner->id }}" class="form-label fw-semibold">Website URL</label>
                    <input id="partner-website-{{ $partner->id }}" class="form-control" type="url" name="website_url" value="{{ $partner->website_url }}" maxlength="2048">
                </div>
                <div class="col-md-4">
                    <label for="partner-logo-{{ $partner->id }}" class="form-label fw-semibold">Replace logo (PNG, JPG, JPEG, SVG, WebP)</label>
                    <input id="partner-logo-{{ $partner->id }}" class="form-control" type="file" name="logo" accept=".png,.jpg,.jpeg,.svg,.webp,image/png,image/jpeg,image/svg+xml,image/webp">
                </div>
                <div class="col-sm-4 col-md-2">
                    <label for="partner-order-{{ $partner->id }}" class="form-label fw-semibold">Display order</label>
                    <input id="partner-order-{{ $partner->id }}" class="form-control" type="number" name="sort_order" min="0" max="100000" value="{{ $partner->sort_order }}" required>
                </div>
                <div class="col-sm-4 col-md-3">
                    <div class="form-check mb-2">
                        <input id="partner-visible-{{ $partner->id }}" class="form-check-input" type="checkbox" name="is_visible" value="1" @checked($partner->is_visible)>
                        <label class="form-check-label" for="partner-visible-{{ $partner->id }}">Show on Catering page</label>
                    </div>
                </div>
                <div class="col-sm-4 col-md-3">
                    <button type="submit" class="btn btn-outline-dark">Save changes</button>
                </div>
            </form>
            <form action="{{ route('admin.marketing.catering-partners.delete', $partner) }}" method="POST" class="mt-2 text-md-end" onsubmit="return confirm('Delete this catering partner and its logo?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-outline-danger">Delete partner</button>
            </form>
        </section>
    @empty
        <div class="card-custom p-5 text-center text-secondary">No catering partners are configured yet.</div>
    @endforelse
</div>
</div>
@endsection
