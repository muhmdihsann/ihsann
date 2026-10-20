<?php

namespace App\Services;

use App\Imports\SpmDataImport;
use App\Models\ImportHistory;
use App\Models\Province;
use App\Models\Regency;
use App\Models\SpmData;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class SpmDatasetImportService
{
    public const Categories = ['Sangat Baik', 'Baik', 'Cukup', 'Kurang'];

    /**
     * @return array<int, array<int, mixed>>
     */
    public function readRows(string $path): array
    {
        return Excel::toArray(new SpmDataImport, Storage::disk('local')->path($path))[0] ?? [];
    }

    /**
     * @param  array<int, array<int, mixed>>  $rows
     * @return array{valid: array<int, array<string, mixed>>, invalid: array<int, array<string, mixed>>}
     */
    public function validateRows(array $rows): array
    {
        $valid = [];
        $invalid = [];
        $lastProvince = null;

        foreach ($rows as $index => $row) {
            $providedProvince = $this->normaliseString($row[1] ?? null);
            $regency = $this->normaliseString($row[2] ?? null);

            if ($providedProvince === null && $regency === null) {
                continue;
            }

            if ($regency !== null && $this->isAggregateRow($regency)) {
                continue;
            }

            if ($providedProvince !== null) {
                $lastProvince = $providedProvince;
            }

            if ($regency === null) {
                continue;
            }

            $province = $providedProvince ?? $lastProvince;

            $excelRow = $index + 6;
            $errors = [];
            $category = $this->normaliseString($row[22] ?? null);
            $invalidCategory = $category !== null && ! in_array($category, self::Categories, true);

            if ($province === null) {
                $errors[] = 'provinsi wajib diisi';
            }

            if (in_array(mb_strtolower($regency), ['kabupaten / kota', 'wilayah'], true)) {
                $errors[] = 'kabupaten/kota wajib diisi';
            }

            if ($invalidCategory) {
                $errors[] = 'kategori harus salah satu dari: '.implode(', ', self::Categories);
            }

            if ($errors !== []) {
                $invalid[] = [
                    'row' => $excelRow,
                    'province' => $province ?? '-',
                    'regency' => $regency ?? '-',
                    'field' => $invalidCategory ? 'kategori' : 'wilayah',
                    'message' => implode('; ', $errors),
                ];

                continue;
            }

            $valid[] = [
                'row' => $excelRow,
                'province' => $province,
                'regency' => $regency,
                'values' => [
                    'jml_kecamatan' => (int) ($row[6] ?? 0), 'jml_pos' => (int) ($row[7] ?? 0),
                    'pr_pos_kecamatan' => (float) ($row[8] ?? 0), 'total_sdm' => (int) ($row[9] ?? 0),
                    'sdm_sertifikat' => (int) ($row[10] ?? 0), 'pr_sdm_sertifikat' => (float) ($row[11] ?? 0),
                    'jml_desa' => (int) ($row[12] ?? 0), 'jml_redkar' => (int) ($row[13] ?? 0),
                    'pr_redkar' => (float) ($row[14] ?? 0), 'dimensi_kelembagaan' => (float) ($row[15] ?? 0),
                    'dimensi_perencanaan' => (float) ($row[16] ?? 0), 'dimensi_capaian' => (float) ($row[17] ?? 0),
                    'dimensi_sarpras' => (float) ($row[18] ?? 0), 'dimensi_sdm_sertifikat' => (float) ($row[19] ?? 0),
                    'dimensi_pemberdayaan' => (float) ($row[20] ?? 0), 'nilai_akhir' => (float) ($row[21] ?? 0),
                    'kategori' => $category,
                ],
            ];
        }

        return compact('valid', 'invalid');
    }

    /** @param array<int, array<string, mixed>> $rows */
    public function import(array $rows, int $year, string $filename, int $userId): ImportHistory
    {
        return DB::transaction(function () use ($rows, $year, $filename, $userId): ImportHistory {
            $history = ImportHistory::create([
                'user_id' => $userId, 'file_name' => $filename, 'year' => $year,
                'status' => ImportHistory::Draft, 'total_row' => count($rows), 'success_row' => count($rows),
            ]);

            foreach ($rows as $row) {
                $province = Province::firstOrCreate(['name' => $row['province']], ['code' => substr(md5($row['province']), 0, 5)]);
                $regency = Regency::firstOrCreate(
                    ['province_id' => $province->id, 'name' => $row['regency']],
                    ['code' => substr(md5($province->id.'|'.$row['regency']), 0, 5)],
                );
                SpmData::create(['import_history_id' => $history->id, 'regency_id' => $regency->id, 'year' => $year, ...$row['values']]);
            }

            return $history;
        });
    }

    private function normaliseString(mixed $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' || $value === '-' ? null : $value;
    }

    private function isAggregateRow(string $regency): bool
    {
        return in_array(mb_strtolower($regency), ['rata-rata', 'rata rata', 'total'], true);
    }
}
