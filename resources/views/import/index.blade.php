@extends('layouts.admin')

@section('title', 'Manajemen Data & Rekonsiliasi - SPM Kebakaran')
@section('page_title', 'Pusat Import & Rekonsiliasi Data SPM')

@section('content')
<style>
    .badge-diff { background-color: #fdeee3; color: #b5502e; font-weight: 600; font-size: 11px; padding: 3px 8px; border-radius: 6px; }
    .badge-same { background-color: #eef1f4; color: #5b6773; font-weight: 600; font-size: 11px; padding: 3px 8px; border-radius: 6px; }
</style>

<div class="row">
    <!-- BAGIAN 1: IMPORT DATA UTAMA (PUBLIK & DASHBOARD) -->
    <div class="col-lg-12 mb-4">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-header bg-primary text-white py-3 px-4 rounded-top-4">
                <h5 class="mb-0 fw-bold"><i class="fas fa-cloud-upload-alt me-2"></i> 1. Import Data Utama (Untuk Beranda & Peta Publik)</h5>
            </div>
            <div class="card-body p-4">
                <div class="alert alert-primary border-0 shadow-sm mb-4 d-flex align-items-center">
                    <i class="fas fa-info-circle fa-2x me-3 text-primary"></i>
                    <div>
                        <strong>Informasi Penting:</strong> Mengunggah file ke menu ini akan memperbarui data utama yang tampil secara langsung di <strong>Dashboard</strong> dan <strong>Beranda Publik / Peta</strong>.
                    </div>
                </div>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form action="{{ url('/admin/import/store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-dark">Tahun Data SPM</label>
                            <select name="year" class="form-select" required>
                                <option value="">-- Pilih Tahun Data --</option>
                                <option value="2024">2024</option>
                                <option value="2025">2025</option>
                                <option value="2026">2026</option>
                            </select>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-bold text-dark">File Data Excel Utama (.xlsx / .xls)</label>
                            <input class="form-control" type="file" name="file" accept=".xlsx, .xls" required>
                        </div>
                    </div>
                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary px-4 py-2 fw-bold shadow-sm">
                            <i class="fas fa-upload me-2"></i> Unggah & Perbarui Dashboard Publik
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- BAGIAN 2: REKONSILIASI BUKU LAPORAN (KHUSUS ADMIN) -->
    <div class="col-lg-12">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-header bg-dark text-white py-3 px-4 rounded-top-4">
                <h5 class="mb-0 fw-bold"><i class="fas fa-balance-scale me-2"></i> 2. Rekonsiliasi Buku Laporan (Internal Admin - Tidak Ubah Publik)</h5>
            </div>
            <div class="card-body p-4">
                <div class="alert alert-secondary border-0 shadow-sm mb-4">
                    <i class="fas fa-info-circle me-2"></i> <strong>Alat Bantu Penyusunan Buku:</strong> Menu ini khusus untuk membandingkan Data Tahun Lalu vs Tahun Ini. Hasil perbandingan ini <strong>tidak mengubah</strong> data utama di beranda publik.
                </div>

                @if(session('error_rekon'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error_rekon') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form action="{{ url('/admin/reconciliation/preview') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Tahun Buku Laporan</label>
                            <select name="year" class="form-select" required>
                                <option value="">-- Pilih Tahun --</option>
                                <option value="2024" {{ (isset($rekonYear) && $rekonYear == 2024) ? 'selected' : '' }}>2024</option>
                                <option value="2025" {{ (isset($rekonYear) && $rekonYear == 2025) ? 'selected' : '' }}>2025</option>
                                <option value="2026" {{ (isset($rekonYear) && $rekonYear == 2026) ? 'selected' : '' }}>2026</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">File Data Tahun Lalu (.xlsx)</label>
                            <input class="form-control" type="file" name="file_lama" accept=".xlsx, .xls" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">File Data Tahun Ini (.xlsx)</label>
                            <input class="form-control" type="file" name="file_baru" accept=".xlsx, .xls" required>
                        </div>
                    </div>
                    <div class="mt-4">
                        <button type="submit" class="btn btn-dark px-4 py-2 fw-bold"><i class="fas fa-search me-2"></i> Analisis & Bandingkan Data</button>
                    </div>
                </form>

                @isset($regions)
                    <div class="mt-5 border-top pt-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold text-dark mb-0">Hasil Perbandingan untuk Buku Laporan</h5>
                            <span class="badge bg-success">Total Wilayah: {{ count($regions) }}</span>
                        </div>

                        <div class="table-responsive" style="max-height: 450px; overflow-y: auto;">
                            <table class="table table-bordered table-hover align-middle small">
                                <thead class="table-dark sticky-top">
                                    <tr>
                                        <th>Provinsi</th>
                                        <th>Kabupaten / Kota</th>
                                        <th>Status Perbandingan</th>
                                        <th>Kolom Berbeda</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($regions as $r)
                                    <tr>
                                        <td class="fw-semibold">{{ $r['provinsi'] }}</td>
                                        <td>{{ $r['label'] }}</td>
                                        <td>
                                            @if($r['status'] == 'diffData')
                                                <span class="badge-diff">Ada Perubahan ({{ $r['diffCount'] }} kolom)</span>
                                            @elseif($r['status'] == 'sameData')
                                                <span class="badge-same">Sama Identik</span>
                                            @elseif($r['status'] == 'oldOnly')
                                                <span class="badge bg-warning text-dark">Hanya di Data Lama</span>
                                            @else
                                                <span class="badge bg-info text-dark">Data Baru</span>
                                            @endif
                                        </td>
                                        <td>{{ $r['diffCount'] > 0 ? $r['diffCount'] . ' Perbedaan' : '—' }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endisset
            </div>
        </div>
    </div>
</div>
@endsection
