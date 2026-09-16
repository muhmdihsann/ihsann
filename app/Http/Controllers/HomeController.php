<?php

namespace App\Http\Controllers;

use App\Models\SpmData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $availableYears = SpmData::query()->select('year')->distinct()->orderByDesc('year')->pluck('year');
        $requestedYear = $request->integer('year');
        $year = $availableYears->contains($requestedYear) ? $requestedYear : $availableYears->first();

        $spmQuery = SpmData::query()->when($year !== null, fn ($query) => $query->where('year', $year));
        $totalSpmData = (clone $spmQuery)->count();
        $totalRegencies = (clone $spmQuery)->distinct()->count('regency_id');
        $totalProvinces = (clone $spmQuery)->join('regencies', 'spm_data.regency_id', '=', 'regencies.id')->distinct()->count('regencies.province_id');
        $avgNilaiAkhir = (clone $spmQuery)->avg('nilai_akhir');
        $kategoriSangatBaik = (clone $spmQuery)->where('kategori', 'Sangat Baik')->count();
        $kategoriBaik = (clone $spmQuery)->where('kategori', 'Baik')->count();
        $kategoriCukup = (clone $spmQuery)->where('kategori', 'Cukup')->count();
        $kategoriKurang = (clone $spmQuery)->where('kategori', 'Kurang')->count();

        $provinceData = (clone $spmQuery)
            ->join('regencies', 'spm_data.regency_id', '=', 'regencies.id')
            ->join('provinces', 'regencies.province_id', '=', 'provinces.id')
            ->select('provinces.id', 'provinces.name', DB::raw('AVG(spm_data.nilai_akhir) as rata_nilai'), DB::raw('SUM(spm_data.jml_pos) as total_pos'), DB::raw('SUM(spm_data.total_sdm) as total_sdm'))
            ->groupBy('provinces.id', 'provinces.name')
            ->orderBy('provinces.name')
            ->get();

        $mapData = $provinceData->mapWithKeys(function ($province): array {
            $average = (float) $province->rata_nilai;
            [$category, $color] = match (true) {
                $average >= 85 => ['Sangat Baik', '#198754'],
                $average >= 70 => ['Baik', '#0d6efd'],
                $average >= 55 => ['Cukup', '#ffc107'],
                default => ['Kurang', '#dc3545'],
            };

            return [$this->provinceMapKey($province->name) => [
                'id' => $province->id,
                'rata_nilai' => number_format($average, 2),
                'kategori' => $category,
                'warna' => $color,
                'total_pos' => (int) $province->total_pos,
                'total_sdm' => (int) $province->total_sdm,
            ]];
        });

        return view('welcome', compact('totalProvinces', 'totalRegencies', 'totalSpmData', 'avgNilaiAkhir', 'kategoriSangatBaik', 'kategoriBaik', 'kategoriCukup', 'kategoriKurang', 'availableYears', 'year', 'mapData'));
    }

    private function provinceMapKey(string $provinceName): string
    {
        return Str::upper((string) preg_replace('/[^[:alnum:]]/u', '', $provinceName));
    }
}
