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
        .admin-account .logout-button {
            background-color: #FBAB18;
            border: 1px solid #FBAB18;
            color: #111;
            font-weight: 700;
        }
        .admin-account .logout-button:hover,
        .admin-account .logout-button:focus-visible {
            background-color: #FFC247;
            border-color: #FFC247;
            color: #111;
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
        .catering-admin-theme {
            background-color: #111111;
            color: #FFFFFF;
        }
        .catering-admin-theme .admin-navbar {
            background-color: #111111 !important;
            border-bottom-color: #333333 !important;
        }
        .catering-admin-theme .admin-navbar .nav-link,
        .catering-admin-theme .admin-navbar .brand-name,
        .catering-admin-theme .admin-account {
            color: #FFFFFF !important;
        }
        .catering-admin-theme .card-custom {
            border-color: #333333;
            background-color: #1A1A1A;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.2);
        }
        .catering-admin-theme .text-secondary,
        .catering-admin-theme .form-text {
            color: #B8B8B8 !important;
        }
        .catering-admin-theme .form-control,
        .catering-admin-theme .form-select {
            color: #FFFFFF;
            background-color: #201D19;
            border-color: #444444;
        }
        .catering-admin-theme .form-control::placeholder {
            color: #9B9B9B;
        }
        .catering-admin-theme .form-select option {
            color: #FFFFFF;
            background-color: #201D19;
        }
        .catering-admin-theme .table {
            --bs-table-color: #FFFFFF;
            --bs-table-bg: transparent;
            --bs-table-hover-color: #FFFFFF;
            --bs-table-hover-bg: #292621;
            --bs-table-border-color: #333333;
        }
        .catering-admin-theme .table thead {
            color: #B8B8B8;
        }
        .catering-admin-theme .nav-link:not(.active) {
            color: #FFFFFF !important;
            border-color: #444444 !important;
            background-color: #201D19;
        }
        .catering-admin-theme .btn-outline-dark {
            color: #FFFFFF;
            border-color: #666666;
        }
        .catering-admin-theme .btn-outline-dark:hover,
        .catering-admin-theme .btn-outline-dark:focus {
            color: #111111;
            background-color: #FBAB18;
            border-color: #FBAB18;
        }
        .catering-admin-theme .pagination {
            --bs-pagination-bg: #201D19;
            --bs-pagination-color: #FFFFFF;
            --bs-pagination-border-color: #444444;
            --bs-pagination-hover-bg: #FBAB18;
            --bs-pagination-hover-color: #111111;
            --bs-pagination-hover-border-color: #FBAB18;
            --bs-pagination-active-bg: #FBAB18;
            --bs-pagination-active-color: #111111;
            --bs-pagination-active-border-color: #FBAB18;
            --bs-pagination-disabled-bg: #1A1A1A;
            --bs-pagination-disabled-color: #777777;
            --bs-pagination-disabled-border-color: #333333;
        }
    </style>
</head>
<body class="<?php echo e(request()->routeIs('admin.catering-menu*', 'admin.marketing.catering-menu*') ? 'catering-admin-theme' : ''); ?>">
    <nav class="navbar navbar-expand-xxl admin-navbar bg-white border-bottom border-warning border-opacity-25 py-3 mb-4">
        <div class="container-fluid px-3 px-xxl-4">
            <a class="navbar-brand d-flex align-items-center gap-2 text-decoration-none" href="<?php echo e(route('home')); ?>" aria-label="MixEat home">
                <img src="<?php echo e(asset('images/mixeat.jpg')); ?>" alt="">
                <span class="brand-name">MIXEAT</span>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#admin-navigation" aria-controls="admin-navigation" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="admin-navigation">
                <ul class="navbar-nav me-auto ms-xxl-3">
                    <?php if(auth()->user()->role === 'marketing_admin' || auth()->user()->is_admin): ?>
                        <li class="nav-item"><a class="nav-link fw-semibold text-dark" href="<?php echo e(route('home')); ?>">Home</a></li>
                        <li class="nav-item"><a class="nav-link fw-semibold text-dark" href="<?php echo e(route('admin.catering-menu.index')); ?>">Manage Catering Menu</a></li>
                    <?php elseif(auth()->user()->isMarketing()): ?>
                        <li class="nav-item"><a class="nav-link fw-semibold text-dark" href="<?php echo e(route('home')); ?>">Home</a></li>
                    <?php elseif(auth()->user()->isSupervisor()): ?>
                        <li class="nav-item"><a class="nav-link fw-bold text-dark active" href="<?php echo e(route('admin.supervisors.index')); ?>">Branch Menu Availability</a></li>
                        <li class="nav-item"><a class="nav-link fw-bold text-dark" href="<?php echo e(route('admin.supervisors.foods.index')); ?>">Manage Foods</a></li>
                        <li class="nav-item"><a class="nav-link fw-bold text-dark" href="<?php echo e(route('admin.supervisors.catering-bookings')); ?>">Catering Management</a></li>
                        <li class="nav-item"><a class="nav-link fw-bold text-dark" href="<?php echo e(route('admin.supervisors.catering-reports')); ?>">Catering Reports</a></li>
                    <?php endif; ?>
                </ul>

                <div class="admin-account d-flex align-items-center mt-3 mt-xxl-0">
                    <span class="me-3 fw-medium">
                        <?php echo e(auth()->user()->name); ?> 
                        <span class="badge-mixeat-role ms-1"><?php echo e(auth()->user()->role); ?></span>
                    </span>
                    <form action="<?php echo e(route('logout')); ?>" method="POST" class="d-inline">
                        <?php echo csrf_field(); ?>
                        <button class="btn btn-sm rounded-pill px-3 logout-button" type="submit">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <main class="container mb-5">
        <?php if(session('admin_status') || session('success')): ?>
            <div class="alert alert-warning border-0 shadow-sm rounded-4 alert-dismissible fade show mb-4" role="alert">
                <?php echo e(session('admin_status') ?? session('success')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php echo $__env->yieldContent('content'); ?>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html><?php /**PATH C:\xampp\htdocs\mixeat\mixeat\resources\views/layouts/admin.blade.php ENDPATH**/ ?>