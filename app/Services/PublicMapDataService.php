<?php

namespace App\Services;

use App\Models\Province;
use App\Models\SpmData;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PublicMapDataService
{
    public function __construct(private MapAreaNameNormalizer $nameNormalizer) {}

    /** @return Collection<int, int> */
    public function availableYears(): Collection
    {
        return SpmData::query()
            ->published()
            ->select('year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year');
    }

    /** @param Collection<int, int> $availableYears */
    public function resolveYear(Collection $availableYears, ?int $requestedYear): ?int
    {
        return $availableYears->contains($requestedYear) ? $requestedYear : $availableYears->first();
    }

    /** @return array{year: ?int, provinces: array<int, array<string, mixed>>} */
    public function data(?int $year): array
    {
        $provinces = Province::query()->orderBy('name')->get(['id', 'code', 'name']);

        if ($year === null) {
            return [
                'year' => null,
                'provinces' => $provinces->map(fn (Province $province): array => $this->emptyProvince($province))->all(),
            ];
        }

        $spmData = SpmData::query()->published()->where('spm_data.year', $year);

        $provinceMetrics = (clone $spmData)
            ->join('regencies', 'spm_data.regency_id', '=', 'regencies.id')
            ->select(
                'regencies.province_id',
                DB::raw('AVG(spm_data.nilai_akhir) as average_index'),
                DB::raw('COUNT(DISTINCT spm_data.regency_id) as total_regencies'),
            )
            ->groupBy('regencies.province_id')
            ->get()
            ->keyBy('province_id');

        $regencies = (clone $spmData)
            ->join('regencies', 'spm_data.regency_id', '=', 'regencies.id')
            ->select(
                'regencies.id',
                'regencies.province_id',
                'regencies.code',
                'regencies.name',
                DB::raw('AVG(spm_data.nilai_akhir) as average_index'),
            )
            ->groupBy('regencies.id', 'regencies.province_id', 'regencies.code', 'regencies.name')
            ->orderByRaw('AVG(spm_data.nilai_akhir) DESC')
            ->orderBy('regencies.name')
            ->get()
            ->groupBy('province_id');

        return [
            'year' => $year,
            'provinces' => $provinces->map(function (Province $province) use ($provinceMetrics, $regencies): array {
                $metrics = $provinceMetrics->get($province->id);
                $index = $metrics === null ? null : round((float) $metrics->average_index, 2);

                return [
                    'id' => $province->id,
                    'code' => $province->code,
                    'geojson_code' => $this->geojsonCode($province->code),
                    'name' => $province->name,
                    'province_name' => $province->name,
                    'map_key' => $this->mapKey($province->name),
                    'index' => $index,
                    'spm' => $index,
                    'category' => $this->categoryFor($index),
                    'total_regencies' => $metrics === null ? 0 : (int) $metrics->total_regencies,
                    'regency_count' => $metrics === null ? 0 : (int) $metrics->total_regencies,
                    'regencies' => ($regencies->get($province->id) ?? collect())
                        ->map(function (object $regency): array {
                            $index = round((float) $regency->average_index, 2);

                            return [
                                'id' => $regency->id,
                                'code' => $regency->code,
                                'name' => $regency->name,
                                'regency_name' => $regency->name,
                                'index' => $index,
                                'spm' => $index,
                                'category' => $this->categoryFor($index),
                                'validation_status' => 'published',
                            ];
                        })
                        ->values()
                        ->all(),
                ];
            })->values()->all(),
        ];
    }

    public function categoryFor(?float $index): ?string
    {
        return match (true) {
            $index === null => null,
            $index >= 85 => 'Sangat Baik',
            $index >= 70 => 'Baik',
            $index >= 55 => 'Cukup',
            default => 'Kurang',
        };
    }

    private function emptyProvince(Province $province): array
    {
        return [
            'id' => $province->id,
            'code' => $province->code,
            'geojson_code' => $this->geojsonCode($province->code),
            'name' => $province->name,
            'province_name' => $province->name,
            'map_key' => $this->mapKey($province->name),
            'index' => null,
            'spm' => null,
            'category' => null,
            'total_regencies' => 0,
            'regency_count' => 0,
            'regencies' => [],
        ];
    }

    private function mapKey(string $provinceName): string
    {
        return $this->nameNormalizer->provinceKey($provinceName);
    }

    private function geojsonCode(string $provinceCode): ?string
    {
        if (preg_match('/^(\\d{2})(?:000)?$/', $provinceCode, $matches) !== 1) {
            return null;
        }

        return $matches[1];
    }
}
