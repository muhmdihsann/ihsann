@extends('layouts.admin')

@section('title', 'Data Provinsi - SPM Kebakaran')
@section('page_title', 'Data Provinsi')

@section('content')
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
        <form action="{{ route('provinces.index') }}" method="GET" class="row g-2 align-items-end">
            <div class="col-md-8">
                <label for="province-search" class="form-label fw-semibold">Cari provinsi</label>
                <input id="province-search" type="search" name="search" value="{{ request('search') }}" class="form-control" placeholder="Nama provinsi atau kode Kemendagri">
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button class="btn btn-primary flex-fill" type="submit"><i class="fas fa-search me-1"></i> Cari</button>
                <a href="{{ route('provinces.index') }}" class="btn btn-outline-secondary" aria-label="Reset pencarian"><i class="fas fa-rotate-left"></i></a>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-header bg-white d-flex flex-wrap justify-content-between align-items-center gap-2 py-3">
        <div>
            <h6 class="m-0 fw-bold text-primary">Daftar Provinsi Terdata</h6>
            <small class="text-muted">Publikasi provinsi mengatur seluruh data kabupaten/kota yang berasal dari dataset published.</small>
        </div>
        <span class="badge bg-light text-dark">{{ $provinces->total() }} provinsi</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Provinsi</th>
                        <th>Kode Kemendagri</th>
                        <th>Data Wilayah</th>
                        <th>Status Publikasi</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($provinces as $province)
                    <tr>
                        <td class="fw-semibold">{{ $province->name }}</td>
                        <td><span class="badge bg-secondary">{{ $province->code }}</span></td>
                        <td>{{ number_format($province->regencies_count) }} kab/kota · {{ number_format($province->published_spm_data_count) }}/{{ number_format($province->publishable_spm_data_count) }} data published</td>
                        <td>
                            @if($province->spm_data_count === 0)
                                <span class="badge bg-light text-secondary">Belum ada data SPM</span>
                            @elseif($province->publishable_spm_data_count === 0)
                                <span class="badge bg-warning text-dark">Dataset masih draft</span>
                            @elseif($province->published_spm_data_count === $province->publishable_spm_data_count)
                                <span class="badge bg-success">Published</span>
                            @elseif($province->published_spm_data_count > 0)
                                <span class="badge bg-warning text-dark">Sebagian published</span>
                            @else
                                <span class="badge bg-secondary">Draft</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="d-inline-flex flex-wrap justify-content-end gap-1">
                                <a href="{{ route('provinces.edit', $province) }}" class="btn btn-sm btn-outline-primary" title="Edit provinsi" aria-label="Edit {{ $province->name }}"><i class="fas fa-pen"></i></a>
                                @if($province->published_spm_data_count < $province->publishable_spm_data_count)
                                    <form action="{{ route('provinces.publish', $province) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button class="btn btn-sm btn-success" type="submit">Publish data</button>
                                    </form>
                                @endif
                                @if($province->published_spm_data_count > 0)
                                    <form action="{{ route('provinces.draft', $province) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button class="btn btn-sm btn-outline-warning" type="submit">Jadikan draft</button>
                                    </form>
                                @endif
                                <form action="{{ route('provinces.destroy', $province) }}" method="POST" onsubmit="return confirm('Hapus provinsi ini beserta seluruh kabupaten/kota dan data SPM terkait? Tindakan ini tidak dapat dibatalkan.');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" type="submit" title="Hapus provinsi" aria-label="Hapus {{ $province->name }}"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">Provinsi tidak ditemukan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white">{{ $provinces->links('pagination::bootstrap-5') }}</div>
</div>
@endsection
