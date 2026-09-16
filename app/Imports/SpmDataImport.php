<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;

class SpmDataImport implements ToArray, WithStartRow, WithCalculatedFormulas
{
    /**
     * Berdasarkan analisis file Excel 'REKAP_Indeks_7_Des',
     * data asli (bukan header) dimulai pada baris ke-6.
     */
    public function startRow(): int
    {
        return 6;
    }

    public function array(array $array): void
    {
        // Kosongkan karena kita hanya mengambil datanya lewat Excel::toArray
    }
}
