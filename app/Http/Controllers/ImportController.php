<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\SpmDataImport;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB; // Wajib untuk Transaction
use App\Models\Province; // Wajib untuk Master Data
use App\Models\Regency;
use App\Models\ImportHistory;
use App\Models\SpmData;

class ImportController extends Controller
{
    // 1. Menampilkan Form Upload
    public function index()
    {
        return view('import.index');
    }

    // 2. Memproses Upload dan Menampilkan Preview
    public function preview(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xls,xlsx',
            'year' => 'required|numeric|digits:4'
        ], [
            'file.required' => 'File Excel wajib diunggah.',
            'file.mimes' => 'Format file harus .xls atau .xlsx',
            'year.required' => 'Tahun data wajib diisi.'
        ]);

        // Simpan nama file untuk digunakan di tahap import selanjutnya
        $fileName = time() . '_' . $request->file('file')->getClientOriginalName();
        $request->file('file')->storeAs('temp', $fileName);

        // Baca data dari Excel langsung dari request file
        $dataExcel = Excel::toArray(new SpmDataImport, $request->file('file'))[0];

        $validData = [];
        $invalidData = [];

        // Lakukan iterasi untuk validasi sederhana
        foreach ($dataExcel as $index => $row) {
            // Cek apakah kolom Provinsi (indeks 1) dan Kabupaten (indeks 2) ada isinya
            // Jika kosong, abaikan (mungkin baris kosong di akhir excel)
            if (empty($row[1]) && empty($row[2])) {
                continue;
            }

            // Validasi: Pastikan nilai akhir (indeks 21) adalah angka
            $nilaiAkhir = $row[21];
            if (!is_numeric($nilaiAkhir) && !is_null($nilaiAkhir)) {
                $invalidData[] = [
                    'row' => $index + 6, // +6 karena startRow kita 6
                    'provinsi' => $row[1],
                    'kabupaten' => $row[2],
                    'alasan' => 'Nilai Akhir bukan format angka'
                ];
            } else {
                $validData[] = $row;
            }
        }

        $year = $request->year;

        return view('import.preview', compact('validData', 'invalidData', 'fileName', 'year'));
    }

    // 3. Menyimpan Data ke Database
    public function store(Request $request)
    {
        $request->validate([
            'file_name' => 'required',
            'year' => 'required'
        ]);

        $fileName = $request->file_name;
        $year = $request->year;
        $filePath = storage_path('app/temp/' . $fileName);

        // Pastikan file masih ada
        if (!file_exists($filePath)) {
            return redirect('/admin/import')->with('error', 'File temporary tidak ditemukan. Silakan upload ulang.');
        }

        // Baca ulang data dari file temporary
        $dataExcel = Excel::toArray(new SpmDataImport, $filePath)[0];

        $successCount = 0;

        // Gunakan Transaction agar jika di tengah jalan error, semua data batal disimpan (aman)
        DB::beginTransaction();

        try {
            // 1. Catat riwayat import terlebih dahulu
            $importHistory = ImportHistory::create([
                'user_id' => auth()->id(),
                'file_name' => $fileName,
                'year' => $year,
                'status' => 'Berhasil', // Default, nanti diupdate kalau ada error parsial
                'total_row' => count($dataExcel),
                'success_row' => 0
            ]);

            // 2. Looping data untuk disimpan
            foreach ($dataExcel as $row) {
                // Abaikan baris kosong
                if (empty($row[1]) && empty($row[2])) {
                    continue;
                }

                $namaProvinsi = trim($row[1]);
                $namaKabupaten = trim($row[2]);

                // A. Cari atau buat Data Provinsi
                $province = Province::firstOrCreate(
                    ['name' => $namaProvinsi],
                    ['code' => substr(md5($namaProvinsi), 0, 5)] // Generate kode sementara
                );

                // B. Cari atau buat Data Kabupaten/Kota
                $regency = Regency::firstOrCreate(
                    ['province_id' => $province->id, 'name' => $namaKabupaten],
                    ['code' => substr(md5($namaKabupaten), 0, 8)] // Generate kode sementara
                );

                // C. Simpan/Update Data SPM (UpdateOrCreate agar jika diimport ulang tahun yang sama, datanya menimpa)
                SpmData::updateOrCreate(
                    [
                        'regency_id' => $regency->id,
                        'year' => $year
                    ],
                    [
                        'import_history_id' => $importHistory->id,
                        'jml_kecamatan' => (int)$row[6],
                        'jml_pos' => (int)$row[7],
                        'pr_pos_kecamatan' => (float)$row[8] * 100,
                        'total_sdm' => (int)$row[9],
                        'sdm_sertifikat' => (int)$row[10],
                        'pr_sdm_sertifikat' => (float)$row[11] * 100,
                        'jml_desa' => (int)$row[12],
                        'jml_redkar' => (int)$row[13],
                        'pr_redkar' => (float)$row[14] * 100,
                        'dimensi_kelembagaan' => (float)$row[15],
                        'dimensi_perencanaan' => (float)$row[16],
                        'dimensi_capaian' => (float)$row[17],
                        'dimensi_sarpras' => (float)$row[18],
                        'dimensi_sdm_sertifikat' => (float)$row[19],
                        'dimensi_pemberdayaan' => (float)$row[20],
                        'nilai_akhir' => (float)$row[21],
                        'kategori' => $row[22]
                    ]
                );

                $successCount++;
            }

            // Update jumlah berhasil di tabel history
            $importHistory->update(['success_row' => $successCount]);

            DB::commit();

            // Hapus file temporary setelah selesai
            unlink($filePath);

            return redirect('/admin/dashboard')->with('success', "Import berhasil! {$successCount} data SPM tahun {$year} telah masuk ke database.");

        } catch (\Exception $e) {
            DB::rollback();
            return redirect('/admin/import')->with('error', 'Terjadi kesalahan saat menyimpan ke database: ' . $e->getMessage());
        }
    }
}
