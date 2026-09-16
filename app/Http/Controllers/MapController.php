<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Province;
use App\Models\Regency;

class MapController extends Controller
{
    // 1. Menampilkan Halaman Peta Utama
    public function index()
    {
        $provinces = Province::select('provinces.id', 'provinces.name')
            ->leftJoin('regencies', 'provinces.id', '=', 'regencies.province_id')
            ->leftJoin('spm_data', 'regencies.id', '=', 'spm_data.regency_id')
            ->selectRaw('AVG(spm_data.nilai_akhir) as rata_nilai, SUM(spm_data.jml_pos) as total_pos, SUM(spm_data.total_sdm) as total_sdm')
            ->groupBy('provinces.id', 'provinces.name')
            ->get();

        $mapData = [];
        foreach ($provinces as $prov) {
            $kategori = 'Belum Ada Data';
            $warna = '#cccccc';

            if ($prov->rata_nilai !== null) {
                if ($prov->rata_nilai >= 85) { $kategori = 'Sangat Baik'; $warna = '#198754'; }
                elseif ($prov->rata_nilai >= 70) { $kategori = 'Baik'; $warna = '#0d6efd'; }
                elseif ($prov->rata_nilai >= 55) { $kategori = 'Cukup'; $warna = '#ffc107'; }
                else { $kategori = 'Kurang'; $warna = '#dc3545'; }
            }

            $mapData[strtoupper($prov->name)] = [
                'id' => $prov->id, // KITA TAMBAHKAN ID DI SINI
                'rata_nilai' => number_format($prov->rata_nilai, 2),
                'kategori' => $kategori,
                'warna' => $warna,
                'total_pos' => $prov->total_pos ?? 0,
                'total_sdm' => $prov->total_sdm ?? 0
            ];
        }

        return view('map', compact('mapData'));
    }

    // 2. Menampilkan Detail Provinsi (Drill-down)
    public function show($id)
    {
        $provinsi = Province::findOrFail($id);

        // Ambil data Kabupaten/Kota beserta nilai SPM-nya (menggunakan Left Join agar yang kosong tetap tampil)
        $regencies = Regency::where('province_id', $id)
            ->leftJoin('spm_data', 'regencies.id', '=', 'spm_data.regency_id')
            ->select('regencies.*', 'spm_data.nilai_akhir', 'spm_data.kategori', 'spm_data.jml_pos', 'spm_data.total_sdm', 'spm_data.pr_redkar')
            ->orderBy('regencies.name', 'asc')
            ->get();

        // Hitung agregat untuk header halaman
        $totalRegencies = $regencies->count();
        $avgNilai = $regencies->avg('nilai_akhir');
        $totalPos = $regencies->sum('jml_pos');
        $totalSdm = $regencies->sum('total_sdm');

        return view('provinsi', compact('provinsi', 'regencies', 'totalRegencies', 'avgNilai', 'totalPos', 'totalSdm'));
    }
}
