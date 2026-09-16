<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Province;
use App\Models\Regency;
use App\Models\SpmData;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Hitung statistik dasar (Card)
        $totalProvinces = Province::count();
        $totalRegencies = Regency::count();
        $totalSpmData = SpmData::count();
        $avgNilaiAkhir = SpmData::avg('nilai_akhir');

        // 2. Data untuk Grafik Distribusi Kategori (Pie Chart)
        $kategoriSangatBaik = SpmData::where('kategori', 'Sangat Baik')->count();
        $kategoriBaik = SpmData::where('kategori', 'Baik')->count();
        $kategoriCukup = SpmData::where('kategori', 'Cukup')->count();
        $kategoriKurang = SpmData::where('kategori', 'Kurang')->count();

        // 3. Data untuk Grafik Perbandingan Provinsi (Bar Chart)
        // Kita menggunakan Query Builder (Join) untuk mengelompokkan data berdasarkan Provinsi
        $provinsiData = SpmData::join('regencies', 'spm_data.regency_id', '=', 'regencies.id')
            ->join('provinces', 'regencies.province_id', '=', 'provinces.id')
            ->selectRaw('provinces.name as provinsi,
                         AVG(nilai_akhir) as rata_nilai,
                         SUM(jml_pos) as total_pos,
                         SUM(total_sdm) as total_sdm')
            ->groupBy('provinces.id', 'provinces.name')
            ->orderBy('provinces.name')
            ->get();

        // Ekstrak data menjadi array agar mudah dibaca oleh Chart.js
        $labelProvinsi = $provinsiData->pluck('provinsi');
        $dataNilai = $provinsiData->pluck('rata_nilai');
        $dataPos = $provinsiData->pluck('total_pos');
        $dataSdm = $provinsiData->pluck('total_sdm');

        return view('dashboard.index', compact(
            'totalProvinces', 'totalRegencies', 'totalSpmData', 'avgNilaiAkhir',
            'kategoriSangatBaik', 'kategoriBaik', 'kategoriCukup', 'kategoriKurang',
            'labelProvinsi', 'dataNilai', 'dataPos', 'dataSdm'
        ));
    }
}
