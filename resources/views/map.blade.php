@extends('layouts.public')

@section('content')
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #map { height: 650px; width: 100%; border-radius: 10px; z-index: 1; }
    .info-legend { background: white; padding: 10px; border-radius: 5px; box-shadow: 0 0 15px rgba(0,0,0,0.2); line-height: 24px; color: #555; }
    .info-legend i { width: 18px; height: 18px; float: left; margin-right: 8px; opacity: 0.8; }
    .map-popup { min-width: 250px; max-width: 320px; }
    .map-popup table { font-size: .75rem; }
    .map-wrapper {
        position: relative;
        overflow: hidden;
        border-radius: 14px;
    }
    #map { height: 650px; width: 100%; border-radius: 14px; z-index: 1; }
    .info-legend { background: white; padding: 12px; border-radius: 8px; box-shadow: 0 0 15px rgba(0,0,0,0.15); line-height: 24px; color: #333; font-size: 0.85rem; font-family: inherit; }
    .info-legend strong { display: block; margin-bottom: 8px; font-size: 0.95rem; }
    .info-legend i { width: 16px; height: 16px; float: left; margin-right: 8px; opacity: 0.9; border-radius: 4px; }
    .map-popup { min-width: 260px; font-family: inherit; }
    .map-popup h6 { font-weight: 700; margin-bottom: 8px; font-size: 1rem; }
    .map-popup .badge { font-size: 0.75rem; padding: 0.35em 0.65em; }
    
    /* Side panel styling */
    .map-side-panel {
        position: absolute;
        top: 15px;
        right: 15px;
        bottom: 15px;
        width: 380px;
        background: white;
        z-index: 1000;
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(0,0,0,0.15);
        display: flex;
        flex-direction: column;
        transform: translateX(420px);
        transition: transform 0.3s cubic-bezier(0.2, 0.8, 0.2, 1);
        font-family: inherit;
    }
    .map-side-panel.is-open {
        transform: translateX(0);
    }
    .panel-header {
        padding: 16px 20px;
        border-bottom: 1px solid #edf2f7;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #f8fafc;
        border-radius: 12px 12px 0 0;
    }
    .panel-header h5 { margin: 0; font-weight: 700; font-size: 1.1rem; color: #1e293b; }
    .panel-body {
        padding: 20px;
        flex: 1;
        overflow-y: auto;
    }
    .breadcrumb-nav {
        font-size: 0.8rem;
        color: #64748b;
        margin-bottom: 12px;
    }
    .breadcrumb-nav a { text-decoration: none; color: #3b82f6; cursor: pointer; }
    .breadcrumb-nav a:hover { text-decoration: underline; }
    .regency-list-item {
        padding: 12px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        margin-bottom: 8px;
        cursor: pointer;
        transition: all 0.2s;
    }
    .regency-list-item:hover {
        border-color: #cbd5e1;
        background: #f8fafc;
    }
    .regency-name { font-weight: 600; color: #334155; margin-bottom: 4px; }
    .regency-score { display: flex; justify-content: space-between; align-items: center; font-size: 0.85rem; }
    
    .detail-card {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 16px;
        margin-bottom: 16px;
    }
    .detail-label { font-size: 0.75rem; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px; }
    .detail-value { font-size: 1.1rem; font-weight: 700; color: #0f172a; margin-bottom: 12px; }
    
    .leaflet-popup-content-wrapper { border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
    .leaflet-popup-content { margin: 16px; }
</style>

<div class="container my-5">
    <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
        <div>
            <h2 class="fw-bold mb-1">Peta Persebaran Kinerja SPM Kebakaran</h2>
            <p class="text-muted mb-0">Klik provinsi untuk melihat indeks dan ringkasan kabupaten/kota.</p>
            <h2 class="fw-bold mb-1">Peta Sebaran Indeks SPM Kebakaran</h2>
            <p class="text-muted mb-0">Distribusi indeks SPM per provinsi berdasarkan data yang dipublikasikan.</p>
        </div>
        <label class="d-flex align-items-center gap-2" for="map-year">
            <span class="fw-semibold">Tahun data</span>
            <select id="map-year" class="form-select">
            <span class="fw-semibold text-secondary">Tahun data</span>
            <select id="map-year" class="form-select shadow-sm border-0 bg-light fw-bold" style="min-width: 120px;">
                @forelse ($availableYears as $availableYear)
                    <option value="{{ $availableYear }}" @selected($year === $availableYear)>{{ $availableYear }}</option>
                @empty
                    <option value="">Belum ada data</option>
                @endforelse
            </select>
        </label>
    </div>

    <div class="card shadow-sm border-0 p-2">
    <div class="card shadow-sm border-0 p-2 bg-white map-wrapper" id="map-container">
        <div id="map"></div>
        
        <!-- Side Panel -->
        <div id="map-panel" class="map-side-panel shadow">
            <div class="panel-header">
                <h5 id="panel-title">Detail Wilayah</h5>
                <button type="button" class="btn-close" id="close-panel" aria-label="Close"></button>
            </div>
            <div class="panel-body" id="panel-content">
                <!-- Content injected via JS -->
            </div>
        </div>
    </div>
</div>

<section class="card border-0 shadow-sm mt-4 overflow-hidden" aria-labelledby="air-quality-heading">
    <div class="card-body p-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
            <div>
                <h3 class="h5 fw-bold mb-1" id="air-quality-heading"><i class="fas fa-wind text-primary me-2"></i>Kualitas Udara Real-Time</h3>
                <p class="text-secondary small mb-0">Data stasiun IQAir terdekat untuk kota-kota utama di Indonesia. Klik titik untuk melihat detail polutan dan cuaca.</p>
            </div>
            <button type="button" class="btn btn-outline-primary btn-sm" id="air-quality-refresh"><i class="fas fa-rotate me-1"></i>Muat ulang</button>
        </div>
        <div id="air-quality-status" class="small text-secondary mb-2" role="status" aria-live="polite">Memuat data IQAir…</div>
        <div id="air-quality-map" style="height: 460px; width: 100%; border-radius: 12px;"></div>
        <div class="d-flex flex-wrap gap-3 small text-secondary mt-3">
            <span><i class="fas fa-circle me-1" style="color:#16a34a"></i>Baik (0–50)</span>
            <span><i class="fas fa-circle me-1" style="color:#eab308"></i>Sedang (51–100)</span>
            <span><i class="fas fa-circle me-1" style="color:#f97316"></i>Tidak sehat untuk kelompok sensitif (101–150)</span>
            <span><i class="fas fa-circle me-1" style="color:#ef4444"></i>Tidak sehat (151–200)</span>
            <span><i class="fas fa-circle me-1" style="color:#7c3aed"></i>Sangat tidak sehat (201+)</span>
        </div>
        <p class="small text-muted mt-2 mb-0">AQI menggunakan standar Amerika Serikat (US AQI). Data diperbarui oleh stasiun IQAir; waktu pengukuran tiap kota dapat berbeda.</p>
    </div>
</section>

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    let mapPayload = {{ Illuminate\Support\Js::from($mapPayload) }};
    const colors = {
        'Sangat Baik': '#198754',
        'Baik': '#0d6efd',
        'Cukup': '#ffc107',
        'Kurang': '#dc3545',
        'Belum ada data': '#cccccc',
        'Belum ada data': '#cbd5e1',
    };
    let mapDataDb = Object.fromEntries(mapPayload.provinces.map((province) => [province.map_key, province]));
    let mapDataByGeojsonCode = {};
    const geoJsonCodeCounts = {};
    
    let mapDataDb = {};
    let currentProvinceLayer = null;

    // Inisialisasi Peta, set koordinat tengah Indonesia
    const map = L.map('map').setView([-2.5489, 118.0149], 5);
    // Normalization must match PHP exactly
    function mapKey(namaProvinsi) {
        let normalized = (namaProvinsi || '').trim().toLocaleUpperCase('id-ID');
        normalized = normalized.replace(/^PROVINSI\s+/i, '');
        normalized = normalized.replace(/[^\p{L}\p{N}]+/gu, ' ');
        normalized = normalized.replace(/\s+/gu, ' ').trim();
        if (normalized === 'DI YOGYAKARTA' || normalized === 'DAERAH ISTIMEWA YOGYAKARTA') return 'YOGYAKARTA';
        if (normalized === 'DKI JAKARTA' || normalized === 'JAKARTA') return 'JAKARTA';
        return normalized.replace(/\s+/g, '');
    }

    // Base layer dari OpenStreetMap
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    function rebuildMapIndexes() {
        mapDataDb = Object.fromEntries(mapPayload.provinces.map((province) => [province.map_key, province]));
    }
    
    rebuildMapIndexes();

    // Map initialization
    const map = L.map('map', {
        zoomControl: false // Move zoom control to avoid overlap with panel
    }).setView([-2.5489, 118.0149], 5);
    
    L.control.zoom({ position: 'topleft' }).addTo(map);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    // Fungsi untuk mewarnai provinsi berdasarkan data dari Database
    function mapKey(namaProvinsi) {
        return (namaProvinsi || '').toLocaleUpperCase('id-ID').replace(/[^\p{L}\p{N}]/gu, '');
    }

    function categoryForIndex(index) {
        if (index === null || index === undefined) return 'Belum ada data';
        if (index >= 85) return 'Sangat Baik';
        if (index >= 70) return 'Baik';
        if (index >= 55) return 'Cukup';
        return 'Kurang';
    }

    function escapeHtml(value) {
        return String(value).replace(/[&<>"']/g, (character) => ({
            '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;',
        })[character]);
        return String(value).replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' })[c]);
    }

    function getWarnaProvinsi(namaProvinsi) {
        return colors[categoryForIndex(mapDataDb[mapKey(namaProvinsi)]?.index)];
    }

    function provinceForFeature(feature) {
        const code = String(feature.properties.KODE_PROV || '').trim();

        return mapDataByGeojsonCode[code] || mapDataDb[mapKey(feature.properties.PROVINSI)];
        return mapDataDb[mapKey(feature.properties.PROVINSI)];
    }

    function rebuildMapIndexes() {
        mapDataDb = Object.fromEntries(mapPayload.provinces.map((province) => [province.map_key, province]));
        mapDataByGeojsonCode = Object.fromEntries(mapPayload.provinces
            .filter((province) => province.geojson_code && geoJsonCodeCounts[province.geojson_code] === 1)
            .map((province) => [province.geojson_code, province]));
    }

    // Fungsi gaya (style) untuk poligon geojson
    function styleFeature(feature) {
        // Coba beberapa kemungkinan nama properti bawaan dari file GeoJSON
        let namaProvinsi = feature.properties.PROVINSI;
        let dataProv = provinceForFeature(feature);

        return {
            fillColor: colors[categoryForIndex(dataProv?.index)],
            weight: 2,
            weight: 1.5,
            opacity: 1,
            color: 'white',
            dashArray: '3',
            fillOpacity: 0.7
            color: '#ffffff',
            fillOpacity: 0.8
        };
    }

    // Fungsi saat provinsi di-hover atau diklik
    function onEachFeature(feature, layer) {
        let namaProvinsi = feature.properties.PROVINSI || "Tidak Diketahui";
        let namaProvinsi = feature.properties.PROVINSI || "Wilayah Tidak Diketahui";
        let dataProv = provinceForFeature(feature);
        
        // Use real province name if matched
        if (dataProv && dataProv.province_name) {
            namaProvinsi = dataProv.province_name;
        }

        const category = categoryForIndex(dataProv?.index);
        const index = dataProv?.index === null || dataProv?.index === undefined ? 'Belum ada data' : Number(dataProv.index).toFixed(2);
        const regencyRows = dataProv?.regencies.slice(0, 5).map((regency, index) => `
            <tr><td>${index + 1}</td><td>${escapeHtml(regency.name)}</td><td>${Number(regency.index).toFixed(2)}</td><td>${categoryForIndex(regency.index)}</td></tr>
        `).join('') || '';
        const detailUrl = dataProv ? '{{ url('/provinsi') }}/${dataProv.id}?year=${mapPayload.year}' : null;
        const hasData = dataProv?.index !== null && dataProv?.index !== undefined;
        const indexStr = hasData ? Number(dataProv.index).toFixed(2) : '-';
        
        const popupContent = `
            <div class="map-popup">
                <h6 class="fw-bold mb-2">${escapeHtml(namaProvinsi)}</h6>
                <div><strong>Indeks SPM:</strong> ${index}</div>
                <div><strong>Kategori:</strong> <span class="badge" style="background-color: ${colors[category]}">${category}</span></div>
                <div><strong>Jumlah Kab/Kota:</strong> ${dataProv?.total_regencies ?? 0}</div>
                ${regencyRows ? `<hr class="my-2"><table class="table table-sm mb-2"><thead><tr><th>No</th><th>Kabupaten/Kota</th><th>Indeks</th><th>Kategori</th></tr></thead><tbody>${regencyRows}</tbody></table>` : ''}
                ${detailUrl ? `<a href="${detailUrl}" class="btn btn-sm btn-primary w-100">Lihat Detail${dataProv.regencies.length > 5 ? ' / Semua' : ''}</a>` : ''}
                <h6 class="text-dark">${escapeHtml(namaProvinsi)}</h6>
                ${hasData ? `
                    <div class="d-flex justify-content-between mb-2 pb-2 border-bottom">
                        <span class="text-secondary">Indeks SPM</span>
                        <strong class="fs-5">${indexStr}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-3 align-items-center">
                        <span class="text-secondary">Kategori</span>
                        <span class="badge text-white" style="background-color: ${colors[category]}">${category}</span>
                    </div>
                    <div class="mb-3 text-secondary small">
                        ${dataProv.regency_count} Kabupaten/Kota terdata
                    </div>
                    <button class="btn btn-sm btn-primary w-100 fw-semibold view-province-btn" data-prov-key="${escapeHtml(dataProv.map_key)}">
                        Lihat Detail Provinsi
                    </button>
                ` : `
                    <div class="alert alert-light border text-secondary mb-0 p-2 text-center small">
                        Belum ada data dipublikasikan untuk tahun ini.
                    </div>
                `}
            </div>
        `;
        
        layer.bindPopup(popupContent);
        layer.bindTooltip(`${namaProvinsi}<br>${index} · ${category}`, { sticky: true });
        layer.bindTooltip(`<strong>${escapeHtml(namaProvinsi)}</strong><br>SPM: ${indexStr}`, { sticky: true, className: 'shadow-sm border-0' });

        // Efek hover
        layer.on({
            mouseover: function (e) {
                var layer = e.target;
                layer.setStyle({ weight: 4, color: '#333', dashArray: '', fillOpacity: 0.9 });
                layer.bringToFront();
                var l = e.target;
                l.setStyle({ weight: 3, color: '#1e293b', fillOpacity: 0.95 });
                if (!L.Browser.ie && !L.Browser.opera && !L.Browser.edge) { l.bringToFront(); }
            },
            mouseout: function (e) {
                geoJsonLayer.resetStyle(e.target);
                if (currentProvinceLayer) {
                    currentProvinceLayer.setStyle({ weight: 3, color: '#0f172a', fillOpacity: 1 });
                    currentProvinceLayer.bringToFront();
                }
            },
            click: function (e) {
                if (currentProvinceLayer) geoJsonLayer.resetStyle(currentProvinceLayer);
                currentProvinceLayer = e.target;
                currentProvinceLayer.setStyle({ weight: 3, color: '#0f172a', fillOpacity: 1 });
                currentProvinceLayer.bringToFront();
            }
        });
    }

    // Load file GeoJSON
    const panel = document.getElementById('map-panel');
    const panelContent = document.getElementById('panel-content');
    const panelTitle = document.getElementById('panel-title');
    
    document.getElementById('close-panel').addEventListener('click', () => {
        panel.classList.remove('is-open');
        if (currentProvinceLayer) {
            geoJsonLayer.resetStyle(currentProvinceLayer);
            currentProvinceLayer = null;
        }
        map.setView([-2.5489, 118.0149], 5);
    });

    document.addEventListener('click', function(e) {
        if (e.target && e.target.classList.contains('view-province-btn')) {
            const provKey = e.target.getAttribute('data-prov-key');
            const dataProv = mapDataDb[provKey];
            if (dataProv) {
                map.closePopup();
                showProvincePanel(dataProv);
                
                geoJsonLayer.eachLayer(function(layer) {
                    if (mapKey(layer.feature.properties.PROVINSI) === provKey) {
                        map.fitBounds(layer.getBounds(), { paddingRight: [400, 0] });
                    }
                });
            }
        }
    });

    function showProvincePanel(dataProv) {
        panelTitle.textContent = "Data Kabupaten/Kota";
        
        let html = `
            <div class="breadcrumb-nav">
                Indonesia / <strong class="text-dark">${escapeHtml(dataProv.province_name)}</strong>
            </div>
            <div class="detail-card mb-4 border-primary border-opacity-25 bg-primary bg-opacity-10">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="detail-label text-primary">Indeks Provinsi</div>
                    <span class="badge" style="background-color: ${colors[dataProv.category]}">${dataProv.category}</span>
                </div>
                <div class="detail-value text-primary fs-3">${Number(dataProv.index).toFixed(2)}</div>
            </div>
            
            <h6 class="fw-bold mb-3 text-secondary text-uppercase" style="font-size: 0.8rem; letter-spacing: 0.05em;">Daftar Kabupaten/Kota</h6>
        `;
        
        if (dataProv.regencies && dataProv.regencies.length > 0) {
            dataProv.regencies.forEach(reg => {
                html += `
                    <div class="regency-list-item" onclick="showRegencyDetail('${escapeHtml(dataProv.map_key)}', ${reg.id})">
                        <div class="regency-name">${escapeHtml(reg.regency_name)}</div>
                        <div class="regency-score">
                            <span class="text-secondary fw-medium">${Number(reg.index).toFixed(2)}</span>
                            <span class="badge rounded-pill text-white" style="background-color: ${colors[reg.category]}; opacity: 0.9">${reg.category}</span>
                        </div>
                    </div>
                `;
            });
            html += `<div class="mt-3 text-center small text-muted fst-italic">Catatan: Batas wilayah kabupaten/kota pada peta belum tersedia. Silakan pilih dari daftar di atas.</div>`;
        } else {
            html += `<div class="alert alert-light border text-secondary text-center small">Belum ada data kabupaten/kota yang dipublikasikan.</div>`;
        }
        
        panelContent.innerHTML = html;
        panel.classList.add('is-open');
    }
    
    window.showRegencyDetail = function(provKey, regencyId) {
        const dataProv = mapDataDb[provKey];
        const regency = dataProv.regencies.find(r => r.id === regencyId);
        if (!regency) return;
        
        panelTitle.textContent = "Detail SPM Wilayah";
        
        const html = `
            <div class="breadcrumb-nav">
                Indonesia / <a onclick="showProvincePanel(mapDataDb['${escapeHtml(provKey)}'])">${escapeHtml(dataProv.province_name)}</a> / 
                <strong class="text-dark">${escapeHtml(regency.regency_name)}</strong>
            </div>
            
            <button class="btn btn-sm btn-outline-secondary mb-4 w-100" onclick="showProvincePanel(mapDataDb['${escapeHtml(provKey)}'])">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar Provinsi
            </button>
            
            <div class="detail-card">
                <div class="detail-label">Indeks SPM</div>
                <div class="detail-value fs-2">${Number(regency.index).toFixed(2)}</div>
                
                <div class="detail-label mt-3">Kategori</div>
                <div class="mb-3"><span class="badge fs-6 py-2 px-3 text-white" style="background-color: ${colors[regency.category]}">${regency.category}</span></div>
                
                <div class="detail-label mt-3">Provinsi</div>
                <div class="detail-value fs-6 fw-normal">${escapeHtml(dataProv.province_name)}</div>
                
                <div class="detail-label mt-3">Status Data</div>
                <div class="detail-value fs-6 fw-normal"><span class="badge bg-success bg-opacity-10 text-success border border-success">Dipublikasikan</span></div>
                
                <div class="detail-label mt-3">Tahun Data</div>
                <div class="detail-value fs-6 fw-normal">${escapeHtml(mapPayload.year)}</div>
            </div>
        `;
        panelContent.innerHTML = html;
    };

    let geoJsonLayer;
    let geoJsonData;
    
    const mapContainer = document.getElementById('map-container');
    const loaderHtml = `<div id="map-loader" class="position-absolute top-50 start-50 translate-middle bg-white p-3 rounded shadow z-3 text-center">
        <div class="spinner-border text-primary mb-2" role="status"></div>
        <div class="fw-semibold text-secondary">Memuat data peta...</div>
    </div>`;
    mapContainer.insertAdjacentHTML('beforeend', loaderHtml);
    
    fetch('{{ url("geojson/indonesia-prov.geojson") }}')
        .then(response => response.json())
        .then(data => {
            data.features.forEach((feature) => {
                const code = String(feature.properties.KODE_PROV || '').trim();
                geoJsonCodeCounts[code] = (geoJsonCodeCounts[code] || 0) + 1;
            });
            rebuildMapIndexes();
            const loader = document.getElementById('map-loader');
            if (loader) loader.remove();
            geoJsonData = data;
            geoJsonLayer = L.geoJson(geoJsonData, {
                style: styleFeature,
                onEachFeature: onEachFeature
            }).addTo(map);
        })
        .catch(error => console.error('Error loading geojson:', error));
        .catch(error => {
            const loader = document.getElementById('map-loader');
            if (loader) loader.innerHTML = `<div class="text-danger fw-bold"><i class="fas fa-exclamation-triangle"></i> Gagal memuat data peta.</div>`;
            console.error('Error loading geojson:', error);
        });

    // Menambahkan Legenda di pojok peta
    let legend = L.control({position: 'bottomright'});
    legend.onAdd = function (map) {
        let div = L.DomUtil.create('div', 'info-legend');
        div.innerHTML += '<strong>Kategori SPM</strong><br>';
        div.innerHTML += '<strong>Kategori Indeks SPM</strong>';
        div.innerHTML += '<i style="background: #198754"></i> Sangat Baik (>= 85)<br>';
        div.innerHTML += '<i style="background: #0d6efd"></i> Baik (70 - 84)<br>';
        div.innerHTML += '<i style="background: #ffc107"></i> Cukup (55 - 69)<br>';
        div.innerHTML += '<i style="background: #0d6efd"></i> Baik (70 - 84.99)<br>';
        div.innerHTML += '<i style="background: #ffc107"></i> Cukup (55 - 69.99)<br>';
        div.innerHTML += '<i style="background: #dc3545"></i> Kurang (< 55)<br>';
        div.innerHTML += '<i style="background: #cccccc"></i> Belum ada data';
        div.innerHTML += '<i style="background: #cbd5e1"></i> Belum ada data';
        return div;
    };
    legend.addTo(map);

    document.getElementById('map-year').addEventListener('change', async (event) => {
        if (!geoJsonData) {
            return;
        if (!geoJsonData) return;
        
        panel.classList.remove('is-open');
        if (currentProvinceLayer) {
            geoJsonLayer.resetStyle(currentProvinceLayer);
            currentProvinceLayer = null;
        }
        
        mapContainer.insertAdjacentHTML('beforeend', loaderHtml);

        const response = await fetch(`{{ route('peta.data') }}?year=${encodeURIComponent(event.target.value)}`, {
            headers: { Accept: 'application/json' },
        });
        try {
            const response = await fetch(`{{ route('peta.data') }}?year=${encodeURIComponent(event.target.value)}`, {
                headers: { Accept: 'application/json' },
            });

        if (!response.ok) {
            console.error('Data peta tidak dapat dimuat.');
            return;
            if (!response.ok) throw new Error('Network response was not ok');

            mapPayload = await response.json();
            rebuildMapIndexes();
            
            map.removeLayer(geoJsonLayer);
            geoJsonLayer = L.geoJson(geoJsonData, { style: styleFeature, onEachFeature: onEachFeature }).addTo(map);
            window.history.replaceState({}, '', `{{ route('peta') }}?year=${mapPayload.year}`);
        } catch (error) {
            console.error('Data peta tidak dapat dimuat.', error);
            alert('Gagal memuat data untuk tahun yang dipilih.');
        } finally {
            const loader = document.getElementById('map-loader');
            if (loader) loader.remove();
        }

        mapPayload = await response.json();
        rebuildMapIndexes();
        map.removeLayer(geoJsonLayer);
        geoJsonLayer = L.geoJson(geoJsonData, { style: styleFeature, onEachFeature: onEachFeature }).addTo(map);
        window.history.replaceState({}, '', `{{ route('peta') }}?year=${mapPayload.year}`);
    });

</script>

<script>
    (() => {
        const target = document.getElementById('air-quality-map');
        if (!target || !window.L) return;

        const map = L.map(target, { scrollWheelZoom: false }).setView([-2.5, 118], 4.5);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors',
            maxZoom: 19,
        }).addTo(map);
        const markers = L.layerGroup().addTo(map);
        const status = document.getElementById('air-quality-status');
        const refreshButton = document.getElementById('air-quality-refresh');
        const format = (value, suffix = '') => value === null || value === undefined || value === '' ? 'Tidak tersedia' : `${value}${suffix}`;
        const escape = (value) => String(value ?? '').replace(/[&<>"']/g, (character) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' })[character]);
        const aqiColor = (aqi) => {
            if (aqi === null || aqi === undefined) return '#64748b';
            if (aqi <= 50) return '#16a34a';
            if (aqi <= 100) return '#eab308';
            if (aqi <= 150) return '#f97316';
            if (aqi <= 200) return '#ef4444';
            if (aqi <= 300) return '#7c3aed';
            return '#7f1d1d';
        };
        const aqiLabel = (aqi) => {
            if (aqi === null || aqi === undefined) return 'AQI tidak tersedia';
            if (aqi <= 50) return 'Baik';
            if (aqi <= 100) return 'Sedang';
            if (aqi <= 150) return 'Tidak sehat untuk kelompok sensitif';
            if (aqi <= 200) return 'Tidak sehat';
            if (aqi <= 300) return 'Sangat tidak sehat';
            return 'Berbahaya';
        };
        const pollutantNames = { P2: 'PM2.5', P1: 'PM1', P10: 'PM10', O3: 'Ozon', N2: 'Nitrogen dioksida', S2: 'Sulfur dioksida', CO: 'Karbon monoksida' };
        const dateLabel = (value) => {
            if (!value) return 'Waktu pengukuran tidak tersedia';
            const date = new Date(value);
            return Number.isNaN(date.getTime()) ? escape(value) : new Intl.DateTimeFormat('id-ID', { dateStyle: 'medium', timeStyle: 'short', timeZone: 'Asia/Jakarta' }).format(date) + ' WIB';
        };

        async function loadAirQuality() {
            refreshButton.disabled = true;
            status.textContent = 'Mengambil data kualitas udara dari IQAir…';
            try {
                const response = await fetch(@json(route('peta.air-quality')), { headers: { Accept: 'application/json' } });
                const payload = await response.json();
                if (!response.ok) throw new Error(payload.message || 'Data kualitas udara belum dapat dimuat.');

                markers.clearLayers();
                (payload.cities || []).forEach((city) => {
                    const aqi = city.aqi_us !== null && city.aqi_us !== undefined && Number.isFinite(Number(city.aqi_us))
                        ? Number(city.aqi_us)
                        : null;
                    const color = aqiColor(aqi);
                    const coordinates = [Number(city.latitude), Number(city.longitude)];
                    if (!coordinates.every(Number.isFinite)) return;

                    const marker = L.circleMarker(coordinates, {
                        radius: 11, color: '#ffffff', weight: 2, fillColor: color, fillOpacity: 0.95,
                    }).addTo(markers);
                    const pollutants = Object.entries(city.pollutants || {})
                        .filter(([, item]) => item.concentration !== null && item.concentration !== undefined)
                        .map(([key, item]) => `<div class="d-flex justify-content-between gap-3 py-1 border-bottom"><span>${escape(pollutantNames[key] || key)}</span><strong>${escape(item.concentration)} µg/m³</strong></div>`)
                        .join('');
                    const mainPollutant = pollutantNames[String(city.main_pollutant || '').toUpperCase()] || city.main_pollutant || 'Tidak tersedia';
                    const weather = city.weather || {};
                    marker.bindPopup(`<div style="min-width:250px;max-width:320px;font-family:inherit">
                        <div class="text-uppercase small text-secondary fw-semibold">${escape(city.state || 'Indonesia')}</div>
                        <h6 class="fw-bold mb-2">${escape(city.city || city.requested_city)}</h6>
                        <div class="d-flex align-items-center gap-2 mb-2"><span style="font-size:2rem;font-weight:800;color:${color}">${escape(aqi ?? '—')}</span><span><strong>US AQI</strong><br><span style="color:${color}">${escape(aqiLabel(aqi))}</span></span></div>
                        <div class="small mb-2"><strong>Polutan utama:</strong> ${escape(mainPollutant)}<br><strong>Diukur:</strong> ${dateLabel(city.measured_at)}</div>
                        <div class="small fw-semibold mt-2 mb-1">Rincian polutan</div>${pollutants || '<div class="small text-secondary">Rincian polutan tidak tersedia pada paket data ini.</div>'}
                        <div class="small mt-2 pt-2 border-top"><strong>Cuaca:</strong> ${format(weather.temperature_c, ' °C')} · kelembapan ${format(weather.humidity_percent, '%')} · angin ${format(weather.wind_speed_m_s, ' m/s')}</div>
                        <div class="small text-muted mt-2">AQI CN: ${format(city.aqi_cn)}</div>
                    </div>`);
                    marker.bindTooltip(`${escape(city.city || city.requested_city)} · US AQI ${escape(aqi ?? '—')}`);
                });

                status.textContent = payload.count
                    ? `${payload.count} lokasi berhasil dimuat · respons diterima ${dateLabel(payload.retrieved_at)}.`
                    : (payload.message || 'Belum ada data stasiun IQAir untuk lokasi ini.');
            } catch (error) {
                status.textContent = error.message || 'Gagal memuat data IQAir. Coba lagi beberapa saat.';
            } finally {
                refreshButton.disabled = false;
            }
        }

        refreshButton.addEventListener('click', loadAirQuality);
        if ('IntersectionObserver' in window) {
            const observer = new IntersectionObserver((entries) => {
                if (!entries.some((entry) => entry.isIntersecting)) return;
                observer.disconnect();
                loadAirQuality();
            }, { rootMargin: '150px' });
            observer.observe(target);
        } else {
            loadAirQuality();
        }
    })();
</script>
@endsection
