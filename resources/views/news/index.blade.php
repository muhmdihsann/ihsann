@extends('layouts.public')

@section('content')
    <main class="news-list-page">
        <div class="news-list-page__top">
            <a href="{{ route('home') }}" class="news-page__back"><i class="fas fa-arrow-left"></i> Kembali ke beranda</a>
            <span class="news-page__eyebrow">{{ number_format($news->total(), 0, ',', '.') }} berita dipublikasikan</span>
        </div>

        <header class="news-list-page__header">
            <span class="news-page__eyebrow">SPM Kebakaran</span>
            <h1>Berita &amp; edukasi</h1>
            <p>Temukan berita dan panduan seputar kebakaran, mitigasi, dan layanan daerah.</p>
        </header>

        <form class="news-search" action="{{ route('news.index') }}" method="GET" role="search">
            <label for="newsSearch">Cari berita</label>
            <div class="news-search__controls">
                <input id="newsSearch" type="search" name="search" value="{{ $search }}" placeholder="Masukkan judul atau kata kunci..." maxlength="100">
                <button type="submit"><i class="fas fa-search"></i> Cari</button>
                @if($search !== '')
                    <a href="{{ route('news.index') }}" class="news-search__clear">Hapus pencarian</a>
                @endif
            </div>
        </form>

        <div class="news-list-page__results">
            @if($search !== '')
                <p>Hasil pencarian: <strong>{{ $search }}</strong> ({{ $news->total() }} berita).</p>
            @else
                <p>Semua berita dan edukasi terbaru.</p>
            @endif
        </div>

        <section class="news-card-grid" aria-label="Daftar berita">
            @forelse($news as $item)
                <article class="news-card">
                    <a class="news-card__image" href="{{ route('news.show', $item->slug) }}" aria-label="Baca {{ $item->title }}">
                        @if($item->image_path)
                            <img src="{{ Storage::disk('public')->url($item->image_path) }}" alt="">
                        @else
                            <span><i class="fas fa-newspaper" aria-hidden="true"></i></span>
                        @endif
                    </a>
                    <div class="news-card__body">
                        <time datetime="{{ $item->published_at?->toAtomString() }}">{{ $item->published_at?->translatedFormat('d F Y') }}</time>
                        <h2><a href="{{ route('news.show', $item->slug) }}">{{ $item->title }}</a></h2>
                        @if($item->excerpt)
                            <p>{{ $item->excerpt }}</p>
                        @endif
                        <a class="news-card__more" href="{{ route('news.show', $item->slug) }}">Baca selengkapnya <i class="fas fa-arrow-right"></i></a>
                    </div>
                </article>
            @empty
                <div class="news-list-page__empty">
                    <i class="fas fa-search" aria-hidden="true"></i>
                    <h2>{{ $search !== '' ? 'Berita tidak ditemukan' : 'Belum ada berita' }}</h2>
                    <p>{{ $search !== '' ? 'Coba kata kunci lain atau hapus pencarian.' : 'Berita dan edukasi akan ditampilkan di sini.' }}</p>
                </div>
            @endforelse
        </section>

        @if($news->hasPages())
            <nav class="news-list-page__pagination" aria-label="Navigasi halaman berita">
                {{ $news->links('pagination::bootstrap-5') }}
            </nav>
        @endif
    </main>
@endsection

@push('styles')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,500;12..96,600;12..96,700&family=Instrument+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        .news-list-page { max-width: 1200px; margin: 0 auto; padding: 40px 24px 72px; color: #102044; font-family: 'Instrument Sans', sans-serif; }
        .news-list-page__top { display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 12px; margin-bottom: 36px; }
        .news-list-page__header { margin-bottom: 28px; }
        .news-list-page__header h1 { margin: 8px 0; color: #0b2e6f; font-family: 'Bricolage Grotesque', sans-serif; font-size: clamp(2rem, 4vw, 3rem); font-weight: 700; }
        .news-list-page__header p, .news-list-page__results { color: #5b6781; }
        .news-search { margin-bottom: 22px; }
        .news-search label { display: block; margin-bottom: 8px; color: #102044; font-weight: 600; }
        .news-search__controls { display: flex; flex-wrap: wrap; align-items: center; gap: 10px; }
        .news-search input { flex: 1 1 280px; min-width: 0; min-height: 48px; border: 1px solid #cbd5e1; border-radius: 8px; padding: 10px 14px; color: #102044; background: #fff; }
        .news-search input:focus { outline: 3px solid rgba(37, 99, 235, .18); border-color: #2563eb; }
        .news-search button { min-height: 48px; border: 0; border-radius: 8px; padding: 10px 18px; color: #0b2e6f; background: #ffc928; font-weight: 700; }
        .news-search__clear { color: #174a9a; font-weight: 600; text-decoration: none; }
        .news-search__clear:hover { text-decoration: underline; }
        .news-list-page__results { margin-bottom: 18px; }
        .news-list-page__results p { margin: 0; }
        .news-card-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 24px; }
        .news-card { display: flex; min-width: 0; flex-direction: column; overflow: hidden; border: 1px solid #dfe6f2; border-radius: 18px; background: #fff; transition: transform .2s, box-shadow .2s; }
        .news-card:hover { transform: translateY(-3px); box-shadow: 0 16px 32px -22px rgba(11, 46, 111, .55); }
        .news-card__image { display: grid; height: 190px; place-items: center; overflow: hidden; background: #edf2f8; color: #174a9a; text-decoration: none; }
        .news-card__image img { width: 100%; height: 100%; object-fit: cover; }
        .news-card__image span { font-size: 3rem; }
        .news-card__body { display: flex; flex: 1; flex-direction: column; align-items: flex-start; gap: 10px; padding: 20px; }
        .news-card__body time { color: #5b6781; font-size: .88rem; }
        .news-card__body h2 { margin: 0; color: #0b2e6f; font-family: 'Bricolage Grotesque', sans-serif; font-size: 1.25rem; line-height: 1.35; }
        .news-card__body h2 a { color: inherit; text-decoration: none; }
        .news-card__body h2 a:hover { text-decoration: underline; }
        .news-card__body p { display: -webkit-box; margin: 0; overflow: hidden; color: #465875; line-height: 1.65; -webkit-box-orient: vertical; -webkit-line-clamp: 3; }
        .news-card__more { margin-top: auto; padding-top: 8px; color: #c92b2b; font-weight: 600; text-decoration: none; }
        .news-card__more:hover { text-decoration: underline; }
        .news-list-page__empty { grid-column: 1 / -1; padding: 48px 24px; border: 1px dashed #cbd5e1; border-radius: 16px; color: #5b6781; text-align: center; }
        .news-list-page__empty > i { color: #174a9a; font-size: 2rem; }
        .news-list-page__empty h2 { margin: 12px 0 6px; color: #0b2e6f; font-size: 1.25rem; }
        .news-list-page__empty p { margin: 0; }
        .news-list-page__pagination { margin-top: 32px; }
        @media (max-width: 900px) { .news-card-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
        @media (max-width: 600px) {
            .news-list-page { padding: 28px 16px 48px; }
            .news-card-grid { grid-template-columns: 1fr; }
            .news-card__image { height: 210px; }
            .news-search button { flex: 1 0 100%; }
        }
    </style>
@endpush