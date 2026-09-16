@extends('layouts.public')

@section('content')
<!-- Hero Section -->
<div class="hero-section text-center shadow-sm">
    <div class="container">
        <h1 class="display-5 fw-bold mb-3">Dashboard Penyelenggaraan SPM</h1>
        <p class="lead mb-4">Sub Urusan Kebakaran Tingkat Daerah Provinsi dan Kabupaten/Kota</p>
        <a href="#statistik" class="btn btn-warning btn-lg px-4 rounded-pill fw-bold text-dark shadow">Lihat Data Nasional</a>
    </div>
</div>

<div class="container mt-5" id="statistik">
    <div class="text-center mb-5">
        <h3 class="fw-bold text-secondary">Ringkasan Capaian Nasional</h3>
        <p class="text-muted">Data terbaru hasil konsolidasi penyelenggaraan SPM.</p>
    </div>

    <!-- Baris Statistik -->
    <div class="row text-center mb-5 justify-content-center">
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm border-0 bg-white h-100 py-4 rounded-4">
                <h6 class="text-muted text-uppercase fw-bold">Total Daerah</h6>
                <h1 class="fw-bold text-dark display-5">{{ $totalRegencies + $totalProvinces }}</h1>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm border-0 bg-white h-100 py-4 rounded-4">
                <h6 class="text-muted text-uppercase fw-bold">Data Tercatat</h6>
                <h1 class="fw-bold text-primary display-5">{{ $totalSpmData }}</h1>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm border-0 bg-white h-100 py-4 rounded-4">
                <h6 class="text-muted text-uppercase fw-bold">Rata-rata Nilai</h6>
                <h1 class="fw-bold text-success display-5">{{ number_format($avgNilaiAkhir, 2, ',', '.') }}</h1>
            </div>
        </div>
    </div>

    <div class="text-center mb-4">
        <h4 class="fw-bold text-secondary">Distribusi Kategori Penilaian Daerah</h4>
    </div>

    <!-- Baris Kategori -->
    <div class="row text-center">
        <div class="col-md-3 mb-3">
            <div class="p-4 bg-success text-white rounded-4 shadow-sm h-100">
                <i class="fas fa-star fa-2x mb-3 text-white-50"></i>
                <h5 class="fw-bold mb-1">Sangat Baik</h5>
                <h2 class="mb-0">{{ $kategoriSangatBaik }}</h2>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="p-4 bg-primary text-white rounded-4 shadow-sm h-100">
                <i class="fas fa-check-circle fa-2x mb-3 text-white-50"></i>
                <h5 class="fw-bold mb-1">Baik</h5>
                <h2 class="mb-0">{{ $kategoriBaik }}</h2>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="p-4 bg-warning text-dark rounded-4 shadow-sm h-100">
                <i class="fas fa-minus-circle fa-2x mb-3 text-black-50"></i>
                <h5 class="fw-bold mb-1">Cukup</h5>
                <h2 class="mb-0">{{ $kategoriCukup }}</h2>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="p-4 bg-danger text-white rounded-4 shadow-sm h-100">
                <i class="fas fa-exclamation-triangle fa-2x mb-3 text-white-50"></i>
                <h5 class="fw-bold mb-1">Kurang</h5>
                <h2 class="mb-0">{{ $kategoriKurang }}</h2>
            </div>
        </div>
    </div>
</div>
@endsection
