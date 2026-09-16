@extends('layouts.admin')

@section('title', 'Cetak Laporan - SPM Kebakaran')
@section('page_title', 'Ekspor & Cetak Laporan')

@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-file-export me-2"></i>Pilih Parameter Laporan</h6>
            </div>
            <div class="card-body">
                <form action="{{ url('/admin/export/pdf') }}" method="GET" target="_blank">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Pilih Tahun Data</label>
                        <select name="year" class="form-select" required>
                            @foreach($years as $y)
                                <option value="{{ $y }}">Tahun {{ $y }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="alert alert-info py-2 small">
                        <i class="fas fa-info-circle me-1"></i> Laporan PDF akan dibuka atau diunduh secara otomatis dengan format tabel mendatar (landscape).
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="form-control btn btn-danger"><i class="fas fa-file-pdf me-2"></i> Download PDF</button>

                        <!-- Tombol untuk Excel -->
                        <button type="submit" formaction="{{ url('/admin/export/excel') }}" class="form-control btn btn-success"><i class="fas fa-file-excel me-2"></i> Download Excel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
