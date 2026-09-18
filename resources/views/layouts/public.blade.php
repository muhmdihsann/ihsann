<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Informasi SPM Sub Urusan Kebakaran</title>
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f4f7f6; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .navbar-brand { font-weight: 800; letter-spacing: 1px; }

        /* Carousel & Slider Styling - Disesuaikan Full Layar & Proporsional */
        .hero-carousel {
            height: 85vh;
            min-height: 550px;
            position: relative;
        }
        .hero-carousel .carousel-inner,
        .hero-carousel .carousel-item {
            height: 100%;
        }
        .hero-carousel .carousel-item {
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }
        .carousel-overlay {
            position: absolute; top: 0; bottom: 0; left: 0; right: 0;
            background: linear-gradient(135deg, rgba(15, 32, 39, 0.85), rgba(32, 58, 67, 0.75), rgba(44, 83, 100, 0.85));
            z-index: 1;
        }

        /* Mengatur posisi teks agar presisi di tengah area transparan */
        .carousel-caption {
            z-index: 2;
            top: 50%;
            transform: translateY(-50%);
            bottom: auto;
            text-align: left;
            left: 8%;
            right: 8%;
        }

        /* Animasi teks saat slide berganti */
        .carousel-caption h1 { animation: fadeInUp 0.8s ease-out; font-weight: 800; text-shadow: 2px 2px 6px rgba(0,0,0,0.6); }
        .carousel-caption p { animation: fadeInUp 1s ease-out; font-size: 1.25rem; text-shadow: 1px 1px 4px rgba(0,0,0,0.5); }
        .carousel-caption .btn { animation: fadeInUp 1.2s ease-out; }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Modern Card Styling */
        .modern-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
            overflow: hidden;
            background: #ffffff;
        }
        .modern-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.08);
        }

        /* Box Kategori SPM */
        .cat-box { color: white; border-radius: 12px; padding: 25px 20px; text-align: center; position: relative; overflow: hidden; }
        .cat-box::after {
            content: ''; position: absolute; top: -50%; right: -50%; width: 100%; height: 100%;
            background: radial-gradient(circle, rgba(255,255,255,0.2) 0%, transparent 60%);
            transform: rotate(45deg); pointer-events: none;
        }
        .bg-sangat-baik { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); }
        .bg-baik { background: linear-gradient(135deg, #2f80ed 0%, #56ccf2 100%); }
        .bg-cukup { background: linear-gradient(135deg, #f2994a 0%, #f2c94c 100%); }
        .bg-kurang { background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%); }

        /* Map Container */
        .map-container { border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.08); border: 2px solid #fff; }
    </style>

    <!-- TAMBAHAN: Untuk menerima CSS tambahan dari halaman anak (seperti Leaflet) -->
    @stack('styles')
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">
                <i class="fas fa-fire-extinguisher text-danger me-2"></i> SPM KEBAKARAN
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link active" href="{{ url('/') }}">Beranda</a></li>
                    <li class="nav-item ms-lg-3"><a class="btn btn-outline-light btn-sm mt-1" href="{{ url('/login') }}"><i class="fas fa-sign-in-alt me-1"></i> Login Operator</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Konten Utama -->
    @yield('content')

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-4 mt-5">
        <div class="container">
            <small>&copy; {{ date('Y') }} Sistem Informasi Penyelenggaraan SPM Sub Urusan Kebakaran.<br>Kementerian Dalam Negeri Republik Indonesia.</small>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- TAMBAHAN: Untuk menerima Script JS tambahan dari halaman anak (seperti Leaflet JS) -->
    @stack('scripts')
</body>
</html>
