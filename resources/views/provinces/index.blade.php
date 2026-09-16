@extends('layouts.admin')

@section('title', 'Data Provinsi - SPM Kebakaran')
@section('page_title', 'Data Provinsi')

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <h6 class="m-0 font-weight-bold text-primary">Daftar Wilayah Provinsi</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" width="5%">No</th>
                        <th width="20%">Kode Kemendagri</th>
                        <th>Nama Provinsi</th>
                        <th class="text-center" width="15%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($provinces as $index => $province)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td><span class="badge bg-secondary">{{ $province->code }}</span></td>
                        <td>{{ $province->name }}</td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-info text-white"><i class="fas fa-eye"></i> Detail</button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">Data Provinsi masih kosong. Silakan import data terlebih dahulu.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
