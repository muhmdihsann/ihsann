@extends('layouts.admin')

@section('title', 'Edit Data SPM - SPM Kebakaran')
@section('page_title', 'Edit Data SPM')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-edit me-2"></i>Edit Capaian Wilayah: {{ $spmData->regency->name }} (Tahun {{ $spmData->year }})
                </h6>
            </div>
            <div class="card-body">
                <!-- Perhatikan method 'POST' tapi kita timpa dengan @method('PUT') khas Laravel -->
                <form action="{{ url('/admin/spm/'.$spmData->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Nilai Akhir</label>
                            <input type="number" step="0.01" name="nilai_akhir" class="form-control" value="{{ $spmData->nilai_akhir }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Kategori Penilaian</label>
                            <select name="kategori" class="form-select" required>
                                <option value="Sangat Baik" {{ $spmData->kategori == 'Sangat Baik' ? 'selected' : '' }}>Sangat Baik</option>
                                <option value="Baik" {{ $spmData->kategori == 'Baik' ? 'selected' : '' }}>Baik</option>
                                <option value="Cukup" {{ $spmData->kategori == 'Cukup' ? 'selected' : '' }}>Cukup</option>
                                <option value="Kurang" {{ $spmData->kategori == 'Kurang' ? 'selected' : '' }}>Kurang</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Jumlah Pos Pemadam</label>
                            <input type="number" name="jml_pos" class="form-control" value="{{ $spmData->jml_pos }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Total SDM Pemadam</label>
                            <input type="number" name="total_sdm" class="form-control" value="{{ $spmData->total_sdm }}" required>
                        </div>
                    </div>

                    <hr>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Simpan Perubahan</button>
                    <a href="{{ url('/admin/spm') }}" class="btn btn-light"><i class="fas fa-arrow-left me-1"></i> Kembali</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
