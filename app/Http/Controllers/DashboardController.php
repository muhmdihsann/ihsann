<?php

namespace App\Http\Controllers;

use App\Models\SpmData;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $availableYears = SpmData::query()
            ->select('year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year');

        $year = $request->filled('year')
            ? (int) $request->input('year')
            : $availableYears->first();

        $spmQuery = SpmData::query();
        if ($year !== null) {
            $spmQuery->where('year', $year);
        }

        $totalSpmData = (clone $spmQuery)->count();
        $totalRegencies = (clone $spmQuery)->distinct('regency_id')->count('regency_id');
        $totalProvinces = (clone $spmQuery)
            ->join('regencies', 'spm_data.regency_id', '=', 'regencies.id')
            ->distinct('regencies.province_id')
            ->count('regencies.province_id');
        $avgNilaiAkhir = (clone $spmQuery)->avg('nilai_akhir');

        $kategoriSangatBaik = (clone $spmQuery)->where('kategori', 'Sangat Baik')->count();
        $kategoriBaik = (clone $spmQuery)->where('kategori', 'Baik')->count();
        $kategoriCukup = (clone $spmQuery)->where('kategori', 'Cukup')->count();
        $kategoriKurang = (clone $spmQuery)->where('kategori', 'Kurang')->count();

        $provinsiData = SpmData::query()
            ->when($year !== null, fn ($query) => $query->where('spm_data.year', $year))
            ->join('regencies', 'spm_data.regency_id', '=', 'regencies.id')
            ->join('provinces', 'regencies.province_id', '=', 'provinces.id')
            ->selectRaw('provinces.name as provinsi,
                         provinces.id as provinsi_id,
                         AVG(nilai_akhir) as rata_nilai,
                         SUM(jml_pos) as total_pos,
                         SUM(total_sdm) as total_sdm')
            ->groupBy('provinces.id', 'provinces.name')
            ->orderBy('provinces.name')
            ->get();

        $mapData = [];
        foreach ($provinsiData as $province) {
            $average = (float) $province->rata_nilai;
            $category = 'Belum Ada Data';
            $color = '#cccccc';

            if ($province->rata_nilai !== null) {
                if ($average >= 85) {
                    $category = 'Sangat Baik';
                    $color = '#198754';
                } elseif ($average >= 70) {
                    $category = 'Baik';
                    $color = '#0d6efd';
                } elseif ($average >= 55) {
                    $category = 'Cukup';
                    $color = '#ffc107';
                } else {
                    $category = 'Kurang';
                    $color = '#dc3545';
                }
            }

            $mapData[strtoupper($province->provinsi)] = [
                'id' => $province->provinsi_id,
                'rata_nilai' => number_format($average, 2),
                'kategori' => $category,
                'warna' => $color,
                'total_pos' => (int) ($province->total_pos ?? 0),
                'total_sdm' => (int) ($province->total_sdm ?? 0),
                'year' => $year,
            ];
        }

        return view('dashboard.index', [
            'totalProvinces' => $totalProvinces,
            'totalRegencies' => $totalRegencies,
            'totalSpmData' => $totalSpmData,
            'avgNilaiAkhir' => $avgNilaiAkhir,
            'kategoriSangatBaik' => $kategoriSangatBaik,
            'kategoriBaik' => $kategoriBaik,
            'kategoriCukup' => $kategoriCukup,
            'kategoriKurang' => $kategoriKurang,
            'labelProvinsi' => $provinsiData->pluck('provinsi'),
            'dataNilai' => $provinsiData->pluck('rata_nilai'),
            'mapData' => $mapData,
            'availableYears' => $availableYears,
            'year' => $year,
        ]);
    }
}
