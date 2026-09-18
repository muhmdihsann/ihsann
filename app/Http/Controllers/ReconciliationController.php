<?php

namespace App\Http\Controllers;

use App\Imports\SpmDataImport;
use App\Services\ReconciliationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Exception;

class ReconciliationController extends Controller
{
    // Melakukan analisis preview perbandingan (hasilnya dikirim ke halaman import utama di bagian bawah)
    public function preview(Request $request)
    {
        $request->validate([
            'file_lama' => 'required|mimes:xls,xlsx,csv',
            'file_baru' => 'required|mimes:xls,xlsx,csv',
            'year' => 'required|numeric|digits:4',
        ]);

        try {
            $pathLama = $request->file('file_lama')->store('temp', 'local');
            $pathBaru = $request->file('file_baru')->store('temp', 'local');

            $rawLama = Excel::toArray(new SpmDataImport, Storage::disk('local')->path($pathLama))[0] ?? [];
            $rawBaru = Excel::toArray(new SpmDataImport, Storage::disk('local')->path($pathBaru))[0] ?? [];

            $rowsLama = ReconciliationService::formatExcelRows($rawLama);
            $rowsBaru = ReconciliationService::formatExcelRows($rawBaru);

            $kolomKunci = 'Kabupaten / Kota';

            $hasilRekonsiliasi = ReconciliationService::cocokkanData($rowsLama, $rowsBaru, $kolomKunci);

            Storage::disk('local')->delete([$pathLama, $pathBaru]);

            // Mengembalikan ke view import.index agar tampil dalam satu halaman yang sama
            return view('import.index', [
                'regions' => $hasilRekonsiliasi['regions'],
                'allColumns' => $hasilRekonsiliasi['allColumns'],
                'rekonYear' => $request->year,
                'fileLama' => $request->file('file_lama')->getClientOriginalName(),
                'fileBaru' => $request->file('file_baru')->getClientOriginalName(),
            ]);

        } catch (Exception $e) {
            return back()->with('error_rekon', 'Gagal memproses perbandingan file: ' . $e->getMessage());
        }
    }
}
