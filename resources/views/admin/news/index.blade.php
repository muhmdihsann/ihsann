@extends('layouts.admin')

@section('title', 'Berita & Edukasi')
@section('page_title', 'Kelola Berita & Edukasi')

@section('content')
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold text-primary">Daftar Berita</h5>
            <a href="{{ route('admin.news.create') }}" class="btn btn-primary btn-sm rounded-pill">
                <i class="fas fa-plus me-1"></i> Tambah Berita
            </a>
        </div>
        <div class="card-body p-0">
            @if($news->isEmpty())
                <div class="p-4 text-center text-muted">Belum ada berita yang dipublikasikan.</div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                        <tr>
                            <th>Judul</th>
                            <th>Tanggal</th>
                            <th>Status</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($news as $item)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $item->title }}</div>
                                    <div class="small text-muted">{{ Str::limit(strip_tags($item->excerpt ?? $item->content), 80) }}</div>
                                </td>
                                <td>{{ $item->published_at?->format('d M Y') ?? '-' }}</td>
                                <td>
                                    @if(!$item->is_published)
                                        <span class="badge bg-secondary">Draft</span>
                                    @elseif($item->published_at?->isFuture())
                                        <span class="badge bg-info text-dark">Terjadwal</span>
                                    @else
                                        <span class="badge bg-success">Published</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="d-flex gap-2 justify-content-end">
                                        <a href="{{ route('admin.news.edit', $item) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                        <form action="{{ route('admin.news.destroy', $item) }}" method="POST" onsubmit="return confirm('Hapus berita ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger" type="submit">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
        @if($news->hasPages())
            <div class="card-footer bg-white">
                {{ $news->links() }}
            </div>
        @endif
    </div>
@endsection
