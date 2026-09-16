<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SpmData;
use App\Models\Province;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SpmExport;
use Barryvdh\DomPDF\Facade\Pdf; // Kita gunakan DomPDF bawaan Laravel untuk PDF

class ExportController extends Controller
{
    // Menampilkan halaman menu cetak laporan
    public function index()
    {
        $years = SpmData::select('year')->distinct()->orderBy('year', 'desc')->pluck('year');
        $provinces = Province::orderBy('name', 'asc')->get();

        return view('export.index', compact('years', 'provinces'));
    }

    // 1. Ekspor ke Excel
    public function exportExcel(Request $request)
    {
        $year = $request->year ?? date('Y');
        $fileName = 'Laporan_SPM_Kebakaran_Tahun_' . $year . '.xlsx';

        // Kita pakai Unduhan sederhana pakai collection macro atau membuat class export
        return Excel::download(new class($year) implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings, \Maatwebsite\Excel\Concerns\WithMapping {
            private $year;
            public function __construct($year) { $this->year = $year; }

            public function collection() {
                return SpmData::with(['regency.province'])->where('year', $this->year)->get();
            }

            public function headings(): array {
                return ['No', 'Tahun', 'Provinsi', 'Kabupaten/Kota', 'Jumlah Pos', 'Total SDM', 'Nilai Akhir', 'Kategori'];
            }

            public function map($row): array {
                static $no = 1;
                return [
                    $no++,
                    $row->year,
                    $row->regency->province->name ?? '-',
                    $row->regency->name ?? '-',
                    $row->jml_pos,
                    $row->total_sdm,
                    $row->nilai_akhir,
                    $row->kategori
                ];
            }
        }, $fileName);
    }

    // 2. Ekspor ke PDF
    public function exportPdf(Request $request)
    {
        $year = $request->year ?? date('Y');
        $data = SpmData::with(['regency.province'])->where('year', $year)->get();

        $pdf = Pdf::loadView('export.pdf', compact('data', 'year'));
        $pdf->setPaper('a4', 'landscape'); // Atur kertas lanskap agar tabelnya muat

        return $pdf->download('Laporan_SPM_Kebakaran_Tahun_' . $year . '.pdf');
    }
}
