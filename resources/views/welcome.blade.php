@extends('layouts.public')

@section('content')

<!-- Hero Slider Section -->
<div id="heroCarousel" class="carousel slide carousel-fade hero-carousel" data-bs-ride="carousel" data-bs-pause="false">
    <div class="carousel-indicators">
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active" aria-current="true"></button>
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1"></button>
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2"></button>
    </div>

    <div class="carousel-inner">
        <!-- Slide 1 -->
        <div class="carousel-item active" style="background-image: url('https://images.unsplash.com/photo-1599839619722-39751411ea63?q=80&w=1920&auto=format&fit=crop');">
            <div class="carousel-overlay"></div>
            <div class="container h-100 position-relative">
                <div class="carousel-caption d-none d-md-block col-md-8 text-start">
                    <span class="badge bg-danger mb-2 px-3 py-2 rounded-pill"><i class="fas fa-broadcast-tower me-1"></i> Siaga Bencana</span>
                    <h1 class="display-4">Dashboard Penyelenggaraan SPM</h1>
                    <p>Sub Urusan Kebakaran Tingkat Daerah Provinsi dan Kabupaten/Kota di Seluruh Indonesia.</p>
                    <a href="#statistik" class="btn btn-warning btn-lg fw-bold rounded-pill mt-3 px-4 shadow">
                        <i class="fas fa-chart-bar me-2"></i> Lihat Data Nasional
                    </a>
                </div>
            </div>
        </div>

        <!-- Slide 2 -->
        <div class="carousel-item" style="background-image: url('https://images.unsplash.com/photo-1612450410478-f71f6526eb06?q=80&w=1920&auto=format&fit=crop');">
            <div class="carousel-overlay"></div>
            <div class="container h-100 position-relative">
                <div class="carousel-caption d-none d-md-block col-md-8 text-start">
                    <span class="badge bg-warning text-dark mb-2 px-3 py-2 rounded-pill"><i class="fas fa-info-circle me-1"></i> Informasi Nasional</span>
                    <h1 class="display-4">Pemenuhan Standar Pelayanan Minimal</h1>
                    <p>Meningkatkan kesiapsiagaan dan respon cepat pemadam kebakaran demi keselamatan masyarakat.</p>
                    <a href="#map-section" class="btn btn-light btn-lg fw-bold rounded-pill mt-3 px-4 shadow">
                        <i class="fas fa-map-marked-alt me-2"></i> Pantau Peta Sebaran
                    </a>
                </div>
            </div>
        </div>

        <!-- Slide 3 -->
        <div class="carousel-item" style="background-image: url('https://images.unsplash.com/photo-1543599538-a6c4f6cc5c05?q=80&w=1920&auto=format&fit=crop');">
            <div class="carousel-overlay"></div>
            <div class="container h-100 position-relative">
                <div class="carousel-caption d-none d-md-block col-md-8 text-start">
                    <span class="badge bg-primary mb-2 px-3 py-2 rounded-pill"><i class="fas fa-shield-alt me-1"></i> Evaluasi Kinerja</span>
                    <h1 class="display-4">Transparansi Data Capaian Daerah</h1>
                    <p>Monitoring berkala pemenuhan mutu layanan dasar sub urusan kebakaran.</p>
                </div>
            </div>
        </div>
    </div>

    <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
    </button>
</div>

<div class="container mt-5 pt-3" id="statistik">
    <div class="text-center mb-5">
        <h2 class="fw-bold text-dark mb-2">Ringkasan Capaian Nasional</h2>
        <p class="text-muted">Statistik dan peta menggunakan tahun data aktif yang sama.</p>

        <div class="d-inline-block mt-2">
            <label class="form-label text-muted small fw-bold text-uppercase">Tahun Data Aktif</label>
            <select class="form-select border-0 shadow-sm fw-bold bg-white" style="width: 250px; cursor: pointer;">
                <option>Belum ada data</option>
            </select>
        </div>
    </div>

    <!-- Statistik Utama -->
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="card modern-card p-4 text-center h-100">
                <div class="text-primary mb-3"><i class="fas fa-map-marker-alt fa-3x opacity-50"></i></div>
                <h6 class="fw-bold text-muted text-uppercase mb-1">Total Daerah</h6>
                <h1 class="display-5 fw-bold text-dark mb-0">0</h1>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card modern-card p-4 text-center h-100">
                <div class="text-info mb-3"><i class="fas fa-file-alt fa-3x opacity-50"></i></div>
                <h6 class="fw-bold text-muted text-uppercase mb-1">Data Tercatat</h6>
                <h1 class="display-5 fw-bold text-primary mb-0">0</h1>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card modern-card p-4 text-center h-100">
                <div class="text-success mb-3"><i class="fas fa-chart-line fa-3x opacity-50"></i></div>
                <h6 class="fw-bold text-muted text-uppercase mb-1">Rata-rata Nilai</h6>
                <h1 class="display-5 fw-bold text-success mb-0">0,00</h1>
            </div>
        </div>
    </div>

    <!-- Kategori Penilaian -->
    <div class="text-center mb-4">
        <h4 class="fw-bold text-dark">Distribusi Kategori Penilaian Daerah</h4>
        <hr class="w-25 mx-auto bg-primary opacity-25" style="height: 3px; border-radius: 5px;">
    </div>

    <div class="row g-4 mb-5">
        <div class="col-md-3">
            <div class="cat-box bg-sangat-baik shadow-sm modern-card">
                <i class="fas fa-star fa-2x mb-2 text-white opacity-75"></i>
                <h5 class="fw-bold mb-1">Sangat Baik</h5>
                <h2 class="fw-bold mb-0">0</h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="cat-box bg-baik shadow-sm modern-card">
                <i class="fas fa-check-circle fa-2x mb-2 text-white opacity-75"></i>
                <h5 class="fw-bold mb-1">Baik</h5>
                <h2 class="fw-bold mb-0">0</h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="cat-box bg-cukup shadow-sm modern-card">
                <i class="fas fa-minus-circle fa-2x mb-2 text-white opacity-75"></i>
                <h5 class="fw-bold mb-1">Cukup</h5>
                <h2 class="fw-bold mb-0">0</h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="cat-box bg-kurang shadow-sm modern-card">
                <i class="fas fa-exclamation-triangle fa-2x mb-2 text-white opacity-75"></i>
                <h5 class="fw-bold mb-1">Kurang</h5>
                <h2 class="fw-bold mb-0">0</h2>
            </div>
        </div>
    </div>

    <!-- Peta Sebaran -->
    <div class="row mb-5" id="map-section">
        <div class="col-12">
            <div class="card modern-card p-0">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                    <h5 class="fw-bold text-dark"><i class="fas fa-map-marked-alt text-danger me-2"></i> Peta Sebaran SPM</h5>
                    <p class="text-muted small">Visualisasi pencapaian standar pelayanan minimal berbasis geospasial.</p>
                </div>
                <div class="card-body p-4 pt-2">
                    <div class="map-container bg-light" style="height: 500px; width: 100%;">
                        <div id="map" style="height: 100%; width: 100%; display: flex; align-items: center; justify-content: center;">
                            <span class="text-muted"><i class="fas fa-spinner fa-spin me-2"></i> Memuat peta...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
    <!-- CSS Leaflet -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <style>
        #map { min-height: 500px; z-index: 1; border-radius: 8px;}
    </style>
@endpush

@push('scripts')
    <!-- JS Leaflet -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var mapElement = document.getElementById('map');
            if (mapElement) {
                mapElement.innerHTML = '';
                var map = L.map('map').setView([-0.789275, 113.921327], 5);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap contributors',
                    maxZoom: 18
                }).addTo(map);
            }
        });
    </script>
@endpush
