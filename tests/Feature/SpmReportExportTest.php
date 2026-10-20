<?php

namespace Tests\Feature;

use App\Exports\SpmReportExport;
use App\Models\ImportHistory;
use App\Models\Province;
use App\Models\Regency;
use App\Models\SpmData;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;

class SpmReportExportTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_excel_report_matches_published_data_and_screenshot_columns(): void
    {
        $operator = User::factory()->create();
        $province = Province::create(['name' => 'Jawa Barat', 'code' => '32000']);
        $regency = Regency::create(['province_id' => $province->id, 'name' => 'KAB. BOGOR', 'code' => '32001']);
        $publishedImport = ImportHistory::create([
            'user_id' => $operator->id,
            'file_name' => 'published-2025.xlsx',
            'year' => 2025,
            'status' => ImportHistory::Published,
        ]);
        SpmData::create([
            'import_history_id' => $publishedImport->id,
            'regency_id' => $regency->id,
            'year' => 2025,
            'kelembagaan' => 'BPBD',
            'perencanaan_anggaran' => 'RKPD',
            'capaian_spm' => 92.5,
            'jml_kecamatan' => 40,
            'jml_pos' => 8,
            'pr_pos_kecamatan' => 20,
            'total_sdm' => 120,
            'sdm_sertifikat' => 30,
            'pr_sdm_sertifikat' => 25,
            'jml_desa' => 416,
            'jml_redkar' => 250,
            'pr_redkar' => 60.1,
            'dimensi_kelembagaan' => 15,
            'dimensi_perencanaan' => 20,
            'dimensi_capaian' => 20,
            'dimensi_sarpras' => 10,
            'dimensi_sdm_sertifikat' => 10,
            'dimensi_pemberdayaan' => 20,
            'nilai_akhir' => 95,
            'kategori' => 'Sangat Baik',
        ]);

        $draftImport = ImportHistory::create([
            'user_id' => $operator->id,
            'file_name' => 'draft-2025.xlsx',
            'year' => 2025,
            'status' => ImportHistory::Draft,
        ]);
        SpmData::create([
            'import_history_id' => $draftImport->id,
            'regency_id' => $regency->id,
            'year' => 2025,
            'nilai_akhir' => 10,
            'kategori' => 'Kurang',
        ]);

        $this->actingAs($operator)
            ->get(route('export.index'))
            ->assertOk()
            ->assertSee('Unduh Excel (.xlsx)')
            ->assertDontSee('PDF');

        Excel::fake();
        $this->actingAs($operator)
            ->get(route('export.excel', ['year' => 2025]))
            ->assertOk();

        Excel::assertDownloaded('Laporan_SPM_Kebakaran_Tahun_2025.xlsx', function (SpmReportExport $export): bool {
            $rows = $export->array();
            $dataRows = array_values(array_filter($rows, fn (array $row): bool => isset($row[0]) && is_int($row[0])));

            $this->assertSame([
                'No', 'Provinsi', 'Kabupaten/Kota', 'Kelembagaan', 'Perencanaan Anggaran', 'Capaian SPM',
                'Jumlah Kecamatan', 'Jumlah Pos Pemadam Kebakaran', 'Pos Pemadam Kebakaran di Kecamatan (%)',
                'Jumlah SDM Pemadam Kebakaran', 'Jumlah SDM Tersertifikasi', 'Persentase SDM Tersertifikasi (%)',
                'Jumlah Desa/Kelurahan', 'Desa yang Sudah Membentuk REDKAR', 'Persentase REDKAR di Desa/Kelurahan (%)',
                'Dimensi Kelembagaan', 'Dimensi Perencanaan Anggaran', 'Dimensi Capaian SPM',
                'Dimensi Pemenuhan Sarpras', 'Dimensi Pemenuhan SDM Tersertifikasi',
                'Dimensi Pemberdayaan Masyarakat', 'Nilai Akhir', 'Keterangan',
            ], $rows[4]);
            $this->assertCount(1, $dataRows);
            $this->assertSame('Jawa Barat', $dataRows[0][1]);
            $this->assertSame('KAB. BOGOR', $dataRows[0][2]);
            $this->assertSame('BPBD', $dataRows[0][3]);
            $this->assertSame('RKPD', $dataRows[0][4]);
            $this->assertSame(92.5, $dataRows[0][5]);
            $this->assertSame(250, $dataRows[0][13]);
            $this->assertSame(95.0, $dataRows[0][21]);
            $this->assertSame('Sangat Baik', $dataRows[0][22]);

            return true;
        });

        $this->actingAs($operator)
            ->get(route('export.excel', ['year' => 2024]))
            ->assertSessionHasErrors('year');
    }

    public function test_excel_download_generates_a_real_xlsx_response(): void
    {
        $operator = User::factory()->create();
        $province = Province::create(['name' => 'Jawa Barat', 'code' => '32000']);
        $regency = Regency::create(['province_id' => $province->id, 'name' => 'KAB. BOGOR', 'code' => '32001']);
        $import = ImportHistory::create([
            'user_id' => $operator->id,
            'file_name' => 'published-2025.xlsx',
            'year' => 2025,
            'status' => ImportHistory::Published,
        ]);
        SpmData::create([
            'import_history_id' => $import->id,
            'regency_id' => $regency->id,
            'year' => 2025,
            'nilai_akhir' => 95,
            'kategori' => 'Sangat Baik',
        ]);

        $this->actingAs($operator)
            ->get(route('export.excel', ['year' => 2025]))
            ->assertOk()
            ->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->assertHeader('content-disposition', 'attachment; filename=Laporan_SPM_Kebakaran_Tahun_2025.xlsx');
    }
}
