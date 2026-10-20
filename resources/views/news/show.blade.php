@extends('layouts.public')

@section('content')
    <main class="news-page">
        <div class="news-page__top">
            <a href="{{ route('news.index') }}" class="news-page__back"><i class="fas fa-arrow-left"></i> Kembali ke berita</a>
            <span class="news-page__eyebrow">Berita &amp; edukasi SPM kebakaran</span>
        </div>

        <article class="news-article">
            @if($news->image_path)
                <img class="news-article__image" src="{{ Storage::disk('public')->url($news->image_path) }}" alt="{{ $news->title }}">
            @endif
            <div class="news-article__content">
                <time datetime="{{ $news->published_at?->toAtomString() }}">{{ $news->published_at?->translatedFormat('l, d F Y · H:i') }}</time>
                <h1>{{ $news->title }}</h1>
                @if($news->excerpt)
                    <p class="news-article__excerpt">{{ $news->excerpt }}</p>
                @endif
                <div class="news-article__body">{!! nl2br(e($news->content)) !!}</div>
                <a href="{{ route('news.index') }}" class="news-article__return">Lihat berita lainnya <i class="fas fa-arrow-right"></i></a>
            </div>
        </article>
    </main>
@endsection

@push('styles')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,500;12..96,600;12..96,700&family=Instrument+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        .news-page { max-width: 1040px; margin: 0 auto; padding: 40px 24px 72px; color: #102044; font-family: 'Instrument Sans', sans-serif; }
        .news-page__top { display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 12px; margin-bottom: 24px; }
        .news-page__back, .news-article__return { color: #174a9a; font-weight: 600; text-decoration: none; }
        .news-page__back:hover, .news-article__return:hover { text-decoration: underline; }
        .news-page__eyebrow { color: #5b6781; font-size: .85rem; font-weight: 600; }
        .news-article { overflow: hidden; border: 1px solid #dfe6f2; border-radius: 12px; background: #fff; }
        .news-article__image { display: block; width: 100%; max-height: 520px; aspect-ratio: 16 / 8; object-fit: cover; background: #edf2f8; }
        .news-article__content { max-width: 780px; margin: 0 auto; padding: 36px 30px 44px; }
        .news-article time { color: #5b6781; font-size: .9rem; }
        .news-article h1 { margin: 12px 0 16px; color: #0b2e6f; font-family: 'Bricolage Grotesque', sans-serif; font-size: clamp(2rem, 4vw, 3rem); font-weight: 700; line-height: 1.12; }
        .news-article__excerpt { margin: 0 0 24px; color: #33415c; font-size: 1.15rem; font-weight: 500; line-height: 1.65; }
        .news-article__body { color: #27344d; font-size: 1rem; line-height: 1.85; overflow-wrap: anywhere; }
        .news-article__return { display: inline-flex; align-items: center; gap: 9px; margin-top: 32px; }
        @media (max-width: 640px) {
            .news-page { padding: 28px 16px 48px; }
            .news-article__content { padding: 26px 20px 32px; }
            .news-article__image { aspect-ratio: 4 / 3; }
        }
    </style>
@endpush