<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Pool;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class IqairService
{
    /** @var array<string, array{latitude: float, longitude: float}> */
    private const CITIES = [
        'Jakarta' => ['latitude' => -6.2088, 'longitude' => 106.8456],
        'Bandung' => ['latitude' => -6.9175, 'longitude' => 107.6191],
        'Semarang' => ['latitude' => -6.9667, 'longitude' => 110.4167],
        'Yogyakarta' => ['latitude' => -7.7956, 'longitude' => 110.3695],
        'Surabaya' => ['latitude' => -7.2575, 'longitude' => 112.7521],
        'Denpasar' => ['latitude' => -8.6705, 'longitude' => 115.2126],
        'Medan' => ['latitude' => 3.5952, 'longitude' => 98.6722],
        'Palembang' => ['latitude' => -2.9761, 'longitude' => 104.7754],
        'Pontianak' => ['latitude' => -0.0263, 'longitude' => 109.3425],
        'Banjarmasin' => ['latitude' => -3.3186, 'longitude' => 114.5944],
        'Makassar' => ['latitude' => -5.1477, 'longitude' => 119.4327],
        'Manado' => ['latitude' => 1.4748, 'longitude' => 124.8421],
        'Jayapura' => ['latitude' => -2.5916, 'longitude' => 140.6690],
    ];

    /** @return array{cities: array<int, array<string, mixed>>, retrieved_at: string} */
    public function cities(): array
    {
        $apiKey = config('services.iqair.api_key');
        if (! is_string($apiKey) || trim($apiKey) === '') {
            throw new \RuntimeException('IQAir API key is not configured.');
        }

        $measurements = [];
        $pendingCities = [];
        foreach (self::CITIES as $city => $coordinates) {
            $cacheKey = 'iqair:nearest-city:'.strtolower($city);
            $cachedMeasurement = Cache::get($cacheKey);

            if (is_array($cachedMeasurement)) {
                $measurements[$city] = $cachedMeasurement;
            } else {
                $pendingCities[$city] = $coordinates;
            }
        }

        $responses = Http::pool(function (Pool $pool) use ($apiKey, $pendingCities): array {
            $requests = [];
            foreach ($pendingCities as $city => $coordinates) {
                $requests[$city] = $pool->as($city)
                    ->connectTimeout(4)
                    ->timeout(8)
                    ->get('https://api.airvisual.com/v2/nearest_city', [
                        'lat' => $coordinates['latitude'],
                        'lon' => $coordinates['longitude'],
                        'key' => trim($apiKey),
                    ]);
            }

            return $requests;
        }, concurrency: 4);

        foreach ($pendingCities as $city => $coordinates) {
            try {
                $response = $responses[$city] ?? null;
                if ($response instanceof \Throwable) {
                    throw $response;
                }

                if (! $response instanceof Response) {
                    throw new \UnexpectedValueException('IQAir returned an invalid city response.');
                }

                $payload = $response->throw()->json();
                if (($payload['status'] ?? null) !== 'success' || ! is_array($payload['data'] ?? null)) {
                    throw new \UnexpectedValueException('IQAir returned an invalid city response.');
                }

                $measurements[$city] = $payload['data'];
                Cache::put('iqair:nearest-city:'.strtolower($city), $payload['data'], now()->addMinutes(30));
            } catch (ConnectionException|RequestException|\UnexpectedValueException $exception) {
                Log::warning('IQAir air quality request failed.', [
                    'city' => $city,
                    'exception' => $exception::class,
                ]);
            }
        }

        $cities = [];
        foreach (self::CITIES as $city => $coordinates) {
            if (isset($measurements[$city])) {
                $cities[] = $this->formatCity($city, $coordinates, $measurements[$city]);
            }
        }

        return ['cities' => $cities, 'retrieved_at' => now()->toIso8601String()];
    }

    /** @param array<string, mixed> $coordinates
     * @param  array<string, mixed>  $measurement
     * @return array<string, mixed>
     */
    private function formatCity(string $city, array $coordinates, array $measurement): array
    {
        $current = $measurement['current'] ?? [];
        $pollution = $current['pollution'] ?? [];
        $weather = $current['weather'] ?? [];

        return [
            'requested_city' => $city,
            'city' => $measurement['city'] ?? $city,
            'state' => $measurement['state'] ?? null,
            'country' => $measurement['country'] ?? 'Indonesia',
            'latitude' => data_get($measurement, 'location.coordinates.1', $coordinates['latitude']),
            'longitude' => data_get($measurement, 'location.coordinates.0', $coordinates['longitude']),
            'aqi_us' => $pollution['aqius'] ?? null,
            'aqi_cn' => $pollution['aqicn'] ?? null,
            'main_pollutant' => $pollution['mainus'] ?? null,
            'measured_at' => $pollution['ts'] ?? null,
            'pollutants' => collect(['p2', 'p1', 'p10', 'o3', 'n2', 's2', 'co'])
                ->mapWithKeys(fn (string $key): array => [strtoupper($key) => [
                    'concentration' => data_get($pollution, $key.'.conc'),
                    'aqi_us' => data_get($pollution, $key.'.aqius'),
                    'aqi_cn' => data_get($pollution, $key.'.aqicn'),
                ]])
                ->all(),
            'weather' => [
                'temperature_c' => $weather['tp'] ?? null,
                'humidity_percent' => $weather['hu'] ?? null,
                'pressure_hpa' => $weather['pr'] ?? null,
                'wind_speed_m_s' => $weather['ws'] ?? null,
                'wind_direction_degrees' => $weather['wd'] ?? null,
            ],
        ];
    }
}
