<?php

namespace App\Http\Controllers;

use App\Models\Province;
use App\Models\Regency;
use App\Models\SpmData;
use App\Services\IqairService;
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
        $source = config('services.nasa.firms.source', 'VIIRS_NOAA21_NRT');
        $allowedSources = ['VIIRS_NOAA21_NRT', 'VIIRS_NOAA20_NRT', 'VIIRS_SNPP_NRT', 'MODIS_NRT'];

        if (! is_string($mapKey) || trim($mapKey) === '' || ! in_array($source, $allowedSources, true)) {
            return response()->json(['message' => 'Kunci atau sumber data NASA FIRMS belum dikonfigurasi dengan benar.'], 503);
        }

        $sources = array_values(array_unique(array_merge(
            [$source],
            ['VIIRS_NOAA21_NRT', 'VIIRS_NOAA20_NRT', 'VIIRS_SNPP_NRT'],
        )));

        try {
            $hotspots = Cache::remember('nasa-firms-hotspots:indonesia:'.implode(':', $sources), now()->addMinutes(5), function () use ($mapKey, $sources): array {
                $features = [];
                $availableSources = [];

                foreach ($sources as $product) {
                    try {
                        $url = sprintf(
                            'https://firms.modaps.eosdis.nasa.gov/api/area/csv/%s/%s/95,-11,141,6/1',
                            rawurlencode(trim($mapKey)),
                            rawurlencode($product),
                        );
                        $csv = Http::accept('text/csv')->connectTimeout(5)->timeout(12)->get($url)->throw()->body();
                        $features = array_merge($features, $this->hotspotFeaturesFromCsv($csv, $product));
                        $availableSources[] = $product;
                    } catch (ConnectionException|RequestException|\UnexpectedValueException) {
                        continue;
                    }
                }

                if ($availableSources === []) {
                    throw new \UnexpectedValueException('No NASA FIRMS product returned hotspot data.');
                }

                return [
                    'features' => $features,
                    'sources' => $availableSources,
                    'retrieved_at' => now()->toIso8601String(),
                ];
            });
        } catch (ConnectionException|RequestException|\UnexpectedValueException $exception) {
            Log::warning('NASA FIRMS hotspot request failed.', ['exception' => $exception::class]);

            return response()->json(['message' => 'Data hotspot NASA FIRMS sementara tidak dapat dimuat.'], 503);
        }

        return response()->json([
            'type' => 'FeatureCollection',
            'source' => 'NASA FIRMS',
            'product' => implode(' + ', $hotspots['sources']),
            'retrieved_at' => $hotspots['retrieved_at'],
            'count' => count($hotspots['features']),
            'features' => $hotspots['features'],
        ]);
    }

    /** @return array<int, array<string, mixed>> */
    private function hotspotFeaturesFromCsv(string $csv, string $source): array
    {
        $lines = preg_split('/\r\n|\r|\n/', trim($csv)) ?: [];
        if ($lines === [] || trim($lines[0]) === '') {
            throw new \UnexpectedValueException('NASA FIRMS returned no hotspot data.');
        }

        $headers = array_map('trim', str_getcsv(array_shift($lines)));
        if (isset($headers[0])) {
            $headers[0] = ltrim($headers[0], "\xEF\xBB\xBF");
        }
        if (! in_array('latitude', $headers, true) || ! in_array('longitude', $headers, true)) {
            throw new \UnexpectedValueException('NASA FIRMS returned an unexpected CSV format.');
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
            if (! is_numeric($row['latitude'] ?? null) || ! is_numeric($row['longitude'] ?? null)) {
                continue;
            }

            $confidence = strtolower(trim((string) ($row['confidence'] ?? '')));
            $features[] = [
                'type' => 'Feature',
                'geometry' => [
                    'type' => 'Point',
                    'coordinates' => [(float) $row['longitude'], (float) $row['latitude']],
                ],
                'properties' => [
                    'acq_date' => $row['acq_date'] ?? null,
                    'acq_time' => $row['acq_time'] ?? null,
                    'satellite' => $row['satellite'] ?? null,
                    'instrument' => $row['instrument'] ?? null,
                    'product' => $source,
                    'confidence' => $confidence,
                    'confidence_label' => match ($confidence) {
                        'h', 'high' => 'Tinggi',
                        'n', 'nominal' => 'Nominal',
                        'l', 'low' => 'Rendah',
                        default => is_numeric($confidence) ? $confidence.'%' : 'Tidak tersedia',
                    },
                    'brightness' => isset($row['bright_ti4']) && is_numeric($row['bright_ti4']) ? (float) $row['bright_ti4'] : null,
                    'frp' => isset($row['frp']) && is_numeric($row['frp']) ? (float) $row['frp'] : null,
                    'daynight' => $row['daynight'] ?? null,
                ],
            ];
        }

        return $features;
    }

    public function airQuality(IqairService $iqair): JsonResponse
    {
        try {
            $data = $iqair->cities();
        } catch (\RuntimeException) {
            return response()->json(['message' => 'Kunci API IQAir belum dikonfigurasi pada server.'], 503);
        }

        return response()->json([
            'source' => 'IQAir AirVisual',
            'retrieved_at' => $data['retrieved_at'],
            'count' => count($data['cities']),
            'cities' => $data['cities'],
            'message' => $data['cities'] === [] ? 'IQAir tidak mengembalikan data. Periksa paket API dan kuota akun.' : null,
        ]);
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
