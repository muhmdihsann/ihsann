@extends('layouts.admin')

@section('title', 'Edit Data SPM - SPM Kebakaran')
@section('page_title', 'Edit Data SPM')

@section('content')
<div class="row">
    <div class="col-lg-10">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-edit me-2"></i>Edit Data: {{ $spmData->regency->name }} / {{ $spmData->regency->province->name }} ({{ $spmData->year }})
                </h6>
            </div>
            <div class="card-body">
                <form action="{{ route('spm.update', ['spmData' => $spmData->id]) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="return_route" value="{{ request('return_route') }}">
                    <input type="hidden" name="year" value="{{ request('year', $spmData->year) }}">
                    <input type="hidden" name="search" value="{{ request('search') }}">

                    <div class="alert alert-info py-2 small">Simpan perubahan akan menjadikannya draft. Setelah itu tekan <strong>Publish</strong> di daftar Data Kab/Kota agar perubahan tampil di publik.</div>
                    <div class="alert alert-light border d-flex flex-wrap align-items-center gap-2 mb-3">
                        <span class="text-muted">Indeks otomatis dari jumlah enam dimensi:</span>
                        <strong id="calculated-index" class="fs-5 text-primary">{{ number_format($spmData->nilai_akhir, 2, ',', '.') }}</strong>
                        <span id="calculated-category" class="badge bg-primary">{{ $spmData->kategori }}</span>
                        <small id="index-cap-warning" class="text-warning-emphasis ms-auto" hidden>Indeks dibatasi maksimal 100.</small>
                    </div>

                    @php
                        $fields = [
                            ['jml_kecamatan', 'Jumlah Kecamatan', 'number', '1', null],
                            ['jml_pos', 'Jumlah Pos Pemadam', 'number', '1', null],
                            ['pr_pos_kecamatan', 'Cakupan Pos (%)', 'number', '0.01', '100'],
                            ['total_sdm', 'Total SDM Pemadam', 'number', '1', null],
                            ['sdm_sertifikat', 'SDM Bersertifikat', 'number', '1', null],
                            ['pr_sdm_sertifikat', 'SDM Bersertifikat (%)', 'number', '0.01', '100'],
                            ['jml_desa', 'Jumlah Desa', 'number', '1', null],
                            ['jml_redkar', 'Jumlah REDKAR', 'number', '1', null],
                            ['pr_redkar', 'Cakupan REDKAR (%)', 'number', '0.01', '100'],
                            ['dimensi_kelembagaan', 'Dimensi Kelembagaan', 'number', '0.01', '20'],
                            ['dimensi_perencanaan', 'Dimensi Perencanaan', 'number', '0.01', '20'],
                            ['dimensi_capaian', 'Dimensi Capaian', 'number', '0.01', '20'],
                            ['dimensi_sarpras', 'Dimensi Sarana Prasarana', 'number', '0.01', '10'],
                            ['dimensi_sdm_sertifikat', 'Dimensi SDM Bersertifikat', 'number', '0.01', '10'],
                            ['dimensi_pemberdayaan', 'Dimensi Pemberdayaan', 'number', '0.01', '20'],
                        ];
                    @endphp
                    <div class="row g-3 mb-3">
                        <div class="col-sm-6 col-lg-4">
                            <label for="kelembagaan" class="form-label fw-semibold">Kelembagaan</label>
                            <input id="kelembagaan" type="text" maxlength="255" name="kelembagaan" class="form-control @error('kelembagaan') is-invalid @enderror" value="{{ old('kelembagaan', $spmData->kelembagaan) }}">
                            @error('kelembagaan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-sm-6 col-lg-4">
                            <label for="perencanaan_anggaran" class="form-label fw-semibold">Perencanaan Anggaran</label>
                            <input id="perencanaan_anggaran" type="text" maxlength="255" name="perencanaan_anggaran" class="form-control @error('perencanaan_anggaran') is-invalid @enderror" value="{{ old('perencanaan_anggaran', $spmData->perencanaan_anggaran) }}">
                            @error('perencanaan_anggaran')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-sm-6 col-lg-4">
                            <label for="capaian_spm" class="form-label fw-semibold">Capaian SPM (%)</label>
                            <input id="capaian_spm" type="number" step="0.01" min="0" max="100" name="capaian_spm" class="form-control @error('capaian_spm') is-invalid @enderror" value="{{ old('capaian_spm', $spmData->capaian_spm) }}">
                            @error('capaian_spm')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        @foreach($fields as [$field, $label, $type, $step, $max])
                            <div class="col-sm-6 col-lg-4">
                                <label for="{{ $field }}" class="form-label fw-semibold">{{ $label }}</label>
                                <input id="{{ $field }}" type="{{ $type }}" step="{{ $step }}" min="0" @if($max !== null) max="{{ $max }}" @endif name="{{ $field }}" @if(str_starts_with($field, 'dimensi_')) data-index-dimension @endif class="form-control @error($field) is-invalid @enderror" value="{{ old($field, $spmData->{$field}) }}">
                                @error($field)<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        @endforeach
                    </div>

                    <hr>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Simpan Perubahan</button>
                    <a href="{{ request('return_route') === 'regencies.index' ? route('regencies.index', array_filter(['search' => request('search'), 'year' => request('year')])) : route('spm.index', array_filter(['search' => request('search'), 'year' => request('year')])) }}" class="btn btn-light"><i class="fas fa-arrow-left me-1"></i> Kembali</a>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var dimensions = document.querySelectorAll('[data-index-dimension]');
        var indexOutput = document.getElementById('calculated-index');
        var categoryOutput = document.getElementById('calculated-category');

        function updateCalculatedIndex() {
            var dimensionTotal = Array.from(dimensions).reduce(function (total, input) {
                return total + (Number(input.value) || 0);
            }, 0);
            var index = Math.min(100, dimensionTotal);
            var category = index >= 85 ? 'Sangat Baik' : (index >= 70 ? 'Baik' : (index >= 55 ? 'Cukup' : 'Kurang'));
            var color = category === 'Sangat Baik' ? 'success' : (category === 'Baik' ? 'primary' : (category === 'Cukup' ? 'warning' : 'danger'));

            indexOutput.textContent = index.toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            categoryOutput.textContent = category;
            categoryOutput.className = 'badge bg-' + color + (category === 'Cukup' ? ' text-dark' : '');
            document.getElementById('index-cap-warning').hidden = dimensionTotal <= 100;
        }

        dimensions.forEach(function (input) {
            input.addEventListener('input', updateCalculatedIndex);
        });
        updateCalculatedIndex();
    });
</script>
@endsection
