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
        :root {
            --sidebar-bg: #1e293b; /* Warna biru gelap slate */
            --sidebar-hover: #334155;
            --primary-color: #2a5298;
        }

        body {
            font-size: 0.9rem;
            background-color: #f1f5f9;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        /* Sidebar Styling */
        .sidebar {
            min-height: 100vh;
            background-color: var(--sidebar-bg);
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
            z-index: 100;
        }

        .sidebar-brand {
            padding: 20px 15px;
            background: rgba(0,0,0,0.15);
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }

        .sidebar .nav-link {
            font-weight: 500;
            color: #94a3b8;
            padding: 10px 15px;
            margin: 4px 15px;
            border-radius: 8px;
            transition: all 0.2s ease;
        }

        .sidebar .nav-link i {
            width: 24px; /* Menyamakan lebar icon */
            text-align: center;
        }

        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            color: #ffffff;
            background-color: var(--sidebar-hover);
            transform: translateX(5px); /* Animasi geser sedikit saat hover */
        }

        .sidebar .nav-link.active {
            background-color: var(--primary-color);
            box-shadow: 0 4px 6px rgba(42, 82, 152, 0.2);
        }

        .sidebar-heading {
            font-size: .75rem;
            text-transform: uppercase;
            color: #64748b;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        /* Main Content Styling & Animation */
        .main-content {
            padding: 30px;
            /* Animasi masuk konten utama */
            animation: fadeIn 0.5s ease-in-out;
        }

        .top-header {
            background-color: white;
            border-radius: 12px;
            padding: 15px 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.02);
            margin-bottom: 25px;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block sidebar collapse p-0">
            <div class="position-sticky">

                <!-- Brand Section -->
                <div class="sidebar-brand text-center mb-4">
                    <i class="fas fa-fire-extinguisher text-warning fs-3 mb-2"></i>
                    <h6 class="text-white fw-bold mb-0">SPM KEBAKARAN</h6>
                    <small class="text-white-50">Kemendagri</small>
                </div>

                <ul class="nav flex-column mb-4">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('admin/dashboard') ? 'active' : '' }}" href="{{ url('/admin/dashboard') }}">
                            <i class="fas fa-home me-2"></i> Dashboard
                        </a>
                    </li>
                </ul>

                <h6 class="sidebar-heading px-4 mt-2 mb-2">
                    Master Data
                </h6>
                <ul class="nav flex-column mb-4">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('admin/provinces*') ? 'active' : '' }}" href="{{ url('/admin/provinces') }}">
                            <i class="fas fa-map me-2"></i> Data Provinsi
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('admin/regencies*') ? 'active' : '' }}" href="{{ url('/admin/regencies') }}">
                            <i class="fas fa-city me-2"></i> Data Kab/Kota
                        </a>
                    </li>
                </ul>

                <h6 class="sidebar-heading px-4 mt-2 mb-2">
                    Manajemen Data
                </h6>
                <ul class="nav flex-column mb-4">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('admin/spm*') ? 'active' : '' }}" href="{{ url('/admin/spm') }}">
                            <i class="fas fa-database me-2"></i> Data SPM
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

                <!-- Logout Button at bottom -->
                <div class="px-3 mt-5 pb-4">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger w-100 rounded-pill">
                            <i class="fas fa-sign-out-alt me-1"></i> Logout
                        </button>
                    </form>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 main-content">

            <!-- Modern Top Header -->
            <div class="top-header d-flex justify-content-between align-items-center">
                <h1 class="h4 fw-bold mb-0 text-dark">@yield('page_title', 'Dashboard')</h1>
                <div class="user-profile d-flex align-items-center">
                    <div class="text-end me-3 d-none d-md-block">
                        <div class="fw-bold fs-6">{{ auth()->user()->name ?? 'Administrator' }}</div>
                        <div class="text-muted" style="font-size: 0.75rem;">Operator Sistem</div>
                    </div>
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 40px; height: 40px;">
                        <i class="fas fa-user"></i>
                    </div>
                </div>
            </div>

            <!-- Pesan Sukses/Error Global (Bisa dianimasikan juga) -->
            @if(session('success'))
                <div class="alert alert-success border-0 shadow-sm alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Konten Halaman Spesifik -->
            <div class="content-wrapper">
                @yield('content')
            </div>

        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
