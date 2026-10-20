<?php

namespace App\Http\Controllers;

use App\Models\Province;
use App\Models\Regency;
use App\Models\SpmData;
use App\Services\PublicMapDataService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MapController extends Controller
{
    public function index(Request $request, PublicMapDataService $mapData): View
    {
        $availableYears = $mapData->availableYears();
        $year = $mapData->resolveYear($availableYears, $request->integer('year'));
        $mapPayload = $mapData->data($year);

        return view('map', compact('availableYears', 'year', 'mapPayload'));
    }

    public function data(Request $request, PublicMapDataService $mapData): JsonResponse
    {
        $availableYears = $mapData->availableYears();
        $year = $mapData->resolveYear($availableYears, $request->integer('year'));

        return response()->json($mapData->data($year));
    }

    public function show(Request $request, Province $province, PublicMapDataService $mapData): View
    {
        $availableYears = $mapData->availableYears();
        $year = $mapData->resolveYear($availableYears, $request->integer('year'));
        $spmData = SpmData::query()
            ->published()
            ->when($year !== null, fn ($query) => $query->where('year', $year))
            ->whereHas('regency', fn ($query) => $query->where('province_id', $province->id))
            ->get()
            ->keyBy('regency_id');

        $regencies = Regency::query()
            ->whereBelongsTo($province)
            ->orderBy('name')
            ->get()
            ->map(function (Regency $regency) use ($spmData, $mapData): Regency {
                $data = $spmData->get($regency->id);
                $regency->setAttribute('nilai_akhir', $data?->nilai_akhir);
                $regency->setAttribute('kategori', $mapData->categoryFor($data?->nilai_akhir));
                $regency->setAttribute('jml_pos', $data?->jml_pos);
                $regency->setAttribute('total_sdm', $data?->total_sdm);
                $regency->setAttribute('pr_redkar', $data?->pr_redkar);

                return $regency;
            });

        $totalRegencies = $regencies->count();
        $avgNilai = $regencies->avg('nilai_akhir');
        $totalPos = $regencies->sum('jml_pos');
        $totalSdm = $regencies->sum('total_sdm');

        return view('provinsi', compact('province', 'regencies', 'totalRegencies', 'avgNilai', 'totalPos', 'totalSdm', 'year'));
    }
}
