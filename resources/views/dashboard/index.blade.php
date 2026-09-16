@extends('layouts.admin')

@section('title', 'Dashboard - SPM Kebakaran')
@section('page_title', 'Dashboard Operator')

@section('content')
    <!-- Baris Statistik Utama -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm border-0 bg-primary text-white h-100">
                <div class="card-body">
                    <h6 class="card-title text-white-50">Total Data SPM</h6>
                    <h2 class="fw-bold">{{ number_format($totalSpmData, 0, ',', '.') }}</h2>
                    <small>Baris Data Tercatat</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 bg-success text-white h-100">
                <div class="card-body">
                    <h6 class="card-title text-white-50">Rata-rata Nasional</h6>
                    <h2 class="fw-bold">{{ number_format($avgNilaiAkhir, 2, ',', '.') }}</h2>
                    <small>Indeks Penyelenggaraan SPM</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 bg-info text-white h-100">
                <div class="card-body">
                    <h6 class="card-title text-white-50">Total Provinsi</h6>
                    <h2 class="fw-bold">{{ $totalProvinces }}</h2>
                    <small>Wilayah Tercatat</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 bg-secondary text-white h-100">
                <div class="card-body">
                    <h6 class="card-title text-white-50">Total Kabupaten/Kota</h6>
                    <h2 class="fw-bold">{{ $totalRegencies }}</h2>
                    <small>Wilayah Tercatat</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Baris Kategori Penilaian -->
    <h5 class="mb-3 text-secondary border-bottom pb-2">Distribusi Kategori Penilaian</h5>
    <div class="row">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm mb-3 border-start border-success border-4">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Sangat Baik</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $kategoriSangatBaik }} Daerah</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm mb-3 border-start border-primary border-4">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Baik</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $kategoriBaik }} Daerah</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm mb-3 border-start border-warning border-4">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Cukup</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $kategoriCukup }} Daerah</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm mb-3 border-start border-danger border-4">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Kurang</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $kategoriKurang }} Daerah</div>
                </div>
            </div>
        </div>
    </div>

    <!-- AREA GRAFIK CHART.JS -->
    <div class="row mt-4">
        <!-- Grafik 1: Distribusi Kategori -->
        <div class="col-md-5 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-chart-pie me-2"></i>Distribusi Kategori</h6>
                </div>
                <div class="card-body">
                    <canvas id="kategoriChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Grafik 2: Rata-rata Nilai per Provinsi -->
        <div class="col-md-7 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-chart-bar me-2"></i>Rata-rata Nilai Akhir per Provinsi</h6>
                </div>
                <div class="card-body">
                    <canvas id="provinsiNilaiChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Panggil Library Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Script Inisialisasi Grafik -->
    <script>
        // Data dari Controller
        const labelsKategori = ['Sangat Baik', 'Baik', 'Cukup', 'Kurang'];
        const dataKategori = [{{ $kategoriSangatBaik }}, {{ $kategoriBaik }}, {{ $kategoriCukup }}, {{ $kategoriKurang }}];

        // Render Pie Chart Kategori
        const ctxKategori = document.getElementById('kategoriChart').getContext('2d');
        new Chart(ctxKategori, {
            type: 'doughnut',
            data: {
                labels: labelsKategori,
                datasets: [{
                    data: dataKategori,
                    backgroundColor: ['#198754', '#0d6efd', '#ffc107', '#dc3545'], // Warna Bootstrap
                    borderWidth: 1
                }]
            },
            options: { responsive: true, maintainAspectRatio: false }
        });

        // Data Provinsi dari Controller (di-convert ke format JSON)
        const labelsProvinsi = {!! json_encode($labelProvinsi) !!};
        const dataNilaiProvinsi = {!! json_encode($dataNilai) !!};

        // Render Bar Chart Provinsi
        const ctxProvinsi = document.getElementById('provinsiNilaiChart').getContext('2d');
        new Chart(ctxProvinsi, {
            type: 'bar',
            data: {
                labels: labelsProvinsi,
                datasets: [{
                    label: 'Rata-rata Nilai',
                    data: dataNilaiProvinsi,
                    backgroundColor: '#0d6efd',
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true, max: 100 }
                }
            }
        });
    </script>
@endsection
