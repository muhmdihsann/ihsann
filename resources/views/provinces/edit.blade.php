@extends('layouts.admin')

@section('title', 'Edit Provinsi - SPM Kebakaran')
@section('page_title', 'Edit Data Provinsi')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
        <div>
            <h2 class="h5 mb-1 fw-bold">{{ $province->name }}</h2>
            <p class="text-muted mb-0">Ringkasan berikut sama dengan data provinsi pada dashboard publik.</p>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white d-flex flex-wrap justify-content-between align-items-center gap-3 py-3">
            <div>
                <h3 class="h6 fw-bold mb-1 text-primary">Data capaian terpublikasi</h3>
                <small class="text-muted">Indeks provinsi adalah rata-rata kabupaten/kota; total fasilitas merupakan jumlah data terpublikasi.</small>
            </div>
            @if($availableYears->isNotEmpty())
                <form method="GET" action="{{ route('provinces.edit', $province) }}" class="d-flex align-items-center gap-2">
                    <label for="province-year" class="small fw-semibold">Tahun</label>
                    <select id="province-year" name="year" class="form-select form-select-sm" onchange="this.form.submit()">
                        @foreach($availableYears as $availableYear)
                            <option value="{{ $availableYear }}" @selected($year === $availableYear)>{{ $availableYear }}</option>
                        @endforeach
                    </select>
                </form>
            @endif
        </div>
        <div class="card-body">
            @if($provinceData && $provinceData['spm'] !== null)
                @php
                    $provinceMetricLabels = [
                        'kecamatan' => 'Kecamatan',
                        'pos_damkar' => 'Pos damkar',
                        'sdm' => 'Personel',
                        'sdm_bersertifikat' => 'SDM bersertifikat',
                        'desa' => 'Desa terdata',
                        'redkar' => 'Anggota REDKAR',
                        'kelembagaan' => 'Kelembagaan',
                        'perencanaan' => 'Perencanaan',
                        'capaian' => 'Capaian layanan',
                        'sarpras' => 'Sarana prasarana',
                        'sdm_dimension' => 'Dimensi SDM',
                        'pemberdayaan' => 'Pemberdayaan',
                    ];
                    $categoryColors = ['Sangat Baik' => 'success', 'Baik' => 'primary', 'Cukup' => 'warning', 'Kurang' => 'danger'];
                @endphp
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3 p-3 rounded border bg-light">
                    <div>
                        <div class="small text-muted">Indeks SPM Provinsi · Tahun {{ $year }}</div>
                        <strong class="fs-2 text-primary">{{ number_format($provinceData['spm'], 2, ',', '.') }}</strong>
                        <span class="badge bg-{{ $categoryColors[$provinceData['category']] ?? 'secondary' }} ms-2">{{ $provinceData['category'] }}</span>
                    </div>
                    <div class="text-muted small">{{ $provinceData['regency_count'] }} kabupaten/kota terpublikasi</div>
                </div>
                <div class="row g-2">
                    @foreach($provinceMetricLabels as $key => $label)
                        @if(array_key_exists($key, $provinceData['metrics']) && $provinceData['metrics'][$key] !== null)
                            <div class="col-6 col-md-4 col-xl-3">
                                <div class="border rounded p-3 h-100 bg-white">
                                    <div class="small text-muted">{{ $label }}</div>
                                    <strong class="fs-5">{{ number_format($provinceData['metrics'][$key], 2, ',', '.') }}</strong>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            @else
                <div class="alert alert-secondary mb-0">Belum ada data SPM yang dipublikasikan untuk provinsi ini pada tahun {{ $year ?? 'yang dipilih' }}.</div>
            @endif
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <h3 class="h6 fw-bold mb-0 text-primary">Data kabupaten/kota tahun {{ $year ?? '-' }}</h3>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr><th>Kabupaten/Kota</th><th class="text-end">Kecamatan</th><th class="text-end">Pos damkar</th><th class="text-end">SDM</th><th class="text-end">REDKAR</th><th>Indeks</th><th>Status</th><th class="text-end">Aksi</th></tr>
                </thead>
                <tbody>
                    @forelse($spmRows as $row)
                        <tr>
                            <td class="fw-semibold">{{ $row->regency->name }}</td>
                            <td class="text-end">{{ number_format((int) $row->jml_kecamatan) }}</td>
                            <td class="text-end">{{ number_format((int) $row->jml_pos) }}</td>
                            <td class="text-end">{{ number_format((int) $row->total_sdm) }}</td>
                            <td class="text-end">{{ number_format((int) $row->jml_redkar) }}</td>
                            <td>{{ number_format($row->nilai_akhir, 2, ',', '.') }} · {{ $row->kategori }}</td>
                            <td>
                                @if($row->importHistory?->status !== 'published')
                                    <span class="badge bg-warning text-dark">Dataset draft</span>
                                @elseif($row->is_published)
                                    <span class="badge bg-success">Published</span>
                                @else
                                    <span class="badge bg-secondary">Draft</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex flex-wrap justify-content-end gap-1">
                                    <a href="{{ route('spm.edit', ['spmData' => $row->id, 'return_route' => 'provinces.edit', 'return_province_id' => $province->id, 'year' => $year]) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-pen me-1"></i>Edit data</a>
                                    @if($row->importHistory?->status === 'published')
                                        <form action="{{ route($row->is_published ? 'spm.draft' : 'spm.publish', $row) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="return_route" value="provinces.edit">
                                            <input type="hidden" name="return_province_id" value="{{ $province->id }}">
                                            <input type="hidden" name="year" value="{{ $year }}">
                                            <button class="btn btn-sm {{ $row->is_published ? 'btn-outline-warning' : 'btn-success' }}" type="submit">{{ $row->is_published ? 'Draft' : 'Publish' }}</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center text-muted py-4">Belum ada record kabupaten/kota untuk tahun ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card border-0 shadow-sm" style="max-width: 720px;">
        <div class="card-header bg-white py-3">
            <h3 class="h6 mb-0 fw-bold text-primary">Identitas Provinsi</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('provinces.update', $province) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label for="province-name" class="form-label fw-semibold">Nama provinsi</label>
                    <input id="province-name" name="name" value="{{ old('name', $province->name) }}" class="form-control @error('name') is-invalid @enderror" required maxlength="255">
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-4">
                    <label for="province-code" class="form-label fw-semibold">Kode Kemendagri</label>
                    <input id="province-code" name="code" value="{{ old('code', $province->code) }}" class="form-control @error('code') is-invalid @enderror" required maxlength="5">
                    @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-primary" type="submit"><i class="fas fa-save me-1"></i> Simpan identitas</button>
                    <a href="{{ route('provinces.index') }}" class="btn btn-outline-secondary">Kembali</a>
                </div>
            </form>
        </div>
    </div>
@endsection