@extends('layouts.public')

@section('content')
<!-- Hero Section -->
<div class="hero-section text-center shadow-sm">
    <div class="container">
        <h1 class="display-5 fw-bold mb-3">Dashboard Penyelenggaraan SPM</h1>
        <p class="lead mb-4">Sub Urusan Kebakaran Tingkat Daerah Provinsi dan Kabupaten/Kota</p>
        <a href="#statistik" class="btn btn-warning btn-lg px-4 rounded-pill fw-bold text-dark shadow">Lihat Data Nasional</a>
    </div>
</div>

<div class="container mt-5" id="statistik">
    <div class="text-center mb-5">
        <h3 class="fw-bold text-secondary">Ringkasan Capaian Nasional</h3>
        <p class="text-muted">Statistik dan peta menggunakan tahun data aktif yang sama.</p>
    </div>

    <form action="{{ route('home') }}#statistik" method="GET" class="row justify-content-center mb-4">
        <div class="col-sm-5 col-md-4">
            <label for="statistik-year" class="form-label fw-bold">Tahun Data Aktif</label>
            <select id="statistik-year" name="year" class="form-select" onchange="this.form.submit()" {{ $availableYears->isEmpty() ? 'disabled' : '' }}>
                @forelse ($availableYears as $availableYear)
                    <option value="{{ $availableYear }}" @selected((string) $year === (string) $availableYear)>Tahun {{ $availableYear }}</option>
                @empty
                    <option>Belum ada data</option>
                @endforelse
            </select>
        </div>
    </form>

    <!-- Baris Statistik -->
    <div class="row text-center mb-5 justify-content-center">
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm border-0 bg-white h-100 py-4 rounded-4">
                <h6 class="text-muted text-uppercase fw-bold">Total Daerah</h6>
                <h1 class="fw-bold text-dark display-5">{{ $totalRegencies + $totalProvinces }}</h1>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm border-0 bg-white h-100 py-4 rounded-4">
                <h6 class="text-muted text-uppercase fw-bold">Data Tercatat</h6>
                <h1 class="fw-bold text-primary display-5">{{ $totalSpmData }}</h1>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm border-0 bg-white h-100 py-4 rounded-4">
                <h6 class="text-muted text-uppercase fw-bold">Rata-rata Nilai</h6>
                <h1 class="fw-bold text-success display-5">{{ number_format($avgNilaiAkhir, 2, ',', '.') }}</h1>
            </div>
        </div>
    </div>

    <div class="text-center mb-4">
        <h4 class="fw-bold text-secondary">Distribusi Kategori Penilaian Daerah</h4>
    </div>

    <!-- Baris Kategori -->
    <div class="row text-center">
        <div class="col-md-3 mb-3">
            <div class="p-4 bg-success text-white rounded-4 shadow-sm h-100">
                <i class="fas fa-star fa-2x mb-3 text-white-50"></i>
                <h5 class="fw-bold mb-1">Sangat Baik</h5>
                <h2 class="mb-0">{{ $kategoriSangatBaik }}</h2>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="p-4 bg-primary text-white rounded-4 shadow-sm h-100">
                <i class="fas fa-check-circle fa-2x mb-3 text-white-50"></i>
                <h5 class="fw-bold mb-1">Baik</h5>
                <h2 class="mb-0">{{ $kategoriBaik }}</h2>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="p-4 bg-warning text-dark rounded-4 shadow-sm h-100">
                <i class="fas fa-minus-circle fa-2x mb-3 text-black-50"></i>
                <h5 class="fw-bold mb-1">Cukup</h5>
                <h2 class="mb-0">{{ $kategoriCukup }}</h2>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="p-4 bg-danger text-white rounded-4 shadow-sm h-100">
                <i class="fas fa-exclamation-triangle fa-2x mb-3 text-white-50"></i>
                <h5 class="fw-bold mb-1">Kurang</h5>
                <h2 class="mb-0">{{ $kategoriKurang }}</h2>
            </div>
        </div>
    </div>
</div>

<div class="container mt-5">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3">
            <h4 class="mb-0 fw-bold text-secondary"><i class="fas fa-map-marked-alt me-2"></i>Peta Sebaran SPM</h4>
            <small class="text-muted">@if ($year) Data tahun {{ $year }}. @else Belum ada data SPM untuk ditampilkan. @endif</small>
        </div>
        <div class="card-body"><div id="spm-map" role="img" aria-label="Peta sebaran SPM berdasarkan provinsi"></div></div>
    </div>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<style>#spm-map { height: 600px; width: 100%; border-radius: 10px; } .spm-map-legend { background: white; padding: 10px; line-height: 24px; } .spm-map-legend i { width: 18px; height: 18px; float: left; margin-right: 8px; }</style>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
const mapDataDb = {{ Illuminate\Support\Js::from($mapData) }};
const activeYear = {{ Illuminate\Support\Js::from($year) }};
const spmMap = L.map('spm-map').setView([-2.5489, 118.0149], 5);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '&copy; OpenStreetMap contributors' }).addTo(spmMap);
const provinceKey = name => String(name || '').replace(/[^a-z0-9]/gi, '').toUpperCase();
function addText(parent, tag, text, className) { const element = document.createElement(tag); element.textContent = text; if (className) element.className = className; parent.appendChild(element); return element; }
function popup(provinceName, data) { const box = document.createElement('div'); addText(box, 'h6', provinceName.toUpperCase(), 'fw-bold mb-1'); const badge = addText(box, 'span', data.kategori, 'badge'); badge.style.backgroundColor = data.warna; const list = document.createElement('ul'); list.className = 'list-unstyled mt-2 mb-0'; addText(list, 'li', `Rata-rata Nilai: ${data.rata_nilai}`); addText(list, 'li', `Total Pos: ${data.total_pos}`); addText(list, 'li', `Total SDM: ${data.total_sdm}`); box.appendChild(list); const link = addText(box, 'a', 'Lihat Kabupaten/Kota', 'btn btn-sm btn-primary w-100 mt-3'); link.href = `{{ url('/provinsi') }}/${encodeURIComponent(String(data.id))}?year=${encodeURIComponent(String(activeYear))}`; return box; }
let geoJsonLayer;
fetch('{{ url('geojson/indonesia-prov.geojson') }}').then(response => response.json()).then(data => { geoJsonLayer = L.geoJson(data, { style: feature => { const name = feature.properties.PROVINSI || feature.properties.Propinsi || feature.properties.name; const province = mapDataDb[provinceKey(name)]; return { fillColor: province ? province.warna : '#cccccc', weight: 2, color: 'white', fillOpacity: .7 }; }, onEachFeature: (feature, layer) => { const name = feature.properties.PROVINSI || feature.properties.Propinsi || feature.properties.name || 'Tidak Diketahui'; const province = mapDataDb[provinceKey(name)]; if (province) { layer.bindPopup(popup(name, province)); } else { const empty = document.createElement('div'); addText(empty, 'strong', name); empty.appendChild(document.createElement('br')); addText(empty, 'span', 'Belum ada data.'); layer.bindPopup(empty); } layer.on({ mouseover: event => event.target.setStyle({ weight: 4, color: '#333' }), mouseout: event => geoJsonLayer.resetStyle(event.target) }); } }).addTo(spmMap); }).catch(error => console.error('Error loading geojson:', error));
const legend = L.control({ position: 'bottomright' }); legend.onAdd = () => { const div = L.DomUtil.create('div', 'spm-map-legend'); addText(div, 'strong', 'Kategori SPM'); [['#198754', 'Sangat Baik'], ['#0d6efd', 'Baik'], ['#ffc107', 'Cukup'], ['#dc3545', 'Kurang'], ['#cccccc', 'Belum ada data']].forEach(([color, label]) => { const row = document.createElement('div'); const swatch = document.createElement('i'); swatch.style.backgroundColor = color; row.appendChild(swatch); addText(row, 'span', label); div.appendChild(row); }); return div; }; legend.addTo(spmMap);
</script>
@endsection
