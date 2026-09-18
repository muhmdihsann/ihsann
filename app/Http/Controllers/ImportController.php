<?php

namespace App\Http\Controllers;

use App\Imports\SpmDataImport;
use App\Models\ImportHistory;
use App\Models\Province;
use App\Models\Regency;
use App\Models\SpmData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Exception;

class ImportController extends Controller
{
    // 1. Menampilkan Form Import Utama
    public function index()
    {
        return view('import.index');
    }

    // 2. Menyimpan Data Utama langsung ke Database (untuk Dashboard & Publik)
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xls,xlsx,csv',
            'year' => 'required|numeric|digits:4',
        ], [
            'file.required' => 'File Excel wajib diunggah.',
            'year.required' => 'Tahun data wajib diisi.',
        ]);

        try {
            $file = $request->file('file');
            $fileName = $file->hashName();
            $path = $file->storeAs('temp', $fileName, 'local');

            $dataExcel = Excel::toArray(new SpmDataImport, Storage::disk('local')->path($path))[0] ?? [];
            $year = $request->year;
            $successCount = 0;

            DB::beginTransaction();

            $importHistory = ImportHistory::create([
                'user_id' => auth()->id(),
                'file_name' => $fileName,
                'year' => $year,
                'status' => 'Berhasil',
                'total_row' => count($dataExcel),
                'success_row' => 0,
            ]);

            foreach ($dataExcel as $row) {
                if (empty($row[1]) && empty($row[2])) continue;

                $namaProvinsi = trim((string)($row[1] ?? ''));
                $namaKabupaten = trim((string)($row[2] ?? ''));

                if ($namaKabupaten === '' || strtolower($namaKabupaten) === 'kabupaten / kota' || strtolower($namaKabupaten) === 'wilayah') {
                    continue;
                }

                $province = Province::firstOrCreate(
                    ['name' => $namaProvinsi !== '' ? $namaProvinsi : 'Tidak Diketahui'],
                    ['code' => substr(md5($namaProvinsi), 0, 5)]
                );

                $regency = Regency::firstOrCreate(
                    ['province_id' => $province->id, 'name' => $namaKabupaten],
                    ['code' => substr(md5($province->id . '|' . $namaKabupaten), 0, 5)]
                );

                // Sanitasi string kategori agar tidak melebihi batas database (misal max 50 karakter)
                $rawKategori = trim((string)($row[22] ?? '-'));
                $kategori = strlen($rawKategori) > 50 ? substr($rawKategori, 0, 50) : $rawKategori;

                SpmData::updateOrCreate(
                    [
                        'regency_id' => $regency->id,
                        'year' => $year,
                    ],
                    [
                        'import_history_id' => $importHistory->id,
                        'jml_kecamatan' => (int) ($row[6] ?? 0),
                        'jml_pos' => (int) ($row[7] ?? 0),
                        'pr_pos_kecamatan' => (float) ($row[8] ?? 0),
                        'total_sdm' => (int) ($row[9] ?? 0),
                        'sdm_sertifikat' => (int) ($row[10] ?? 0),
                        'pr_sdm_sertifikat' => (float) ($row[11] ?? 0),
                        'jml_desa' => (int) ($row[12] ?? 0),
                        'jml_redkar' => (int) ($row[13] ?? 0),
                        'pr_redkar' => (float) ($row[14] ?? 0),
                        'dimensi_kelembagaan' => (float) ($row[15] ?? 0),
                        'dimensi_perencanaan' => (float) ($row[16] ?? 0),
                        'dimensi_capaian' => (float) ($row[17] ?? 0),
                        'dimensi_sarpras' => (float) ($row[18] ?? 0),
                        'dimensi_sdm_sertifikat' => (float) ($row[19] ?? 0),
                        'dimensi_pemberdayaan' => (float) ($row[20] ?? 0),
                        'nilai_akhir' => (float) ($row[21] ?? 0),
                        'kategori' => $kategori,
                    ]
                );

                $successCount++;
            }

            $importHistory->update(['success_row' => $successCount]);
            DB::commit();
            Storage::disk('local')->delete($path);

            return redirect('/admin/dashboard?year=' . urlencode($year))
                ->with('success', "Berhasil mengimpor {$successCount} data SPM tahun {$year} ke Dashboard!");

        } catch (Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal mengimpor data: ' . $e->getMessage());
        }
    }
}
