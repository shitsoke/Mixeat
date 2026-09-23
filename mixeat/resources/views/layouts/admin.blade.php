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
        .mixeat-brand {
            background-color: #FFC107;
            color: #000;
            font-weight: 800;
            padding: 4px 12px;
            border-radius: 8px;
            text-decoration: none;
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
    <nav class="navbar navbar-expand-lg bg-white border-bottom border-warning border-opacity-25 py-3 mb-4">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="#">
                <span class="mixeat-brand">MIX EAT</span>
            </a>
            
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-3">
                    @if(auth()->user()->isMarketing())
                        <li class="nav-item"><a class="nav-link fw-semibold text-dark" href="{{ route('admin.branches.index') }}">Branches</a></li>
                        <li class="nav-item"><a class="nav-link fw-semibold text-dark" href="{{ route('admin.products.index') }}">Master Menu</a></li>
                        <li class="nav-item"><a class="nav-link fw-semibold text-dark" href="{{ route('admin.promotion') }}">Promotions</a></li>
                        <li class="nav-item"><a class="nav-link fw-semibold text-dark" href="{{ route('admin.cms.about') }}">About Page</a></li>
                        <li class="nav-item"><a class="nav-link fw-semibold text-dark" href="{{ route('admin.cms.contact') }}">Contact Page</a></li>
                    @elseif(auth()->user()->isSupervisor())
                        <li class="nav-item"><a class="nav-link fw-bold text-dark active" href="{{ route('admin.supervisors.index') }}">Branch Menu Availability</a></li>
                    @endif
                </ul>

                <div class="d-flex align-items-center">
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