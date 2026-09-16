<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Province;
use App\Models\Regency;
use App\Models\SpmData;

class HomeController extends Controller
{
    public function index()
    {
        // Hitung statistik untuk publik
        $totalProvinces = Province::count();
        $totalRegencies = Regency::count();
        $totalSpmData = SpmData::count();
        $avgNilaiAkhir = SpmData::avg('nilai_akhir');

        $kategoriSangatBaik = SpmData::where('kategori', 'Sangat Baik')->count();
        $kategoriBaik = SpmData::where('kategori', 'Baik')->count();
        $kategoriCukup = SpmData::where('kategori', 'Cukup')->count();
        $kategoriKurang = SpmData::where('kategori', 'Kurang')->count();

        return view('welcome', compact(
            'totalProvinces', 'totalRegencies', 'totalSpmData', 'avgNilaiAkhir',
            'kategoriSangatBaik', 'kategoriBaik', 'kategoriCukup', 'kategoriKurang'
        ));
    }
}
