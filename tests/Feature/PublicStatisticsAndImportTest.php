<?php

namespace Tests\Feature;

use App\Models\Province;
use App\Models\Regency;
use App\Models\SpmData;
use App\Models\User;
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
        SpmData::create(['regency_id' => $regency->id, 'year' => 2024, 'nilai_akhir' => 50, 'kategori' => 'Kurang']);
        SpmData::create(['regency_id' => $regency->id, 'year' => 2025, 'nilai_akhir' => 90, 'kategori' => 'Sangat Baik', 'jml_pos' => 4, 'total_sdm' => 40]);

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

        $fileName = basename(Storage::disk('local')->files('temp')[0]);

        $this->actingAs($operator)
            ->post(route('import.store'), ['year' => 2025, 'file_name' => $fileName])
            ->assertRedirect('/admin/dashboard?year=2025');

        $this->assertDatabaseCount('spm_data', 2);
        $this->assertDatabaseHas('spm_data', ['year' => 2025, 'nilai_akhir' => 90]);
        $this->assertDatabaseHas('regencies', ['name' => 'Kota Bandung']);
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
}
