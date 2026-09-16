@extends('layouts.admin')

@section('title', 'Import Data Excel - SPM Kebakaran')
@section('page_title', 'Import Data Excel')

@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <form action="{{ url('/admin/import/preview') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tahun Data SPM</label>
                        <select name="year" class="form-select" required>
                            <option value="">-- Pilih Tahun --</option>
                            <option value="2024">2024</option>
                            <option value="2025">2025</option>
                            <option value="2026">2026</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-bold">Upload File Excel (.xlsx / .xls)</label>
                        <input class="form-control" type="file" name="file" accept=".xlsx, .xls" required>
                        <div class="form-text">Gunakan format file Excel standar. Data utama harus berada di Sheet pertama ("Hasil Indeks").</div>
                    </div>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-search me-2"></i> Baca & Preview Data</button>
                    <a href="{{ url('/admin/dashboard') }}" class="btn btn-light">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
