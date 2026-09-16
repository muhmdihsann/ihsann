<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel - SPM Kebakaran')</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-size: .875rem; background-color: #f8f9fa;}
        .sidebar { min-height: 100vh; background-color: #343a40; box-shadow: inset -1px 0 0 rgba(0, 0, 0, .1); }
        .sidebar .nav-link { font-weight: 500; color: #ced4da; padding: 0.75rem 1rem;}
        .sidebar .nav-link:hover, .sidebar .nav-link.active { color: #fff; background-color: rgba(255,255,255,.1); }
        .sidebar-heading { font-size: .75rem; text-transform: uppercase; color: #adb5bd; }
        .main-content { padding: 20px; }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block sidebar collapse">
            <div class="position-sticky pt-3">
                <div class="text-center mb-4 mt-2">
                    <h6 class="text-white fw-bold">SPM KEBAKARAN</h6>
                    <small class="text-muted">Kemendagri</small>
                </div>

                <ul class="nav flex-column mb-3">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('admin/dashboard') ? 'active' : '' }}" href="{{ url('/admin/dashboard') }}">
                            <i class="fas fa-home me-2"></i> Dashboard
                        </a>
                    </li>
                </ul>

                <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1">
                    <span>Master Data</span>
                </h6>
                <ul class="nav flex-column mb-3">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('admin/provinces*') ? 'active' : '' }}" href="{{ url('/admin/provinces') }}">
                            <i class="fas fa-map me-2"></i> Data Provinsi
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('admin/regencies*') ? 'active' : '' }}" href="{{ url('/admin/regencies') }}">
                            <i class="fas fa-city me-2"></i> Data Kabupaten/Kota
                        </a>
                    </li>
                </ul>

                <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1">
                    <span>Manajemen Data</span>
                </h6>
                <ul class="nav flex-column mb-3">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('admin/spm*') ? 'active' : '' }}" href="{{ url('/admin/spm') }}">
                            <i class="fas fa-database me-2"></i> Data SPM Sub Urusan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('admin/import*') ? 'active' : '' }}" href="{{ url('/admin/import') }}">
                            <i class="fas fa-file-excel me-2"></i> Import Excel
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('admin/export*') ? 'active' : '' }}" href="{{ url('/admin/export') }}">
                            <i class="fas fa-print me-2"></i> Cetak Laporan
                        </a>
                    </li>
                </ul>

                <hr class="text-secondary">
                <ul class="nav flex-column mb-2">
                    <li class="nav-item px-3">
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger w-100 btn-sm">
                                <i class="fas fa-sign-out-alt me-1"></i> Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">@yield('page_title', 'Dashboard')</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <span class="text-muted"><i class="fas fa-user-circle me-1"></i> {{ auth()->user()->name }}</span>
                </div>
            </div>

            <!-- Pesan Sukses/Error Global -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Konten Halaman Spesifik -->
            @yield('content')

        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
