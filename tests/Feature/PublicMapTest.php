<?php

namespace Tests\Feature;

use App\Models\ImportHistory;
use App\Models\Province;
use App\Models\Regency;
use App\Models\SpmData;
use App\Models\User;
use App\Services\MapAreaNameNormalizer;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class PublicMapTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_public_map_page_can_be_opened(): void
    {
        $this->get(route('peta'))
            ->assertOk()
            ->assertSee('Peta Persebaran Kinerja SPM Kebakaran')
            ->assertSee(route('peta.data'), false);
    }

    public function test_map_data_returns_the_selected_year_with_index_category_and_regencies(): void
    {
        $province = Province::create(['code' => '33000', 'name' => 'Jawa Tengah']);
        $regency = Regency::create(['province_id' => $province->id, 'code' => '33001', 'name' => 'Kab. Cilacap']);
        SpmData::create(['import_history_id' => $this->publishedImport(2025)->id, 'regency_id' => $regency->id, 'year' => 2025, 'nilai_akhir' => 82.36]);
        SpmData::create(['import_history_id' => $this->publishedImport(2026)->id, 'regency_id' => $regency->id, 'year' => 2026, 'nilai_akhir' => 87]);

        $this->getJson(route('peta.data', ['year' => 2025]))
            ->assertOk()
            ->assertJsonPath('year', 2025)
            ->assertJsonPath('provinces.0.geojson_code', '33')
            ->assertJsonPath('provinces.0.province_name', 'Jawa Tengah')
            ->assertJsonPath('provinces.0.index', 82.36)
            ->assertJsonPath('provinces.0.spm', 82.36)
            ->assertJsonPath('provinces.0.category', 'Baik')
            ->assertJsonPath('provinces.0.regency_count', 1)
            ->assertJsonPath('provinces.0.regencies.0.name', 'Kab. Cilacap')
            ->assertJsonPath('provinces.0.regencies.0.regency_name', 'Kab. Cilacap')
            ->assertJsonPath('provinces.0.regencies.0.index', 82.36)
            ->assertJsonPath('provinces.0.regencies.0.category', 'Baik');

        $this->getJson(route('peta.data', ['year' => 2026]))
            ->assertOk()
            ->assertJsonPath('year', 2026)
            ->assertJsonPath('provinces.0.index', 87)
            ->assertJsonPath('provinces.0.category', 'Sangat Baik');
    }

    public function test_draft_data_is_excluded_and_a_province_without_data_returns_null_metrics(): void
    {
        $province = Province::create(['code' => '32000', 'name' => 'Jawa Barat']);
        $regency = Regency::create(['province_id' => $province->id, 'code' => '32001', 'name' => 'Kab. Bogor']);
        $draftImport = ImportHistory::create([
            'user_id' => User::factory()->create()->id,
            'file_name' => 'draft.xlsx',
            'year' => 2025,
            'status' => ImportHistory::Draft,
        ]);
        SpmData::create(['import_history_id' => $draftImport->id, 'regency_id' => $regency->id, 'year' => 2025, 'nilai_akhir' => 10]);

        $this->getJson(route('peta.data', ['year' => 2025]))
            ->assertOk()
            ->assertJsonPath('year', null)
            ->assertJsonPath('provinces.0.index', null)
            ->assertJsonPath('provinces.0.category', null)
            ->assertJsonPath('provinces.0.regencies', []);
    }

    public function test_map_normalizes_verified_yogyakarta_names_and_derives_all_categories_from_the_index(): void
    {
        $normalizer = app(MapAreaNameNormalizer::class);
        $this->assertSame('JAWATENGAH', $normalizer->provinceKey('PROVINSI JAWA TENGAH'));
        $this->assertSame('JAWABARAT', $normalizer->provinceKey('JAWA BARAT '));
        $this->assertSame('ACEH', $normalizer->provinceKey('ACEH'));
        $this->assertSame('SUMATERAUTARA', $normalizer->provinceKey('SUMATERA UTARA'));
        $this->assertSame('YOGYAKARTA', $normalizer->provinceKey('Daerah Istimewa Yogyakarta'));

        $province = Province::create(['code' => '34000', 'name' => 'DI Yogyakarta']);
        $regency = Regency::create(['province_id' => $province->id, 'code' => '34001', 'name' => 'Kabupaten Sleman']);
        foreach ([2025 => 87, 2026 => 72, 2027 => 60, 2028 => 40] as $year => $index) {
            SpmData::create([
                'import_history_id' => $this->publishedImport($year)->id,
                'regency_id' => $regency->id,
                'year' => $year,
                'nilai_akhir' => $index,
            ]);
        }

        $this->getJson(route('peta.data', ['year' => 2025]))
            ->assertJsonPath('provinces.0.map_key', 'YOGYAKARTA')
            ->assertJsonPath('provinces.0.category', 'Sangat Baik');
        $this->getJson(route('peta.data', ['year' => 2026]))->assertJsonPath('provinces.0.category', 'Baik');
        $this->getJson(route('peta.data', ['year' => 2027]))->assertJsonPath('provinces.0.category', 'Cukup');
        $this->getJson(route('peta.data', ['year' => 2028]))->assertJsonPath('provinces.0.category', 'Kurang');
    }

    public function test_deleted_dataset_data_is_not_returned_by_the_public_map(): void
    {
        $province = Province::create(['code' => '33000', 'name' => 'Jawa Tengah']);
        $regency = Regency::create(['province_id' => $province->id, 'code' => '33001', 'name' => 'Kabupaten Semarang']);
        $import = $this->publishedImport(2025);
        SpmData::create(['import_history_id' => $import->id, 'regency_id' => $regency->id, 'year' => 2025, 'nilai_akhir' => 87]);
        $import->spmData()->delete();
        $import->delete();

        $this->getJson(route('peta.data', ['year' => 2025]))
            ->assertJsonPath('year', null)
            ->assertJsonPath('provinces.0.spm', null)
            ->assertJsonPath('provinces.0.regencies', []);
    }

    private function publishedImport(int $year): ImportHistory
    {
        return ImportHistory::create([
            'user_id' => User::factory()->create()->id,
            'file_name' => "published-{$year}.xlsx",
            'year' => $year,
            'status' => ImportHistory::Published,
        ]);
    }
}
