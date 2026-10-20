<?php

namespace App\Http\Controllers;

use App\Models\Province;
use App\Models\Regency;
use App\Models\SpmData;
use App\Services\PublicMapDataService;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
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

    public function hotspots(): JsonResponse
    {
        $mapKey = config('services.nasa.firms.map_key');
        $primarySource = config('services.nasa.firms.source', 'VIIRS_NOAA21_NRT');
        $allowedSources = ['VIIRS_NOAA21_NRT', 'VIIRS_NOAA20_NRT', 'VIIRS_SNPP_NRT', 'MODIS_NRT'];

        if (! is_string($mapKey) || trim($mapKey) === '') {
            return response()->json(['message' => 'Data titik panas belum tersedia.'], 503);
        }

        if (! in_array($primarySource, $allowedSources, true)) {
            return response()->json(['message' => 'Sumber data hotspot tidak valid.'], 503);
        }

        $sources = array_values(array_unique(array_merge(
            [$primarySource],
            ['VIIRS_NOAA21_NRT', 'VIIRS_NOAA20_NRT', 'VIIRS_SNPP_NRT'],
        )));

        try {
            $hotspotData = Cache::remember(
                'nasa-firms-hotspots:indonesia:'.implode(':', $sources),
                now()->addMinutes(5),
                function () use ($mapKey, $sources): array {
                    $features = [];
                    $availableSources = [];

                    foreach ($sources as $source) {
                        try {
                            $url = sprintf(
                                'https://firms.modaps.eosdis.nasa.gov/api/area/csv/%s/%s/95,-11,141,6/1',
                                rawurlencode(trim($mapKey)),
                                rawurlencode($source),
                            );

                            $csv = Http::accept('text/csv')
                                ->connectTimeout(5)
                                ->timeout(12)
                                ->get($url)
                                ->throw()
                                ->body();

                            $features = array_merge($features, $this->hotspotFeaturesFromCsv($csv, $source));
                            $availableSources[] = $source;
                        } catch (ConnectionException|RequestException|\UnexpectedValueException) {
                            continue;
                        }
                    }

                    if ($availableSources === []) {
                        throw new \UnexpectedValueException('No NASA FIRMS product returned valid data.');
                    }

                    return [
                        'features' => $features,
                        'sources' => $availableSources,
                        'retrieved_at' => now()->toIso8601String(),
                    ];
                },
            );
        } catch (ConnectionException|RequestException|\UnexpectedValueException $exception) {
            Log::warning('NASA FIRMS hotspot request failed.', [
                'source' => $source,
                'exception' => $exception::class,
            ]);

            return response()->json(['message' => 'Data satelit sementara tidak dapat dimuat. Coba lagi beberapa saat.'], 503);
        }

        return response()->json([
            'type' => 'FeatureCollection',
            'source' => 'NASA FIRMS',
            'product' => implode(' + ', $hotspotData['sources']),
            'sources' => $hotspotData['sources'],
            'retrieved_at' => $hotspotData['retrieved_at'],
            'count' => count($hotspotData['features']),
            'features' => $hotspotData['features'],
        ]);
    }

    /** @return array<int, array<string, mixed>> */
    private function hotspotFeaturesFromCsv(string $csv, string $source): array
    {
        $lines = preg_split('/\r\n|\r|\n/', trim($csv)) ?: [];
        if ($lines === [] || trim($lines[0]) === '') {
            throw new \UnexpectedValueException('NASA FIRMS returned no hotspot rows.');
        }

        $headers = array_map('trim', str_getcsv(array_shift($lines)));
        if (isset($headers[0])) {
            $headers[0] = ltrim($headers[0], "\xEF\xBB\xBF");
        }
        if (! in_array('latitude', $headers, true) || ! in_array('longitude', $headers, true)) {
            throw new \UnexpectedValueException('NASA FIRMS returned an unexpected CSV format.');
        }
        if ($lines === []) {
            return [];
        }

        $features = [];
        foreach ($lines as $line) {
            if (trim($line) === '') {
                continue;
            }

            $values = str_getcsv($line);
            if (count($values) !== count($headers)) {
                continue;
            }

            $row = array_combine($headers, $values);
            $latitude = $row['latitude'] ?? null;
            $longitude = $row['longitude'] ?? null;
            if (! is_numeric($latitude) || ! is_numeric($longitude)) {
                continue;
            }

            $confidence = strtolower(trim((string) ($row['confidence'] ?? '')));
            $confidenceLabel = match ($confidence) {
                'h', 'high' => 'Tinggi',
                'n', 'nominal' => 'Nominal',
                'l', 'low' => 'Rendah',
                default => is_numeric($confidence) ? $confidence.'%' : 'Tidak tersedia',
            };

            $features[] = [
                'type' => 'Feature',
                'geometry' => [
                    'type' => 'Point',
                    'coordinates' => [(float) $longitude, (float) $latitude],
                ],
                'properties' => [
                    'acq_date' => $row['acq_date'] ?? null,
                    'acq_time' => $row['acq_time'] ?? null,
                    'satellite' => $row['satellite'] ?? null,
                    'instrument' => $row['instrument'] ?? null,
                    'product' => $source,
                    'confidence' => $confidence,
                    'confidence_label' => $confidenceLabel,
                    'brightness' => isset($row['bright_ti4']) && is_numeric($row['bright_ti4']) ? (float) $row['bright_ti4'] : null,
                    'frp' => isset($row['frp']) && is_numeric($row['frp']) ? (float) $row['frp'] : null,
                    'daynight' => $row['daynight'] ?? null,
                ],
            ];
        }

        return $features;
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
