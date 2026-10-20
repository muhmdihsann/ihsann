<?php

namespace App\Exports;

use App\Models\SpmData;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SpmReportExport implements FromArray, WithColumnFormatting, WithColumnWidths, WithEvents, WithStrictNullComparison, WithStyles
{
    public function __construct(private readonly int $year) {}

    /** @return array<int, array<int, int|float|string|null>> */
    public function array(): array
    {
        $records = SpmData::query()
            ->where('spm_data.year', $this->year)
            ->join('regencies as report_regencies', 'spm_data.regency_id', '=', 'report_regencies.id')
            ->join('provinces as report_provinces', 'report_regencies.province_id', '=', 'report_provinces.id')
            ->select('spm_data.*')
            ->with('regency')
            ->orderBy('report_provinces.name')
            ->orderBy('report_regencies.name')
            ->get();

        $headings = [
            'Kabupaten/Kota',
            'Kelembagaan',
            'Perencanaan Anggaran',
            'Capaian SPM',
            'Jumlah Kecamatan',
            'Jumlah Pos Pemadam Kebakaran',
            'Pos Pemadam Kebakaran di Kecamatan (%)',
            'Jumlah SDM Pemadam Kebakaran',
            'Jumlah SDM Tersertifikasi',
            'Persentase SDM Tersertifikasi (%)',
            'Jumlah Desa/Kelurahan',
            'Desa yang Sudah Membentuk REDKAR',
            'Persentase REDKAR di Desa/Kelurahan (%)',
            'Dimensi Kelembagaan',
            'Dimensi Perencanaan Anggaran',
            'Dimensi Capaian SPM',
            'Dimensi Pemenuhan Sarpras',
            'Dimensi Pemenuhan SDM Tersertifikasi',
            'Dimensi Pemberdayaan Masyarakat',
            'Nilai Akhir',
            'Keterangan',
        ];

        $rows = [
            $headings,
        ];

        foreach ($records as $record) {
            $rows[] = [
                $record->regency->name,
                $record->kelembagaan,
                $record->perencanaan_anggaran,
                $record->capaian_spm === null ? null : (float) $record->capaian_spm,
                (int) $record->jml_kecamatan,
                (int) $record->jml_pos,
                $this->valueOrDash($record->pr_pos_kecamatan),
                (int) $record->total_sdm,
                (int) $record->sdm_sertifikat,
                $this->valueOrDash($record->pr_sdm_sertifikat),
                (int) $record->jml_desa,
                (int) $record->jml_redkar,
                $this->valueOrDash($record->pr_redkar),
                (float) $record->dimensi_kelembagaan,
                (float) $record->dimensi_perencanaan,
                (float) $record->dimensi_capaian,
                (float) $record->dimensi_sarpras,
                (float) $record->dimensi_sdm_sertifikat,
                (float) $record->dimensi_pemberdayaan,
                (float) $record->nilai_akhir,
                $record->kategori,
            ];
        }

        return $rows;
    }

    /** @return array<string, string> */
    public function columnFormats(): array
    {
        return [
            'D' => '0.00',
            'G' => '0.00',
            'J' => '0.00',
            'M' => '0.00',
            'N' => '0.00',
            'O' => '0.00',
            'P' => '0.00',
            'Q' => '0.00',
            'R' => '0.00',
            'S' => '0.00',
            'T' => '0.00',
        ];
    }

    /** @return array<string, int> */
    public function columnWidths(): array
    {
        return [
            'A' => 30, 'B' => 17, 'C' => 22, 'D' => 13, 'E' => 14, 'F' => 18,
            'G' => 20, 'H' => 19, 'I' => 16, 'J' => 22, 'K' => 18, 'L' => 20,
            'M' => 18, 'N' => 21, 'O' => 21, 'P' => 22, 'Q' => 22, 'R' => 22,
            'S' => 24, 'T' => 20, 'U' => 16,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => '000000']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'D9D9D9']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
            ],
        ];
    }

    /** @return array<class-string, callable> */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event): void {
                $sheet = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow();
                $sheet->freezePane('B2');
                $sheet->setAutoFilter("A1:U{$lastRow}");
                $sheet->getRowDimension(1)->setRowHeight(48);
                if ($lastRow > 1) {
                    $sheet->getStyle("A1:U{$lastRow}")->getBorders()->getAllBorders()
                        ->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('000000');
                    $sheet->getStyle("B2:B{$lastRow}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FFFF00');
                    $sheet->getStyle("C2:C{$lastRow}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FFD966');
                    $sheet->getStyle("E2:G{$lastRow}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('A9D18E');
                    $sheet->getStyle("N2:S{$lastRow}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('D9D9D9');
                    $sheet->getStyle("U2:U{$lastRow}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('D9D9D9');
                }
                $sheet->getPageSetup()->setOrientation('landscape')->setFitToWidth(1)->setFitToHeight(0);
                $sheet->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(1, 1);
            },
        ];
    }

    private function valueOrDash(mixed $value): int|float|string|null
    {
        return $value === null ? null : (float) $value;
    }
}
