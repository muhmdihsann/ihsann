@extends('layouts.admin')

@section('title', 'Cetak Laporan - SPM Kebakaran')
@section('page_title', 'Ekspor & Cetak Laporan')

@section('content')
<div class="card border-0 shadow-sm" style="max-width: 960px;">
    <div class="card-header bg-white py-3">
        <h2 class="h6 fw-bold text-primary mb-1"><i class="fas fa-file-excel me-2"></i>Laporan SPM Kebakaran</h2>
        <p class="small text-muted mb-0">Excel mengikuti format rekap SPM dan hanya memuat data published yang tampil pada dashboard publik.</p>
    </div>
    <div class="card-body">
        @if($years->isEmpty())
            <div class="alert alert-secondary mb-0">Belum ada data SPM published untuk dibuatkan laporan.</div>
        @else
            <form action="{{ route('export.excel') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-sm-8 col-md-6">
                    <label for="report-year" class="form-label fw-semibold">Tahun data</label>
                    <select id="report-year" name="year" class="form-select" required>
                        @foreach($years as $year)
                            <option value="{{ $year }}" @selected($loop->first)>Tahun {{ $year }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-4 col-md-4">
                    <button type="submit" class="btn btn-success w-100"><i class="fas fa-download me-2"></i>Unduh Excel (.xlsx)</button>
                </div>
            </form>
            <div class="mt-3 small text-muted">Data draft tidak disertakan. Metadata kolom yang belum tersedia pada record lama akan ditandai “-”.</div>
        @endif
</div>
@endsection
