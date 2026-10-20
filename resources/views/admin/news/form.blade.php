@extends('layouts.admin')

@section('title', isset($news->id) ? 'Edit Berita' : 'Tambah Berita')
@section('page_title', isset($news->id) ? 'Edit Berita' : 'Tambah Berita Baru')

@section('content')
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-bold text-primary">{{ isset($news->id) ? 'Edit Berita' : 'Tambah Berita' }}</h5>
        </div>
        <div class="card-body">
            <form action="{{ isset($news->id) ? route('admin.news.update', $news) : route('admin.news.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @if(isset($news->id))
                    @method('PUT')
                @endif

                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label fw-semibold">Judul Berita</label>
                        <input type="text" name="title" class="form-control" value="{{ old('title', $news->title) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Tanggal Publikasi</label>
                        <input type="datetime-local" name="published_at" class="form-control" value="{{ old('published_at', optional($news->published_at)->format('Y-m-d\TH:i')) }}" required>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">Ringkasan</label>
                        <textarea name="excerpt" rows="3" class="form-control" placeholder="Isi ringkas mengenai berita ini...">{{ old('excerpt', $news->excerpt) }}</textarea>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">Isi Berita</label>
                        <textarea name="content" rows="8" class="form-control" required>{{ old('content', $news->content) }}</textarea>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Gambar</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                        @if($news->image_path)
                            <div class="mt-2">
                                <img src="{{ Storage::disk('public')->url($news->image_path) }}" alt="Preview berita" class="img-fluid rounded border" style="max-height: 180px;">
                            </div>
                        @endif
                    </div>

                    <div class="col-md-6 d-flex align-items-center">
                        <div class="form-check form-switch mt-4">
                            <input class="form-check-input" type="checkbox" name="is_published" id="is_published" value="1" {{ old('is_published', $news->is_published) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="is_published">Tampilkan di dashboard publik</label>
                        </div>
                    </div>
                </div>

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-save me-1"></i> {{ isset($news->id) ? 'Simpan Perubahan' : 'Publish Berita' }}
                    </button>
                    <a href="{{ route('admin.news.index') }}" class="btn btn-outline-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection
