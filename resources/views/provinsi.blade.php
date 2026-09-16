@extends('layouts.public')

@section('content')
<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0">Provinsi {{ $provinsi->name }}</h2>
            <p class="text-muted">Detail Penyelenggaraan SPM Sub Urusan Kebakaran Tingkat Kabupaten/Kota</p>
        </div>
        <a href="{{ url('/peta') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i> Kembali ke Peta</a>
    </div>

    <!-- Ringkasan Provinsi -->
    <div class="row text-center mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm border-0 py-3 bg-white h-100 border-bottom border-primary border-4">
                <h6 class="text-muted text-uppercase fw-bold mb-1">Rata-rata Nilai</h6>
                <h2 class="fw-bold text-primary mb-0">{{ number_format($avgNilai, 2, ',', '.') }}</h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 py-3 bg-white h-100 border-bottom border-success border-4">
                <h6 class="text-muted text-uppercase fw-bold mb-1">Total Wilayah</h6>
                <h2 class="fw-bold text-success mb-0">{{ $totalRegencies }} <span class="fs-6 text-muted fw-normal">Kab/Kota</span></h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 py-3 bg-white h-100 border-bottom border-danger border-4">
                <h6 class="text-muted text-uppercase fw-bold mb-1">Total Pos Pemadam</h6>
                <h2 class="fw-bold text-danger mb-0">{{ number_format($totalPos, 0, ',', '.') }}</h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 py-3 bg-white h-100 border-bottom border-info border-4">
                <h6 class="text-muted text-uppercase fw-bold mb-1">Total SDM</h6>
                <h2 class="fw-bold text-info mb-0">{{ number_format($totalSdm, 0, ',', '.') }}</h2>
            </div>
        </div>
    </div>

    <!-- Tabel Data Kabupaten/Kota -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 font-weight-bold"><i class="fas fa-list me-2"></i>Daftar Capaian per Kabupaten/Kota</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th class="text-center" width="5%">No</th>
                            <th>Nama Kabupaten/Kota</th>
                            <th class="text-center">Pos Pemadam</th>
                            <th class="text-center">Total SDM</th>
                            <th class="text-center">Desa REDKAR</th>
                            <th class="text-center">Nilai Akhir</th>
                            <th class="text-center">Kategori</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($regencies as $index => $reg)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td class="fw-bold">{{ $reg->name }}</td>
                            <td class="text-center">{{ $reg->jml_pos ?? '-' }}</td>
                            <td class="text-center">{{ $reg->total_sdm ?? '-' }}</td>
                            <td class="text-center">{{ $reg->pr_redkar ? number_format($reg->pr_redkar, 2) . '%' : '-' }}</td>
                            <td class="text-center fw-bold text-primary">{{ $reg->nilai_akhir ? number_format($reg->nilai_akhir, 2) : '0' }}</td>
                            <td class="text-center">
                                @if($reg->kategori == 'Sangat Baik') <span class="badge bg-success">Sangat Baik</span>
                                @elseif($reg->kategori == 'Baik') <span class="badge bg-primary">Baik</span>
                                @elseif($reg->kategori == 'Cukup') <span class="badge bg-warning text-dark">Cukup</span>
                                @elseif($reg->kategori == 'Kurang') <span class="badge bg-danger">Kurang</span>
                                @else <span class="badge bg-secondary">Belum ada data</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">Belum ada data kabupaten/kota untuk provinsi ini.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
