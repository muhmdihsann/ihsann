@extends('layouts.public')

@section('content')
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #map { height: 600px; width: 100%; border-radius: 10px; z-index: 1;}
    .info-legend { background: white; padding: 10px; border-radius: 5px; box-shadow: 0 0 15px rgba(0,0,0,0.2); line-height: 24px; color: #555; }
    .info-legend i { width: 18px; height: 18px; float: left; margin-right: 8px; opacity: 0.8; }
</style>

<div class="container my-5">
    <div class="text-center mb-4">
        <h2 class="fw-bold">Peta Persebaran Kinerja SPM Kebakaran</h2>
        <p class="text-muted">Klik pada area provinsi untuk melihat detail ringkasan.</p>
    </div>

    <div class="card shadow-sm border-0 p-2">
        <div id="map"></div>
    </div>
</div>

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    // Data dari Controller
    const mapDataDb = {!! json_encode($mapData) !!};

    // Inisialisasi Peta, set koordinat tengah Indonesia
    const map = L.map('map').setView([-2.5489, 118.0149], 5);

    // Base layer dari OpenStreetMap
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    // Fungsi untuk mewarnai provinsi berdasarkan data dari Database
    function getWarnaProvinsi(namaProvinsi) {
        if(!namaProvinsi) return '#cccccc';

        let namaUpper = namaProvinsi.toUpperCase();
        if (mapDataDb[namaUpper]) {
            return mapDataDb[namaUpper].warna;
        }
        return '#cccccc'; // Abu-abu jika tidak ada data
    }

    // Fungsi gaya (style) untuk poligon geojson
    function styleFeature(feature) {
        // Coba beberapa kemungkinan nama properti bawaan dari file GeoJSON
        let namaProvinsi = feature.properties.Propinsi || feature.properties.name || feature.properties.provinsi || feature.properties.state;

        return {
            fillColor: getWarnaProvinsi(namaProvinsi),
            weight: 2,
            opacity: 1,
            color: 'white',
            dashArray: '3',
            fillOpacity: 0.7
        };
    }

    // Fungsi saat provinsi di-hover atau diklik
    function onEachFeature(feature, layer) {
        let namaProvinsi = feature.properties.Propinsi || feature.properties.name || feature.properties.provinsi || feature.properties.state || "Tidak Diketahui";
        let dataProv = mapDataDb[namaProvinsi.toUpperCase()];

        if (dataProv) {
            // Popup HTML (TOMBOL SUDAH DITAMBAHKAN DI SINI)
            let popupContent = `
                <div class="text-center">
                    <h6 class="fw-bold mb-1">${namaProvinsi.toUpperCase()}</h6>
                    <span class="badge" style="background-color: ${dataProv.warna}">${dataProv.kategori}</span>
                    <hr class="my-2">
                </div>
                <ul class="list-unstyled mb-0" style="font-size: 0.9em;">
                    <li><strong>Rata-rata Nilai:</strong> ${dataProv.rata_nilai}</li>
                    <li><strong>Total Pos:</strong> ${dataProv.total_pos}</li>
                    <li><strong>Total SDM:</strong> ${dataProv.total_sdm}</li>
                </ul>
                <div class="text-center mt-3">
                    <a href="{{ url('/provinsi') }}/${dataProv.id}" class="btn btn-sm btn-primary w-100" style="font-size: 0.8em;">Lihat Kabupaten/Kota</a>
                </div>
            `;
            layer.bindPopup(popupContent);
        } else {
            layer.bindPopup(`<b>${namaProvinsi}</b><br>Belum ada data.`);
        }

        // Efek hover
        layer.on({
            mouseover: function (e) {
                var layer = e.target;
                layer.setStyle({ weight: 4, color: '#333', dashArray: '', fillOpacity: 0.9 });
                layer.bringToFront();
            },
            mouseout: function (e) {
                geoJsonLayer.resetStyle(e.target);
            }
        });
    }

    // Load file GeoJSON
    let geoJsonLayer;
    fetch('{{ url("geojson/indonesia-prov.geojson") }}')
        .then(response => response.json())
        .then(data => {
            geoJsonLayer = L.geoJson(data, {
                style: styleFeature,
                onEachFeature: onEachFeature
            }).addTo(map);
        })
        .catch(error => console.error('Error loading geojson:', error));

    // Menambahkan Legenda di pojok peta
    let legend = L.control({position: 'bottomright'});
    legend.onAdd = function (map) {
        let div = L.DomUtil.create('div', 'info-legend');
        div.innerHTML += '<strong>Kategori SPM</strong><br>';
        div.innerHTML += '<i style="background: #198754"></i> Sangat Baik (>= 85)<br>';
        div.innerHTML += '<i style="background: #0d6efd"></i> Baik (70 - 84)<br>';
        div.innerHTML += '<i style="background: #ffc107"></i> Cukup (55 - 69)<br>';
        div.innerHTML += '<i style="background: #dc3545"></i> Kurang (< 55)<br>';
        div.innerHTML += '<i style="background: #cccccc"></i> Belum ada data';
        return div;
    };
    legend.addTo(map);

</script>
@endsection
