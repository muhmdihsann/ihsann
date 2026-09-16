<?php

namespace App\Http\Controllers;

use App\Models\Province;
use App\Models\Regency;

class MapController extends Controller
{
    // 1. Menampilkan Halaman Peta Utama
    public function index()
    {
        return redirect()->to(route('home').'#statistik');
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
