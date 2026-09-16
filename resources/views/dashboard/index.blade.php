@extends('layouts.admin')

@section('title', 'Dashboard - SPM Kebakaran')
@section('page_title', 'Dashboard Operator')

@section('content')
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
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-map-marked-alt me-2"></i>Peta Persebaran SPM</h6>
                </div>
                <div class="card-body">
                    <div id="dashboard-map" role="img" aria-label="Peta persebaran SPM berdasarkan provinsi"></div>
                </div>
            </div>
        </div>
    </div>

    <style>
        #dashboard-map { height: 600px; width: 100%; border-radius: 10px; z-index: 1; }
        .dashboard-map-legend { background: white; padding: 10px; border-radius: 5px; box-shadow: 0 0 15px rgba(0,0,0,0.2); line-height: 24px; color: #555; }
        .dashboard-map-legend i { width: 18px; height: 18px; float: left; margin-right: 8px; opacity: 0.8; }
    </style>

    <!-- Panggil Library Chart.js dan Leaflet -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <!-- Script Inisialisasi Grafik -->
    <script>
        const mapDataDb = {{ Illuminate\Support\Js::from($mapData) }};

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

        function createProvincePopup(provinceName, provinceData) {
            const wrapper = document.createElement('div');
            addText(wrapper, 'h6', provinceName.toUpperCase(), 'fw-bold mb-1 text-center');

            const badge = addText(wrapper, 'span', provinceData.kategori, 'badge');
            badge.style.backgroundColor = provinceData.warna;

            const divider = document.createElement('hr');
            divider.className = 'my-2';
            wrapper.appendChild(divider);

            const list = document.createElement('ul');
            list.className = 'list-unstyled mb-0';
            addText(list, 'li', `Rata-rata Nilai: ${provinceData.rata_nilai}`);
            addText(list, 'li', `Total Pos: ${provinceData.total_pos}`);
            addText(list, 'li', `Total SDM: ${provinceData.total_sdm}`);
            wrapper.appendChild(list);

            const link = addText(wrapper, 'a', 'Lihat Kabupaten/Kota', 'btn btn-sm btn-primary w-100 mt-3');
            link.href = '{{ url('/provinsi') }}/' + encodeURIComponent(String(provinceData.id));

            return wrapper;
        }

        function provinceColor(provinceName) {
            const provinceData = mapDataDb[provinceName?.toUpperCase()];
            return provinceData ? provinceData.warna : '#cccccc';
        }

        let dashboardGeoJsonLayer;
        fetch('{{ url("geojson/indonesia-prov.geojson") }}')
            .then(response => response.json())
            .then(data => {
                dashboardGeoJsonLayer = L.geoJson(data, {
                    style: feature => {
                        const provinceName = feature.properties.Propinsi || feature.properties.name || feature.properties.provinsi || feature.properties.state;
                        return {
                            fillColor: provinceColor(provinceName),
                            weight: 2,
                            opacity: 1,
                            color: 'white',
                            dashArray: '3',
                            fillOpacity: 0.7
                        };
                    },
                    onEachFeature: (feature, layer) => {
                        const provinceName = feature.properties.Propinsi || feature.properties.name || feature.properties.provinsi || feature.properties.state || 'Tidak Diketahui';
                        const provinceData = mapDataDb[provinceName.toUpperCase()];

                        if (provinceData) {
                            layer.bindPopup(createProvincePopup(provinceName, provinceData));
                        } else {
                            const emptyPopup = document.createElement('div');
                            addText(emptyPopup, 'strong', provinceName);
                            addText(emptyPopup, 'br', '');
                            addText(emptyPopup, 'span', 'Belum ada data.');
                            layer.bindPopup(emptyPopup);
                        }

                        layer.on({
                            mouseover: event => {
                                event.target.setStyle({ weight: 4, color: '#333', dashArray: '', fillOpacity: 0.9 });
                                event.target.bringToFront();
                            },
                            mouseout: event => dashboardGeoJsonLayer.resetStyle(event.target)
                        });
                    }
                }).addTo(dashboardMap);
            })
            .catch(error => console.error('Error loading geojson:', error));

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
    </script>
@endsection
