@extends('layouts.admin')

@section('title', 'Dashboard - SPM Kebakaran')
@section('page_title', 'Dashboard Operator')

@section('content')
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white py-3"><h6 class="m-0 fw-bold text-primary">Dataset / Riwayat Import</h6></div>
        <div class="card-body">
            @forelse($importHistories as $importHistory)
                <div class="d-flex flex-wrap justify-content-between align-items-center border-bottom py-3 gap-2">
                    <div><strong>Data SPM {{ $importHistory->year }}</strong><div class="text-muted small">{{ $importHistory->file_name }} · Jumlah data: {{ $importHistory->spm_data_count }}</div></div>
                    <div class="d-flex gap-2 align-items-center">
                        <span class="badge {{ $importHistory->status === 'published' ? 'bg-success' : 'bg-warning text-dark' }}">{{ strtoupper($importHistory->status) }}</span>
                        @if($importHistory->status === 'draft')
                            <form action="{{ route('import.publish', $importHistory) }}" method="POST">@csrf @method('PATCH')<button class="btn btn-sm btn-success">Publish</button></form>
                        @else
                            <form action="{{ route('import.draft', $importHistory) }}" method="POST">@csrf @method('PATCH')<button class="btn btn-sm btn-warning">Draft</button></form>
                        @endif
                        <form action="{{ route('import.destroy', $importHistory) }}" method="POST" onsubmit="return confirm('Hapus seluruh data import tahun {{ $importHistory->year }}? Seluruh data dari import ini akan dihapus dan harus import ulang.');">@csrf @method('DELETE')<button class="btn btn-sm btn-danger">Hapus</button></form>
                    </div>
                </div>
            @empty
                <p class="text-muted mb-0">Belum ada dataset yang diimpor.</p>
            @endforelse
        </div>
    </div>
    <form action="{{ url('/admin/dashboard') }}" method="GET" class="row g-2 align-items-end mb-4">
        <div class="col-sm-4 col-md-3">
            <label for="dashboard-year" class="form-label fw-bold">Tahun Data</label>
            <select id="dashboard-year" name="year" class="form-select" onchange="this.form.submit()" {{ $availableYears->isEmpty() ? 'disabled' : '' }}>
                @forelse($availableYears as $availableYear)
                    <option value="{{ $availableYear }}" {{ (string) $year === (string) $availableYear ? 'selected' : '' }}>
                        Tahun {{ $availableYear }}
                    </option>
                @empty
                    <option value="">Belum ada data</option>
                @endforelse
            </select>
        </div>
        @if($year)
            <div class="col-auto pb-2 text-muted">Dashboard dan peta menampilkan data tahun {{ $year }}.</div>
        @endif
    </form>

    <!-- Baris Statistik Utama -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm border-0 bg-primary text-white h-100">
                <div class="card-body">
                    <h6 class="card-title text-white-50">Total Data SPM</h6>
                    <h2 class="fw-bold">{{ number_format($totalSpmData, 0, ',', '.') }}</h2>
                    <small>Baris Data Tercatat</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 bg-success text-white h-100">
                <div class="card-body">
                    <h6 class="card-title text-white-50">Rata-rata Nasional</h6>
                    <h2 class="fw-bold">{{ number_format($avgNilaiAkhir, 2, ',', '.') }}</h2>
                    <small>Indeks Penyelenggaraan SPM</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 bg-info text-white h-100">
                <div class="card-body">
                    <h6 class="card-title text-white-50">Total Provinsi</h6>
                    <h2 class="fw-bold">{{ $totalProvinces }}</h2>
                    <small>Wilayah Tercatat</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 bg-secondary text-white h-100">
                <div class="card-body">
                    <h6 class="card-title text-white-50">Total Kabupaten/Kota</h6>
                    <h2 class="fw-bold">{{ $totalRegencies }}</h2>
                    <small>Wilayah Tercatat</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Baris Kategori Penilaian -->
    <h5 class="mb-3 text-secondary border-bottom pb-2">Distribusi Kategori Penilaian</h5>
    <div class="row">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm mb-3 border-start border-success border-4">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Sangat Baik</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $kategoriSangatBaik }} Daerah</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm mb-3 border-start border-primary border-4">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Baik</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $kategoriBaik }} Daerah</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm mb-3 border-start border-warning border-4">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Cukup</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $kategoriCukup }} Daerah</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm mb-3 border-start border-danger border-4">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Kurang</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $kategoriKurang }} Daerah</div>
                </div>
            </div>
        </div>
    </div>

    <!-- AREA GRAFIK CHART.JS -->
    <div class="row mt-4">
        <!-- Grafik 1: Distribusi Kategori -->
        <div class="col-md-5 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-chart-pie me-2"></i>Distribusi Kategori</h6>
                </div>
                <div class="card-body">
                    <canvas id="kategoriChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Grafik 2: Rata-rata Nilai per Provinsi -->
        <div class="col-md-7 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-chart-bar me-2"></i>Rata-rata Nilai Akhir per Provinsi</h6>
                </div>
                <div class="card-body">
                    <canvas id="provinsiNilaiChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-2">
        <div class="col-12 mb-4">
            <div class="card border-0 shadow-sm overflow-hidden">
                <div class="card-header bg-white py-3 d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <div>
                        <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-map-marked-alt me-2"></i>Peta Sebaran Indeks SPM Kebakaran</h6>
                        <small class="text-muted">Distribusi indeks SPM per provinsi berdasarkan data yang dipublikasikan.</small>
                    </div>
                    <label class="small fw-semibold" for="dashboard-map-year">Tahun peta
                        <select id="dashboard-map-year" class="form-select form-select-sm mt-1" {{ $mapAvailableYears->isEmpty() ? 'disabled' : '' }}>
                            @forelse ($mapAvailableYears as $availableMapYear)
                                <option value="{{ $availableMapYear }}" @selected($mapYear === $availableMapYear)>{{ $availableMapYear }}</option>
                            @empty
                                <option value="">Belum ada data</option>
                            @endforelse
                        </select>
                    </label>
                </div>
                <div class="card-body">
                    <div id="dashboard-map-breadcrumb" class="small text-muted mb-2">Indonesia</div>
                    <div id="dashboard-map-state" class="alert alert-secondary py-2 small mb-2" role="status">Memuat data peta...</div>
                    <div id="dashboard-map" role="img" aria-label="Peta persebaran SPM berdasarkan provinsi"></div>
                    <div id="dashboard-map-detail" class="mt-3" hidden></div>
                </div>
            </div>
        </div>
    </div>

    <style>
        #dashboard-map { height: 640px; width: 100%; border-radius: 14px; z-index: 1; }
        .dashboard-map-legend { background: rgba(255, 255, 255, .96); padding: 10px 12px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,.12); line-height: 24px; color: #555; }
        .dashboard-map-legend i { width: 18px; height: 18px; float: left; margin-right: 8px; opacity: 0.8; }
    </style>

    <!-- Panggil Library Chart.js dan Leaflet -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <!-- Script Inisialisasi Grafik -->
    <script>
        let mapPayload = {{ Illuminate\Support\Js::from($mapPayload) }};
        let mapDataDb = {};
        let mapDataByGeojsonCode = {};
        const geoJsonCodeCounts = {};
        const mapColors = {
            'Sangat Baik': '#198754',
            'Baik': '#0d6efd',
            'Cukup': '#ffc107',
            'Kurang': '#dc3545',
            'Belum ada data': '#cccccc',
        };

        // Data chart berasal dari query tahun aktif yang sama dengan peta.
        const labelsKategori = ['Sangat Baik', 'Baik', 'Cukup', 'Kurang'];
        const dataKategori = [{{ $kategoriSangatBaik }}, {{ $kategoriBaik }}, {{ $kategoriCukup }}, {{ $kategoriKurang }}];

        // Render Pie Chart Kategori
        const ctxKategori = document.getElementById('kategoriChart').getContext('2d');
        new Chart(ctxKategori, {
            type: 'doughnut',
            data: {
                labels: labelsKategori,
                datasets: [{
                    data: dataKategori,
                    backgroundColor: ['#198754', '#0d6efd', '#ffc107', '#dc3545'], // Warna Bootstrap
                    borderWidth: 1
                }]
            },
            options: { responsive: true, maintainAspectRatio: false }
        });

        // Data Provinsi dari Controller (di-convert ke format JSON)
        const labelsProvinsi = {!! json_encode($labelProvinsi) !!};
        const dataNilaiProvinsi = {!! json_encode($dataNilai) !!};

        // Render Bar Chart Provinsi
        const ctxProvinsi = document.getElementById('provinsiNilaiChart').getContext('2d');
        new Chart(ctxProvinsi, {
            type: 'bar',
            data: {
                labels: labelsProvinsi,
                datasets: [{
                    label: 'Rata-rata Nilai',
                    data: dataNilaiProvinsi,
                    backgroundColor: '#0d6efd',
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true, max: 100 }
                }
            }
        });

        const dashboardMap = L.map('dashboard-map').setView([-2.5489, 118.0149], 5);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(dashboardMap);

        function addText(parent, tagName, text, className) {
            const element = document.createElement(tagName);
            element.textContent = text;
            if (className) element.className = className;
            parent.appendChild(element);
            return element;
        }

        function mapKey(name) {
            let normalized = (name || '').toLocaleUpperCase('id-ID').trim()
                .replace(/^PROVINSI\s+/, '')
                .replace(/[^\p{L}\p{N}]+/gu, ' ')
                .trim();

            if (['DI YOGYAKARTA', 'DAERAH ISTIMEWA YOGYAKARTA'].includes(normalized)) return 'YOGYAKARTA';
            if (['DKI JAKARTA', 'JAKARTA'].includes(normalized)) return 'JAKARTA';

            return normalized.replace(/\s+/g, '');
        }

        function categoryForIndex(index) {
            if (index === null || index === undefined) return 'Belum ada data';
            if (index >= 85) return 'Sangat Baik';
            if (index >= 70) return 'Baik';
            if (index >= 55) return 'Cukup';
            return 'Kurang';
        }

        function rebuildMapIndexes() {
            mapDataDb = Object.fromEntries(mapPayload.provinces.map((province) => [province.map_key, province]));
            mapDataByGeojsonCode = Object.fromEntries(mapPayload.provinces
                .filter((province) => province.geojson_code && geoJsonCodeCounts[province.geojson_code] === 1)
                .map((province) => [province.geojson_code, province]));
        }

        function provinceForFeature(feature) {
            const code = String(feature.properties.KODE_PROV || '').trim();

            return mapDataByGeojsonCode[code] || mapDataDb[mapKey(feature.properties.PROVINSI)] || null;
        }

        function showRegencyDetail(provinceData, regency) {
            const detail = document.getElementById('dashboard-map-detail');
            detail.hidden = false;
            detail.replaceChildren();
            addText(detail, 'h6', `${provinceData.province_name} / ${regency.regency_name}`, 'fw-bold mb-2');
            addText(detail, 'div', `Indeks SPM: ${Number(regency.spm).toFixed(2)}`);
            addText(detail, 'div', `Kategori: ${regency.category}`);
            addText(detail, 'div', `Status: ${regency.validation_status}`);
            addText(detail, 'div', `Tahun: ${mapPayload.year}`);
        }

        function createProvincePopup(provinceName, provinceData) {
            const wrapper = document.createElement('div');
            addText(wrapper, 'h6', provinceData?.province_name || provinceName, 'fw-bold mb-1');

            if (!provinceData || provinceData.spm === null) {
                addText(wrapper, 'p', `Belum ada data yang dipublikasikan untuk tahun ${mapPayload.year ?? 'ini'}.`, 'mb-0 text-muted');
                return wrapper;
            }

            const badge = addText(wrapper, 'span', provinceData.category, 'badge');
            badge.style.backgroundColor = mapColors[provinceData.category];

            const divider = document.createElement('hr');
            divider.className = 'my-2';
            wrapper.appendChild(divider);

            const list = document.createElement('ul');
            list.className = 'list-unstyled mb-0';
            addText(list, 'li', `Indeks SPM: ${Number(provinceData.spm).toFixed(2)}`);
            addText(list, 'li', `Data Kabupaten/Kota: ${provinceData.regency_count}`);
            wrapper.appendChild(list);

            if (provinceData.regencies.length > 0) {
                const topTitle = addText(wrapper, 'strong', 'Top Kabupaten/Kota', 'd-block mt-2');
                const regencyList = document.createElement('ol');
                regencyList.className = 'mb-0 ps-3';
                provinceData.regencies.slice(0, 5).forEach((regency) => {
                    const item = document.createElement('li');
                    const button = addText(item, 'button', `${regency.regency_name} — ${Number(regency.spm).toFixed(2)}`, 'btn btn-link btn-sm p-0 text-start');
                    button.type = 'button';
                    button.addEventListener('click', () => showRegencyDetail(provinceData, regency));
                    regencyList.appendChild(item);
                });
                wrapper.appendChild(topTitle);
                wrapper.appendChild(regencyList);
            }

            const link = addText(wrapper, 'a', 'Lihat semua kabupaten/kota', 'btn btn-sm btn-primary w-100 mt-3');
            link.href = '{{ url('/provinsi') }}/' + encodeURIComponent(String(provinceData.id)) + '?year=' + encodeURIComponent(String(mapPayload.year));

            return wrapper;
        }

        function provinceColor(provinceName) {
            const provinceData = mapDataDb[mapKey(provinceName)];
            return mapColors[categoryForIndex(provinceData?.spm)];
        }

        let dashboardGeoJsonLayer;
        let dashboardGeoJsonData;
        let selectedProvinceLayer;
        fetch('{{ url("geojson/indonesia-prov.geojson") }}')
            .then(response => response.json())
            .then(data => {
                dashboardGeoJsonData = data;
                data.features.forEach((feature) => {
                    const code = String(feature.properties.KODE_PROV || '').trim();
                    geoJsonCodeCounts[code] = (geoJsonCodeCounts[code] || 0) + 1;
                });
                rebuildMapIndexes();
                dashboardGeoJsonLayer = L.geoJson(dashboardGeoJsonData, {
                    style: feature => {
                        const provinceData = provinceForFeature(feature);
                        return {
                            fillColor: mapColors[categoryForIndex(provinceData?.spm)],
                            weight: 2,
                            opacity: 1,
                            color: 'white',
                            dashArray: '3',
                            fillOpacity: 0.7
                        };
                    },
                    onEachFeature: (feature, layer) => {
                        const provinceName = feature.properties.PROVINSI;
                        const provinceData = provinceForFeature(feature);
                        layer.bindPopup(createProvincePopup(provinceName, provinceData));
                        layer.bindTooltip(`${provinceName}<br>${provinceData?.spm === null || !provinceData ? 'Belum ada data' : `${Number(provinceData.spm).toFixed(2)} · ${provinceData.category}`}`, { sticky: true });

                        layer.on({
                            mouseover: event => {
                                event.target.setStyle({ weight: 4, color: '#333', dashArray: '', fillOpacity: 0.9 });
                                event.target.bringToFront();
                            },
                            mouseout: event => dashboardGeoJsonLayer.resetStyle(event.target),
                            click: event => {
                                if (selectedProvinceLayer) dashboardGeoJsonLayer.resetStyle(selectedProvinceLayer);
                                selectedProvinceLayer = event.target;
                                selectedProvinceLayer.setStyle({ weight: 4, color: '#1f2937', dashArray: '', fillOpacity: .9 });
                                dashboardMap.fitBounds(selectedProvinceLayer.getBounds(), { padding: [24, 24] });
                                document.getElementById('dashboard-map-breadcrumb').textContent = `Indonesia / ${provinceName}`;
                            }
                        });
                    }
                }).addTo(dashboardMap);
                const mapState = document.getElementById('dashboard-map-state');
                mapState.textContent = mapPayload.year === null
                    ? 'Belum ada data dipublikasikan untuk tahun ini.'
                    : `Menampilkan data published tahun ${mapPayload.year}.`;
                mapState.className = mapPayload.year === null ? 'alert alert-secondary py-2 small mb-2' : 'alert alert-success py-2 small mb-2';
            })
            .catch(error => {
                document.getElementById('dashboard-map-state').textContent = 'Gagal memuat data peta.';
                document.getElementById('dashboard-map-state').className = 'alert alert-danger py-2 small mb-2';
                console.error('Error loading geojson:', error);
            });

        const legend = L.control({ position: 'bottomright' });
        legend.onAdd = function () {
            const div = L.DomUtil.create('div', 'dashboard-map-legend');
            addText(div, 'strong', 'Kategori SPM');
            const items = [
                ['#198754', 'Sangat Baik (>= 85)'],
                ['#0d6efd', 'Baik (70 - 84)'],
                ['#ffc107', 'Cukup (55 - 69)'],
                ['#dc3545', 'Kurang (< 55)'],
                ['#cccccc', 'Belum ada data']
            ];
            items.forEach(([color, label]) => {
                const row = document.createElement('div');
                const swatch = document.createElement('i');
                swatch.style.backgroundColor = color;
                row.appendChild(swatch);
                addText(row, 'span', label);
                div.appendChild(row);
            });
            return div;
        };
        legend.addTo(dashboardMap);

        document.getElementById('dashboard-map-year').addEventListener('change', async (event) => {
            if (!dashboardGeoJsonData) return;

            const mapState = document.getElementById('dashboard-map-state');
            mapState.textContent = 'Memuat data peta...';
            mapState.className = 'alert alert-secondary py-2 small mb-2';

            try {
                const response = await fetch(`{{ route('peta.data') }}?year=${encodeURIComponent(event.target.value)}`, {
                    headers: { Accept: 'application/json' },
                });
                if (!response.ok) throw new Error('Map data request failed.');

                mapPayload = await response.json();
                rebuildMapIndexes();
                dashboardMap.removeLayer(dashboardGeoJsonLayer);
                dashboardGeoJsonLayer = L.geoJson(dashboardGeoJsonData, {
                    style: feature => ({
                        fillColor: mapColors[categoryForIndex(provinceForFeature(feature)?.spm)],
                        weight: 2, opacity: 1, color: 'white', dashArray: '3', fillOpacity: .7,
                    }),
                    onEachFeature: (feature, layer) => {
                        const provinceName = feature.properties.PROVINSI;
                        const provinceData = provinceForFeature(feature);
                        layer.bindPopup(createProvincePopup(provinceName, provinceData));
                        layer.bindTooltip(`${provinceName}<br>${provinceData?.spm === null || !provinceData ? 'Belum ada data' : `${Number(provinceData.spm).toFixed(2)} · ${provinceData.category}`}`, { sticky: true });
                        layer.on({
                            mouseover: mapEvent => {
                                mapEvent.target.setStyle({ weight: 4, color: '#333', dashArray: '', fillOpacity: .9 });
                                mapEvent.target.bringToFront();
                            },
                            mouseout: mapEvent => dashboardGeoJsonLayer.resetStyle(mapEvent.target),
                            click: mapEvent => {
                                if (selectedProvinceLayer) dashboardGeoJsonLayer.resetStyle(selectedProvinceLayer);
                                selectedProvinceLayer = mapEvent.target;
                                selectedProvinceLayer.setStyle({ weight: 4, color: '#1f2937', dashArray: '', fillOpacity: .9 });
                                dashboardMap.fitBounds(selectedProvinceLayer.getBounds(), { padding: [24, 24] });
                                document.getElementById('dashboard-map-breadcrumb').textContent = `Indonesia / ${provinceName}`;
                            },
                        });
                    },
                }).addTo(dashboardMap);
                document.getElementById('dashboard-map-breadcrumb').textContent = 'Indonesia';
                mapState.textContent = mapPayload.year === null ? 'Belum ada data dipublikasikan untuk tahun ini.' : `Menampilkan data published tahun ${mapPayload.year}.`;
                mapState.className = mapPayload.year === null ? 'alert alert-secondary py-2 small mb-2' : 'alert alert-success py-2 small mb-2';
            } catch (error) {
                mapState.textContent = 'Gagal memuat data peta.';
                mapState.className = 'alert alert-danger py-2 small mb-2';
                console.error('Error loading map data:', error);
            }
        });
    </script>
@endsection
