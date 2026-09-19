@extends('layouts.public')

@section('content')
<script>document.documentElement.classList.add("js-anim");</script>
<div class="sp">

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
                    <input type="text" id="searchInput" placeholder="Cari kabupaten, kota, atau provinsi" aria-label="Cari wilayah">
                    <button type="button">Cari wilayah</button>
                </div>

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
                        <strong>82,45</strong>
                        <span>dari 100</span>
                    </div>
                </div>
                <h2>Rata-rata nilai nasional</h2>
                <p>Kategori kinerja: <b>Baik</b></p>
            </aside>
        </div>
    </section>

    {{-- ============ BERITA & EDUKASI (SLIDER) ============ --}}
    @php
        // CONTOH DATA. Ganti dengan data dari database/controller, misalnya: $beritas = \App\Models\Berita::latest()->take(6)->get();
        $beritas = [
            ['kategori' => 'Berita kebakaran terkini', 'judul' => 'Waspada titik panas di musim kemarau, ini yang perlu disiapkan daerah', 'ringkas' => 'Pemerintah daerah diminta memperkuat kesiapsiagaan pos pemadam dan memantau titik panas secara berkala.', 'tanggal' => '19 Sep 2026', 'url' => '#', 'ikon' => 'fa-fire', 'bg' => 'var(--red)', 'fg' => '#ffffff'],
            ['kategori' => 'Artikel mitigasi', 'judul' => 'Langkah sederhana mencegah kebakaran lahan dan permukiman', 'ringkas' => 'Panduan singkat untuk warga dan perangkat desa dalam mengurangi risiko api di lingkungan sekitar.', 'tanggal' => '17 Sep 2026', 'url' => '#', 'ikon' => 'fa-shield-alt', 'bg' => 'var(--navy)', 'fg' => '#ffffff'],
            ['kategori' => 'Siaran pers Kemendagri', 'judul' => 'Kemendagri dorong percepatan pelaporan SPM sub urusan kebakaran', 'ringkas' => 'Daerah yang belum melapor diminta segera melengkapi data agar capaian layanan dapat dipantau.', 'tanggal' => '14 Sep 2026', 'url' => '#', 'ikon' => 'fa-bullhorn', 'bg' => 'var(--yellow)', 'fg' => '#0b2e6f'],
            ['kategori' => 'Kesehatan & asap', 'judul' => 'Melindungi diri dari paparan asap kebakaran', 'ringkas' => 'Kenali tanda gangguan pernapasan dan cara mengurangi paparan asap saat kabut melanda.', 'tanggal' => '10 Sep 2026', 'url' => '#', 'ikon' => 'fa-heartbeat', 'bg' => 'var(--blue)', 'fg' => '#ffffff'],
            ['kategori' => 'Kisah sukses Pemda', 'judul' => 'Daerah dengan capaian SPM terbaik berbagi praktik baiknya', 'ringkas' => 'Cerita dari daerah yang berhasil meningkatkan mutu layanan pemadam kebakaran.', 'tanggal' => '5 Sep 2026', 'url' => '#', 'ikon' => 'fa-award', 'bg' => 'var(--red-dark)', 'fg' => '#ffffff'],
        ];
    @endphp
    <section class="sp-news" id="berita">
        <div class="sp-wrap">
            <div class="sp-head reveal">
                <div>
                    <h2>Berita &amp; edukasi</h2>
                    <p>Kabar terbaru seputar kebakaran, mitigasi, dan layanan daerah.</p>
                </div>
                <div class="sp-news__ctrl">
                    <a href="#" class="sp-news__all">Lihat semua</a>
                    <button type="button" id="newsPrev" aria-label="Berita sebelumnya"><i class="fas fa-chevron-left"></i></button>
                    <button type="button" id="newsNext" aria-label="Berita berikutnya"><i class="fas fa-chevron-right"></i></button>
                </div>
            </div>

            <div class="sp-news__track reveal" id="newsTrack" tabindex="0" aria-label="Daftar berita, geser ke samping">
                @foreach ($beritas as $b)
                    <a class="sp-news__card" href="{{ $b['url'] }}" draggable="false" style="--c: {{ $b['bg'] }}; --t: {{ $b['fg'] }};">
                        <div class="sp-news__thumb">
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
                @endforeach
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
                <label class="sp-year">
                    <span>Tahun data</span>
                    <select>
                        <option selected>2025 / 2026</option>
                    </select>
                </label>
            </div>

            <div class="sp-stats reveal--stagger">
                <div class="sp-stat">
                    <span class="sp-stat__label">Daerah tercatat</span>
                    <strong data-count="514">514</strong>
                    <small>Kabupaten / kota di Indonesia</small>
                </div>
                <div class="sp-stat">
                    <span class="sp-stat__label">Laporan masuk</span>
                    <strong data-count="490">490</strong>
                    <small>Terverifikasi sistem pusat</small>
                    <div class="sp-progress" title="490 dari 514 daerah"><i style="width: 95.3%"></i></div>
                </div>
                <div class="sp-stat">
                    <span class="sp-stat__label">Belum melapor</span>
                    <strong data-count="24">24</strong>
                    <small>Daerah menunggu pengiriman data</small>
                </div>
            </div>

            {{-- Distribusi kategori --}}
            <div class="sp-panel reveal">
                <div class="sp-panel__head">
                    <h3>Distribusi kategori penilaian daerah</h3>
                    <p>Pengelompokan mutu layanan pemadam kebakaran di setiap daerah.</p>
                </div>

                <div class="sp-dist reveal--bar" role="img" aria-label="Sangat baik 142, baik 210, cukup 98, kurang 40 daerah">
                    <i style="flex: 142; background: var(--good2)"></i>
                    <i style="flex: 210; background: var(--good)"></i>
                    <i style="flex: 98;  background: var(--mid)"></i>
                    <i style="flex: 40;  background: var(--bad)"></i>
                </div>

                <div class="sp-legend reveal--stagger">
                    <div><span class="dot" style="background: var(--good2)"></span><b>Sangat baik</b><strong data-count="142">142</strong><small>29,0% daerah</small></div>
                    <div><span class="dot" style="background: var(--good)"></span><b>Baik</b><strong data-count="210">210</strong><small>42,9% daerah</small></div>
                    <div><span class="dot" style="background: var(--mid)"></span><b>Cukup</b><strong data-count="98">98</strong><small>20,0% daerah</small></div>
                    <div><span class="dot" style="background: var(--bad)"></span><b>Kurang</b><strong data-count="40">40</strong><small>8,2% daerah</small></div>
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
                        <i class="fas fa-satellite-dish"></i> Satelit NASA (live)
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
                        <b>Menghubungkan ke satelit NASA</b>
                        <span>Memuat titik panas 24 jam terakhir (MODIS/VIIRS)</span>
                    </div>

                    <div class="sp-fire-legend">
                        <b>Tingkat keyakinan deteksi</b>
                        <div class="bar">
                            <i style="background:#fdd64b" title="Sedang (40-60%)"></i>
                            <i style="background:#ff9b57" title="Tinggi (61-84%)"></i>
                            <i style="background:#fe6a69" title="Sangat tinggi (>85%)"></i>
                        </div>
                        <div class="ends"><span>Titik panas</span><span>Api aktif</span></div>
                    </div>
                </div>
            </div>
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
                <div><span>Aceh</span><em>--</em></div>
                <div><span>Bali</span><em>--</em></div>
                <div><span>Banten</span><em>--</em></div>
                <div><span>Jawa Barat</span><em>--</em></div>
                <div><span>Jawa Tengah</span><em>--</em></div>
                <div><span>Jawa Timur</span><em>--</em></div>
                <div class="is-hot"><span>Kalimantan Barat</span><em><i class="fas fa-fire"></i> <span id="count-kalbar">0</span></em></div>
                <div class="is-hot"><span>Kalimantan Tengah</span><em><i class="fas fa-fire"></i> <span id="count-kalteng">0</span></em></div>
                <div class="is-hot"><span>Kalimantan Timur</span><em><i class="fas fa-fire"></i> <span id="count-kaltim">0</span></em></div>
                <div class="is-hot"><span>Sumatera Selatan</span><em><i class="fas fa-fire"></i> <span id="count-sumsel">0</span></em></div>
                <div class="is-hot"><span>Riau</span><em><i class="fas fa-fire"></i> <span id="count-riau">0</span></em></div>
                <div><span>Papua</span><em>--</em></div>
            </div>
            <p class="sp-note">Dan 27 provinsi lainnya.</p>
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
                        <li><a href="{{ url('/login') }}">Portal evaluasi nasional</a></li>
                        <li><a href="{{ url('/login') }}">Lapor data SPM</a></li>
                        <li><a href="{{ url('/login') }}">Pendaftaran Pemda</a></li>
                        <li><a href="#">Panduan penggunaan</a></li>
                    </ul>
                </div>

                <div>
                    <h6>Kebakaran &amp; darurat</h6>
                    <ul>
                        <li><a href="tel:112">Call center 112</a></li>
                        <li><a href="#map-section" onclick="document.getElementById('pills-fire-tab').click()">Lapor titik api aktif</a></li>
                        <li><a href="#berita">Tips pencegahan &amp; mitigasi</a></li>
                        <li><a href="#berita">Kesehatan &amp; asap</a></li>
                        <li><a href="#">Direktori pos damkar</a></li>
                    </ul>
                </div>
            </div>

            <div class="site-footer__bottom">
                <div class="region">
                    <img src="https://flagcdn.com/w20/id.png" alt="Bendera Indonesia">
                    <b>Indonesia</b>
                    <span><i class="fas fa-fire"></i> SPM Kebakaran, Nasional</span>
                </div>
                <div class="links">
                    <a href="mailto:info@kemendagri.go.id">Hubungi kami</a>
                    <a href="mailto:support@damkar.go.id">Lapor kendala teknis</a>
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
            .sp-news__all { display: none; }
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

            // Animasi ring nilai nasional (82,45 dari 100)
            var ring = document.getElementById('ringValue');
            var circumference = 578; // 2 * PI * 92
            var score = 82.45;
            requestAnimationFrame(function () {
                ring.style.strokeDashoffset = circumference * (1 - score / 100);
            });

            // Peta SPM
            var mapSpm = L.map('map-spm').setView([-0.789275, 113.921327], 5);
            L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
                attribution: '&copy; OpenStreetMap &copy; CARTO', maxZoom: 18
            }).addTo(mapSpm);

            // Peta titik api (basemap terang agar sesuai tema putih)
            var mapFire = L.map('map-fire').setView([-0.789275, 113.921327], 5);
            L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
                attribution: '&copy; OpenStreetMap &copy; CARTO | Data: NASA FIRMS', maxZoom: 18
            }).addTo(mapFire);

            var fireIcon = L.divIcon({
                html: '<div class="pulse-fire-icon"><i class="fas fa-fire"></i></div>',
                className: 'custom-leaflet-fire-icon', iconSize: [35, 35], iconAnchor: [17, 17], popupAnchor: [0, -15]
            });

            var nasaUrl = 'https://firms.modaps.eosdis.nasa.gov/data/active_fire/modis-c6.1/csv/MODIS_C6_1_South_East_Asia_24h.csv';

            async function fetchNasaData() {
                const proxies = [
                    'https://api.allorigins.win/get?url=',
                    'https://api.codetabs.com/v1/proxy?quest=',
                    'https://corsproxy.io/?'
                ];
                for (let i = 0; i < proxies.length; i++) {
                    try {
                        let response = await fetch(proxies[i] + encodeURIComponent(nasaUrl));
                        if (!response.ok) continue;
                        if (proxies[i].includes('allorigins')) {
                            let json = await response.json();
                            if (json.contents) return json.contents;
                        } else {
                            return await response.text();
                        }
                    } catch (e) {}
                }
                throw new Error('Gagal terhubung ke satelit.');
            }

            fetchNasaData()
                .then(function (csvText) {
                    document.getElementById('nasa-loading').style.display = 'none';
                    var lines = csvText.trim().split('\n');
                    var fireGroup = L.layerGroup().addTo(mapFire);

                    var countKalbar = 0, countKalteng = 0, countKaltim = 0, countSumsel = 0, countRiau = 0;

                    for (var i = 1; i < lines.length; i++) {
                        var data = lines[i].split(',');
                        if (data.length < 9) continue;
                        var lat = parseFloat(data[0]);
                        var lng = parseFloat(data[1]);
                        var confidence = parseInt(data[8]);

                        if (lat >= -11 && lat <= 6 && lng >= 95 && lng <= 141 && confidence >= 40) {
                            if (lat >= -3 && lat <= 2 && lng >= 108 && lng <= 114) countKalbar++;
                            else if (lat >= -4 && lat <= 1 && lng >= 111 && lng <= 116) countKalteng++;
                            else if (lat >= -2 && lat <= 3 && lng >= 115 && lng <= 119) countKaltim++;
                            else if (lat >= -4 && lat <= -1 && lng >= 102 && lng <= 106) countSumsel++;
                            else if (lat >= -1 && lat <= 2 && lng >= 100 && lng <= 103) countRiau++;

                            var color = confidence >= 85 ? '#fe6a69' : (confidence >= 65 ? '#ff9b57' : '#fdd64b');
                            L.circle([lat, lng], { color: color, fillColor: color, fillOpacity: 0.4, weight: 0, radius: confidence * 120 }).addTo(fireGroup);

                            if (confidence >= 85) {
                                L.marker([lat, lng], { icon: fireIcon }).addTo(fireGroup)
                                    .bindPopup('<b>Titik api aktif</b><br>Akurasi: ' + confidence + '%');
                            }
                        }
                    }

                    document.getElementById('count-kalbar').innerText = countKalbar;
                    document.getElementById('count-kalteng').innerText = countKalteng;
                    document.getElementById('count-kaltim').innerText = countKaltim;
                    document.getElementById('count-sumsel').innerText = countSumsel;
                    document.getElementById('count-riau').innerText = countRiau;
                })
                .catch(function () {
                    document.getElementById('nasa-loading').innerHTML =
                        '<b>Data satelit belum bisa dimuat</b><span>Periksa koneksi internet, lalu muat ulang halaman.</span>';
                });

            // Tab peta (tanpa ketergantungan Bootstrap JS)
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
