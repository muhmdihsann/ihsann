@extends('layouts.admin')

@section('title', $indexRouteName === 'regencies.index' ? 'Data Kabupaten/Kota - SPM Kebakaran' : 'Manajemen Data SPM - SPM Kebakaran')
@section('page_title', $indexRouteName === 'regencies.index' ? 'Data Kabupaten/Kota' : 'Data SPM Sub Urusan Kebakaran')

@section('content')
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body bg-light">
        <!-- Form Filter dan Pencarian -->
        <form action="{{ route($indexRouteName) }}" method="GET" class="row g-3 align-items-center">
            <div class="col-md-3">
                <label class="form-label fw-bold">Filter Tahun</label>
                <select name="year" class="form-select">
                    <option value="">Semua Tahun</option>
                    @foreach($years as $y)
                        <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>Tahun {{ $y }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-bold">Cari Kabupaten/Kota/Provinsi</label>
                <input type="search" name="search" class="form-control" placeholder="Contoh: Banyumas" value="{{ request('search') }}">
            </div>
            <div class="col-md-3 d-flex align-items-end mt-4">
                <button type="submit" class="btn btn-primary w-100 me-2"><i class="fas fa-search me-1"></i> Cari Data</button>
                <a href="{{ route($indexRouteName) }}" class="btn btn-outline-secondary" aria-label="Reset filter"><i class="fas fa-sync-alt"></i></a>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <h6 class="m-0 font-weight-bold text-primary">{{ $indexRouteName === 'regencies.index' ? 'Data SPM per Kabupaten/Kota' : 'Daftar Capaian SPM' }}</h6>
        <!-- Tombol tambah manual -->
        <button class="btn btn-sm btn-success disabled"><i class="fas fa-plus me-1"></i> Tambah Data Manual</button>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th class="text-center">No</th>
                        <th>Tahun</th>
                        <th>Provinsi</th>
                        <th>Kabupaten/Kota</th>
                        <th class="text-center">Nilai Akhir</th>
                        <th class="text-center">REDKAR</th>
                        <th class="text-center">Kategori</th>
                        <th class="text-center">Status Publikasi</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($spmData as $index => $data)
                    <tr>
                        <td class="text-center">{{ $spmData->firstItem() + $index }}</td>
                        <td><span class="badge bg-secondary">{{ $data->year }}</span></td>
                        <td>{{ $data->regency->province->name ?? '-' }}</td>
                        <td class="fw-bold">{{ $data->regency->name ?? '-' }}</td>
                        <td class="text-center text-primary fw-bold">{{ $data->nilai_akhir }}</td>
                        <td class="text-center">{{ number_format((int) $data->jml_redkar) }}</td>
                        <td class="text-center">
                            @if($data->kategori == 'Sangat Baik') <span class="badge bg-success">Sangat Baik</span>
                            @elseif($data->kategori == 'Baik') <span class="badge bg-primary">Baik</span>
                            @elseif($data->kategori == 'Cukup') <span class="badge bg-warning text-dark">Cukup</span>
                            @elseif($data->kategori == 'Kurang') <span class="badge bg-danger">Kurang</span>
                            @else <span class="badge bg-secondary">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($data->importHistory?->status !== 'published')
                                <span class="badge bg-warning text-dark">Dataset draft</span>
                            @elseif($data->is_published)
                                <span class="badge bg-success">Published</span>
                            @else
                                <span class="badge bg-secondary">Draft</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="d-inline-flex flex-wrap justify-content-center gap-1">
                            <a href="{{ route('spm.edit', ['spmData' => $data->id, 'year' => request('year'), 'search' => request('search'), 'return_route' => $indexRouteName]) }}" class="btn btn-sm btn-outline-primary" title="Edit data"><i class="fas fa-pen"></i></a>
                            @if($data->importHistory?->status === 'published')
                                <form action="{{ route($data->is_published ? 'spm.draft' : 'spm.publish', $data) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="return_route" value="{{ $indexRouteName }}">
                                    <input type="hidden" name="year" value="{{ request('year') }}">
                                    <input type="hidden" name="search" value="{{ request('search') }}">
                                    <button class="btn btn-sm {{ $data->is_published ? 'btn-outline-warning' : 'btn-success' }}" type="submit">{{ $data->is_published ? 'Draft' : 'Publish' }}</button>
                                </form>
                            @endif
                            <form action="{{ route('spm.destroy', $data) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus data SPM {{ addslashes($data->regency->name ?? 'wilayah') }} tahun {{ $data->year }}?');">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="return_route" value="{{ $indexRouteName }}">
                                <input type="hidden" name="year" value="{{ request('year') }}">
                                <input type="hidden" name="search" value="{{ request('search') }}">
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus Data"><i class="fas fa-trash"></i></button>
                            </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-4 text-muted">Data SPM tidak ditemukan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white border-0 pt-4">
        <!-- Menampilkan Pagination Bawaan Laravel -->
        {{ $spmData->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection
