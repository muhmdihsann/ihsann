@extends('layouts.admin')

@section('title', 'Preview Data Excel - SPM Kebakaran')
@section('page_title', 'Preview Data Import')

@section('content')
<div class="alert alert-info border-0 shadow-sm">
    <i class="fas fa-info-circle me-2"></i> <strong>Tahap Validasi:</strong> Data belum disimpan ke database. Silakan periksa tabel di bawah ini sebelum melanjutkan.
</div>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Ringkasan Validasi (Tahun: {{ $year }})</h6>

        <!-- Form Konfirmasi Import -->
        <form action="{{ url('/admin/import/store') }}" method="POST" class="d-inline">
            @csrf
            <input type="hidden" name="file_name" value="{{ $fileName }}">
            <input type="hidden" name="year" value="{{ $year }}">
            <button type="submit" class="btn btn-success btn-sm shadow-sm" {{ count($invalidData) > 0 ? 'disabled' : '' }}>
                <i class="fas fa-save me-1"></i> Konfirmasi & Simpan ke Database
            </button>
        </form>
    </div>
    <div class="card-body">
        <div class="row text-center mb-3">
            <div class="col-md-6">
                <div class="p-3 bg-light rounded border">
                    <h5 class="text-success">{{ count($validData) }}</h5>
                    <small>Baris Data Valid</small>
                </div>
            </div>
            <div class="col-md-6">
                <div class="p-3 bg-light rounded border">
                    <h5 class="text-danger">{{ count($invalidData) }}</h5>
                    <small>Baris Data Error</small>
                </div>
            </div>
        </div>

        @if(count($invalidData) > 0)
            <h6 class="text-danger mt-4"><i class="fas fa-exclamation-triangle me-2"></i>Data Tidak Valid (Tidak akan di-import)</h6>
            <div class="table-responsive mb-4">
                <table class="table table-bordered table-sm">
                    <thead class="table-danger">
                        <tr>
                            <th>Baris Excel</th>
                            <th>Provinsi</th>
                            <th>Kabupaten/Kota</th>
                            <th>Alasan Error</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invalidData as $error)
                        <tr>
                            <td>{{ $error['row'] }}</td>
                            <td>{{ $error['provinsi'] }}</td>
                            <td>{{ $error['kabupaten'] }}</td>
                            <td>{{ $error['alasan'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <h6 class="text-success mt-4"><i class="fas fa-check-circle me-2"></i>Sample Data Valid yang akan di-import</h6>
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-sm text-nowrap">
                <thead class="table-light">
                    <tr>
                        <th>Provinsi</th>
                        <th>Kabupaten/Kota</th>
                        <th>Jml Kecamatan</th>
                        <th>Jml Pos</th>
                        <th>Nilai Akhir</th>
                        <th>Kategori</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Menampilkan maksimal 10 data saja sebagai preview agar tidak berat -->
                    @foreach(array_slice($validData, 0, 10) as $row)
                    <tr>
                        <td>{{ $row[1] }}</td> <!-- Kolom Provinsi -->
                        <td>{{ $row[2] }}</td> <!-- Kolom Kab/Kota -->
                        <td>{{ $row[6] }}</td> <!-- Jumlah Kecamatan -->
                        <td>{{ $row[7] }}</td> <!-- Jumlah Pos -->
                        <td class="fw-bold">{{ $row[21] }}</td> <!-- Nilai Akhir -->
                        <td>{{ $row[22] }}</td> <!-- Kategori -->
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @if(count($validData) > 10)
                <div class="text-center text-muted mt-2"><small><i>... dan {{ count($validData) - 10 }} baris lainnya disembunyikan.</i></small></div>
            @endif
        </div>
    </div>
</div>
@endsection
