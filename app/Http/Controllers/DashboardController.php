<?php

namespace App\Http\Controllers;

use App\Models\ImportHistory;
use App\Models\SpmData;
use App\Services\PublicMapDataService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request, PublicMapDataService $mapData)
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

        $mapAvailableYears = $mapData->availableYears();
        $mapYear = $mapData->resolveYear($mapAvailableYears, $request->integer('map_year'));
        $mapPayload = $mapData->data($mapYear);

        return view('dashboard.index', [
            'importHistories' => ImportHistory::withCount('spmData')->latest()->get(),
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
            'mapAvailableYears' => $mapAvailableYears,
            'mapYear' => $mapYear,
            'mapPayload' => $mapPayload,
            'availableYears' => $availableYears,
            'year' => $year,
        ]);
    }
}
