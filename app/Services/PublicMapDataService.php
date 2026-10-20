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
                DB::raw('SUM(spm_data.jml_kecamatan) as total_kecamatan'),
                DB::raw('SUM(spm_data.jml_pos) as total_pos'),
                DB::raw('SUM(spm_data.total_sdm) as total_sdm'),
                DB::raw('SUM(spm_data.sdm_sertifikat) as total_sdm_sertifikat'),
                DB::raw('SUM(spm_data.jml_desa) as total_desa'),
                DB::raw('SUM(spm_data.jml_redkar) as total_redkar'),
                DB::raw('AVG(spm_data.dimensi_kelembagaan) as average_kelembagaan'),
                DB::raw('AVG(spm_data.dimensi_perencanaan) as average_perencanaan'),
                DB::raw('AVG(spm_data.dimensi_capaian) as average_capaian'),
                DB::raw('AVG(spm_data.dimensi_sarpras) as average_sarpras'),
                DB::raw('AVG(spm_data.dimensi_sdm_sertifikat) as average_sdm_sertifikat'),
                DB::raw('AVG(spm_data.dimensi_pemberdayaan) as average_pemberdayaan'),
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
                DB::raw('SUM(spm_data.jml_kecamatan) as total_kecamatan'),
                DB::raw('SUM(spm_data.jml_pos) as total_pos'),
                DB::raw('SUM(spm_data.total_sdm) as total_sdm'),
                DB::raw('SUM(spm_data.sdm_sertifikat) as total_sdm_sertifikat'),
                DB::raw('SUM(spm_data.jml_desa) as total_desa'),
                DB::raw('SUM(spm_data.jml_redkar) as total_redkar'),
                DB::raw('AVG(spm_data.dimensi_kelembagaan) as average_kelembagaan'),
                DB::raw('AVG(spm_data.dimensi_perencanaan) as average_perencanaan'),
                DB::raw('AVG(spm_data.dimensi_capaian) as average_capaian'),
                DB::raw('AVG(spm_data.dimensi_sarpras) as average_sarpras'),
                DB::raw('AVG(spm_data.dimensi_sdm_sertifikat) as average_sdm_sertifikat'),
                DB::raw('AVG(spm_data.dimensi_pemberdayaan) as average_pemberdayaan'),
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
                    'metrics' => $metrics === null ? [] : $this->metrics($metrics),
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
                                'metrics' => $this->metrics($regency),
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
            'metrics' => [],
            'regencies' => [],
        ];
    }

    /** @return array<string, int|float|null> */
    private function metrics(object $data): array
    {
        return [
            'kecamatan' => (int) ($data->total_kecamatan ?? 0),
            'pos_damkar' => (int) ($data->total_pos ?? 0),
            'sdm' => (int) ($data->total_sdm ?? 0),
            'sdm_bersertifikat' => (int) ($data->total_sdm_sertifikat ?? 0),
            'desa' => (int) ($data->total_desa ?? 0),
            'redkar' => (int) ($data->total_redkar ?? 0),
            'kelembagaan' => $this->nullableAverage($data->average_kelembagaan ?? null),
            'perencanaan' => $this->nullableAverage($data->average_perencanaan ?? null),
            'capaian' => $this->nullableAverage($data->average_capaian ?? null),
            'sarpras' => $this->nullableAverage($data->average_sarpras ?? null),
            'sdm_dimension' => $this->nullableAverage($data->average_sdm_sertifikat ?? null),
            'pemberdayaan' => $this->nullableAverage($data->average_pemberdayaan ?? null),
        ];
    }

    private function nullableAverage(mixed $value): ?float
    {
        return $value === null ? null : round((float) $value, 2);
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
