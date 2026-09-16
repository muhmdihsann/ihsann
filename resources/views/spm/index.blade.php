@extends('layouts.admin')

@section('title', 'Manajemen Data SPM - SPM Kebakaran')
@section('page_title', 'Data SPM Sub Urusan Kebakaran')

@section('content')
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body bg-light">
        <!-- Form Filter dan Pencarian -->
        <form action="{{ url('/admin/spm') }}" method="GET" class="row g-3 align-items-center">
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
                <label class="form-label fw-bold">Cari Wilayah</label>
                <input type="text" name="search" class="form-control" placeholder="Ketik nama kabupaten/kota..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3 d-flex align-items-end mt-4">
                <button type="submit" class="btn btn-primary w-100 me-2"><i class="fas fa-search me-1"></i> Cari Data</button>
                <a href="{{ url('/admin/spm') }}" class="btn btn-outline-secondary"><i class="fas fa-sync-alt"></i></a>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <h6 class="m-0 font-weight-bold text-primary">Daftar Capaian SPM</h6>
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
                        <th class="text-center">Kategori</th>
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
                        <td class="text-center">
                            @if($data->kategori == 'Sangat Baik') <span class="badge bg-success">Sangat Baik</span>
                            @elseif($data->kategori == 'Baik') <span class="badge bg-primary">Baik</span>
                            @elseif($data->kategori == 'Cukup') <span class="badge bg-warning text-dark">Cukup</span>
                            @elseif($data->kategori == 'Kurang') <span class="badge bg-danger">Kurang</span>
                            @else <span class="badge bg-secondary">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <!-- Tombol Edit -->
                            <a href="{{ url('/admin/spm/'.$data->id.'/edit') }}" class="btn btn-sm btn-warning" title="Edit Data">
                                <i class="fas fa-edit"></i>
                            </a>

                            <!-- Tombol Hapus (dibungkus form agar aman) -->
                            <form action="{{ url('/admin/spm/'.$data->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini? Data yang dihapus tidak dapat dikembalikan.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" title="Hapus Data">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">Data SPM tidak ditemukan.</td>
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
