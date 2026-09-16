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
        body { background-color: #f8f9fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .hero-section { background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); color: white; padding: 80px 0; border-bottom: 5px solid #ffc107;}
        .navbar-brand { font-weight: bold; letter-spacing: 1px; }
    </style>
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
</body>
</html>
