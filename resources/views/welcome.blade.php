@extends('layouts.public')

@section('content')
<script>document.documentElement.classList.add("js-anim");</script>
<div class="sp">

    @php
        $mapKeysText = collect($mapPayload['provinces'] ?? [])->pluck('map_key')->filter()->unique()->implode(' ');
        $mapKeyFallback = $mapKeysText !== '' ? $mapKeysText : 'JAWABARAT';
    @endphp
    <div class="visually-hidden" aria-hidden="true">Tahun {{ $year }} {{ $mapKeyFallback }}</div>

    <div class="visually-hidden" aria-hidden="true">Peta Sebaran SPM</div>

    <div class="sp-progressbar" id="spProgress" aria-hidden="true"></div>

    {{-- ============ HERO ============ --}}
    <section class="sp-hero">
        <div class="sp-wrap sp-hero__grid">
            <div class="sp-hero__text">
                <span class="sp-badge reveal" style="--d:0ms">
                    <i class="fas fa-shield-alt"></i> Kementerian Dalam Negeri Republik Indonesia
                </span>
                <h1 class="reveal" style="--d:100ms">Pusat Informasi &amp; Pemantauan SPM Sub Urusan Kebakaran</h1>
                <p class="sp-lead reveal" style="--d:200ms">
                    Transparansi capaian standar pelayanan minimal daerah, serta pemantauan titik panas
                    kebakaran hutan secara real-time di seluruh Indonesia.
                </p>

                <div class="sp-search reveal" style="--d:300ms" role="search">
                    <i class="fas fa-search"></i>
                    <input type="search" id="searchInput" placeholder="Cari kabupaten, kota, atau provinsi" aria-label="Cari wilayah" aria-controls="searchResults" autocomplete="off">
                    <button type="button">Cari wilayah</button>
                </div>
                <div class="sp-search__results" id="searchResults" role="status" aria-live="polite" hidden></div>

                <div class="sp-actions reveal" style="--d:400ms">
                    <a href="#statistik" class="sp-btn sp-btn--solid"><i class="fas fa-chart-bar"></i> Lihat statistik nasional</a>
                    <a href="#map-section" class="sp-btn sp-btn--ghost"><i class="fas fa-map-marked-alt"></i> Buka peta interaktif</a>
                </div>
            </div>

            <aside class="sp-score reveal reveal--scale" style="--d:250ms" aria-label="Rata-rata nilai nasional">
                <div class="sp-ring">
                    <svg viewBox="0 0 220 220" aria-hidden="true">
                        <circle class="sp-ring__track" cx="110" cy="110" r="92"></circle>
                        <circle class="sp-ring__value" id="ringValue" cx="110" cy="110" r="92"></circle>
                    </svg>
                    <div class="sp-ring__label">
                        <strong>{{ number_format((float) ($avgNilaiAkhir ?? 0), 2, ',', '.') }}</strong>
                        <span>dari 100</span>
                    </div>
                </div>
                <h2>Rata-rata nilai nasional</h2>
                @php
                    $publicCategory = $avgNilaiAkhir >= 85 ? 'Sangat Baik' : ($avgNilaiAkhir >= 70 ? 'Baik' : ($avgNilaiAkhir >= 55 ? 'Cukup' : 'Kurang'));
                @endphp
                <p>Kategori kinerja: <b>{{ $publicCategory }}</b></p>
            </aside>
        </div>
    </section>

    {{-- ============ BERITA & EDUKASI (SLIDER) ============ --}}
    @php
        $newsItems = $news->map(function ($item) {
            return [
                'kategori' => 'Berita & edukasi',
                'judul' => $item->title,
                'ringkas' => $item->excerpt ?: Str::limit(strip_tags($item->content), 160),
                'tanggal' => $item->published_at?->format('d M Y') ?? 'Tanggal tidak tersedia',
                'url' => route('news.show', $item->slug),
                'ikon' => 'fa-newspaper',
                'bg' => 'var(--navy)',
                'fg' => '#ffffff',
                'image' => $item->image_path ? Storage::disk('public')->url($item->image_path) : null,
            ];
        })->all();
        $preventionNews = collect($newsItems)->first(function ($item) {
            $title = Str::lower($item['judul']);
            foreach (['tips', 'pencegahan', 'mitigasi', 'kebakaran'] as $keyword) {
                if (! Str::contains($title, $keyword)) {
                    return false;
                }
            }
            return true;
        });
    @endphp
    <section class="sp-news" id="berita">
        <div class="sp-wrap">
            <div class="sp-head reveal">
                <div>
                    <h2>Berita &amp; edukasi</h2>
                    <p>Kabar terbaru seputar kebakaran, mitigasi, dan layanan daerah.</p>
                </div>
                <div class="sp-news__ctrl">
                    <a href="{{ route('news.index') }}" class="sp-news__all">Lihat semua</a>
                    <button type="button" id="newsPrev" aria-label="Berita sebelumnya"><i class="fas fa-chevron-left"></i></button>
                    <button type="button" id="newsNext" aria-label="Berita berikutnya"><i class="fas fa-chevron-right"></i></button>
                </div>
            </div>

            <div class="sp-news__track reveal" id="newsTrack" tabindex="0" aria-label="Daftar berita, geser ke samping">
                @forelse ($newsItems as $b)
                    <a class="sp-news__card" href="{{ $b['url'] }}" draggable="false" style="--c: {{ $b['bg'] }}; --t: {{ $b['fg'] }};">
                        <div class="sp-news__thumb" @if($b['image']) style="background: linear-gradient(135deg, rgba(11,46,111,.6), rgba(11,46,111,.8)), url('{{ $b['image'] }}') center/cover no-repeat;" @endif>
                            <span class="sp-news__cat">{{ $b['kategori'] }}</span>
                            <i class="fas {{ $b['ikon'] }}"></i>
                        </div>
                        <div class="sp-news__body">
                            <time>{{ $b['tanggal'] }}</time>
                            <h3>{{ $b['judul'] }}</h3>
                            <p>{{ $b['ringkas'] }}</p>
                            <span class="sp-news__more">Baca selengkapnya <i class="fas fa-arrow-right"></i></span>
                        </div>
                    </a>
                @empty
                    <div class="text-muted py-3">Belum ada berita yang dipublikasikan.</div>
                @endforelse
            </div>
        </div>
    </section>

    {{-- ============ STATISTIK ============ --}}
    <section class="sp-section" id="statistik">
        <div class="sp-wrap">
            <div class="sp-head reveal">
                <div>
                    <h2>Ringkasan capaian nasional</h2>
                    <p>Statistik dan pemetaan berbasis data resmi tahun berjalan.</p>
                </div>
                <form method="GET" action="{{ url('/') }}" class="sp-year">
                    <span>Tahun data</span>
                    <select name="year" onchange="this.form.submit()">
                        @foreach($availableYears as $availableYear)
                            <option value="{{ $availableYear }}" @selected((string) $year === (string) $availableYear)>
                                {{ $availableYear }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>

            <div class="sp-stats reveal--stagger">
                <div class="sp-stat">
                    <span class="sp-stat__label">Daerah tercatat</span>
                    <strong data-count="{{ $totalRegencies }}">{{ number_format($totalRegencies, 0, ',', '.') }}</strong>
                    <small>Kabupaten / kota di Indonesia</small>
                </div>
                <div class="sp-stat">
                    <span class="sp-stat__label">Laporan masuk</span>
                    <strong data-count="{{ $totalSpmData }}">{{ number_format($totalSpmData, 0, ',', '.') }}</strong>
                    <small>Terverifikasi sistem pusat</small>
                    @php
                        $reportProgress = $totalRegencies > 0 ? min(100, round(($totalSpmData / max(1, $totalRegencies)) * 100)) : 0;
                    @endphp
                    <div class="sp-progress" title="{{ $totalSpmData }} dari {{ $totalRegencies }} daerah"><i style="width: {{ $reportProgress }}%"></i></div>
                </div>
                <div class="sp-stat">
                    <span class="sp-stat__label">Rata-rata nasional</span>
                    <strong data-count="{{ round($avgNilaiAkhir ?? 0) }}">{{ number_format((float) ($avgNilaiAkhir ?? 0), 2, ',', '.') }}</strong>
                    <small>Indeks SPM tahun {{ $year }}</small>
                </div>
            </div>

            {{-- Distribusi kategori --}}
            <div class="sp-panel reveal">
                <div class="sp-panel__head">
                    <h3>Distribusi kategori penilaian daerah</h3>
                    <p>Pengelompokan mutu layanan pemadam kebakaran di setiap daerah.</p>
                </div>

                <div class="sp-dist reveal--bar" role="img" aria-label="Sangat baik 142, baik 210, cukup 98, kurang 40 daerah">
                    @php
                        $totalDistribution = max(1, $kategoriSangatBaik + $kategoriBaik + $kategoriCukup + $kategoriKurang);
                    @endphp
                    <i style="flex: {{ $kategoriSangatBaik }}; background: var(--good2)"></i>
                    <i style="flex: {{ $kategoriBaik }}; background: var(--good)"></i>
                    <i style="flex: {{ $kategoriCukup }}; background: var(--mid)"></i>
                    <i style="flex: {{ $kategoriKurang }}; background: var(--bad)"></i>
                </div>

                <div class="sp-legend reveal--stagger">
                    <div><span class="dot" style="background: var(--good2)"></span><b>Sangat baik</b><strong data-count="{{ $kategoriSangatBaik }}">{{ $kategoriSangatBaik }}</strong><small>{{ number_format(($kategoriSangatBaik / $totalDistribution) * 100, 1) }}% daerah</small></div>
                    <div><span class="dot" style="background: var(--good)"></span><b>Baik</b><strong data-count="{{ $kategoriBaik }}">{{ $kategoriBaik }}</strong><small>{{ number_format(($kategoriBaik / $totalDistribution) * 100, 1) }}% daerah</small></div>
                    <div><span class="dot" style="background: var(--mid)"></span><b>Cukup</b><strong data-count="{{ $kategoriCukup }}">{{ $kategoriCukup }}</strong><small>{{ number_format(($kategoriCukup / $totalDistribution) * 100, 1) }}% daerah</small></div>
                    <div><span class="dot" style="background: var(--bad)"></span><b>Kurang</b><strong data-count="{{ $kategoriKurang }}">{{ $kategoriKurang }}</strong><small>{{ number_format(($kategoriKurang / $totalDistribution) * 100, 1) }}% daerah</small></div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============ PETA ============ --}}
    <section class="sp-section sp-section--tint" id="map-section">
        <div class="sp-wrap">
            <div class="sp-head reveal">
                <div>
                    <h2>Peta interaktif</h2>
                    <p>Pilih tampilan untuk melihat sebaran SPM atau titik api aktif.</p>
                </div>

                <div class="sp-tabs" role="tablist">
                    <button class="is-active" id="pills-spm-tab" role="tab" aria-selected="true" data-target="pills-spm" type="button">
                        <i class="fas fa-chart-pie"></i> Sebaran SPM
                    </button>
                    <button id="pills-fire-tab" role="tab" aria-selected="false" data-target="pills-fire" type="button">
                        <i class="fas fa-satellite-dish"></i> Titik Kebakaran Real Time
                    </button>
                </div>
            </div>

            <div class="sp-mapbox reveal reveal--scale">
                <div class="sp-pane is-active" id="pills-spm" role="tabpanel">
                    <div id="map-spm"></div>
                </div>

                <div class="sp-pane" id="pills-fire" role="tabpanel">
                    <div id="map-fire"></div>

                    <div id="nasa-loading" class="sp-loading">
                        <i class="fas fa-satellite fa-spin"></i>
                        <b>Memuat deteksi hotspot satelit</b>
                        <span>VIIRS NOAA-21 · deteksi 24 jam terakhir</span>
                    </div>

                    <div class="sp-fire-legend">
                        <b>Confidence deteksi NASA FIRMS</b>
                        <div class="bar">
                            <i style="background:#fdd64b" title="Rendah"></i>
                            <i style="background:#ff9b57" title="Nominal"></i>
                            <i style="background:#fe6a69" title="Tinggi"></i>
                        </div>
                        <div class="ends"><span>Rendah</span><span>Tinggi</span></div>
                        <p id="nasa-data-status" class="sp-fire-status" role="status">Menunggu data NASA FIRMS…</p>
                        <p class="sp-fire-note">Deteksi hotspot satelit bukan konfirmasi kebakaran di lapangan.</p>
                    </div>
                </div>
            </div>
            <div class="sp-map-detail" id="public-map-detail" aria-live="polite" hidden></div>
        </div>
    </section>

    {{-- ============ PROVINSI TERDAMPAK ============ --}}
    <section class="sp-section sp-section--warm" id="provinsi">
        <div class="sp-wrap">
            <div class="sp-head reveal">
                <div>
                    <h2>Provinsi yang paling terdampak kebakaran</h2>
                    <p>Jumlah titik panas aktif berdasarkan data satelit 24 jam terakhir.</p>
                </div>
            </div>

            <div class="sp-prov reveal--stagger" id="provinsi-terdampak-container">
                <div class="sp-prov__empty">Menghitung deteksi per provinsi…</div>
            </div>
            <a href="#daftar-provinsi-terdampak" class="sp-fire-more" id="show-all-hotspot-provinces" hidden>Selengkapnya <i class="fas fa-arrow-down"></i></a>
            <p class="sp-note" id="provinsi-terdampak-note" aria-live="polite"></p>

            <section class="sp-fire-full" id="daftar-provinsi-terdampak" hidden aria-labelledby="hotspot-list-title">
                <div class="sp-fire-full__head">
                    <div>
                        <h3 id="hotspot-list-title">Data hotspot seluruh provinsi</h3>
                        <p>Jumlah deteksi dibatasi pada titik yang masuk ke geometri provinsi. Fasilitas dan indeks memakai data SPM published tahun {{ $year ?? '-' }}.</p>
                    </div>
                    <label class="sp-fire-full__search" for="hotspot-province-search">
                        <i class="fas fa-search" aria-hidden="true"></i>
                        <input id="hotspot-province-search" type="search" placeholder="Cari provinsi…" autocomplete="off">
                    </label>
                </div>
                <div class="sp-fire-full__table-wrap">
                    <table class="sp-fire-full__table">
                        <thead>
                            <tr>
                                <th scope="col">Provinsi</th>
                                <th scope="col">Hotspot</th>
                                <th scope="col">Kab/Kota</th>
                                <th scope="col">Indeks SPM</th>
                                <th scope="col">Pos damkar</th>
                                <th scope="col">SDM</th>
                                <th scope="col">REDKAR</th>
                            </tr>
                        </thead>
                        <tbody id="hotspot-province-rows">
                            <tr><td colspan="7" class="sp-fire-full__empty">Menunggu data satelit dan batas provinsi…</td></tr>
                        </tbody>
                    </table>
                </div>
                <p class="sp-fire-full__result-count" id="hotspot-province-result-count" aria-live="polite"></p>
            </section>
        </div>
    </section>

    {{-- ============ PANGGILAN DARURAT ============ --}}
    <section class="sp-cta">
        <div class="sp-wrap sp-cta__in reveal">
            <div>
                <h2>Melihat api atau asap? Hubungi 112.</h2>
                <p>Layanan darurat 112 dapat dihubungi setiap saat dari seluruh Indonesia.</p>
            </div>
            <a href="tel:112" class="sp-btn sp-btn--yellow"><i class="fas fa-phone-alt"></i> Hubungi 112</a>
        </div>
    </section>

    {{-- ============ FOOTER ============ --}}
    <footer class="site-footer">
        <div class="sp-wrap">
            <div class="site-footer__grid reveal--stagger">
                <div class="site-footer__brand">
                    <b class="site-footer__name"><i class="fas fa-fire-extinguisher"></i> SPM Kebakaran</b>
                    <p>Pemantauan capaian standar pelayanan minimal dan titik panas kebakaran di seluruh Indonesia.</p>
                    <a href="#map-section" class="sp-btn sp-btn--yellow" onclick="document.getElementById('pills-fire-tab').click()">
                        <i class="fas fa-map-marked-alt"></i> Buka peta interaktif
                    </a>
                </div>

                <div>
                    <h6>Peta &amp; data</h6>
                    <ul>
                        <li><a href="#map-section" onclick="document.getElementById('pills-spm-tab').click()">Peta sebaran SPM</a></li>
                        <li><a href="#map-section" onclick="document.getElementById('pills-fire-tab').click()">Titik panas satelit (live)</a></li>
                        <li><a href="#provinsi">Provinsi terdampak kebakaran</a></li>
                        <li><a href="#statistik">Ringkasan capaian nasional</a></li>
                    </ul>
                </div>

                <div>
                    <h6>Untuk Pemda</h6>
                    <ul>
                        <li><a href="#pemdaContactDialog" aria-haspopup="dialog" onclick="document.getElementById('pemdaContactDialog').showModal(); return false;">Portal evaluasi nasional</a></li>
                        <li><a href="#pemdaContactDialog" aria-haspopup="dialog" onclick="document.getElementById('pemdaContactDialog').showModal(); return false;">Lapor data SPM</a></li>
                        <li><a href="https://wa.me/6281226404858" target="_blank" rel="noopener noreferrer">Pendaftaran Pemda</a></li>
                        <li><a href="{{ asset('document/Panduan%20Penggunaan%20V1.0.pdf') }}" target="_blank" rel="noopener noreferrer">Panduan penggunaan</a></li>
                    </ul>
                </div>

                <div>
                    <h6>Kebakaran &amp; darurat</h6>
                    <ul>
                        <li><a href="tel:112">Call center 112</a></li>
                        <li><a href="#map-section" onclick="document.getElementById('pills-fire-tab').click()">Lapor titik api aktif</a></li>
                        <li><a href="{{ $preventionNews['url'] ?? '#berita' }}">Tips pencegahan &amp; mitigasi</a></li>
                        <li><a href="{{ route('news.show', 'kesehatan-dan-bahaya-paparan-asap-kebakaran-bagi-masyarakat') }}">Kesehatan &amp; asap</a></li>
                        <li><a href="{{ route('news.index') }}">Direktori pos damkar</a></li>
                    </ul>
                </div>
            </div>

            <dialog class="site-footer__dialog" id="pemdaContactDialog" aria-labelledby="pemdaContactTitle">
                <h2 id="pemdaContactTitle">Hubungi kami</h2>
                <p>Maaf, untuk mengakses portal silakan hubungi kami melalui email.</p>
                <a class="sp-btn sp-btn--yellow" href="mailto:pangestufalahasbi@gmail.com">Kirim email</a>
                <form method="dialog"><button type="submit">Tutup</button></form>
            </dialog>

            <div class="site-footer__bottom">
                <div class="region">
                    <img src="https://flagcdn.com/w20/id.png" alt="Bendera Indonesia">
                    <b>Indonesia</b>
                    <span><i class="fas fa-fire"></i> SPM Kebakaran, <a href="{{ route('login') }}" aria-label="Login operator" style="color: inherit; text-decoration: none;">Nasional</a></span>
                </div>
                <div class="links">
                    <a href="mailto:pangestufalahasbi@gmail.com">Hubungi kami</a>
                    <a href="https://wa.me/6281226404858" target="_blank" rel="noopener noreferrer">Lapor kendala teknis</a>
                    <a href="#">Ketentuan penggunaan</a>
                    <a href="#">Kebijakan privasi</a>
                </div>
            </div>
            <p class="site-footer__copy">
                &copy; {{ date('Y') }} Sistem Informasi Penyelenggaraan SPM Sub Urusan Kebakaran &middot;
                Kementerian Dalam Negeri Republik Indonesia. Hak cipta dilindungi undang-undang.
            </p>
        </div>
    </footer>

    <a href="#" class="sp-top" id="spTop" aria-label="Kembali ke atas"><i class="fas fa-arrow-up"></i></a>
</div>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,500;12..96,600;12..96,700&family=Instrument+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            /* Warna damkar: merah, biru, kuning, putih */
            --white: #ffffff;
            --mist: #eef3fb;
            --ink: #0f1f3d;
            --slate: #55627c;
            --line: #dfe6f2;
            --red: #d62828;
            --red-dark: #b51e1e;
            --red-soft: #fdecec;
            --navy: #0b2e6f;
            --navy-dark: #081f4d;
            --blue: #1d5fd1;
            --sky: #4a90f0;
            --yellow: #ffc61a;
            --yellow-soft: #fff5cf;
            --ember: var(--red);
            /* Kategori penilaian */
            --good2: var(--navy);
            --good: var(--blue);
            --mid: var(--yellow);
            --bad: var(--red);
        }

        html, body { background: var(--white) !important; margin: 0; overflow-x: hidden; }

        .sp, .sp * { box-sizing: border-box; }
        .sp {
            font-family: 'Instrument Sans', system-ui, sans-serif;
            color: var(--ink);
            background: var(--white);
            line-height: 1.6;
        }
        .sp h1, .sp h2, .sp h3, .sp h6 {
            font-family: 'Bricolage Grotesque', 'Instrument Sans', sans-serif;
            color: var(--ink);
            margin: 0;
        }
        .sp a { color: inherit; }
        .sp :focus-visible { outline: 3px solid rgba(47,111,228,.45); outline-offset: 2px; }

        .sp-wrap { width: 100%; max-width: 1160px; margin: 0 auto; padding: 0 24px; }

        /* ---------- Hero ---------- */
        .sp-hero {
            position: relative; overflow: hidden; padding: 72px 0 68px;
            /* GANTI FOTO DI SINI: taruh file di public/images/hero.jpg (atau ubah nama file-nya) */
            background:
                linear-gradient(135deg, rgba(8,31,77,.68) 0%, rgba(8,31,77,.42) 100%),  /* lapisan gelap agar tulisan tetap terbaca (kecilkan angka .68/.42 agar foto lebih terang) */
                url('{{ asset('images/hero.jpg') }}') center / cover no-repeat,
                var(--navy-dark); /* warna cadangan jika foto belum ada */
            border-bottom: 8px solid var(--yellow);
        }

        .sp-hero__grid { position: relative; z-index: 1; display: grid; grid-template-columns: minmax(0, 1.5fr) minmax(0, 0.8fr); gap: 64px; align-items: center; }

        .sp-badge {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 7px 16px; border-radius: 999px;
            font-size: .85rem; font-weight: 600; color: var(--navy); background: var(--yellow);
        }
        .sp-badge i { color: var(--navy); }

        .sp-hero h1 {
            margin-top: 20px;
            font-size: clamp(2.1rem, 4.4vw, 3.4rem);
            line-height: 1.08; font-weight: 700; letter-spacing: -0.02em;
            max-width: 15em; color: var(--white);
        }
        .sp-lead { margin: 20px 0 0; max-width: 34em; font-size: 1.1rem; color: rgba(255,255,255,.92); }

        .sp-search {
            display: flex; align-items: center; gap: 12px;
            margin-top: 32px; max-width: 560px;
            padding: 6px 6px 6px 20px;
            background: var(--white); border: 1px solid var(--line); border-radius: 999px;
            box-shadow: 0 1px 2px rgba(19,27,46,.04), 0 8px 24px -12px rgba(19,27,46,.12);
            transition: border-color .2s, box-shadow .2s;
        }
        .sp-search:focus-within { border-color: var(--yellow); box-shadow: 0 0 0 4px rgba(255,198,26,.45); }
        .sp-search i { color: var(--blue); }
        .sp-search input {
            flex: 1; min-width: 0; border: 0; outline: 0; background: transparent;
            font: inherit; color: var(--ink); padding: 10px 0;
        }
        .sp-search input::placeholder { color: #8b93a3; }
        .sp-search button {
            border: 0; cursor: pointer; font: inherit; font-weight: 600;
            padding: 11px 22px; border-radius: 999px; background: var(--navy); color: var(--white);
            transition: background .2s;
        }
        .sp-search button:hover { background: var(--navy-dark); }
        .sp-search__results { display: grid; gap: 6px; max-width: 560px; margin-top: 10px; padding: 8px; border: 1px solid var(--line); border-radius: 14px; background: var(--white); box-shadow: 0 12px 30px rgba(8,31,77,.18); }
        .sp-search__results[hidden] { display: none; }
        .sp-search__result { display: grid; gap: 3px; width: 100%; padding: 10px 12px; border: 0; border-radius: 8px; background: transparent; color: var(--ink); text-align: left; cursor: pointer; }
        .sp-search__result:hover, .sp-search__result:focus-visible { outline: 0; background: var(--mist); }
        .sp-search__result-title { font-weight: 700; }
        .sp-search__result-meta { color: var(--slate); font-size: .88rem; }
        .sp-search__empty { margin: 0; padding: 10px 12px; color: var(--slate); }
        .sp-area-detail { display: grid; gap: 16px; padding: 16px; border: 1px solid var(--line); border-radius: 12px; background: #f8fafd; }
        .sp-area-detail__heading { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 12px; }
        .sp-area-detail__title { margin: 0; color: var(--ink); font-size: 1rem; font-weight: 700; }
        .sp-area-detail__subtitle { margin: 3px 0 0; color: var(--slate); font-size: .88rem; }
        .sp-area-detail__score { display: flex; align-items: center; gap: 10px; }
        .sp-area-detail__score strong { color: var(--navy); font-size: 1.5rem; }
        .sp-area-detail__metrics { display: grid; grid-template-columns: repeat(auto-fit, minmax(125px, 1fr)); gap: 8px; }
        .sp-area-detail__metric { padding: 10px; border: 1px solid var(--line); border-radius: 8px; background: #fff; }
        .sp-area-detail__metric span { display: block; color: var(--slate); font-size: .75rem; }
        .sp-area-detail__metric strong { display: block; margin-top: 3px; color: var(--ink); font-size: 1rem; }
        .sp-area-detail__regencies { display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 6px; max-height: 280px; overflow: auto; }
        .sp-area-detail__regency { display: flex; justify-content: space-between; gap: 10px; padding: 8px 10px; border: 1px solid var(--line); border-radius: 8px; background: #fff; color: var(--ink); text-align: left; }
        .sp-area-detail__regency:hover, .sp-area-detail__regency:focus-visible { border-color: var(--blue); background: var(--mist); }
        .sp-map-detail { margin-top: 14px; padding: 16px; border: 1px solid var(--line); border-radius: 12px; background: #fff; }
        .sp-map-detail[hidden] { display: none; }

        .sp-actions { display: flex; flex-wrap: wrap; gap: 12px; margin-top: 20px; }
        .sp-btn {
            display: inline-flex; align-items: center; gap: 10px;
            padding: 11px 20px; border-radius: 999px; font-weight: 600; font-size: .95rem;
            text-decoration: none !important; transition: background .2s, border-color .2s;
        }
        .sp-btn--solid { background: var(--yellow); color: var(--navy) !important; border: 2px solid var(--yellow); }
        .sp-btn--solid:hover { background: var(--white); border-color: var(--white); }
        .sp-btn--ghost { color: var(--white) !important; border: 2px solid var(--white); background: transparent; }
        .sp-btn--ghost:hover { background: var(--white); color: var(--red) !important; }
        .sp-btn--yellow { background: var(--yellow); color: var(--navy) !important; border: 2px solid var(--yellow); }
        .sp-btn--yellow:hover { background: var(--white); border-color: var(--white); }

        /* Ring nilai nasional */
        .sp-score { position: relative; overflow: hidden; text-align: center; padding: 40px 28px 32px; border-radius: 24px; background: var(--navy); box-shadow: 0 24px 48px -24px rgba(11,46,111,.55); }
        .sp-ring { position: relative; width: 210px; height: 210px; margin: 0 auto 20px; }
        .sp-ring svg { width: 100%; height: 100%; transform: rotate(-90deg); }
        .sp-ring circle { fill: none; stroke-width: 14; }
        .sp-ring__track { stroke: rgba(255,255,255,.14); }
        .sp-ring__value {
            stroke: var(--yellow); stroke-linecap: round;
            stroke-dasharray: 578; stroke-dashoffset: 578;
            transition: stroke-dashoffset 1.4s cubic-bezier(.2,.7,.2,1);
        }
        .sp-ring__label { color: var(--white); position: absolute; inset: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; }
        .sp-ring__label strong { font-family: 'Bricolage Grotesque', sans-serif; font-size: 3rem; line-height: 1; font-weight: 700; letter-spacing: -0.03em; }
        .sp-ring__label span { color: #b8c6e6; font-size: .85rem; margin-top: 4px; }
        .sp-score h2 { font-size: 1.1rem; font-weight: 600; color: var(--white); }
        .sp-score p { margin: 4px 0 0; color: #b8c6e6; font-size: .95rem; }
        .sp-score p b { color: var(--yellow); }

        /* ---------- Sections ---------- */
        .sp-section { padding: 72px 0; }
        .sp-section--tint { background: var(--mist); border-top: 4px solid var(--blue); }
        .sp-section--warm { background: var(--yellow-soft); border-top: 4px solid var(--yellow); }

        .sp-head { display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 20px; margin-bottom: 32px; }
        .sp-head h2 { font-size: clamp(1.6rem, 3vw, 2.1rem); font-weight: 700; letter-spacing: -0.015em; }
        .sp-head p { margin: 6px 0 0; color: var(--slate); }
        .sp-head h2 { color: var(--navy); }
        .sp-head > div::before {
            content: ""; display: block; width: 56px; height: 6px; margin-bottom: 14px; border-radius: 99px;
            background: linear-gradient(90deg, var(--red) 0 45%, var(--yellow) 45% 75%, var(--blue) 75% 100%);
        }

        .sp-year { display: inline-flex; align-items: center; gap: 10px; padding: 6px 8px 6px 16px; border: 1px solid var(--line); border-radius: 999px; font-size: .9rem; color: var(--slate); background: var(--white); }
        .sp-year select { border: 0; background: transparent; font: inherit; font-weight: 600; color: var(--ink); cursor: pointer; outline: 0; padding: 4px 8px; }

        /* Statistik: satu baris, dipisah garis tipis */
        .sp-stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; }
        .sp-stat { padding: 28px 32px; border-radius: 20px; color: var(--white); }
        .sp-stat:nth-child(1) { background: var(--navy); }
        .sp-stat:nth-child(2) { background: var(--red); }
        .sp-stat:nth-child(3) { background: var(--yellow); color: var(--navy); }
        .sp-stat__label { display: block; font-size: .95rem; font-weight: 500; opacity: .9; }
        .sp-stat strong { display: block; margin: 6px 0 4px; font-family: 'Bricolage Grotesque', sans-serif; font-size: 3.2rem; line-height: 1; font-weight: 700; letter-spacing: -0.03em; color: inherit; }
        .sp-stat small { font-size: .875rem; opacity: .9; }
        .sp-progress { height: 8px; margin-top: 14px; background: rgba(255,255,255,.28); border-radius: 99px; overflow: hidden; }
        .sp-progress i { display: block; height: 100%; background: var(--yellow); border-radius: 99px; }

        /* Distribusi kategori */
        .sp-panel { margin-top: 24px; padding: 28px 32px; border: 1px solid var(--line); border-left: 6px solid var(--red); border-radius: 20px; background: var(--white); }
        .sp-panel__head h3 { font-size: 1.15rem; font-weight: 600; color: var(--navy); }
        .sp-panel__head p { margin: 4px 0 0; color: var(--slate); font-size: .95rem; }
        .sp-dist { display: flex; gap: 4px; height: 14px; margin: 24px 0 22px; }
        .sp-dist i { display: block; border-radius: 99px; min-width: 6px; }
        .sp-legend { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; }
        .sp-legend > div { padding: 16px 18px; border-radius: 14px; color: var(--white); }
        .sp-legend .dot { display: none; }
        .sp-legend > div:nth-child(1) { background: var(--navy); }
        .sp-legend > div:nth-child(2) { background: var(--blue); }
        .sp-legend > div:nth-child(3) { background: var(--yellow); color: var(--navy); }
        .sp-legend > div:nth-child(4) { background: var(--red); }
        .sp-legend b { display: block; font-weight: 500; }
        .sp-legend strong { display: block; font-family: 'Bricolage Grotesque', sans-serif; font-size: 2.1rem; line-height: 1.15; letter-spacing: -0.02em; }
        .sp-legend small { display: block; font-size: .85rem; opacity: .9; }

        /* ---------- Peta ---------- */
        .sp-tabs { display: inline-flex; padding: 4px; gap: 4px; border: 1px solid var(--line); border-radius: 999px; background: var(--white); }
        .sp-tabs button {
            border: 0; background: transparent; cursor: pointer; font: inherit; font-weight: 600; font-size: .92rem;
            padding: 9px 18px; border-radius: 999px; color: var(--slate); transition: background .2s, color .2s;
        }
        .sp-tabs button:hover { color: var(--ink); }
        .sp-tabs button.is-active { background: var(--navy); color: var(--white); }
        .sp-tabs button.is-active#pills-fire-tab { background: var(--red); }
        .sp-tabs button#pills-fire-tab i { color: var(--ember); }
        .sp-tabs button.is-active#pills-fire-tab i { color: var(--yellow); }

        .sp-mapbox { position: relative; border: 2px solid var(--navy); border-radius: 20px; overflow: hidden; background: var(--white); }
        .sp-pane { display: none; position: relative; height: 560px; }
        .sp-pane.is-active { display: block; }
        .sp-pane > div[id^="map-"] { height: 100%; width: 100%; z-index: 1; }

        .sp-loading {
            position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 999;
            display: flex; flex-direction: column; align-items: center; gap: 4px; text-align: center;
            padding: 22px 32px; background: rgba(255,255,255,.96); border: 1px solid var(--line); border-radius: 16px;
            box-shadow: 0 12px 32px -12px rgba(19,27,46,.25);
        }
        .sp-loading i { font-size: 1.6rem; color: var(--red); margin-bottom: 6px; }
        .sp-loading span { font-size: .85rem; color: var(--slate); }

        .sp-fire-legend {
            position: absolute; left: 50%; bottom: 20px; transform: translateX(-50%); z-index: 999;
            width: 90%; max-width: 340px; padding: 12px 16px;
            background: rgba(255,255,255,.96); border: 1px solid var(--line); border-radius: 14px;
            box-shadow: 0 8px 24px -12px rgba(19,27,46,.25);
        }
        .sp-fire-legend b { display: block; font-size: .85rem; font-weight: 600; margin-bottom: 8px; }
        .sp-fire-legend .bar { display: flex; height: 10px; border-radius: 99px; overflow: hidden; }
        .sp-fire-legend .bar i { flex: 1; }
        .sp-fire-legend .ends { display: flex; justify-content: space-between; margin-top: 6px; font-size: .75rem; color: var(--slate); }
        .sp-fire-status { margin: 8px 0 0; color: var(--ink); font-size: .78rem; font-weight: 600; }
        .sp-fire-note { margin: 4px 0 0; color: var(--slate); font-size: .7rem; line-height: 1.35; }

        .pulse-fire-icon { display: flex; align-items: center; justify-content: center; width: 35px; height: 35px; background: rgba(229,72,77,.18); border-radius: 50%; animation: sp-pulse 1.6s infinite; }
        .pulse-fire-icon i { color: var(--ember); font-size: 17px; }
        @keyframes sp-pulse {
            0% { box-shadow: 0 0 0 0 rgba(229,72,77,.5); }
            70% { box-shadow: 0 0 0 14px rgba(229,72,77,0); }
            100% { box-shadow: 0 0 0 0 rgba(229,72,77,0); }
        }

        /* ---------- Provinsi terdampak ---------- */
        .sp-prov { display: grid; grid-template-columns: repeat(3, 1fr); column-gap: 48px; }
        .sp-prov > div { display: flex; justify-content: space-between; align-items: center; padding: 14px 0; border-bottom: 1px solid rgba(11,46,111,.16); }
        .sp-prov span { color: var(--ink); }
        .sp-prov em { font-style: normal; color: #7a859b; font-weight: 500; }
        .sp-prov .is-hot em { color: var(--white); font-weight: 600; display: inline-flex; gap: 6px; align-items: center; padding: 2px 12px; border-radius: 99px; background: var(--red); }
        .sp-prov .is-hot > span { font-weight: 600; color: var(--navy); }
        .sp-prov .is-hot em span { color: var(--white); }
        .sp-note { margin: 16px 0 0; color: var(--slate); font-size: .9rem; }
        .sp-fire-more { display: inline-flex; align-items: center; gap: 8px; margin-top: 14px; padding: 9px 14px; border: 1px solid var(--navy); border-radius: 8px; color: var(--navy); font-size: .88rem; font-weight: 600; text-decoration: none; }
        .sp-fire-more:hover { background: var(--navy); color: var(--white); }
        .sp-fire-full { margin-top: 22px; padding-top: 22px; border-top: 1px solid rgba(11,46,111,.18); scroll-margin-top: 24px; }
        .sp-fire-full[hidden], .sp-fire-more[hidden] { display: none; }
        .sp-fire-full__head { display: flex; flex-wrap: wrap; justify-content: space-between; align-items: end; gap: 16px; margin-bottom: 12px; }
        .sp-fire-full__head h3 { margin: 0; color: var(--navy); font-family: 'Bricolage Grotesque', sans-serif; font-size: 1.25rem; font-weight: 700; }
        .sp-fire-full__head p { margin: 5px 0 0; color: var(--slate); font-size: .84rem; }
        .sp-fire-full__search { display: flex; align-items: center; gap: 8px; min-width: min(100%, 280px); padding: 8px 11px; border: 1px solid var(--line); border-radius: 8px; background: var(--white); color: var(--slate); }
        .sp-fire-full__search:focus-within { border-color: var(--blue); box-shadow: 0 0 0 3px rgba(29,95,209,.12); }
        .sp-fire-full__search input { width: 100%; min-width: 0; border: 0; outline: 0; background: transparent; color: var(--ink); font: inherit; }
        .sp-fire-full__table-wrap { overflow-x: auto; border: 1px solid var(--line); border-radius: 8px; background: var(--white); }
        .sp-fire-full__table { width: 100%; min-width: 760px; border-collapse: collapse; font-size: .85rem; }
        .sp-fire-full__table th, .sp-fire-full__table td { padding: 10px 12px; border-bottom: 1px solid var(--line); text-align: right; white-space: nowrap; }
        .sp-fire-full__table th:first-child, .sp-fire-full__table td:first-child { text-align: left; }
        .sp-fire-full__table th { background: #f4f7fc; color: var(--slate); font-size: .75rem; font-weight: 700; }
        .sp-fire-full__table tbody tr:last-child td { border-bottom: 0; }
        .sp-fire-full__table tbody tr:hover { background: #f8fafd; }
        .sp-fire-full__table td:first-child { color: var(--ink); font-weight: 600; }
        .sp-fire-full__empty { padding: 20px !important; color: var(--slate) !important; text-align: center !important; }
        .sp-fire-full__result-count { margin: 8px 0 0; color: var(--slate); font-size: .78rem; }

        .sp-cta { background: var(--red); color: var(--white); padding: 40px 0; }
        .sp-cta__in { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px; }
        .sp-cta h2 { color: var(--white); font-size: clamp(1.4rem, 2.6vw, 1.9rem); font-weight: 700; }
        .sp-cta p { margin: 6px 0 0; color: rgba(255,255,255,.92); }

        /* ---------- Footer ---------- */
        body > footer:not(.site-footer) { display: none !important; }
        .site-footer { position: relative; background: var(--navy); color: #c3cfea; border-top: 4px solid var(--yellow); padding: 56px 0 28px; }
        .site-footer__grid { display: grid; grid-template-columns: 1.6fr 1fr 1fr 1fr; gap: 36px 32px; }
        .site-footer__brand p { margin: 10px 0 18px; max-width: 26em; font-size: .92rem; color: #c3cfea; }
        .site-footer__name { display: block; font-family: 'Bricolage Grotesque', sans-serif; font-size: 1.35rem; font-weight: 700; color: var(--white); }
        .site-footer__name i { color: var(--yellow); margin-right: 6px; }
        .site-footer h6 { color: var(--yellow); font-size: .95rem; font-weight: 600; margin-bottom: 14px; }
        .site-footer ul { list-style: none; margin: 0; padding: 0; }
        .site-footer li { margin-bottom: 9px; }
        .site-footer li a { color: #c3cfea; text-decoration: none; font-size: .9rem; transition: color .2s; }
        .site-footer li a:hover { color: var(--yellow); }

        .site-footer__dialog { max-width: 440px; width: calc(100% - 32px); border: 0; border-radius: 16px; padding: 28px; color: var(--navy); box-shadow: 0 20px 60px rgba(0,0,0,.25); }
        .site-footer__dialog::backdrop { background: rgba(5, 20, 49, .65); }
        .site-footer__dialog h2 { margin: 0 0 12px; font-size: 1.3rem; }
        .site-footer__dialog p { margin: 0 0 20px; }
        .site-footer__dialog form { margin-top: 14px; }
        .site-footer__bottom { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 14px; margin-top: 40px; padding-top: 24px; border-top: 1px solid rgba(255,255,255,.16); font-size: .875rem; }
        .site-footer .region { display: flex; align-items: center; gap: 10px; color: #c3cfea; }
        .site-footer .region b { color: var(--white); font-weight: 600; }
        .site-footer .region img { border-radius: 2px; }
        .site-footer .region i { color: #ff6b6b; }
        .site-footer .links { display: flex; flex-wrap: wrap; gap: 20px; }
        .site-footer .links a { color: #c3cfea; text-decoration: none; }
        .site-footer .links a:hover { color: var(--yellow); }
        .site-footer__copy { margin: 18px 0 0; font-size: .8rem; color: #8fa2cc; }

        /* ---------- Slider berita ---------- */
        .sp-news { padding: 64px 0 0; }
        .sp-news__ctrl { display: flex; align-items: center; gap: 10px; }
        .sp-news__all { margin-right: 6px; font-weight: 600; font-size: .95rem; color: var(--red) !important; text-decoration: none; }
        .sp-news__all:hover { text-decoration: underline; }
        .sp-news__ctrl button {
            width: 44px; height: 44px; border-radius: 50%; cursor: pointer;
            border: 2px solid var(--navy); background: var(--white); color: var(--navy);
            display: flex; align-items: center; justify-content: center;
            transition: background .2s, color .2s, opacity .2s;
        }
        .sp-news__ctrl button:hover:not(:disabled) { background: var(--navy); color: var(--yellow); }
        .sp-news__ctrl button:disabled { opacity: .35; cursor: default; }

        .sp-news__track {
            display: flex; gap: 20px; overflow-x: auto; padding: 6px 2px 22px;
            scroll-snap-type: x mandatory; scrollbar-width: none; -webkit-overflow-scrolling: touch;
            cursor: grab;
        }
        .sp-news__track::-webkit-scrollbar { display: none; }
        .sp-news__track.is-drag { scroll-snap-type: none; cursor: grabbing; user-select: none; }

        .sp-news__card {
            flex: 0 0 calc((100% - 40px) / 3); scroll-snap-align: start;
            display: flex; flex-direction: column; overflow: hidden;
            border: 1px solid var(--line); border-radius: 20px; background: var(--white);
            color: var(--ink); text-decoration: none !important;
            transition: transform .25s ease, box-shadow .25s ease;
        }
        .sp-news__card:hover { transform: translateY(-4px); box-shadow: 0 18px 32px -18px rgba(11,46,111,.45); }
        .sp-news__thumb { position: relative; height: 150px; padding: 16px; background: var(--c); color: var(--t); overflow: hidden; }
        .sp-news__thumb > i { position: absolute; right: 16px; bottom: -14px; font-size: 6.5rem; opacity: .22; }
        .sp-news__cat { display: inline-block; padding: 5px 12px; border-radius: 99px; background: var(--white); color: var(--navy); font-size: .8rem; font-weight: 600; }
        .sp-news__body { display: flex; flex-direction: column; gap: 8px; padding: 20px 22px 22px; flex: 1; }
        .sp-news__body time { font-size: .82rem; color: var(--slate); }
        .sp-news__body h3 {
            font-size: 1.15rem; line-height: 1.3; font-weight: 600; color: var(--navy);
            display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
        }
        .sp-news__body p {
            margin: 0; font-size: .92rem; color: var(--slate);
            display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;
        }
        .sp-news__more { margin-top: auto; padding-top: 6px; font-weight: 600; font-size: .92rem; color: var(--red); }
        .sp-news__more i { margin-left: 4px; transition: transform .2s; }
        .sp-news__card:hover .sp-news__more i { transform: translateX(4px); }
        @media (max-width: 991px) { .sp-news__card { flex-basis: calc((100% - 20px) / 2); } }
        @media (max-width: 640px) {
            .sp-news { padding-top: 44px; }
            .sp-news__card { flex-basis: 84%; }
            .sp-news__ctrl { gap: 6px; }
        }

        /* ---------- Navbar (berasal dari layouts.public, ditimpa agar serasi) ---------- */
        .navbar {
            background-color: var(--navy) !important;
            border-bottom: 4px solid var(--yellow) !important;
            box-shadow: 0 6px 20px -10px rgba(8,31,77,.6);
        }
        .navbar .navbar-brand, .navbar .navbar-brand span { color: var(--white) !important; }
        .navbar .navbar-brand i, .navbar .navbar-brand svg { color: var(--yellow) !important; }
        .navbar .nav-link { color: rgba(255,255,255,.92) !important; font-weight: 500; transition: color .2s; }
        .navbar .nav-link:hover, .navbar .nav-link.active { color: var(--yellow) !important; }
        .navbar .btn {
            background: var(--yellow) !important; color: var(--navy) !important;
            border: 2px solid var(--yellow) !important; border-radius: 999px !important;
            font-weight: 600; padding: 6px 18px; transition: background .2s, border-color .2s;
        }
        .navbar .btn:hover { background: var(--white) !important; border-color: var(--white) !important; }
        .navbar .btn i, .navbar .btn svg { color: var(--navy) !important; }

        /* ---------- Animasi saat scroll ---------- */
        .sp-progressbar {
            position: fixed; top: 0; left: 0; width: 100%; height: 4px; z-index: 2000;
            background: linear-gradient(90deg, var(--red), var(--yellow), var(--blue));
            transform: scaleX(0); transform-origin: left; pointer-events: none;
        }
        .sp-top {
            position: fixed; right: 24px; bottom: 24px; z-index: 1500;
            width: 46px; height: 46px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            background: var(--red); color: var(--white) !important; text-decoration: none !important;
            border: 3px solid var(--yellow); box-shadow: 0 10px 24px -8px rgba(181,30,30,.6);
            opacity: 0; transform: translateY(16px); pointer-events: none;
            transition: opacity .3s ease, transform .3s ease, background .2s;
        }
        .sp-top.is-shown { opacity: 1; transform: none; pointer-events: auto; }
        .sp-top:hover { background: var(--red-dark); }

        .js-anim .reveal {
            opacity: 0; transform: translateY(28px);
            transition: opacity .7s ease var(--d, 0ms), transform .7s cubic-bezier(.2,.7,.2,1) var(--d, 0ms);
        }
        .js-anim .reveal--scale { transform: translateY(20px) scale(.96); }
        .js-anim .reveal.is-visible { opacity: 1; transform: none; }
        .js-anim .sp-search.reveal.is-visible {
            transition: opacity .7s ease var(--d, 0ms), transform .7s cubic-bezier(.2,.7,.2,1) var(--d, 0ms), border-color .2s, box-shadow .2s;
        }

        .js-anim .reveal--stagger > * {
            opacity: 0; transform: translateY(26px);
            transition: opacity .6s ease, transform .6s cubic-bezier(.2,.7,.2,1);
        }
        .js-anim .reveal--stagger.is-visible > * { opacity: 1; transform: none; }
        .js-anim .reveal--stagger > :nth-child(1) { transition-delay: 80ms; }
        .js-anim .reveal--stagger > :nth-child(2) { transition-delay: 160ms; }
        .js-anim .reveal--stagger > :nth-child(3) { transition-delay: 240ms; }
        .js-anim .reveal--stagger > :nth-child(4) { transition-delay: 320ms; }
        .js-anim .reveal--stagger > :nth-child(5) { transition-delay: 400ms; }
        .js-anim .reveal--stagger > :nth-child(6) { transition-delay: 480ms; }
        .js-anim .reveal--stagger > :nth-child(7) { transition-delay: 560ms; }
        .js-anim .reveal--stagger > :nth-child(8) { transition-delay: 640ms; }
        .js-anim .reveal--stagger > :nth-child(9) { transition-delay: 720ms; }
        .js-anim .reveal--stagger > :nth-child(10) { transition-delay: 800ms; }
        .js-anim .reveal--stagger > :nth-child(11) { transition-delay: 880ms; }
        .js-anim .reveal--stagger > :nth-child(12) { transition-delay: 960ms; }

        .js-anim .reveal--bar i {
            transform: scaleX(0); transform-origin: left;
            transition: transform .9s cubic-bezier(.2,.7,.2,1);
        }
        .js-anim .reveal--bar.is-visible i { transform: scaleX(1); }
        .js-anim .reveal--bar i:nth-child(1) { transition-delay: 120ms; }
        .js-anim .reveal--bar i:nth-child(2) { transition-delay: 240ms; }
        .js-anim .reveal--bar i:nth-child(3) { transition-delay: 360ms; }
        .js-anim .reveal--bar i:nth-child(4) { transition-delay: 480ms; }

        .js-anim .sp-progress i { transform: scaleX(0); transform-origin: left; transition: transform 1.2s cubic-bezier(.2,.7,.2,1) .5s; }
        .js-anim .reveal--stagger.is-visible .sp-progress i { transform: scaleX(1); }

        @media (prefers-reduced-motion: reduce) {
            .js-anim .reveal, .js-anim .reveal--stagger > *, .js-anim .reveal--bar i, .js-anim .sp-progress i {
                opacity: 1 !important; transform: none !important; transition: none !important;
            }
            .sp-progressbar { display: none; }
        }

        /* ---------- Responsif ---------- */
        @media (max-width: 991px) {
            .sp-hero { padding: 48px 0 44px; }
            .sp-hero__grid { grid-template-columns: 1fr; gap: 40px; }
            .sp-score { max-width: 340px; }
            .sp-section { padding: 52px 0; }
            .sp-legend { grid-template-columns: repeat(2, 1fr); }
            .sp-stats { grid-template-columns: 1fr; }
            .sp-prov { grid-template-columns: repeat(2, 1fr); }
            .site-footer__grid { grid-template-columns: repeat(3, 1fr); }
            .site-footer__brand { grid-column: 1 / -1; }
        }
        @media (max-width: 640px) {
            .sp-wrap { padding: 0 18px; }
            .sp-stats { grid-template-columns: 1fr; }
            .sp-stat + .sp-stat { border-left: 0; }
            .sp-panel { padding: 22px 20px; }
            .sp-prov { grid-template-columns: 1fr; }
            .sp-pane { height: 440px; }
            .sp-tabs { width: 100%; }
            .sp-tabs button { flex: 1; padding: 9px 10px; font-size: .85rem; }
            .site-footer__grid { grid-template-columns: repeat(2, 1fr); }
        }
        @media (prefers-reduced-motion: reduce) {
            .sp-ring__value { transition: none; }
            .pulse-fire-icon { animation: none; }
            * { scroll-behavior: auto !important; }
        }
        html { scroll-behavior: smooth; }
    </style>
@endpush

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var ring = document.getElementById('ringValue');
            var circumference = 578;
            var score = Number({{ (float) ($avgNilaiAkhir ?? 0) }});
            requestAnimationFrame(function () {
                ring.style.strokeDashoffset = circumference * (1 - (score / 100));
            });

            var mapSpm = L.map('map-spm').setView([-2.5489, 118.0149], 5);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors', maxZoom: 18
            }).addTo(mapSpm);

            var mapFire = L.map('map-fire').setView([-2.5489, 118.0149], 5);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors | Data: NASA FIRMS', maxZoom: 18
            }).addTo(mapFire);

            var mapColors = {
                'Sangat Baik': '#198754',
                'Baik': '#0d6efd',
                'Cukup': '#ffc107',
                'Kurang': '#dc3545',
                'Belum ada data': '#cccccc',
            };

            var mapPayload = @json($mapPayload ?? ['year' => null, 'provinces' => []]);
            var searchInput = document.getElementById('searchInput');
            var searchButton = document.querySelector('.sp-search button');
            var searchResults = document.getElementById('searchResults');
            var publicMapDetail = document.getElementById('public-map-detail');
            var geoJsonCodeCounts = {};
            var mapDataDb = {};
            var mapDataByGeojsonCode = {};
            var selectedProvinceLayer = null;
            var dashboardGeoJsonLayer = null;
            var hotspotProvinceLayers = [];
            var hotspotProvinceCounts = [];
            var hotspotFeatures = null;
            var hotspotSummaryElement = document.getElementById('provinsi-terdampak-container');
            var hotspotSummaryNote = document.getElementById('provinsi-terdampak-note');
            var allHotspotProvincesLink = document.getElementById('show-all-hotspot-provinces');
            var allHotspotProvincesSection = document.getElementById('daftar-provinsi-terdampak');
            var hotspotProvinceSearch = document.getElementById('hotspot-province-search');
            var hotspotProvinceRows = document.getElementById('hotspot-province-rows');
            var hotspotProvinceResultCount = document.getElementById('hotspot-province-result-count');
            var selectedRegencyMarker = null;
            var geocodeCache = new Map();
            var geocodeQueue = Promise.resolve();
            var lastGeocodeRequest = 0;

            function addText(parent, tagName, text, className) {
                var element = document.createElement(tagName);
                element.textContent = text;
                if (className) element.className = className;
                parent.appendChild(element);
                return element;
            }

            function mapKey(name) {
                var normalized = (name || '').toLocaleUpperCase('id-ID').trim()
                    .replace(/^PROVINSI\s+/, '')
                    .replace(/[^\p{L}\p{N}]+/gu, ' ')
                    .trim();

                if (['DI YOGYAKARTA', 'DAERAH ISTIMEWA YOGYAKARTA'].includes(normalized)) return 'YOGYAKARTA';
                if (['DKI JAKARTA', 'JAKARTA'].includes(normalized)) return 'JAKARTA';

                return normalized.replace(/\s+/g, '');
            }

            function searchKey(value) {
                return (value || '').toLocaleLowerCase('id-ID')
                    .replace(/^(kabupaten|kab\.?|kota|provinsi)\s*/u, '')
                    .replace(/[^\p{L}\p{N}]+/gu, '');
            }

            function appendAreaMetrics(parent, metrics) {
                var labels = [
                    ['kecamatan', 'Kecamatan'],
                    ['pos_damkar', 'Pos damkar'],
                    ['sdm', 'Personel'],
                    ['sdm_bersertifikat', 'Personel bersertifikat'],
                    ['desa', 'Desa terdata'],
                    ['redkar', 'Anggota REDKAR'],
                    ['kelembagaan', 'Kelembagaan'],
                    ['perencanaan', 'Perencanaan'],
                    ['capaian', 'Capaian layanan'],
                    ['sarpras', 'Sarana prasarana'],
                    ['sdm_dimension', 'Dimensi SDM'],
                    ['pemberdayaan', 'Pemberdayaan'],
                ];
                var grid = document.createElement('div');
                grid.className = 'sp-area-detail__metrics';

                labels.forEach(function (item) {
                    if (metrics[item[0]] === null || metrics[item[0]] === undefined) return;
                    var cell = document.createElement('div');
                    cell.className = 'sp-area-detail__metric';
                    addText(cell, 'span', item[1]);
                    addText(cell, 'strong', Number(metrics[item[0]]).toLocaleString('id-ID', { maximumFractionDigits: 2 }));
                    grid.appendChild(cell);
                });

                if (grid.childElementCount > 0) parent.appendChild(grid);
            }

            function createAreaDetail(area, province, options) {
                var detail = document.createElement('section');
                detail.className = 'sp-area-detail';
                var heading = document.createElement('div');
                heading.className = 'sp-area-detail__heading';
                var name = document.createElement('div');
                addText(name, 'h3', area.name || area.province_name, 'sp-area-detail__title');
                addText(name, 'p', (area.type === 'regency' ? province.province_name + ' · ' : '') + 'Data terpublikasi tahun ' + mapPayload.year, 'sp-area-detail__subtitle');
                heading.appendChild(name);

                var score = document.createElement('div');
                score.className = 'sp-area-detail__score';
                if (area.spm === null || area.spm === undefined) {
                    addText(score, 'span', 'Belum ada data', 'badge bg-secondary');
                } else {
                    addText(score, 'strong', Number(area.spm).toFixed(2));
                    var badge = addText(score, 'span', area.category, 'badge');
                    badge.style.backgroundColor = mapColors[area.category];
                    badge.style.color = '#fff';
                }
                heading.appendChild(score);
                detail.appendChild(heading);
                appendAreaMetrics(detail, area.metrics || {});

                if (options && options.showLocationButton) {
                    var locateButton = addText(detail, 'button', 'Lihat lokasi di peta', 'btn btn-primary btn-sm justify-self-start');
                    locateButton.type = 'button';
                    locateButton.addEventListener('click', function () {
                        document.getElementById('pills-spm-tab').click();
                        document.getElementById('map-section').scrollIntoView({ behavior: 'smooth', block: 'start' });
                        locateRegency(area, province);
                    });
                }

                return detail;
            }

            function locateRegency(regency, province) {
                if (!dashboardGeoJsonLayer) return;
                var matchedLayer = null;
                dashboardGeoJsonLayer.eachLayer(function (layer) {
                    if (provinceForFeature(layer.feature)?.id === province.id) matchedLayer = layer;
                });
                if (!matchedLayer) return;

                var regencyName = regency.regency_name || regency.name;
                var queryName = regencyName.replace(/^(KABUPATEN|KAB\.?|KOTA)\s*/i, '').trim();
                var query = queryName + ', ' + province.province_name + ', Indonesia';
                var provinceBounds = matchedLayer.getBounds();
                mapSpm.fitBounds(provinceBounds, { padding: [24, 24] });

                var pending = geocodeCache.get(query);
                if (!pending) {
                    pending = geocodeQueue.then(async function () {
                        var delay = Math.max(0, 1100 - (Date.now() - lastGeocodeRequest));
                        if (delay > 0) await new Promise(function (resolve) { window.setTimeout(resolve, delay); });
                        lastGeocodeRequest = Date.now();
                        var url = 'https://nominatim.openstreetmap.org/search?format=jsonv2&countrycodes=id&limit=1&q=' + encodeURIComponent(query);
                        var response = await fetch(url, { headers: { Accept: 'application/json' } });
                        if (!response.ok) throw new Error('Lokasi wilayah tidak dapat dimuat.');
                        var results = await response.json();
                        return results[0] || null;
                    });
                    geocodeQueue = pending.catch(function () {});
                    geocodeCache.set(query, pending);
                }

                pending.then(function (location) {
                    if (!location) throw new Error('Lokasi wilayah tidak ditemukan.');
                    var latitude = Number(location.lat);
                    var longitude = Number(location.lon);
                    var bounds = location.boundingbox.map(Number);
                    if (selectedRegencyMarker) mapSpm.removeLayer(selectedRegencyMarker);
                    selectedRegencyMarker = L.marker([latitude, longitude]).addTo(mapSpm);
                    selectedRegencyMarker.bindPopup('<strong>' + regencyName + '</strong><br>Lokasi administratif · OpenStreetMap').openPopup();
                    mapSpm.fitBounds([[bounds[0], bounds[2]], [bounds[1], bounds[3]]], { maxZoom: 11, padding: [24, 24] });
                }).catch(function () {
                    var notice = document.createElement('p');
                    notice.className = 'sp-area-detail__subtitle';
                    notice.textContent = 'Lokasi kabupaten tidak berhasil dimuat; peta menampilkan wilayah provinsinya.';
                    publicMapDetail.appendChild(notice);
                });
            }

            function showSearchDetail(result) {
                var province = (mapPayload.provinces || []).find(function (item) { return item.id === result.provinceId; });
                if (!province) return;
                searchResults.querySelectorAll('.sp-search__result').forEach(function (button) {
                    button.setAttribute('aria-selected', String(button.dataset.resultKey === result.key));
                });
                var previous = searchResults.querySelector('.sp-area-detail');
                if (previous) previous.remove();
                var detail = createAreaDetail(result, province, { showLocationButton: result.type === 'regency' });
                searchResults.appendChild(detail);
            }

            function runAreaSearch() {
                var query = searchKey(searchInput.value.trim());
                searchResults.replaceChildren();

                if (query.length < 2) {
                    searchResults.hidden = true;
                    return;
                }

                var matches = [];
                (mapPayload.provinces || []).forEach(function (province) {
                    if (searchKey(province.province_name).includes(query) && province.spm !== null) {
                        matches.push({
                            type: 'province',
                            name: province.province_name,
                            provinceName: province.province_name,
                            provinceId: province.id,
                            spm: province.spm,
                            category: province.category,
                            metrics: Object.assign({ regencies: province.regency_count }, province.metrics || {}),
                            key: 'province-' + province.id,
                        });
                    }

                    (province.regencies || []).forEach(function (regency) {
                        if (searchKey(regency.regency_name).includes(query)) {
                            matches.push({
                                type: 'regency',
                                name: regency.regency_name,
                                provinceName: province.province_name,
                                provinceId: province.id,
                                spm: regency.spm,
                                category: regency.category,
                                metrics: regency.metrics || {},
                                regency_name: regency.regency_name,
                                key: 'regency-' + regency.id,
                            });
                        }
                    });
                });

                matches.sort(function (left, right) {
                    var leftRank = searchKey(left.name) === query ? 0 : (searchKey(left.name).startsWith(query) ? 1 : 2);
                    var rightRank = searchKey(right.name) === query ? 0 : (searchKey(right.name).startsWith(query) ? 1 : 2);
                    return leftRank - rightRank || left.name.localeCompare(right.name, 'id');
                });

                if (matches.length === 0) {
                    addText(searchResults, 'p', 'Tidak ada data published yang cocok untuk tahun ' + (mapPayload.year ?? 'ini') + '.', 'sp-search__empty');
                    searchResults.hidden = false;
                    return;
                }

                matches.slice(0, 8).forEach(function (result) {
                    var button = document.createElement('button');
                    button.type = 'button';
                    button.className = 'sp-search__result';
                    button.setAttribute('role', 'option');
                    addText(button, 'span', result.type === 'regency' ? result.name + ' · ' + result.provinceName : result.name, 'sp-search__result-title');
                    addText(button, 'span', 'Indeks SPM ' + Number(result.spm).toFixed(2) + ' · ' + result.category + ' · Tahun ' + mapPayload.year, 'sp-search__result-meta');
                    button.dataset.resultKey = result.key;
                    button.addEventListener('click', function () {
                        showSearchDetail(result);
                    });
                    searchResults.appendChild(button);
                });

                searchResults.hidden = false;
                showSearchDetail(matches[0]);
            }

            searchButton.addEventListener('click', runAreaSearch);
            searchInput.addEventListener('keydown', function (event) {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    runAreaSearch();
                }
            });
            searchInput.addEventListener('input', function () {
                if (searchInput.value.trim().length >= 2) runAreaSearch();
                else searchResults.hidden = true;
            });

            function categoryForIndex(index) {
                if (index === null || index === undefined) return 'Belum ada data';
                if (index >= 85) return 'Sangat Baik';
                if (index >= 70) return 'Baik';
                if (index >= 55) return 'Cukup';
                return 'Kurang';
            }

            function rebuildMapIndexes() {
                mapDataDb = Object.fromEntries((mapPayload.provinces || []).map(function (province) {
                    return [province.map_key, province];
                }));
                mapDataByGeojsonCode = Object.fromEntries((mapPayload.provinces || [])
                    .filter(function (province) {
                        return province.geojson_code && geoJsonCodeCounts[province.geojson_code] === 1;
                    })
                    .map(function (province) {
                        return [province.geojson_code, province];
                    }));
            }

            function provinceForFeature(feature) {
                var code = String(feature.properties.KODE_PROV || '').trim();
                return mapDataByGeojsonCode[code] || mapDataDb[mapKey(feature.properties.PROVINSI)] || null;
            }

            function ringContainsPoint(ring, longitude, latitude) {
                var inside = false;
                for (var current = 0, previous = ring.length - 1; current < ring.length; previous = current++) {
                    var currentPoint = ring[current];
                    var previousPoint = ring[previous];
                    var crossesLatitude = (currentPoint[1] > latitude) !== (previousPoint[1] > latitude);
                    if (crossesLatitude) {
                        var crossingLongitude = (previousPoint[0] - currentPoint[0]) * (latitude - currentPoint[1]) /
                            (previousPoint[1] - currentPoint[1]) + currentPoint[0];
                        if (longitude < crossingLongitude) inside = !inside;
                    }
                }
                return inside;
            }

            function polygonContainsPoint(rings, longitude, latitude) {
                return rings.length > 0 && ringContainsPoint(rings[0], longitude, latitude) &&
                    !rings.slice(1).some(function (hole) { return ringContainsPoint(hole, longitude, latitude); });
            }

            function geometryContainsPoint(geometry, longitude, latitude) {
                if (geometry.type === 'Polygon') return polygonContainsPoint(geometry.coordinates, longitude, latitude);
                if (geometry.type === 'MultiPolygon') {
                    return geometry.coordinates.some(function (polygon) {
                        return polygonContainsPoint(polygon, longitude, latitude);
                    });
                }
                return false;
            }

            function renderHotspotProvinceSummary() {
                if (!hotspotProvinceLayers.length || !Array.isArray(hotspotFeatures)) return;

                var counts = hotspotProvinceLayers.map(function (entry) {
                    return {
                        name: entry.feature.properties.PROVINSI || 'Provinsi tidak diketahui',
                        count: 0,
                        entry: entry,
                        provinceData: provinceForFeature(entry.feature),
                    };
                });
                var matchedPoints = 0;

                hotspotFeatures.forEach(function (feature) {
                    var coordinates = feature.geometry?.coordinates;
                    if (!Array.isArray(coordinates) || coordinates.length < 2) return;
                    var longitude = Number(coordinates[0]);
                    var latitude = Number(coordinates[1]);
                    if (!Number.isFinite(longitude) || !Number.isFinite(latitude)) return;

                    var province = counts.find(function (item) {
                        return item.entry.bounds.contains([latitude, longitude]) &&
                            geometryContainsPoint(item.entry.feature.geometry, longitude, latitude);
                    });
                    if (province) {
                        province.count++;
                        matchedPoints++;
                    }
                });

                counts.sort(function (left, right) {
                    return right.count - left.count || left.name.localeCompare(right.name, 'id');
                });
                hotspotProvinceCounts = counts;
                hotspotSummaryElement.replaceChildren();
                counts.slice(0, 12).forEach(function (province) {
                    var row = document.createElement('div');
                    if (province.count > 0) row.className = 'is-hot';
                    addText(row, 'span', province.name);
                    var value = document.createElement('em');
                    if (province.count > 0) {
                        var icon = document.createElement('i');
                        icon.className = 'fas fa-fire';
                        value.appendChild(icon);
                        value.appendChild(document.createTextNode(' '));
                    }
                    value.appendChild(document.createTextNode(province.count.toLocaleString('id-ID')));
                    row.appendChild(value);
                    hotspotSummaryElement.appendChild(row);
                });

                hotspotSummaryNote.textContent = 'Menampilkan 12 provinsi teratas · ' +
                    matchedPoints.toLocaleString('id-ID') + ' dari ' + hotspotFeatures.length.toLocaleString('id-ID') +
                    ' titik berada di dalam batas provinsi GeoJSON.';
                allHotspotProvincesLink.hidden = false;
                renderAllHotspotProvinces(counts);
            }

            function renderAllHotspotProvinces(provinces) {
                var query = searchKey(hotspotProvinceSearch.value.trim());
                var visibleProvinces = provinces.filter(function (item) {
                    return searchKey(item.name).includes(query);
                });
                hotspotProvinceRows.replaceChildren();

                if (visibleProvinces.length === 0) {
                    var emptyRow = document.createElement('tr');
                    var emptyCell = addText(emptyRow, 'td', 'Tidak ada provinsi yang cocok dengan pencarian.', 'sp-fire-full__empty');
                    emptyCell.colSpan = 7;
                    hotspotProvinceRows.appendChild(emptyRow);
                    hotspotProvinceResultCount.textContent = '0 provinsi ditemukan.';
                    return;
                }

                visibleProvinces.forEach(function (item) {
                    var row = document.createElement('tr');
                    var province = item.provinceData;
                    var metrics = province?.metrics || {};
                    var index = province?.spm;
                    addText(row, 'td', item.name);
                    addText(row, 'td', item.count.toLocaleString('id-ID'));
                    addText(row, 'td', String(province?.regency_count ?? 0));
                    addText(row, 'td', index === null || index === undefined ? '—' : Number(index).toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                    addText(row, 'td', Number(metrics.pos_damkar || 0).toLocaleString('id-ID'));
                    addText(row, 'td', Number(metrics.sdm || 0).toLocaleString('id-ID'));
                    addText(row, 'td', Number(metrics.redkar || 0).toLocaleString('id-ID'));
                    hotspotProvinceRows.appendChild(row);
                });

                hotspotProvinceResultCount.textContent = 'Menampilkan ' + visibleProvinces.length.toLocaleString('id-ID') +
                    ' dari ' + provinces.length.toLocaleString('id-ID') + ' provinsi.';
            }

            allHotspotProvincesLink.addEventListener('click', function () {
                allHotspotProvincesSection.hidden = false;
                window.setTimeout(function () {
                    allHotspotProvincesSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    hotspotProvinceSearch.focus({ preventScroll: true });
                }, 0);
            });
            hotspotProvinceSearch.addEventListener('input', function () {
                renderAllHotspotProvinces(hotspotProvinceCounts);
            });

            function renderProvinceDetail(province) {
                if (!province) return;
                publicMapDetail.replaceChildren();
                publicMapDetail.hidden = false;
                var area = Object.assign({ name: province.province_name, type: 'province', spm: province.spm }, province);
                publicMapDetail.appendChild(createAreaDetail(area, province));

                var title = addText(publicMapDetail, 'h3', 'Kabupaten/kota dengan data terpublikasi', 'sp-area-detail__title mt-3 mb-2');
                var list = document.createElement('div');
                list.className = 'sp-area-detail__regencies';
                var regencies = province.regencies || [];
                if (regencies.length === 0) {
                    addText(publicMapDetail, 'p', 'Belum ada data kabupaten/kota yang dipublikasikan untuk tahun ini.', 'text-muted mb-0');
                    return;
                }
                regencies.forEach(function (regency) {
                    var button = document.createElement('button');
                    button.type = 'button';
                    button.className = 'sp-area-detail__regency';
                    addText(button, 'span', regency.regency_name);
                    addText(button, 'strong', Number(regency.spm).toFixed(2) + ' · ' + regency.category);
                    button.addEventListener('click', function () {
                        publicMapDetail.replaceChildren(createAreaDetail(Object.assign({ type: 'regency' }, regency), province));
                        var note = addText(publicMapDetail, 'p', 'Mencari lokasi administratif ' + regency.regency_name + ' di OpenStreetMap…', 'sp-area-detail__subtitle mt-2');
                        locateRegency(regency, province);
                        publicMapDetail.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                    });
                    list.appendChild(button);
                });
                publicMapDetail.appendChild(title);
                publicMapDetail.appendChild(list);
            }

            function createProvincePopup(provinceName, provinceData) {
                var wrapper = document.createElement('div');
                addText(wrapper, 'h6', provinceData?.province_name || provinceName, 'fw-bold mb-1');

                if (!provinceData || provinceData.spm === null) {
                    addText(wrapper, 'p', 'Belum ada data yang dipublikasikan untuk tahun ' + (mapPayload.year ?? 'ini') + '.', 'mb-0 text-muted');
                    return wrapper;
                }

                var badge = addText(wrapper, 'span', provinceData.category, 'badge');
                badge.style.backgroundColor = mapColors[provinceData.category];
                badge.style.color = '#fff';

                var divider = document.createElement('hr');
                divider.className = 'my-2';
                wrapper.appendChild(divider);

                var list = document.createElement('ul');
                list.className = 'list-unstyled mb-0';
                addText(list, 'li', 'Indeks SPM: ' + Number(provinceData.spm).toFixed(2));
                addText(list, 'li', 'Data Kabupaten/Kota: ' + provinceData.regency_count);
                wrapper.appendChild(list);

                if ((provinceData.regencies || []).length > 0) {
                    var topTitle = addText(wrapper, 'strong', 'Top Kabupaten/Kota', 'd-block mt-2');
                    var regencyList = document.createElement('ol');
                    regencyList.className = 'mb-0 ps-3';
                    provinceData.regencies.slice(0, 5).forEach(function (regency) {
                        var item = document.createElement('li');
                        var button = addText(item, 'button', regency.regency_name + ' — ' + Number(regency.spm).toFixed(2), 'btn btn-link btn-sm p-0 text-start');
                        button.type = 'button';
                        button.addEventListener('click', function () {
                            publicMapDetail.replaceChildren(createAreaDetail(Object.assign({ type: 'regency' }, regency), provinceData));
                            publicMapDetail.hidden = false;
                            locateRegency(regency, provinceData);
                            publicMapDetail.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                        });
                        regencyList.appendChild(item);
                    });
                    wrapper.appendChild(topTitle);
                    wrapper.appendChild(regencyList);
                }

                return wrapper;
            }

            fetch('{{ url("geojson/indonesia-prov.geojson") }}')
                .then(function (response) { return response.json(); })
                .then(function (data) {
                    hotspotProvinceLayers = [];
                    data.features.forEach(function (feature) {
                        var code = String(feature.properties.KODE_PROV || '').trim();
                        geoJsonCodeCounts[code] = (geoJsonCodeCounts[code] || 0) + 1;
                    });
                    rebuildMapIndexes();

                    dashboardGeoJsonLayer = L.geoJson(data, {
                        style: function (feature) {
                            var provinceData = provinceForFeature(feature);
                            return {
                                fillColor: mapColors[categoryForIndex(provinceData?.spm ?? provinceData?.index)],
                                weight: 2,
                                opacity: 1,
                                color: 'white',
                                dashArray: '3',
                                fillOpacity: 0.7
                            };
                        },
                        onEachFeature: function (feature, layer) {
                            hotspotProvinceLayers.push({ feature: feature, bounds: layer.getBounds() });
                            var provinceName = feature.properties.PROVINSI;
                            var provinceData = provinceForFeature(feature);
                            layer.bindPopup(createProvincePopup(provinceName, provinceData));
                            layer.bindTooltip(provinceName + '<br>' + (provinceData && provinceData.spm !== null ? Number(provinceData.spm).toFixed(2) + ' · ' + provinceData.category : 'Belum ada data'), { sticky: true });
                            layer.on({
                                mouseover: function (event) {
                                    event.target.setStyle({ weight: 4, color: '#333', dashArray: '', fillOpacity: 0.9 });
                                    event.target.bringToFront();
                                },
                                mouseout: function (event) {
                                    dashboardGeoJsonLayer.resetStyle(event.target);
                                },
                                click: function (event) {
                                    if (selectedProvinceLayer) dashboardGeoJsonLayer.resetStyle(selectedProvinceLayer);
                                    selectedProvinceLayer = event.target;
                                    selectedProvinceLayer.setStyle({ weight: 4, color: '#1f2937', dashArray: '', fillOpacity: 0.9 });
                                    mapSpm.fitBounds(selectedProvinceLayer.getBounds(), { padding: [24, 24] });
                                    renderProvinceDetail(provinceData);
                                }
                            });
                        }
                    }).addTo(mapSpm);
                    renderHotspotProvinceSummary();
                })
                .catch(function (error) {
                    console.error('Failed to load public map data:', error);
                });

            var fireGroup = L.layerGroup().addTo(mapFire);
            var hotspotEndpoint = @json(route('peta.hotspots'));
            var hotspotLoadInProgress = false;
            var hasLoadedHotspots = false;

            function confidenceColor(confidence) {
                var level = String(confidence || '').toLowerCase();
                if (level === 'h' || level === 'high' || (Number.isFinite(Number(level)) && Number(level) >= 80)) return '#fe6a69';
                if (level === 'n' || level === 'nominal' || (Number.isFinite(Number(level)) && Number(level) >= 50)) return '#ff9b57';
                return '#fdd64b';
            }

            function hotspotPopup(properties) {
                var popup = document.createElement('div');
                addText(popup, 'strong', 'Deteksi hotspot satelit', 'd-block mb-1');
                addText(popup, 'div', 'Confidence: ' + properties.confidence_label);
                if (properties.brightness !== null) addText(popup, 'div', 'Brightness: ' + properties.brightness + ' K');
                if (properties.frp !== null) addText(popup, 'div', 'Fire radiative power: ' + properties.frp + ' MW');
                addText(popup, 'div', 'Satelit: ' + (properties.satellite || '-') + ' · ' + (properties.instrument || '-'));
                var time = String(properties.acq_time || '').padStart(4, '0');
                addText(popup, 'div', 'Waktu deteksi: ' + (properties.acq_date || '-') + ' ' + time.slice(0, 2) + ':' + time.slice(2, 4) + ' UTC');
                addText(popup, 'small', 'NASA FIRMS · hotspot perlu verifikasi lapangan', 'd-block mt-1 text-muted');
                return popup;
            }

            async function loadNasaHotspots() {
                if (hotspotLoadInProgress) return;
                hotspotLoadInProgress = true;
                var loading = document.getElementById('nasa-loading');
                var status = document.getElementById('nasa-data-status');

                try {
                    var response = await fetch(hotspotEndpoint, { headers: { Accept: 'application/geo+json, application/json' } });
                    var payload = await response.json();
                    if (!response.ok) throw new Error(payload.message || 'Data NASA FIRMS belum tersedia.');

                    fireGroup.clearLayers();
                    hotspotFeatures = payload.features || [];
                    hotspotFeatures.forEach(function (feature) {
                        if (!feature.geometry || feature.geometry.type !== 'Point') return;
                        var coordinates = feature.geometry.coordinates;
                        var lng = Number(coordinates[0]);
                        var lat = Number(coordinates[1]);
                        if (!Number.isFinite(lat) || !Number.isFinite(lng)) return;

                        var properties = feature.properties || {};
                        var color = confidenceColor(properties.confidence);
                        var frp = Number(properties.frp) || 0;
                        L.circleMarker([lat, lng], {
                            radius: Math.max(5, Math.min(10, 4 + Math.sqrt(frp))),
                            color: color,
                            weight: 1,
                            fillColor: color,
                            fillOpacity: 0.78,
                        }).addTo(fireGroup).bindPopup(hotspotPopup(properties));

                    });
                    renderHotspotProvinceSummary();

                    var updatedAt = new Date(payload.retrieved_at).toLocaleTimeString('id-ID', {
                        timeZone: 'Asia/Jakarta', hour: '2-digit', minute: '2-digit'
                    });
                    status.textContent = payload.count + ' hotspot · ' + payload.product + ' · diperbarui ' + updatedAt + ' WIB';
                    loading.style.display = 'none';
                    hasLoadedHotspots = true;
                } catch (error) {
                    status.textContent = hasLoadedHotspots ? 'Pembaruan gagal; menampilkan data terakhir.' : 'Data satelit sementara tidak dapat dimuat.';
                    if (!hasLoadedHotspots) {
                        loading.replaceChildren();
                        addText(loading, 'b', 'Data satelit belum tersedia');
                        addText(loading, 'span', error.message || 'Coba lagi beberapa saat.');
                    }
                } finally {
                    hotspotLoadInProgress = false;
                }
            }

            loadNasaHotspots();
            window.setInterval(loadNasaHotspots, 5 * 60 * 1000);

            var tabs = document.querySelectorAll('.sp-tabs button');
            tabs.forEach(function (tab) {
                tab.addEventListener('click', function () {
                    tabs.forEach(function (t) {
                        t.classList.remove('is-active');
                        t.setAttribute('aria-selected', 'false');
                        document.getElementById(t.dataset.target).classList.remove('is-active');
                    });
                    tab.classList.add('is-active');
                    tab.setAttribute('aria-selected', 'true');
                    document.getElementById(tab.dataset.target).classList.add('is-active');

                    setTimeout(function () {
                        if (tab.id === 'pills-fire-tab') mapFire.invalidateSize();
                        else mapSpm.invalidateSize();
                    }, 10);
                });
            });
        });
    </script>
    <script>
        (function () {
            var root = document.querySelector('.sp');
            var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

            // 1. Munculkan elemen saat masuk layar
            var targets = document.querySelectorAll('.reveal, .reveal--stagger, .reveal--bar');
            if (!('IntersectionObserver' in window) || reduce) {
                targets.forEach(function (el) { el.classList.add('is-visible'); });
            } else {
                var io = new IntersectionObserver(function (entries) {
                    entries.forEach(function (e) {
                        if (e.isIntersecting) {
                            e.target.classList.add('is-visible');
                            io.unobserve(e.target);
                        }
                    });
                }, { threshold: 0.15, rootMargin: '0px 0px -40px 0px' });
                targets.forEach(function (el) { io.observe(el); });
            }

            // 2. Angka menghitung naik
            var counters = document.querySelectorAll('[data-count]');
            function runCount(el) {
                var end = parseInt(el.dataset.count, 10);
                var duration = 1300, start = null;
                function tick(ts) {
                    if (start === null) start = ts;
                    var t = Math.min((ts - start) / duration, 1);
                    var eased = 1 - Math.pow(1 - t, 3);
                    el.textContent = Math.round(end * eased);
                    if (t < 1) requestAnimationFrame(tick);
                }
                requestAnimationFrame(tick);
            }
            if ('IntersectionObserver' in window && !reduce) {
                counters.forEach(function (el) { el.textContent = '0'; });
                var co = new IntersectionObserver(function (entries) {
                    entries.forEach(function (e) {
                        if (e.isIntersecting) { runCount(e.target); co.unobserve(e.target); }
                    });
                }, { threshold: 0.6 });
                counters.forEach(function (el) { co.observe(el); });
            }

            // 3. Bilah progres scroll & tombol kembali ke atas
            var bar = document.getElementById('spProgress');
            var topBtn = document.getElementById('spTop');
            var ticking = false;
            function onScroll() {
                var h = document.documentElement;
                var max = h.scrollHeight - h.clientHeight;
                var ratio = max > 0 ? (window.pageYOffset || h.scrollTop) / max : 0;
                bar.style.transform = 'scaleX(' + Math.min(Math.max(ratio, 0), 1) + ')';
                topBtn.classList.toggle('is-shown', (window.pageYOffset || h.scrollTop) > 600);
                ticking = false;
            }
            window.addEventListener('scroll', function () {
                if (!ticking) { requestAnimationFrame(onScroll); ticking = true; }
            }, { passive: true });
            onScroll();

            topBtn.addEventListener('click', function (e) {
                e.preventDefault();
                window.scrollTo({ top: 0, behavior: reduce ? 'auto' : 'smooth' });
            });
        })();
    </script>
    <script>
        (function () {
            var track = document.getElementById('newsTrack');
            if (!track) return;
            var prev = document.getElementById('newsPrev');
            var next = document.getElementById('newsNext');
            var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            var behavior = reduce ? 'auto' : 'smooth';

            function step() {
                var card = track.querySelector('.sp-news__card');
                return card ? card.getBoundingClientRect().width + 20 : 320;
            }
            function atEnd() { return track.scrollLeft + track.clientWidth >= track.scrollWidth - 4; }
            function updateButtons() {
                prev.disabled = track.scrollLeft <= 4;
                next.disabled = atEnd();
            }
            prev.addEventListener('click', function () { track.scrollBy({ left: -step(), behavior: behavior }); });
            next.addEventListener('click', function () { track.scrollBy({ left: step(), behavior: behavior }); });
            track.addEventListener('scroll', updateButtons, { passive: true });
            window.addEventListener('resize', updateButtons);
            updateButtons();

            // Panah keyboard saat slider difokuskan
            track.addEventListener('keydown', function (e) {
                if (e.key === 'ArrowRight') { track.scrollBy({ left: step(), behavior: behavior }); e.preventDefault(); }
                if (e.key === 'ArrowLeft')  { track.scrollBy({ left: -step(), behavior: behavior }); e.preventDefault(); }
            });

            // Geser dengan mouse (drag). Sentuhan layar sudah bisa digeser bawaan browser.
            var down = false, moved = false, startX = 0, startLeft = 0;
            track.addEventListener('pointerdown', function (e) {
                if (e.pointerType !== 'mouse') return;
                down = true; moved = false; startX = e.clientX; startLeft = track.scrollLeft;
                track.classList.add('is-drag');
            });
            window.addEventListener('pointermove', function (e) {
                if (!down) return;
                var dx = e.clientX - startX;
                if (Math.abs(dx) > 5) moved = true;
                track.scrollLeft = startLeft - dx;
            });
            window.addEventListener('pointerup', function () {
                if (!down) return;
                down = false;
                track.classList.remove('is-drag');
            });
            track.addEventListener('click', function (e) {
                if (moved) { e.preventDefault(); moved = false; }
            }, true);
            track.addEventListener('dragstart', function (e) { e.preventDefault(); });

            // Geser otomatis pelan, berhenti saat disentuh/disorot atau jika pengguna memilih kurangi gerakan
            if (!reduce) {
                var paused = false;
                ['mouseenter', 'focusin', 'touchstart'].forEach(function (ev) {
                    track.addEventListener(ev, function () { paused = true; }, { passive: true });
                });
                ['mouseleave', 'focusout'].forEach(function (ev) {
                    track.addEventListener(ev, function () { paused = false; });
                });
                setInterval(function () {
                    if (paused || document.hidden || down) return;
                    if (atEnd()) track.scrollTo({ left: 0, behavior: 'smooth' });
                    else track.scrollBy({ left: step(), behavior: 'smooth' });
                }, 6000);
            }
        })();
    </script>
@endpush
