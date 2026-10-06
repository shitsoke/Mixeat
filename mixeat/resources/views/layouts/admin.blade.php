<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MixEat Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #FFFDF5;
            font-family: system-ui, -apple-system, sans-serif;
            color: #1A1A1A;
        }
        .admin-navbar .container-fluid {
            max-width: 1680px;
        }
        .admin-navbar .navbar-collapse {
            align-items: center;
            gap: 1rem;
        }
        .admin-navbar .navbar-nav {
            flex-wrap: wrap;
            column-gap: 0.2rem;
        }
        .admin-navbar .nav-link {
            color: #555;
            font-size: 0.9rem;
            font-weight: 650;
            white-space: nowrap;
        }
        .admin-navbar .nav-link:hover,
        .admin-navbar .nav-link:focus {
            color: #b78300;
        }
        .admin-navbar .navbar-brand img {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            object-fit: contain;
        }
        .admin-navbar .brand-name {
            color: #090909;
            font-size: 1.25rem;
            font-weight: 800;
            letter-spacing: 0.14em;
        }
        .admin-navbar .admin-account {
            white-space: nowrap;
        }
        @media (max-width: 1399.98px) {
            .admin-navbar .navbar-collapse {
                padding-top: 0.75rem;
            }
            .admin-navbar .navbar-nav {
                align-items: flex-start;
            }
        }
        .btn-mixeat-yellow {
            background-color: #FFC107;
            color: #000;
            font-weight: 700;
            border-radius: 50px;
            border: none;
            padding: 8px 24px;
        }
        .btn-mixeat-yellow:hover {
            background-color: #e0a800;
            color: #000;
        }
        .badge-mixeat-role {
            background-color: #FFC107;
            color: #000;
            font-weight: 700;
            border-radius: 50px;
            padding: 4px 10px;
            font-size: 0.75rem;
            text-transform: uppercase;
        }
        .card-custom {
            border: 1px solid #F3EBDD;
            border-radius: 20px;
            background-color: #FFFFFF;
            box-shadow: 0 4px 20px rgba(0,0,0,0.02);
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-xxl admin-navbar bg-white border-bottom border-warning border-opacity-25 py-3 mb-4">
        <div class="container-fluid px-3 px-xxl-4">
            <a class="navbar-brand d-flex align-items-center gap-2 text-decoration-none" href="{{ route('home') }}" aria-label="MixEat home">
                <img src="{{ asset('images/mixeat.jpg') }}" alt="">
                <span class="brand-name">MIXEAT</span>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#admin-navigation" aria-controls="admin-navigation" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="admin-navigation">
                <ul class="navbar-nav me-auto ms-xxl-3">
                    @if(auth()->user()->role === 'marketing_admin')
                        <li class="nav-item"><a class="nav-link fw-semibold text-dark" href="{{ route('home') }}">Home</a></li>
                        <li class="nav-item"><a class="nav-link fw-semibold text-dark" href="{{ route('admin.marketing.catering-bookings') }}">Catering Bookings</a></li>
                        <li class="nav-item"><a class="nav-link fw-semibold text-dark" href="{{ route('admin.marketing.catering-calendar') }}">Catering Event Calendar</a></li>
                        <li class="nav-item"><a class="nav-link fw-semibold text-dark" href="{{ route('admin.catering.content') }}">Catering Content</a></li>
                        <li class="nav-item"><a class="nav-link fw-semibold text-dark" href="{{ route('admin.marketing.catering-partners') }}">Catering Partners</a></li>
                    @elseif(auth()->user()->isMarketing())
                        <li class="nav-item"><a class="nav-link fw-semibold text-dark" href="{{ route('home') }}">Home</a></li>
                        <li class="nav-item"><a class="nav-link fw-semibold text-dark" href="{{ route('admin.catering.marketing') }}">Catering</a></li>
                        <li class="nav-item"><a class="nav-link fw-semibold text-dark" href="{{ route('admin.marketing.catering-bookings') }}">Catering Bookings</a></li>
                        <li class="nav-item"><a class="nav-link fw-semibold text-dark" href="{{ route('admin.marketing.catering-calendar') }}">Catering Event Calendar</a></li>
                        <li class="nav-item"><a class="nav-link fw-semibold text-dark" href="{{ route('admin.catering.content') }}">Catering Content</a></li>
                        <li class="nav-item"><a class="nav-link fw-semibold text-dark" href="{{ route('admin.marketing.catering-partners') }}">Catering Clients &amp; Partners</a></li>
                    @elseif(auth()->user()->isSupervisor())
                        <li class="nav-item"><a class="nav-link fw-bold text-dark active" href="{{ route('admin.supervisors.index') }}">Branch Menu Availability</a></li>
                    @endif
                </ul>

                <div class="admin-account d-flex align-items-center mt-3 mt-xxl-0">
                    <span class="me-3 fw-medium">
                        {{ auth()->user()->name }} 
                        <span class="badge-mixeat-role ms-1">{{ auth()->user()->role }}</span>
                    </span>
                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button class="btn btn-outline-dark btn-sm rounded-pill px-3" type="submit">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <main class="container mb-5">
        @if(session('admin_status') || session('success'))
            <div class="alert alert-warning border-0 shadow-sm rounded-4 alert-dismissible fade show mb-4" role="alert">
                {{ session('admin_status') ?? session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>