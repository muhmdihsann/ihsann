<?php

namespace Tests\Feature;

use App\Models\ImportHistory;
use App\Models\Province;
use App\Models\Regency;
use App\Models\SpmData;
use App\Models\User;
use App\Services\SpmDatasetImportService;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\TestCase;

class PublicStatisticsAndImportTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_public_statistics_and_map_use_the_selected_year(): void
    {
        $province = Province::create(['name' => 'Jawa Barat', 'code' => '32000']);
        $regency = Regency::create(['province_id' => $province->id, 'name' => 'Kota Bandung', 'code' => '32730']);
        $operator = User::factory()->create();
        $import2024 = ImportHistory::create(['user_id' => $operator->id, 'file_name' => 'spm-2024.xlsx', 'year' => 2024, 'status' => ImportHistory::Published]);
        $import2025 = ImportHistory::create(['user_id' => $operator->id, 'file_name' => 'spm-2025.xlsx', 'year' => 2025, 'status' => ImportHistory::Published]);
        SpmData::create(['import_history_id' => $import2024->id, 'regency_id' => $regency->id, 'year' => 2024, 'nilai_akhir' => 50, 'kategori' => 'Kurang']);
        SpmData::create(['import_history_id' => $import2025->id, 'regency_id' => $regency->id, 'year' => 2025, 'nilai_akhir' => 90, 'kategori' => 'Sangat Baik', 'jml_pos' => 4, 'total_sdm' => 40]);

        $this->get('/?year=2025')
            ->assertSee('Tahun 2025')
            ->assertSee('90,00')
            ->assertSee('Peta Sebaran SPM')
            ->assertSee('JAWABARAT');
    }

    public function test_excel_import_persists_data_for_the_selected_year(): void
    {
        Storage::fake('local');
        $operator = User::factory()->create();
        $file = UploadedFile::fake()->createWithContent('spm.xlsx', $this->spreadsheetContent());

        $this->actingAs($operator)
            ->post(route('import.preview'), ['year' => 2025, 'file' => $file])
            ->assertSee('2')
            ->assertSee('Baris Data Valid');

        $this->actingAs($operator)
            ->post(route('import.store'))
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseCount('spm_data', 2);
        $this->assertDatabaseHas('spm_data', ['year' => 2025, 'nilai_akhir' => 90]);
        $this->assertDatabaseHas('regencies', ['name' => 'Kota Bandung']);
    }

    public function test_preview_carries_forward_province_and_skips_province_headers_without_persisting_data(): void
    {
        $rows = [
            $this->importRow('ACEH', '-'),
            $this->importRow('-', 'KAB. ACEH SELATAN', 'Sangat Baik'),
            $this->importRow('', 'KAB. ACEH TENGGARA', '-'),
            $this->importRow('SUMATERA UTARA', null),
            $this->importRow('-', 'KAB. TAPANULI TENGAH', 'Baik'),
            $this->importRow(null, 'KAB. TAPANULI UTARA', 'Cukup'),
            $this->importRow('JAWA BARAT', 'KAB. BOGOR', 'Kurang'),
            $this->importRow('RATA-RATA', 'RATA-RATA'),
        ];

        $preview = app(SpmDatasetImportService::class)->validateRows($rows);

        $this->assertSame(5, count($preview['valid']));
        $this->assertSame(0, count($preview['invalid']));
        $this->assertSame(
            ['ACEH', 'ACEH', 'SUMATERA UTARA', 'SUMATERA UTARA', 'JAWA BARAT'],
            array_column($preview['valid'], 'province'),
        );
        $this->assertSame(
            ['KAB. ACEH SELATAN', 'KAB. ACEH TENGGARA', 'KAB. TAPANULI TENGAH', 'KAB. TAPANULI UTARA', 'KAB. BOGOR'],
            array_column($preview['valid'], 'regency'),
        );
        $this->assertNull($preview['valid'][1]['values']['kategori']);

        $withoutProvince = app(SpmDatasetImportService::class)->validateRows([
            $this->importRow('-', 'KAB. TANPA PROVINSI', 'Baik'),
        ]);

        $this->assertSame([], $withoutProvince['valid']);
        $this->assertSame('provinsi wajib diisi', $withoutProvince['invalid'][0]['message']);

        Storage::fake('local');
        $operator = User::factory()->create();
        $file = UploadedFile::fake()->createWithContent('spm.xlsx', $this->hierarchicalSpreadsheetContent());

        $this->actingAs($operator)
            ->post(route('import.preview'), ['year' => 2025, 'file' => $file])
            ->assertSee('5')
            ->assertSee('Baris Data Valid')
            ->assertSee('KAB. ACEH SELATAN');

        $this->assertDatabaseCount('spm_data', 0);
        $this->assertDatabaseMissing('regencies', ['name' => '-']);
    }

    public function test_operator_login_still_redirects_to_the_admin_dashboard(): void
    {
        $operator = User::factory()->create(['email' => 'operator@example.test']);

        $this->post('/login', ['email' => $operator->email, 'password' => 'password'])
            ->assertRedirect('/admin/dashboard');
    }

    private function spreadsheetContent(): string
    {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray([
            [null, 'Jawa Barat', 'Kota Bandung', null, null, null, 30, 4, .5, 40, 20, .5, 100, 20, .2, 10, 10, 10, 10, 10, 10, 90, 'Sangat Baik'],
            [null, 'Jawa Barat', 'Kabupaten Bogor', null, null, null, 40, 2, .2, 20, 10, .5, 200, 40, .2, 10, 10, 10, 10, 10, 10, 80, 'Baik'],
        ], null, 'A6');

        $temporaryFile = tempnam(sys_get_temp_dir(), 'spm-test-');
        (new Xlsx($spreadsheet))->save($temporaryFile);
        $content = file_get_contents($temporaryFile);
        unlink($temporaryFile);

        return $content;
    }

    /** @return array<int, mixed> */
    private function importRow(?string $province, ?string $regency, ?string $category = null): array
    {
        return [null, $province, $regency, null, null, null, 30, 4, .5, 40, 20, .5, 100, 20, .2, 10, 10, 10, 10, 10, 10, 90, $category];
    }

    private function hierarchicalSpreadsheetContent(): string
    {
        $spreadsheet = new Spreadsheet;
        $spreadsheet->getActiveSheet()->fromArray([
            $this->importRow('ACEH', '-'),
            $this->importRow('-', 'KAB. ACEH SELATAN', 'Sangat Baik'),
            $this->importRow('', 'KAB. ACEH TENGGARA', '-'),
            $this->importRow('SUMATERA UTARA', null),
            $this->importRow('-', 'KAB. TAPANULI TENGAH', 'Baik'),
            $this->importRow(null, 'KAB. TAPANULI UTARA', 'Cukup'),
            $this->importRow('JAWA BARAT', 'KAB. BOGOR', 'Kurang'),
        ], null, 'A6');

        $temporaryFile = tempnam(sys_get_temp_dir(), 'spm-test-');
        (new Xlsx($spreadsheet))->save($temporaryFile);
        $content = file_get_contents($temporaryFile);
        unlink($temporaryFile);

        return $content;
    }
}
